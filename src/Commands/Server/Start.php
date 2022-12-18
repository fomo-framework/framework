<?php

namespace Fomo\Commands\Server;

use Exception;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Console\Style;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'server:start' , description: 'start http server')]
class Start extends Command
{
    protected Style $output;
    
    protected function configure(): void
    {
        $this->addOption('daemonize', 'd', InputOption::VALUE_NONE, 'The program works in the background');
        $this->addOption('watch', 'w', InputOption::VALUE_NONE, 'If there is a change in the program code, it applies the changes instantly');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /*
         * load Fomo style
         * resolve configs
         */
        $this->output = new Style($input, $output);
        $configs = resolve('config');
        /*
         * check exist swoole extension
         */
        if (!extension_loaded('swoole') && !extension_loaded('openswoole')){
            $this->output->error("the swoole extension is not found" , true);
            return Command::FAILURE;
        }

        /*
         * check not used daemonize and watch mode currency
         */
        if ($input->getOption('daemonize') && $input->getOption('watch')){
            $this->output->error('cannot use watcher in daemonize mode' , true);
            return Command::FAILURE;
        }

        /*
         * check server is running
         */
        if (httpServerIsRunning()){
            $this->output->error("failed to listen server port[{$configs->get('server.host')}:{$configs->get('server.port')}], Error: Address already" , true);

            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Do you want the server to terminate? (defaults to no)',
                ['no', 'yes'],
                0
            );
            $question->setErrorMessage('Your selection is invalid.');

            $answer = $helper->ask($input, $output, $question);


            if ($answer != 'yes'){
                return Command::FAILURE;
            }

            posix_kill(getMasterProcessId() , SIGTERM);
            posix_kill(getManagerProcessId() , SIGTERM);

            if (!is_null(getWatcherProcessId()) && posix_kill(getWatcherProcessId(), SIG_DFL)){
                posix_kill(getWatcherProcessId(), SIGTERM);
            }

            foreach (getWorkerProcessIds() as $processId) {
                posix_kill($processId , SIGTERM);
            }

            sleep(1);
        }

        /*
         * check ssl certificate file
         */
        if (!is_null($configs->get('server.ssl.ssl_cert_file')) && !file_exists($configs->get('server.ssl.ssl_cert_file'))){
            $this->output->error("ssl certificate file is not found" , true);
            return Command::FAILURE;
        }

        /*
         * check ssl certificate key
         */
        if (!is_null($configs->get('server.ssl.ssl_key_file')) && !file_exists($configs->get('server.ssl.ssl_key_file'))){
            $this->output->error("ssl key file is not found" , true);
            return Command::FAILURE;
        }

        /*
         * delete old routes closure cache files
         */
        if (file_exists(storagePath('routes'))){
            $dir = storagePath('routes');
            foreach (array_diff(scandir($dir), ['.', '..']) as $file) {
                unlink("$dir/$file");
            }
        }else {
            mkdir(storagePath('routes'));
        }

        /*
         * create listen message
         */
        $protocol = !is_null($configs->get('server.ssl.ssl_cert_file')) && !is_null($configs->get('server.ssl.ssl_cert_file')) ? 'https' : 'http';
        $listenMessage = "listen on $protocol://{$configs->get('server.host')}:{$configs->get('server.port')}";

        /*
         * send running server
         * send listen message
         */
        $this->output->success('http server running…');
        $this->output->info($listenMessage , true);

        /*
         * check if exist daemonize not send general information
         */
        if(! $input->getOption('daemonize')){
            /*
             * create socket type of server
             */
            $serverSocketType = match ($configs->get('server.sockType')){
                SWOOLE_SOCK_TCP => 'TCP' ,
                SWOOLE_SOCK_UDP => 'UDP' ,
                default => 'other type'
            };

            /*
             * create general information table
             */
            $table = new Table($output);
            $table
                ->setHeaderTitle('general information')
                ->setHeaders([
                    '<fg=#FFCB8B;options=bold> PHP VERSION </>' ,
                    '<fg=#FFCB8B;options=bold> FOMO VERSION </>' ,
                    '<fg=#FFCB8B;options=bold> WORKER COUNT </>' ,
                    '<fg=#FFCB8B;options=bold> SOCKET TYPE </>' ,
                    '<fg=#FFCB8B;options=bold> WATCH MODE </>'
                ])
                ->setRows([
                    [
                        '<options=bold> '. PHP_VERSION .' </>' ,
                        '<options=bold> ' . app()->version() . ' </>' ,
                        "<options=bold> {$configs->get('server.additional.worker_num')} </>" ,
                        "<options=bold> $serverSocketType </>" ,
                        $input->getOption('watch') ? '<fg=#C3E88D;options=bold> ACTIVE </>' : "<fg=#FF5572;options=bold> DEACTIVE </>"
                    ] ,
                ]);
            $table->setHorizontal();
            $table->render();

            /*
             * send info message for stop server
             */
            $this->output->info('Press Ctrl+C to stop the server');
        }

        /*
         * create and start server
         */
        $server = new Process([
            (new PhpExecutableFinder())->find(),
            'server',
            realpath('./'),
            (bool) $input->getOption('daemonize')
        ], __DIR__.'/../../Servers/Http', []);
        $server->start();

        return $this->processOutputs($server, $input);
    }

    protected function startWatcherServer(InputInterface $input): Process|bool
    {
        if ($input->getOption('watch')){
            $server = tap(new Process([
                (new PhpExecutableFinder())->find(),
                'watcher',
                realpath('./')
            ], __DIR__.'/../../Servers', []));
            $server->start();

            return $server;
        }

        return false;
    }

    protected function processOutputs(Process $server, InputInterface $input): int
    {
        while (! $server->isStarted()) {
            sleep(1);
        }

        $watcher = $this->startWatcherServer($input);

        try {
            while ($server->isRunning()) {
                $this->writeServerOutput($server);

                if ($watcher instanceof Process &&
                    $watcher->isRunning() &&
                    $watcher->getIncrementalOutput()) {
                    $this->output->info('Application change detected. Restarting workers…');

                    $this->reloadServer();
                } elseif ($watcher->isTerminated()) {
                    $this->output->error(
                        'Watcher process has terminated. Please ensure Node and chokidar are installed.'.PHP_EOL.
                        $watcher->getErrorOutput()
                    );

                    return 1;
                }

                usleep(500 * 1000);
            }

            $this->writeServerOutput($server);
        } catch (Exception) {
            return 1;
        } finally {
            $this->stopServer();
        }

        return $server->getExitCode();
    }

    protected function writeServerOutput(Process $server)
    {
        [$output, $errorOutput] = $this->getServerOutput($server);

        Str::of($output)
            ->explode("\n")
            ->filter()
            ->each(function ($output) {
                is_array($stream = json_decode($output, true))
                    ? $this->output->error(json_encode($stream))
                    : $this->output->error($output);
            });

        Str::of($errorOutput)
            ->explode("\n")
            ->filter()
            ->groupBy(fn ($output) => $output)
            ->each(function ($group) {
                is_array($stream = json_decode($output = $group->first(), true)) && isset($stream['type'])
                    ? $this->output->error(json_encode($stream))
                    : $this->raw($output);
            });
    }

    protected function getServerOutput(Process $server)
    {
        return tap([
            $server->getIncrementalOutput(),
            $server->getIncrementalErrorOutput(),
        ], fn () => $server->clearOutput()->clearErrorOutput());
    }

    protected function raw($string)
    {
        $this->output instanceof Style
            ? fwrite(STDERR, $string."\n")
            : $this->output->writeln($string);
    }

    protected function reloadServer()
    {
        posix_kill(getManagerProcessId(), SIGUSR1);
        posix_kill(getMasterProcessId(), SIGUSR1);

        foreach (getWorkerProcessIds() as $processId) {
            posix_kill($processId , SIGUSR1);
        }
    }

    protected function stopServer()
    {
        if (posix_kill(getMasterProcessId(), SIG_DFL)){
            posix_kill(getMasterProcessId(), SIGTERM);
        }

        if (posix_kill(getManagerProcessId(), SIG_DFL)){
            posix_kill(getManagerProcessId(), SIGTERM);
        }

        if (posix_kill(getWatcherProcessId(), SIG_DFL)){
            posix_kill(getWatcherProcessId(), SIGTERM);
        }

        foreach (getWorkerProcessIds() as $processId) {
            if (posix_kill($processId, SIG_DFL)){
                posix_kill($processId, SIGTERM);
            }
        }
    }
}

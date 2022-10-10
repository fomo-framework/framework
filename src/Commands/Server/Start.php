<?php

namespace Fomo\Commands\Server;

use Swoole\Process;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Fomo\Servers\Http;
use Fomo\Console\Style;
use Fomo\Servers\Watcher;

#[AsCommand(name: 'server:start' , description: 'start http server')]
class Start extends Command
{
    protected function configure(): void
    {
        $this->addOption('daemonize', 'd', InputOption::VALUE_NONE, 'The program works in the background');
        $this->addOption('watch', 'w', InputOption::VALUE_NONE, 'If there is a change in the program code, it applies the changes instantly');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /*
         * load tower style
         */
        $io = new Style($input, $output);

        /*
         * check exist swoole extension
         */
        if (!extension_loaded('swoole')){
            $io->error("the swoole extension is not found" , true);
            return Command::FAILURE;
        }

        /*
         * check not used daemonize and watch mode currency
         */
        if ($input->getOption('daemonize') && $input->getOption('watch')){
            $io->error('cannot use watcher in daemonize mode' , true);
            return self::FAILURE;
        }

        /*
         * check server is running
         */
        if (httpServerIsRunning()){
            $io->error('failed to listen server port[127.0.0.1:9004], Error: Address already' , true);
            return self::FAILURE;
        }

        /*
         * check ssl certificate file
         */
        if (!is_null(config('server.ssl.ssl_cert_file')) && !file_exists(config('server.ssl.ssl_cert_file'))){
            $io->error("ssl certificate file is not found" , true);
            return Command::FAILURE;
        }

        /*
         * check ssl certificate key
         */
        if (!is_null(config('server.ssl.ssl_cert_file')) && !file_exists(config('server.ssl.ssl_cert_file'))){
            $io->error("ssl key file is not found" , true);
            return Command::FAILURE;
        }

        /*
         * create listen message
         */
        $protocol = !is_null(config('server.ssl.ssl_cert_file')) && !is_null(config('server.ssl.ssl_cert_file')) ? 'https' : 'http';
        $listenMessage = "listen on $protocol://" . config('server.host') . ':' . config('server.port');

        /*
         * send running server
         * send listen message
         */
        $io->success('http server running…');
        $io->info($listenMessage , true);

        /*
         * check if exist daemonize not send general information
         */
        if(! $input->getOption('daemonize')){
            /*
             * create socket type of server
             */
            $serverSocketType = match (config('server.sockType')){
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
                        '<options=bold> '. PHP_VERSION .'</>' ,
                        '<options=bold> ' . FOMO_VERSION . ' </>' ,
                        '<options=bold> ' . config('server.additional.worker_num') . '</>' ,
                        "<options=bold> $serverSocketType</>" ,
                        $input->getOption('watch') ? '<fg=#C3E88D;options=bold> ACTIVE </>' : "<fg=#FF5572;options=bold> DEACTIVE </>"
                    ] ,
                ]);
            $table->setHorizontal();
            $table->render();

            /*
             * send info message for stop server
             */
            $io->info('Press Ctrl+C to stop the server');

            /*
             * create watcher server
             */
            if ($input->getOption('watch')){
                (new Process(function (Process $process) use($io) {
                    setWatcherProcessId($process->pid);
                    (new Watcher($io))->start();
                }))->start();
            }
        }

        /*
         * create and start server
         */
        (new Http())->createServer()->start($input->getOption('daemonize'));
        return self::SUCCESS;
    }
}

<?php

namespace Fomo\Servers;

use Swoole\Process;
use Fomo\Console\Style;

class Watcher
{
    protected array $paths;

    public function __construct(
        protected readonly Style $io
    ){
        $this->paths = config('server.watcher');
    }

    public function start(): void
    {
        while (!httpServerIsRunning()) {
            sleep(1);
        }

        while (httpServerIsRunning()){
            foreach ($this->paths as $path) {
                $this->check(basePath($path));
            }
            sleep(1);
        }
    }

    protected function check($dir): void
    {
        static $last_mtime;

        if (!$last_mtime) {
            $last_mtime = time();
        }

        clearstatcache();

        if (!is_dir($dir)) {
            if (!is_file($dir)) {
                return;
            }
            $iterator = [new \SplFileInfo($dir)];
        } else {
            $dir_iterator = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS);
            $iterator = new \RecursiveIteratorIterator($dir_iterator);
        }
        foreach ($iterator as $file) {
            if (is_dir($file)) {
                continue;
            }

            if ($last_mtime < $file->getMTime()) {
                $var = 0;
                exec('"'.PHP_BINARY . '" -l ' . $file, $out, $var);
                $last_mtime = $file->getMTime();
                if ($var) {
                    continue;
                }

                $this->io->info("{$file->getFilename()} has been changed. server reloaded");

                posix_kill(getManagerProcessId(), SIGUSR1);
                posix_kill(getMasterProcessId(), SIGUSR1);

                break;
            }
        }
    }
}
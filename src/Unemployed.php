<?php

namespace Tower;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Tower\Console\Color;
use Workerman\Timer;

class Unemployed
{
    protected array $extensions;

    public function __construct()
    {
        $this->extensions = ['php' , 'env'];
    }

    public function check(): void
    {
        Timer::add(1, function () {
            $this->checkFilesChange(basePath());
        });
    }

    protected function checkFilesChange(string $dir): void
    {
        static $lastMTime;
        if (!$lastMTime)
            $lastMTime = time();

        clearstatcache();

        if (!is_dir($dir)) {
            $iterator = [new SplFileInfo($dir)];
        } else {
            $dirIterator = new RecursiveDirectoryIterator($dir);
            $iterator = new RecursiveIteratorIterator($dirIterator);
        }
        foreach ($iterator as $file) {
            if (is_dir($file))
                continue;

            if ($lastMTime < $file->getMTime() && in_array($file->getExtension(), $this->extensions)) {
                $var = 0;
                exec(PHP_BINDIR . "/php -l " . $file, $out, $var);
                if ($var) {
                    $lastMTime = $file->getMTime();
                    continue;
                }
                echo Color::success($file . " update and reload");

                posix_kill(posix_getppid(), SIGUSR1);
                $lastMTime = $file->getMTime();
                break;
            }
        }
    }
}
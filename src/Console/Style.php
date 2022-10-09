<?php

namespace Fomo\Console;

use Symfony\Component\Console\Style\SymfonyStyle;

class Style extends SymfonyStyle
{
    public function ok($message , bool $withNewLine = false): void
    {
        $this->writeln([
            '',
            "  <fg=#C792EA;options=bold> OK </> $message",
        ]);

        if ($withNewLine){
            $this->newLine();
        }
    }

    public function success($message , bool $withNewLine = false): void
    {
        $this->writeln([
            '',
            "  <fg=#C3E88D;options=bold> SUCCESS </> $message",
        ]);

        if ($withNewLine){
            $this->newLine();
        }
    }

    public function info($message , bool $withNewLine = false): void
    {
        $this->writeln([
            '',
            "  <fg=#82AAFF;options=bold> INFO </> $message",
        ]);

        if ($withNewLine){
            $this->newLine();
        }
    }

    public function warning($message , bool $withNewLine = false): void
    {
        $this->writeln([
            '',
            "  <fg=#FFCB8B;options=bold> WARNING </> $message",
        ]);

        if ($withNewLine){
            $this->newLine();
        }
    }

    public function error($message , bool $withNewLine = false): void
    {
        $this->writeln([
            '',
            "  <fg=#FF5572;options=bold> ERROR </> $message",
        ]);

        if ($withNewLine){
            $this->newLine();
        }
    }

    public function failed($message , bool $withNewLine = false): void
    {
        $this->writeln([
            '',
            "  <fg=#FF5572;options=bold> FAILED </> $message",
        ]);

        if ($withNewLine){
            $this->newLine();
        }
    }

    public function done($message , bool $withNewLine = false): void
    {
        $this->writeln([
            '',
            "  <fg=#C3E88D;options=bold> DONE </> $message",
        ]);

        if ($withNewLine){
            $this->newLine();
        }
    }
}
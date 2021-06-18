<?php

namespace Tower\Exception;

use Carbon\Carbon;

class QueueException extends \Exception
{
    protected string $type;
    protected string $queue;
    protected array $data;

    public function __construct(string $type , string $queue , array $data , string $message , string $file , int $line)
    {
        $this->type = $type;
        $this->queue = $queue;
        $this->data = $data;
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
    }

    public function handle()
    {
        $log = fopen(storagePath() . "logs/queue.log", 'a');
        fwrite($log, $this->type . ' | ' .
            'queue : ' . $this->queue .' | ' .
            'data : ' . json_encode($this->data) . ' | ' .
            'time : ' . Carbon::now() . ' | ' .
            'message : ' . $this->getMessage() . ' | ' .
            'file : ' . $this->getFile() . ' | ' .
            'line : ' . $this->getLine() .
            PHP_EOL .
            '<------------------------------------------------------------------------------>' .
            PHP_EOL
        );
        fclose($log);
    }
}
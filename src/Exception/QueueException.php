<?php

namespace Tower\Exception;

use Exception;
use Tower\Log;

class QueueException extends Exception
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
        $data = json_encode($this->data);
        $message = $this->getMessage();
        $file = $this->getFile();
        $line = $this->getLine();

        (new Log())->channel('queue')->critical("type[$this->type] queue[$this->queue] data[$data] message[$message] file[$file] line[$line]" , $this->data);
    }
}
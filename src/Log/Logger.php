<?php

namespace Fomo\Log;

use Carbon\Carbon;

class Logger
{
    protected string $channel = 'fomo';
    protected string $message;
    protected ?array $content;
    protected string $type;

    public function channel(string $name): self
    {
        $this->channel = $name;

        return $this;
    }

    public function info(string $message , ?array $content = null): void
    {
        $this->message = $message;
        $this->content = $content;
        $this->type = 'INFO';

        $this->storeLog();
    }

    public function alert(string $message , ?array $content = null): void
    {
        $this->message = $message;
        $this->content = $content;
        $this->type = 'ALERT';

        $this->storeLog();
    }

    public function critical(string $message , ?array $content = null): void
    {
        $this->message = $message;
        $this->content = $content;
        $this->type = 'CRITICAL';

        $this->storeLog();
    }

    public function debug(string $message , ?array $content = null): void
    {
        $this->message = $message;
        $this->content = $content;
        $this->type = 'DEBUG';

        $this->storeLog();
    }

    public function emergency(string $message , ?array $content = null): void
    {
        $this->message = $message;
        $this->content = $content;
        $this->type = 'EMERGENCY';

        $this->storeLog();
    }

    public function error(string $message , ?array $content = null): void
    {
        $this->message = $message;
        $this->content = $content;
        $this->type = 'ERROR';

        $this->storeLog();
    }

    public function log(string $message , ?array $content = null): void
    {
        $this->message = $message;
        $this->content = $content;
        $this->type = 'LOG';

        $this->storeLog();
    }

    public function notice(string $message , ?array $content = null): void
    {
        $this->message = $message;
        $this->content = $content;
        $this->type = 'NOTICE';

        $this->storeLog();
    }

    public function warning(string $message , ?array $content = null): void
    {
        $this->message = $message;
        $this->content = $content;
        $this->type = 'WARNING';

        $this->storeLog();
    }

    protected function storeLog(): void
    {
        $time = Carbon::now()->format('Y-m-d H:i:s');
        $env = env('APP_ENV' , 'local');
        $log = fopen(storagePath("logs/$this->channel.log"), 'a');

        if (is_null($this->content)){
            $content = null;
        } else{
            $content = implode(" " , $this->content);
        }

        if (is_null($content)){
            fwrite($log , "[$time] $env.$this->type: $this->message\n");
        } else{
            fwrite($log , "[$time] $env.$this->type: $this->message [$content]\n");
        }
        fclose($log);
    }
}
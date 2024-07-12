<?php

namespace Fomo\Response;

use Closure;

trait AdditionalTrait
{
    public function noContent(): string
    {
        $this->status = 204;
        $this->body = '';

        $this->headers['Connection'] = 'keep-alive';
        $this->headers['Content-Type'] = 'text/html; charset=utf-8';
        $this->headers['Content-Length'] = 0;

        return $this->buildResponse();
    }

    public function html(string $data , int $status = 200): string
    {
        $this->status = $status;
        $this->body = $data;

        $this->headers['Connection'] = 'keep-alive';
        $this->headers['Content-Type'] = 'text/html; charset=utf-8';
        $this->headers['Content-Length'] = \strlen($data);

        return $this->buildResponse();
    }
    
    public function plainText(string $data , int $status = 200): string
    {
        $this->status = $status;
        $this->body = $data;

        $this->headers['Connection'] = 'keep-alive';
        $this->headers['Content-Type'] = 'text/plain; charset=utf-8';
        $this->headers['Content-Length'] = \strlen($data);

        return $this->buildResponse();
    }

    public function json(array $data , int $status = 200): string
    {
        $body = json_encode($data , JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);

        $this->status = $status;
        $this->body = $body;

        $this->headers['Connection'] = 'keep-alive';
        $this->headers['Content-Type'] = 'application/json';
        $this->headers['Content-Length'] = \strlen($body);

        return $this->buildResponse();
    }

    public function custom(Closure $callback = null): string
    {
        if(is_null($callback)){
            return $this->buildResponse();
        }

        return $callback($this->status, $this->headers, $this->body);
    }
}

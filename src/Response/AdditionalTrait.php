<?php

namespace Fomo\Response;

trait AdditionalTrait
{
    public function asNoContent(): self
    {
        $this->status = 204;
        $this->body = '';

        $this->headers['Content-Type'] = 'text/html; charset=utf-8';
        $this->headers['Content-Length'] = 0;

        return $this;
    }

    public function asHtml(string $data , int $status = 200): self
    {
        $this->status = $status;
        $this->body = $data;

        $this->headers['Content-Type'] = 'text/html; charset=utf-8';
        $this->headers['Content-Length'] = \strlen($data);

        return $this;
    }

    public function asJson(array $data , int $status = 200): self
    {
        $body = json_encode($data , JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);

        $this->status = $status;
        $this->body = $body;

        $this->headers['Content-Type'] = 'application/json';
        $this->headers['Content-Length'] = \strlen($body);

        return $this;
    }
}
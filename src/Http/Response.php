<?php

namespace Tower\Http;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Collection;

class Response
{
    protected GuzzleResponse $response;

    public function __construct(GuzzleResponse $response)
    {
        $this->response = $response;
    }

    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    public function status(): int
    {
        return $this->response->getStatusCode();
    }

    public function json(): object|array
    {
        return json_decode($this->body() , true);
    }

    public function object(): object|array
    {
        return json_decode($this->body() , false);
    }

    public function collect(): Collection
    {
        return new Collection(json_decode($this->body() , true));
    }

    public function header(string $header): string
    {
        return $this->response->getHeaderLine($header);
    }

    public function headers(): array
    {
        return collect($this->response->getHeaders())->mapWithKeys(function ($v, $k) {
            return [$k => $v];
        })->all();
    }

    public function isSuccess(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    public function isOk(): bool
    {
        return $this->status() === 200;
    }

    public function isRedirect(): bool
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    public function isClientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    public function isServerError(): bool
    {
        return $this->status() >= 500;
    }

    public function isFailed(): bool
    {
        return $this->isServerError() || $this->isClientError();
    }

    public function onError(callable $callback): self
    {
        if ($this->isFailed()) {
            $callback($this);
        }

        return $this;
    }

    public function close(): self
    {
        $this->response->getBody()->close();

        return $this;
    }

    public function toPsrResponse(): GuzzleResponse
    {
        return $this->response;
    }

    public function offsetExists(int $offset): bool
    {
        return isset($this->json()[$offset]);
    }

    public function offsetGet(int $offset): object|array
    {
        return $this->json()[$offset];
    }

    public function __toString(): string
    {
        return $this->body();
    }
}
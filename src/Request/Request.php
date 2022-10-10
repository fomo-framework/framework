<?php

namespace Fomo\Request;

use FastRoute\Dispatcher;
use Swoole\Server;

class Request
{
    use AdditionalTrait;

    protected string $buffer;

    protected int $connectionId;

    protected bool $advancedMode;

    protected array $methodCache = [];

    protected array $uriCache = [];

    protected array $pathCache = [];

    protected array $queryStringCache = [];

    protected array $protocolVersionCache = [];

    protected array $headersCache = [];

    protected array $getsCache = [];

    protected array $postsCache = [];

    public function __construct(
        protected readonly Server $server ,
        protected readonly Dispatcher $dispatcher
    ){
        $this->advancedMode = config('server.advanceMode.request');
    }

    /*
     * set buffer and connectionId
     */
    public function setBC(string $buffer , int $connectionId): void
    {
        $this->buffer = $buffer;
        $this->connectionId = $connectionId;
    }

    public function get(?string $name = null, string|int|bool|array|float|null $default = null): string|int|bool|array|float|null
    {
        $gets = [];

        if (isset($this->getsCache[$this->buffer])) {
            $gets = $this->getsCache[$this->buffer];

            if ($name == null) {
                return $gets;
            }

            if ($this->advancedMode === false){
                return $gets[$name] ?? $default;
            }

            return $this->getData($gets , $name , $default);
        }

        $queryString = $this->queryString();

        if (is_null($queryString)) {
            $this->getsCache[$this->buffer] = [];

            return [];
        }

        \parse_str($queryString, $gets);

        if (\count($this->getsCache) >= 256) {
            unset($this->getsCache[key($this->getsCache)]);
        }

        $this->getsCache[$this->buffer] = $gets;

        if ($name == null) {
            return $gets;
        }

        if ($this->advancedMode === false){
            return $gets[$name] ?? $default;
        }

        return $this->getData($gets , $name , $default);
    }

    public function post(?string $name = null, string|int|bool|array|float|null $default = null): string|int|bool|array|float|null
    {
        $posts = [];
        if (isset($this->postsCache[$this->buffer])) {
            $posts = $this->postsCache[$this->buffer];

            if ($name == null) {
                return $posts;
            }

            if ($this->advancedMode === false){
                return $posts[$name] ?? $default;
            }

            return $this->getData($posts , $name , $default);
        }

        $body = \substr($this->buffer, \strpos($this->buffer, "\r\n\r\n") + 4);
        if ($body == '') {
            $this->postsCache[$this->buffer] = [];

            return [];
        }

        $contentType = $this->header('content-type', '');
        if ($contentType == 'application/vnd.api+json' || $contentType = 'application/json') {
            $posts = \json_decode($body, true);
        } else {
            \parse_str($body, $posts);
        }

        if (\count($this->postsCache) >= 256) {
            unset($this->postsCache[key($this->postsCache)]);
        }

        $this->postsCache[$this->buffer] = $posts;

        if ($name == null) {
            return $posts;
        }

        if ($this->advancedMode === false){
            return $posts[$name] ?? $default;
        }

        return $this->getData($posts , $name , $default);
    }

    public function header(?string $name = null, string|int|bool|array|float|null $default = null): string|int|bool|array|float|null
    {
        $headers = [];
        if (isset($this->headersCache[$this->buffer])) {
            $headers = $this->headersCache[$this->buffer];

            if ($name == null) {
                return $headers;
            }

            $name = strtolower($name);
            if ($this->advancedMode === false){
                return $headers[$name] ?? $default;
            }

            return $this->getData($headers , $name , $default);
        }

        $head = \strstr($this->buffer, "\r\n\r\n", true);
        $head = \explode("\r\n", \substr($head, \strpos($head, "\r\n") + 2));

        foreach ($head as $content) {
            if (str_contains($content, ':')) {
                [$key, $value] = \explode(':', $content, 2);
                $key = \strtolower($key);
                $value = \ltrim($value);
            } else {
                $key = \strtolower($content);
                $value = '';
            }
            if (isset($headers[$key])) {
                $headers[$key] = "$headers[$key],$value";
            } else {
                $headers[$key] = $value;
            }
        }

        if (\count($this->headersCache) >= 256) {
            unset($this->headersCache[key($this->headersCache)]);
        }

        $this->headersCache[$this->buffer] = $headers;

        if ($name == null) {
            return $headers;
        }

        $name = strtolower($name);
        if ($this->advancedMode === false){
            return $headers[$name] ?? $default;
        }

        return $this->getData($headers , $name , $default);
    }

    public function method(): string
    {
        $firstLine = \strstr($this->buffer, "\r\n", true);
        if (isset($this->methodCache[$firstLine])) {
            return $this->methodCache[$firstLine];
        }

        if (\count($this->methodCache) >= 256) {
            unset($this->methodCache[key($this->methodCache)]);
        }

        return $this->methodCache[$firstLine] = \explode(' ', $firstLine , 3)[0];
    }

    public function protocolVersion(): string
    {
        $firstLine = \strstr($this->buffer, "\r\n", true);
        if (isset($this->protocolVersionCache[$firstLine])) {
            return $this->protocolVersionCache[$firstLine];
        }

        if (\count($this->protocolVersionCache) >= 256) {
            unset($this->protocolVersionCache[key($this->protocolVersionCache)]);
        }

        return $this->protocolVersionCache[$firstLine] = \substr(\strstr($firstLine, 'Http/'), 5);
    }

    public function uri(): string
    {
        $firstLine = \strstr($this->buffer, "\r\n", true);
        if (isset($this->uriCache[$firstLine])) {
            return $this->uriCache[$firstLine];
        }

        if (\count($this->uriCache) >= 256) {
            unset($this->uriCache[key($this->uriCache)]);
        }

        return $this->uriCache[$firstLine] = \explode(' ', $firstLine , 3)[1];
    }

    public function path(): string
    {
        $uri = $this->uri();
        if (isset($this->pathCache[$uri])) {
            return $this->pathCache[$uri];
        }

        if (\count($this->pathCache) >= 256) {
            unset($this->pathCache[key($this->pathCache)]);
        }

        return $this->pathCache[$uri] = \parse_url($uri , PHP_URL_PATH);
    }

    public function queryString(): ?string
    {
        $uri = $this->uri();
        if (isset($this->queryStringCache[$uri])) {
            return $this->queryStringCache[$uri];
        }

        if (\count($this->queryStringCache) >= 256) {
            unset($this->queryStringCache[key($this->queryStringCache)]);
        }

        return $this->queryStringCache[$uri] = \parse_url($uri , PHP_URL_QUERY);
    }

    public function variable(string $variable): string|null
    {
        $routeInfo = $this->dispatcher->dispatch($this->method(), $this->path());

        if ($routeInfo[0] === 1 && isset($routeInfo[2][$variable])) {
            return $routeInfo[2][$variable];
        }

        return null;
    }

    public function remoteIp(): string
    {
        return $this->server->getClientInfo($this->connectionId)['remote_ip'];
    }

    public function remotePort(): int
    {
        return $this->server->getClientInfo($this->connectionId)['remote_port'];
    }

    public function localhost(): string
    {
        return config('server.host');
    }

    public function localPort(): int
    {
        return (int) config('server.port');
    }
}
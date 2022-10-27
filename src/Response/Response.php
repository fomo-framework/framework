<?php

namespace Fomo\Response;

class Response
{
    use AdditionalTrait;

    protected array $phrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'Http Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    public function __construct(
        protected int $status = 200 ,
        protected array $headers = ['Connection' => 'keep-alive'] ,
        protected string $body = ''
    ){}

    public function withHeader(string $name , string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function withHeaders(array $headers): self
    {
        foreach ($headers as $name => $value){
            $this->headers[$name] = $value;
        }

        return $this;
    }

    public function withStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function withBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function withoutHeader(string $name): self
    {
        if (isset($this->headers[$name])){
            unset($this->headers[$name]);
        }

        return $this;
    }

    public function withoutHeaders(array $headers): self
    {
        foreach ($headers as $header){
            if (isset($this->headers[$header])){
                unset($this->headers[$header]);
            }
        }

        return $this;
    }

    public function getHeader(string $name): string|float|int|array|bool|null
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }

        return null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getPhrase(int $status): string|null
    {
        return $this->phrases[$status] ?? null;
    }

    public function rawBody(): string
    {
        return $this->body;
    }

    public function __toString()
    {
        $head = "HTTP/1.1 $this->status {$this->phrases[$this->status]}\r\n";

        foreach ($this->headers as $name => $value) {
            if (\is_array($value)) {
                foreach ($value as $item) {
                    $head .= "$name: $item\r\n";
                }
                continue;
            }
            $head .= "$name: $value\r\n";
        }

        return "$head\r\n$this->body";
    }
}

<?php

namespace Fomo\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\RequestException;

class Http
{
    protected Client $client;

    protected string $url;

    protected ?string $bodyFormat = null;

    protected array|string $body;

    protected array $headers = [];

    protected array $options = [];

    public function __construct()
    {
        $this->client = new Client([
            'cookies' => true
        ]);
    }

    public function withHeaders(array $headers): self
    {
        foreach ($headers as $index => $header){
            $this->headers[$index] = $header;
        }

        return $this;
    }

    public function withOptions(array $options): self
    {
        foreach ($options as $index => $option){
            $this->options[$index] = $option;
        }

        return $this;
    }

    public function attach(array|string $name, string $contents = '', string $filename = null, array $headers = []): self
    {
        if (is_array($name)) {
            foreach ($name as $file) {
                $this->attach(...$file);
            }

            return $this;
        }

        $this->asMultipart();

        $this->body[] = array_filter([
            'name' => $name,
            'contents' => $contents,
            'headers' => $headers,
            'filename' => $filename,
        ]);

        return $this;
    }

    public function withToken(string $token , string $type = 'Bearer'): self
    {
        return $this->withHeaders([
            'Authorization' => "$type $token"
        ]);
    }

    public function withBasicAuth(string $username, string $password): self
    {
        return $this->withHeaders([
            'auth' => [
                $username ,
                $password
            ]
        ]);
    }

    public function withDigestAuth(string $username, string $password): self
    {
        return $this->withHeaders([
            'auth' => [
                $username ,
                $password ,
                'digest'
            ]
        ]);
    }

    public function withUserAgent(string $userAgent): self
    {
        return $this->withHeaders([
            'User-Agent' => $userAgent
        ]);
    }

    public function withCookies(array $cookies, string $domain): self
    {
        return $this->withOptions([
            'cookies' => CookieJar::fromArray($cookies, $domain),
        ]);
    }

    public function withoutRedirecting(): self
    {
        return $this->withOptions([
            'allow_redirects' => false,
        ]);
    }

    public function withoutVerifying(): self
    {
        return $this->withOptions([
            'verify' => false,
        ]);
    }

    public function timeout(int $seconds): self
    {
        return $this->withOptions([
            'timeout' => $seconds,
        ]);
    }

    public function asBody(string $contentType): self
    {
        return $this->bodyFormat('body')->contentType($contentType);
    }

    public function asJson(): self
    {
        return $this->bodyFormat('json')->contentType('application/json');
    }

    public function asForm(): self
    {
        return $this->bodyFormat('form_params')->contentType('application/x-www-form-urlencoded');
    }

    public function asMultipart(): self
    {
        return $this->bodyFormat('multipart');
    }

    public function bodyFormat(string $format): self
    {
        $this->bodyFormat = $format;

        return $this;
    }

    public function contentType(string $contentType): self
    {
        return $this->withHeaders(['Content-Type' => $contentType]);
    }

    public function accept(string $contentType = 'application/json'): self
    {
        return $this->withHeaders(['Accept' => $contentType]);
    }

    public function head(string $url , array $query = []): Response
    {
        $this->parsGetRequest($url , $query);

        return $this->send('HEAD');
    }

    public function get(string $url , array $query = []): Response
    {
        $this->parsGetRequest($url , $query);

        return $this->send('GET');
    }

    public function post(string $url , array $body = [] , string $contentType = 'application/json'): Response
    {
        $this->parsPostRequest($url , $body , $contentType);

        return $this->send('POST');
    }

    public function patch(string $url , array $body = [] , string $contentType = 'application/json'): Response
    {
        $this->parsPostRequest($url , $body , $contentType);

        return $this->send('PATCH');
    }

    public function put(string $url , array $body = [] , string $contentType = 'application/json'): Response
    {
        $this->parsPostRequest($url , $body , $contentType);

        return $this->send('PUT');
    }

    public function delete(string $url , array $body = [] , string $contentType = 'application/json'): Response
    {
        $this->parsPostRequest($url , $body , $contentType);

        return $this->send('DELETE');
    }

    protected function parsPostRequest(string $url , array $body , string $contentType): void
    {
        $this->url = $url;

        if (!empty($body)){
            if (is_null($this->bodyFormat) || $this->bodyFormat == 'body'){
                $this->asBody($contentType);
                $this->body = json_encode($body);
            }else{
                $this->body = $body;
            }
        }
    }

    protected function parsGetRequest(string $url , array $query): void
    {
        if (!empty($query)){
            $url = "$url?";
            foreach ($query as $index => $item){
                $url .= "$index=$item&";
            }
            $url = substr($url, 0, -1);
        }
        $this->url = $url;
    }

    protected function send(string $type): Response
    {
        $options = [];

        if (!is_null($this->bodyFormat)){
            $options[$this->bodyFormat] = $this->body;
        }

        if (!empty($this->headers)){
            foreach ($this->headers as $index => $header){
                $options['headers'][$index] = $header;
            }
        }

        if (!empty($this->options)){
            foreach ($this->options as $index => $option){
                $options[$index] = $option;
            }
        }

        try {
            return new Response($this->client->request($type , $this->url , $options));
        }catch (RequestException $exception){
            return new Response($exception->getResponse());
        }
    }
}
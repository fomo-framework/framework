<?php

namespace Tower;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Capsule\Manager as Capsule;
use PHPUnit\Framework\TestCase as BaseTestCase;
use RedisException;
use Tower\TestCase\Response;

class TestCase extends BaseTestCase
{
    private Client $client;

    private string $url;

    private ?string $bodyFormat = null;

    private array|string $body;

    private array $headers = [];

    private array $options = [];


    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        Dotenv::createImmutable(basePath())->load();

        $app = include configPath() . "app.php";

        Loader::save([
            'app' => configPath() . "app.php" ,
            'database' => configPath() . "database.php" ,
            'elastic' => configPath() . "elastic.php" ,
            'mail' => configPath() . "mail.php" ,
            'redis' => configPath() . "redis.php" ,
            'server' => configPath() . "server.php" ,
            'errors' => languagePath() . 'validation/' . $app['locale'] . '/errors.php' ,
        ]);

        Elastic::setInstance();

        Mail::setInstance();

        $this->setRedis();

        $this->setDatabase();

        $this->client = new Client([
            'cookies' => true
        ]);

        parent::__construct($name, $data, $dataName);
    }

    private function setDatabase(): void
    {
        $capsule = new Capsule();

        $capsule->addConnection(Loader::get('database')['mysql']);

        $capsule->setAsGlobal();
    }

    private function setRedis(): void
    {
        try {
            Redis::setInstance();
        } catch (RedisException $e)
        {
            (new Log())->channel('tests')->alert($e->getMessage());
        }
    }

    protected function withHeaders(array $headers): self
    {
        foreach ($headers as $index => $header)
            $this->headers[$index] = $header;

        return $this;
    }

    protected function withOptions(array $options): self
    {
        foreach ($options as $index => $option)
            $this->options[$index] = $option;

        return $this;
    }

    protected function attach(array|string $name, string $contents = '', string $filename = null, array $headers = []): self
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

    protected function withToken(string $token , string $type = 'Bearer'): self
    {
        return $this->withHeaders([
            'Authorization' => "$type $token"
        ]);
    }

    protected function withBasicAuth(string $username, string $password): self
    {
        return $this->withHeaders([
            'auth' => [
                $username ,
                $password
            ]
        ]);
    }

    protected function withDigestAuth(string $username, string $password): self
    {
        return $this->withHeaders([
            'auth' => [
                $username ,
                $password ,
                'digest'
            ]
        ]);
    }

    protected function withUserAgent(string $userAgent): self
    {
        return $this->withHeaders([
            'User-Agent' => $userAgent
        ]);
    }

    protected function withCookies(array $cookies, string $domain): self
    {
        return $this->withOptions([
            'cookies' => CookieJar::fromArray($cookies, $domain),
        ]);
    }

    protected function withoutRedirecting(): self
    {
        return $this->withOptions([
            'allow_redirects' => false,
        ]);
    }

    protected function withoutVerifying(): self
    {
        return $this->withOptions([
            'verify' => false,
        ]);
    }

    protected function timeout(int $seconds): self
    {
        return $this->withOptions([
            'timeout' => $seconds,
        ]);
    }

    protected function asBody(string $contentType): self
    {
        return $this->bodyFormat('body')->contentType($contentType);
    }

    protected function asJson(): self
    {
        return $this->bodyFormat('json')->contentType('application/json');
    }

    protected function asForm(): self
    {
        return $this->bodyFormat('form_params')->contentType('application/x-www-form-urlencoded');
    }

    protected function asMultipart(): self
    {
        return $this->bodyFormat('multipart');
    }

    protected function bodyFormat(string $format): self
    {
        $this->bodyFormat = $format;

        return $this;
    }

    protected function contentType(string $contentType): self
    {
        return $this->withHeaders(['Content-Type' => $contentType]);
    }

    protected function accept(string $contentType = 'application/json'): self
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
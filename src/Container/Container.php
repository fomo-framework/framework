<?php

namespace Fomo\Container;

use Faker\Factory;
use Fomo\Auth\Auth;
use Fomo\Cache\Cache;
use Fomo\Config\Config;
use Fomo\Database\DB;
use Fomo\Elasticsearch\Elasticsearch;
use Fomo\Facades\Setter;
use Fomo\Http\Http as HttpClient;
use Fomo\Language\Language;
use Fomo\Log\Logger;
use Fomo\Mail\Mail;
use Fomo\Redis\Redis;
use Fomo\Relationship\Relationship;
use Fomo\Request\Request;
use Fomo\Response\Response;
use Fomo\Router\Router;
use Fomo\ServerState\ServerState;
use Fomo\Validation\Validation;
use Illuminate\Pagination\Paginator;
use PHPMailer\PHPMailer\PHPMailer;
use ReflectionClass;

class Container
{
    protected static self $instance;

    protected array $bindings = [];

    protected string $basePath;

    public function __construct(string $basePath = null)
    {
        $this->setInstance();
        $this->setBasePath($basePath);

        $this->registerBaseBindings();
        $this->registerFacades();
    }

    public function bind(string $name, mixed $value, bool $isSingleton = false): void
    {
        if (!isset($this->bindings[$name])) {
            $this->bindings[$name] = [
                'instance' => $value,
                'isSingleton' => $isSingleton,
                'class' => (new ReflectionClass($value))->getName(),
            ];
        }
    }

    public function singleton(string $name, mixed $value): void
    {
        $this->bind($name, $value, true);
    }

    public function make(string $name, array $parameters = []): mixed
    {
        if (isset($this->bindings[$name])) {
            if ($this->isSingleton($name)) {
                return empty($parameters) ? $this->bindings[$name]['instance'] : new $this->bindings[$name]['class'](...$parameters);
            }
            return new $this->bindings[$name]['class'](...$parameters);
        }

        return null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->bindings);
    }

    public function isSingleton(string $name): bool
    {
        return isset($this->bindings[$name]) && $this->bindings[$name]['isSingleton'] === true;
    }

    public static function getInstance(): static
    {
        return self::$instance;
    }

    protected function setInstance(): void
    {
        self::$instance = $this;
    }

    protected function setBasePath(string $basePath = null): void
    {
        if (!is_null($basePath)) {
            $this->basePath = $basePath;
        }
    }

    protected function registerFacades(): void
    {
        Setter::addClass('config', $this->make('config'));
        Setter::addClass('serverState', $this->make('serverState'));
        Setter::addClass('request', $this->make('request'));
        Setter::addClass('route', $this->make('router'));
        Setter::addClass('response', $this->make('response'));
        Setter::addClass('language', $this->make('language'));
        Setter::addClass('auth', $this->make('auth'));
        Setter::addClass('cache', $this->make('cache'));
        Setter::addClass('httpClient', $this->make('httpClient'));
        Setter::addClass('log', $this->make('log'));
        Setter::addClass('relationship', $this->make('relationship'));
        Setter::addClass('validation', $this->make('validation'));
        Setter::addClass('faker', $this->make('faker'));
        Setter::addClass('db', $this->make('db'));
        Setter::addClass('elasticsearch', $this->make('elasticsearch'));
        Setter::addClass('redis', $this->make('redis'));
        Setter::addClass('mail', $this->make('mail'));
    }

    protected function registerBaseBindings(): void
    {
        $this->registerConfigBinding();
        $this->registerServerStateBinding();
        $this->registerRequestBinding();
        $this->registerRouterBinding();
        $this->registerResponseBinding();
        $this->registerLanguageBinding();
        $this->registerAuthBinding();
        $this->registerCacheBinding();
        $this->registerHttpClientBinding();
        $this->registerLogBinding();
        $this->registerRelationshipBinding();
        $this->registerValidationBinding();
        $this->registerFakerBinding();
        $this->registerDbBinding();
        $this->registerElasticsearchBinding();
        $this->registerRedisBinding();
        $this->registerMailBinding();
    }

    protected function registerConfigBinding(): void
    {
        $this->singleton('config', new Config);
    }

    protected function registerServerStateBinding(): void
    {
        $this->singleton('serverState', new ServerState);
    }

    protected function registerRequestBinding(): void
    {
        $this->singleton('request', new Request);
    }

    protected function registerRouterBinding(): void
    {
        $this->singleton('router', new Router);
    }

    protected function registerResponseBinding(): void
    {
        $this->singleton('response', new Response);
    }

    protected function registerLanguageBinding(): void
    {
        $this->singleton('language', new Language);
    }

    protected function registerAuthBinding(): void
    {
        $this->singleton('auth', new Auth);
    }

    protected function registerCacheBinding(): void
    {
        $this->singleton('cache', new Cache);
    }

    protected function registerHttpClientBinding(): void
    {
        $this->singleton('httpClient', new HttpClient);
    }

    protected function registerLogBinding(): void
    {
        $this->singleton('log', new Logger);
    }

    protected function registerRelationshipBinding(): void
    {
        $this->singleton('relationship', new Relationship);
    }

    protected function registerValidationBinding(): void
    {
        $this->singleton('validation', new Validation);
    }

    protected function registerFakerBinding(): void
    {
        $this->singleton('faker', Factory::create($this->make('config')->get('app.faker_locale')));
    }

    protected function registerDbBinding(): void
    {
        $connection = new DB();

        $connection->addConnection($this->make('config')->get('database.connections.' . $this->make('config')->get('database.default')));

        $connection->setAsGlobal();

        Paginator::currentPageResolver(function () {
            $page = request()->get('page');

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int)$page >= 1) {
                return (int)$page;
            }

            return 1;
        });

        $this->singleton('db', $connection);
    }

    protected function registerElasticsearchBinding(): void
    {
        $connection = (new Elasticsearch())->setHosts([$this->make('config')->get('elasticsearch.host') . ':' . $this->make('config')->get('elasticsearch.port')]);

        if ($this->make('config')->get('elasticsearch.username') != null && $this->make('config')->get('elasticsearch.password') != null) {
            $connection->setBasicAuthentication($this->make('config')->get('elasticsearch.username'), $this->make('config')->get('elasticsearch.password'));
        }

        $this->singleton('elasticsearch', $connection->build());
    }

    protected function registerRedisBinding(): void
    {
        $connection = new Redis();
        $connection->connect($this->make('config')->get('redis.host'), $this->make('config')->get('redis.port'));
        $connection->select($this->make('config')->get('redis.database'));

        if (!is_null($this->make('config')->get('redis.username')) && !is_null($this->make('config')->get('redis.password'))) {
            $connection->auth([$this->make('config')->get('redis.username'), $this->make('config')->get('redis.password')]);
        }

        $this->singleton('redis', $connection);
    }

    protected function registerMailBinding(): void
    {
        $connection = new Mail();
        switch (env('MAIL_MAILER', 'smtp')) {
            case 'smtp':
                $connection->isSMTP();
                if ($this->make('config')->get('mail.username') != null && $this->make('config')->get('mail.password') != null) {
                    $connection->SMTPAuth = true;
                }
                $connection->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                break;
            case 'mail':
                $connection->isMail();
                break;
            case 'sendmail':
                $connection->isSendmail();
                break;
            case 'qmail':
                $connection->isQmail();
                break;
        }

        $connection->Host = $this->make('config')->get('mail.host');
        $connection->Username = $this->make('config')->get('mail.username');
        $connection->Password = $this->make('config')->get('mail.password');
        $connection->Port = $this->make('config')->get('mail.port');

        try {
            $connection->setFrom(env('MAIL_FROM_ADDRESS', 'hello@example.com'), env('MAIL_FROM_NAME', 'Example'));
        } catch (\Exception $e) {
            $this->make('log')->channel('mailer')->error($e->getMessage());
        }

        $this->singleton('mail', $connection);
    }
}
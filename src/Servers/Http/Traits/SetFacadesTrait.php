<?php

namespace Fomo\Servers\Http\Traits;

use Fomo\Database\DB;
use Fomo\Elasticsearch\Elasticsearch;
use Fomo\Facades\Log;
use Fomo\Facades\Setter;
use Fomo\Mail\Mail;
use Fomo\Redis\Redis;
use Illuminate\Pagination\Paginator;
use PHPMailer\PHPMailer\PHPMailer;

trait SetFacadesTrait
{
    protected function setDBFacade(): void
    {
        $connection = new DB();

        $connection->addConnection(config('database.connections.' . config('database.default')));

        $connection->setAsGlobal();

        Paginator::currentPageResolver(function () {
            $page = $this->request->get('page');

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                return (int) $page;
            }

            return 1;
        });

        Setter::addClass('db', $connection);
    }

    protected function setElasticsearchFacade(): void
    {
        $connection = (new Elasticsearch())->setHosts([config('elasticsearch.host') . ':' . config('elasticsearch.port')]);

        if (config('elasticsearch.username') != null && config('elasticsearch.password') != null){
            $connection->setBasicAuthentication(config('elasticsearch.username') , config('elasticsearch.password'));
        }

        Setter::addClass('elasticsearch', $connection->build());
    }

    protected function setRedisFacade(): void
    {
        $connection = new Redis();
        $connection->connect(config('redis.host') , config('redis.port'));
        $connection->select(config('redis.database'));

        if (! is_null(config('redis.username')) && ! is_null(config('redis.password'))){
            $connection->auth([config('redis.username') , config('redis.password')]);
        }

        Setter::addClass('redis', $connection);
    }

    protected function setMailFacade(): void
    {
        $connection = new Mail();
        switch (env('MAIL_MAILER' , 'smtp')) {
            case 'smtp':
                $connection->isSMTP();
                if (config('mail.username') != null && config('mail.password') != null){
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

        $connection->Host = config('mail.host');
        $connection->Username = config('mail.username');
        $connection->Password = config('mail.password');
        $connection->Port = config('mail.port');

        try {
            $connection->setFrom(env('MAIL_FROM_ADDRESS', 'hello@example.com'), env('MAIL_FROM_NAME', 'Example'));
        } catch (\Exception $e) {
            Log::channel('mailer')->error($e->getMessage());
        }

        Setter::addClass('mail', $connection);
    }
}
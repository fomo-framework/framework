<?php

namespace Fomo\Servers\Http\Traits;

use Fomo\Elasticsearch\Elasticsearch;
use Fomo\Facades\Setter;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Pagination\Paginator;

trait SetFacadesTrait
{
    protected function setDBFacade(): void
    {
        $connection = new Capsule();

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
}
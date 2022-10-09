<?php

namespace Fomo\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Pagination\Paginator;
use Swoole\Server;
use Fomo\Request\Request;

class Database
{
    public function boot(Server $server = null, Request $request = null): void
    {
        $capsule = new Capsule();

        $capsule->addConnection(config('database.connections.' . config('database.default')));

        $capsule->setAsGlobal();

        if (!is_null($request)){
            Paginator::currentPageResolver(function () use ($request) {
                $page = $request->get('page');

                if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                    return (int) $page;
                }

                return 1;
            });
        }
    }
}
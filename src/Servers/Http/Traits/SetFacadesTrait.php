<?php

namespace Fomo\Servers\Http\Traits;

use Fomo\Facades\Setter;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Pagination\Paginator;

trait SetFacadesTrait
{
    protected function setDBFacade(): void
    {
        $capsule = new Capsule();

        $capsule->addConnection(config('database.connections.' . config('database.default')));

        $capsule->setAsGlobal();

        Paginator::currentPageResolver(function () {
            $page = $this->request->get('page');

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                return (int) $page;
            }

            return 1;
        });

        Setter::addClass('db', $capsule);
    }
}
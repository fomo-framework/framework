<?php

namespace Fomo\Job;

use Fomo\Redis\Redis;

trait DispatchTrait
{
    public static function dispatch(...$parameters): void
    {
        Redis::getInstance()->rPush(env('APP_NAME' , 'fomo') . 'Queue' , json_encode([
            'dispatcher' => self::class ,
            'parameters' => [...$parameters] ,
        ]));
    }
}
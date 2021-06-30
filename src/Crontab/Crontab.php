<?php

namespace Tower\Crontab;

use Workerman\Lib\Timer;
use Closure;

class Crontab
{
    protected string $rule;

    protected Closure $callback;

    protected int $id;

    protected static array $instances = [];

    public function __construct(string $rule , Closure $callback)
    {
        $this->rule = $rule;
        $this->callback = $callback;
        $this->id = static::createId();
        static::$instances[$this->id] = $this;
        static::tryInit();
    }

    public function getRule(): string
    {
        return $this->rule;
    }

    public function getCallback(): Closure
    {
        return $this->callback;
    }

    public function getId(): int
    {
        return $this->id;
    }

    protected static function createId(): int
    {
        static $id = 0;
        return ++$id;
    }

    protected static function tryInit(): void
    {
        static $inited = false;
        if ($inited) {
            return;
        }
        $inited = true;
        $parser = new Parser();
        $callback = function () use ($parser, &$callback) {
            foreach (static::$instances as $crontab) {
                $rule = $crontab->getRule();
                $cb = $crontab->getCallback();
                if (!$cb || !$rule) {
                    continue;
                }
                $times = $parser->parse($rule);
                $now = time();
                foreach ($times as $time) {
                    $diffTime = $time->timestamp - $now;
                    if ($diffTime <= 0) {
                        $diffTime = 0.000001;
                    }
                    Timer::add($diffTime , $cb, null, false);
                }
            }
            Timer::add(60 - time() % 60 , $callback, null, false);
        };

        $next_time = time() % 60;
        if ($next_time == 0) {
            $next_time = 0.00001;
        } else {
            $next_time = 60 - $next_time;
        }
        Timer::add($next_time, $callback, null, false);
    }
}
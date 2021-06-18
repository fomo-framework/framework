<?php

namespace Tower;

class Queue
{
    public static function store(string $queue , array $data , int $attempts = 5): void
    {
        $queueData = [
            'queue' => $queue ,
            'data' => $data ,
            'attempts' => $attempts ,
        ];

        Redis::getInstance()->rPush('towerQueue' , json_encode($queueData));
    }
}
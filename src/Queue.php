<?php

namespace Tower;

class Queue
{
    public function store(string $queue , array $data , int $attempts = 5): void
    {
        $queueData = [
            'queue' => $queue ,
            'data' => $data ,
            'attempts' => $attempts ,
        ];

        Redis::getInstance()->rPush(env('APP_NAME' , 'tower') . 'Queue' , json_encode($queueData));
    }
}
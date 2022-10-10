<?php

namespace Fomo\Inserter;

use Fomo\Database\DB;
use Fomo\Elasticsearch\Elasticsearch;

class Inserter
{
    protected string $table;
    protected array $data;
    protected string $connection = 'mysql';
    protected array $insertData;

    public function __construct(string $table , array $data)
    {
        $this->table = $table;
        $this->data = $data;
    }

    public function connection(string $connection = 'mysql'): static
    {
        $this->connection = $connection;

        return $this;
    }

    public function create(int $count): void
    {
        if ($count > 10000){
            $count = 10000;
        }

        for ($i=0;$i<$count;$i++){
            $this->insertData[] = $this->data;
        }

        $this->insert();
    }

    public function mCreate(int $count): void
    {
        if ($count > 10000)
            $count = 10000;

        for ($i=0;$i<$count;$i++){
            $data = [];
            foreach ($this->data as $index => $item) {
                if (is_array($item)){
                    $class = $item['class'];
                    if (is_string($class))
                        $class = new $class();

                    $method = $item['method'];
                    $args = $item['args'] ?? [];
                    $type = $item['type'] ?? null;
                    if (!is_null($type)){
                        $data[$index] = $this->mCreateType($class , $method , $args , $type);
                    } else{
                        $data[$index] = call_user_func_array([$class , $method] , $args);
                    }
                } else{
                    $data[$index] = $item;
                }
            }
            $this->insertData[] = $data;
        }

        $this->insert();
    }

    protected function mCreateType(string $class , string $method , array $args , string $type): float|object|int|bool|array|string
    {
        return match ($type) {
            'int' => (int) call_user_func_array([$class, $method], $args),
            'string' => (string) call_user_func_array([$class, $method], $args),
            'array' => (array) call_user_func_array([$class, $method], $args),
            'float' => (float) call_user_func_array([$class, $method], $args),
            'integer' => (integer) call_user_func_array([$class, $method], $args),
            'double' => (double) call_user_func_array([$class, $method], $args),
            'boolean' => (boolean) call_user_func_array([$class, $method], $args),
            'bool' => (bool) call_user_func_array([$class, $method], $args),
            'object' => (object) call_user_func_array([$class, $method], $args),
        };
    }

    protected function insert(): void
    {
        if ($this->connection == 'mysql'){
            DB::table($this->table)->insert($this->insertData);
        } else{
            for($i = 0; $i < count($this->insertData); $i++) {
                if (isset($this->insertData[$i]['id'])){
                    $params['body'][] = [
                        'index' => [
                            '_index' => $this->table,
                            '_id' => $this->insertData[$i]['id'],
                        ]
                    ];
                } else{
                    $params['body'][] = [
                        'index' => [
                            '_index' => $this->table,
                        ]
                    ];
                }

                $params['body'][] = $this->insertData[$i];
            }

            Elasticsearch::getInstance()->bulk($params);
        }
    }
}
<?php

namespace Fomo\ServerState;

class ServerState
{
    protected static ?self $instance = null;
    protected readonly string $path;

    public function __construct(){
        $this->path = storagePath('serverState.json');
    }

    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            return self::$instance = new self();
        }
        return self::$instance;
    }

    public function getInformation(): array
    {
        $data = is_readable($this->path)
            ? json_decode(file_get_contents($this->path), true)
            : [];

        return [
            'masterProcessId' => $data['pIds']['masterProcessId'] ?? null ,
            'managerProcessId' => $data['pIds']['managerProcessId'] ?? null ,
            'watcherProcessId' => $data['pIds']['watcherProcessId'] ?? null ,
            'factoryProcessId' => $data['pIds']['factoryProcessId'] ?? null ,
            'queueProcessId' => $data['pIds']['queueProcessId'] ?? null ,
            'schedulingProcessId' => $data['pIds']['schedulingProcessId'] ?? null ,
            'workerProcessIds' => $data['pIds']['workerProcessIds'] ?? [] ,
        ];
    }

    public function setManagerProcessId(int $id): void
    {
        $this->setId('managerProcessId', $id);
    }

    public function setMasterProcessId(int $id): void
    {
        $this->setId('masterProcessId', $id);
    }

    public function setWatcherProcessId(int $id): void
    {
        $this->setId('watcherProcessId', $id);
    }

    public function setFactoryProcessId(int $id): void
    {
        $this->setId('factoryProcessId', $id);
    }

    public function setQueueProcessId(int $id): void
    {
        $this->setId('queueProcessId', $id);
    }

    public function setSchedulingProcessId(int $id): void
    {
        $this->setId('schedulingProcessId', $id);
    }

    public function setWorkerProcessIds(array $ids): void
    {
        $this->setId('workerProcessIds', $ids);
    }

    public function getManagerProcessId(): int|null
    {
        return $this->getInformation()['managerProcessId'];
    }

    public function getMasterProcessId(): int|null
    {
        return $this->getInformation()['masterProcessId'];
    }

    public function getWatcherProcessId(): int|null
    {
        return $this->getInformation()['watcherProcessId'];
    }

    public function getFactoryProcessId(): int|null
    {
        return $this->getInformation()['factoryProcessId'];
    }

    public function getQueueProcessId(): int|null
    {
        return $this->getInformation()['queueProcessId'];
    }

    public function getSchedulingProcessId(): int|null
    {
        return $this->getInformation()['schedulingProcessId'];
    }

    public function getWorkerProcessIds(): array
    {
        return $this->getInformation()['workerProcessIds'];
    }

    protected function setId(string $key, int|array $id): void
    {
        file_put_contents($this->path, json_encode(
            [
                'pIds' => array_merge($this->getInformation(), [$key => $id])
            ],
            JSON_PRETTY_PRINT
        ));
    }
}
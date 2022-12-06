<?php

namespace Fomo\Facades;

/**
 * @method static void setManagerProcessId(int $id)
 * @method static void setMasterProcessId(int $id)
 * @method static void setWatcherProcessId(int $id)
 * @method static void setFactoryProcessId(int $id)
 * @method static void setQueueProcessId(int $id)
 * @method static void setSchedulingProcessId(int $id)
 * @method static void setWorkerProcessIds(array $ids)
 * @method static int|null getManagerProcessId()
 * @method static int|null getMasterProcessId()
 * @method static int|null getWatcherProcessId()
 * @method static int|null getFactoryProcessId()
 * @method static int|null getQueueProcessId()
 * @method static int|null getSchedulingProcessId()
 * @method static array getWorkerProcessIds()
 */
class ServerState extends Facade
{
    protected static function getMainClass(): string
    {
        return 'serverState';
    }
}
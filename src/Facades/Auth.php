<?php

namespace Fomo\Facades;

use stdClass;

/**
 * @method static void setUser(stdClass $user)
 * @method static stdClass user()
 * @method static int|string id()
 * @method static bool check()
 */
class Auth extends Facade
{
    protected static function getMainClass(): string
    {
        return 'auth';
    }
}
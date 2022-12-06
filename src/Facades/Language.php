<?php

namespace Fomo\Facades;

/**
 * @method static array getErrorMessages()
 */
class Language extends Facade
{
    protected static function getMainClass(): string
    {
        return 'language';
    }
}
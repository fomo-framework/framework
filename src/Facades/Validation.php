<?php

namespace Fomo\Facades;

/**
 * @method static \Fomo\Validation\Validation validate(array $data , array $rules)
 * @method static bool hasError()
 * @method static bool hasMessage()
 * @method static array getMessages()
 * @method static string firstMessage()
 * @method static array getErrors()
 * @method static array firstError()
 */
class Validation extends Facade
{
    protected static function getMainClass(): string
    {
        return 'validation';
    }
}
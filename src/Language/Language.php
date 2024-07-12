<?php

namespace Fomo\Language;

class Language
{
    protected static self $instance;

    protected array $cache = [];

    public static function setInstance(): void
    {
        if (!isset(self::$instance)){
            self::$instance = new self();
        }
    }

    public static function getInstance(): Language
    {
        return self::$instance;
    }

    public function getErrorMessages(): array
    {
        if (empty($this->cache)) {
            return $this->cache = require_once languagePath('validation/' . config('app.locale') . '/errors.php');
        }

        return $this->cache;
    }
}
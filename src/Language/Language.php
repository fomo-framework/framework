<?php

namespace Fomo\Language;

class Language
{
    protected static self $instance;

    protected array $cache = [];

    public static function getInstance(): self
    {
        if (isset(self::$instance)) {
            return self::$instance;
        }

        return self::$instance = new self();
    }

    public function getErrorMessages(): array
    {
        if (empty($this->cache)) {
            return $this->cache = require_once languagePath('validation/' . config('app.locale') . '/errors.php');
        }

        return $this->cache;
    }
}
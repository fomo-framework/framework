<?php

namespace Fomo\Language;

class Language
{
    protected static ?self $instance = null;

    protected array $cache = [];

    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            return self::$instance = new self();
        }
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
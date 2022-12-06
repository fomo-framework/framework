<?php

namespace Fomo\Language;

class Language
{
    protected array $cache = [];

    public function getErrorMessages(): array
    {
        if (empty($this->cache)) {
            return $this->cache = require_once languagePath('validation/' . config('app.locale') . '/errors.php');
        }

        return $this->cache;
    }
}
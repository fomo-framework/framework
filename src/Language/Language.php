<?php

namespace Fomo\Language;

use Fomo\Facades\Contracts\InstanceInterface;

class Language implements InstanceInterface
{
    protected array $cache = [];

    public function getErrorMessages(): array
    {
        if (empty($this->cache)) {
            return $this->cache = require_once languagePath('validation/' . app()->make('config')->get('app.locale') . '/errors.php');
        }

        return $this->cache;
    }

    public function getInstance(): self
    {
        return $this;
    }
}
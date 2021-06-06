<?php

namespace Tower\Middleware;

use Tower\Response;

interface Contract{
    public function handle(): bool|Response;
}
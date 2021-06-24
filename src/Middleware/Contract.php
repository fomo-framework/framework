<?php

namespace Tower\Middleware;

use Tower\Request;
use Tower\Response;

interface Contract
{
    public function handle(Request $request): bool|Response;
}
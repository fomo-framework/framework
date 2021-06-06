<?php
namespace Tower\Exception;

use Tower\Response;

interface Contract
{
    public function handle(): Response;
}
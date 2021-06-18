<?php

namespace Tower\Job;

interface Contract
{
    public function handle(\stdClass $data): void;
}
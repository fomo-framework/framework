<?php

namespace Tower\Attribute;

use Attribute;

/*
 * This attribute points to the address associated with this method
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpMethod
{
    public function __construct(string $method = 'GET') {}
}

<?php

namespace Tower\Attribute\Http;

use Attribute;

/*
 * This attribute points to the route method associated with this method
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Method
{
    public function __construct(string $method = 'GET') {}
}

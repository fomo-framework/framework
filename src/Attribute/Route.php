<?php

namespace Tower\Attribute;

use Attribute;

/*
 * This attribute points to the address associated with this method
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(string $route) {}
}

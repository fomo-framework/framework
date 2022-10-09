<?php

namespace Fomo\Attributes;

use Attribute;

/*
 * This attribute points to the route method associated with this method
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Method
{
    public function __construct(string $method) {}
}
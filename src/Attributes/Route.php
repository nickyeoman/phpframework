<?php

namespace Nickyeoman\Framework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD, Attribute::IS_REPEATABLE)] // Set IS_REPEATABLE flag to true
class Route {
    public string $path;
    public array $methods;

    public function __construct(string $path, array $methods = ['GET']) {
        $this->path = $path;
        $this->methods = $methods;
    }
}

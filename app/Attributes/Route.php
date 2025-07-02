<?php

declare(strict_types=1);

namespace app\Attributes;

use Attribute;

#[Attribute]

class Route
{
    public function __construct(public string $route, public string $method)
    {
    }
}
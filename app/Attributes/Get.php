<?php

declare(strict_types=1);

namespace app\Attributes;

use Attribute;

#[Attribute]

class Get extends Route
{
    public function __construct(public string $route)
    {
        parent::__construct($route, 'get');
    }
}
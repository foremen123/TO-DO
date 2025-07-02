<?php

declare(strict_types=1);

namespace app\Attributes;

use app\Router;
use Attribute;

#[Attribute]

class Post extends Route
{
    public function __construct(public string $route)
    {
        parent::__construct($route, 'post');
    }
}
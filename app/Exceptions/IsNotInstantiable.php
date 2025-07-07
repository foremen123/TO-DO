<?php

namespace app\Exceptions;

use Exception;
use http\Message;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class IsNotInstantiable extends Exception implements ContainerExceptionInterface
{
    protected $message = 'This class cannot be instantiated directly.
     Use the appropriate factory method or interface to create an instance.';
}
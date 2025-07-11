<?php

declare(strict_types=1);

namespace app;

use app\Attributes\Route;
use app\DI\Container;
use app\Exceptions\RouteNotFoundException;
use Exception;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

class Router
{

    public function __construct(protected Container $container)
    {
    }

    private array $routes = [];

    public function register(string $requestMethod, string $route, callable|array $action): self
    {
        $this->routes[$requestMethod][$route] = $action;
        return $this;
    }

    /**
     * @throws ReflectionException
     */
    public function registerAttributeRoute(array $controllers): void
    {
        foreach ($controllers as $controller) {
            $methods = new ReflectionClass($controller);

            foreach ($methods->getMethods() as $method) {
                $attributes = $method->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF);

                foreach ($attributes as $attribute) {
                    $route = $attribute->newInstance();

                    $this->register($route->method, $route->route, [$controller, $method->getName()]);
                }
            }
        }
    }

    public function routes(): array
    {
        return $this->routes;
    }

    public function resolve(string $requestURI, string $requestMethod)
    {
        $route = explode('?', $requestURI)[0];
        $action = $this->routes[$requestMethod][$route] ?? null;
        if (!$action) {
            throw new RouteNotFoundException('Route not found');
        }
        if (is_callable($action)) {
            return call_user_func($action);
        }
        [$class, $method] = $action;
        if (class_exists($class)) {
            $instance = $this->container->get($class);
            if (method_exists($class, $method)) {
                return call_user_func_array([$instance, $method], []);
            }
        }
        throw new RouteNotFoundException("Method $method not found in class $class");
    }
}
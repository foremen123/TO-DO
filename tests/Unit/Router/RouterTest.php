<?php

namespace Tests\Unit\Router;

use app\DI\Container;
use app\Exceptions\RouteNotFoundException;
use app\Router;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;

class RouterTest extends TestCase
{
    private Router $router;

    public function setUp(): void
    {
        parent::setUp();
        $container = new Container();
        $this->router = new Router($container);
    }

    #[Test]
    public function it_register_route_into_router(): void
    {
        $this->router->register('get', '/get', ['Users', 'index']);
        $this->router->register('post', '/post', ['Users', 'index']);

        $expected =
            [
                'get' => ['/get' => ['Users', 'index']],
                'post' => ['/post' => ['Users', 'index']]
            ];

        $this->assertSame($expected, $this->router->routes());
    }

    #[Test]
    public function it_return_empty_route(): void
    {
        $this->assertEmpty($this->router->routes());
    }

    #[Test]
    #[DataProviderExternal(\Tests\DataProvider\RouterDataProvider::class, 'attributesRoutes')]
    public function it_register_route_with_attribute(object $class, array $expected): void
    {
        $this->router->registerAttributeRoute([$class]);

        $this->assertEquals($expected, $this->router->routes());
    }

    #[Test]
    #[DataProviderExternal(\Tests\DataProvider\RouterDataProvider::class, 'ExceptionsRoutes')]
    public function it_throw_exception_when_resolve_register(
        string $uri,
        string $method,
        ?array $customController = null): void
    {
        if ($customController !== null) {
            $this->router->register($method, $uri, [$customController['class'], $customController['method']]);
        }

        $this->expectException(RouteNotFoundException::class);
        $this->router->resolve($uri, $method);
    }

    #[Test]
    #[DataProviderExternal(\Tests\DataProvider\RouterDataProvider::class, 'succeedRoute')]
    public function it_resolve_register_route(
        string $requestUri,
        string $requestMethod,
        array $controller
    ): void
    {
        $instance = new $controller['class'];
        $this->router->registerAttributeRoute([$instance]);
        $actually = $this->router->resolve($requestUri, $requestMethod);

        $this->assertTrue($actually);
    }

    #[Test]
    public function it_register_closure_into_route(): void
    {
        $closure = fn()=> 'hello';
        $this->router->register('get', '/get', $closure);

        $expected = ['get' => ['/get' => $closure]];

        $this->assertEquals($expected, $this->router->routes());
    }
}
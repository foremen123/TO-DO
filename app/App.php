<?php

declare(strict_types=1);

namespace app;

use app\DI\Container;
use app\Exceptions\RouteNotFoundException;
use app\interface\AuthRepositoryInterface;
use app\interface\DatabaseInterface;
use app\interface\NoteRepositoryInterface;
use app\Models\AuthModel;
use app\Models\NoteModel;
use app\NoteHelper\RedirectResponse;
use Exception;

class App
{
    static private DB $db;

    public function __construct(
        protected Router $router,
        protected array $request,
        protected Config $config,
        protected Container $container

    )
    {
        static::$db = new DB($config->db ?? []);

        $this->container->set(Config::class, fn() => $this->config);
        $this->container->set(DB::class, fn() => static::$db);
        $this->container->set(DatabaseInterface::class, fn(Container $c) => static::$db);

        $this->container->set(AuthRepositoryInterface::class, fn(Container $c) => $c->get(AuthModel::class));
        $this->container->set(NoteRepositoryInterface::class, fn(Container $c) => $c->get(NoteModel::class));
    }

    public function run(): void
    {
        try {
            $response = $this->router->resolve(
                $this->request['uri'],
                $this->request['method']
            );

            if ($response instanceof RedirectResponse) {
                header("Location: {$response->location}");
                exit;
            }

            if ($response instanceof View) {
                echo $response->render();
                return;
            }
            echo $response;

        } catch (RouteNotFoundException $e) {
            http_response_code(404);

            error_log($e->getMessage());
            echo $e->getMessage() . $e->getCode() . $e->getFile() . $e->getLine();
            exit;
        } catch (Exception $e) {
            http_response_code(500);

            echo $e->getMessage() . $e->getFile() . $e->getCode();

            error_log($e->getMessage());
            echo View::make('Errors/Error500');
            exit;
        }

    }

    static public function db(): DB
    {
        return static::$db;
    }
}
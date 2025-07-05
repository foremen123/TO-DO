<?php

declare(strict_types=1);

namespace app;

use Exception;

class App
{

    static private DB $db;

    public function __construct(
        protected Router $router,
        protected array $request,
        protected Config $config
    )
    {
        static::$db = new DB($config->db ?? []);
    }

    public function run(): void
    {
        try {
            echo $this->router->resolve(
                $this->request['uri'],
                $this->request['method']
            );
        } catch (Exception $e) {
            http_response_code(500);

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
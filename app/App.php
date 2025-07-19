<?php

declare(strict_types=1);

namespace app;

use app\Controllers\EmailController;
use app\DI\Container;
use app\Exceptions\RouteNotFoundException;
use app\interface\AuthRepositoryInterface;
use app\interface\DatabaseInterface;
use app\interface\EmailRepositoryInterface;
use app\interface\NoteRepositoryInterface;
use app\Models\AuthModel;
use app\Models\EmailModel;
use app\Models\NoteModel;
use app\NoteHelper\RedirectResponse;
use app\Service\CustomMailer;
use Dotenv\Dotenv;
use Exception;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class App
{
    static private DB $db;
    private Config $config;

    public function __construct(
        protected Container $container,
        protected array $request = [],
        protected ?Router $router = null

    )
    {
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

            echo View::make('Errors/Error404');
            exit;
        } catch (Exception $e) {
            http_response_code(500);

            echo $e->getMessage() . $e->getFile();

            error_log($e->getMessage());
            echo View::make('Errors/Error500');
            exit;
        }

    }

    public function boot(): static
    {
        $dotenv = Dotenv:: createImmutable(dirname(__DIR__));
        $dotenv->load ();

        $this->config = new Config($_ENV);

        static::$db = new DB($this->config->db ?? []);

        $this->container->set(Config::class, fn() => $this->config);
        $this->container->set(DB::class, fn() => static::$db);
        $this->container->set(DatabaseInterface::class, fn(Container $c) => static::$db);

        $this->container->set(AuthRepositoryInterface::class, fn(Container $c) => $c->get(AuthModel::class));
        $this->container->set(NoteRepositoryInterface::class, fn(Container $c) => $c->get(NoteModel::class));
        $this->container->set(EmailRepositoryInterface::class, fn(Container $c) => $c->get(EmailModel::class));

        $this->container->set(Environment::class, function (Container $c) {
            $loader = new FilesystemLoader(__DIR__ . '/../views');
            return new Environment($loader);
        });

        $this->container->set(MailerInterface::class, fn() => new CustomMailer($this->config->mailer['dsn']));
        return $this;
    }

    static public function db(): DB
    {
        return static::$db;
    }
}
<?php

declare(strict_types=1);

namespace app;

class View
{
    public function __construct(protected string $view, protected array $params = [])
    {
    }

    public function render(): string
    {
        try {
            $viewFile = VIEW_PATH . $this->view . '.php';
            if (!file_exists($viewFile)) {
                throw new \Exception($viewFile . ' not found');
            }
            foreach ($this->params as $key => $value) {
                $$key = $value;
            }
            ob_start();
            include $viewFile;
            return (string)ob_get_clean();
        } catch (\Exception $e) {
            http_response_code(404);
            error_log($e->getMessage());

            echo View::make('/Errors/Error.404');
            exit;
        }
    }

    static public function make(string $view, array $params = []): static
    {
        return new View($view, $params);
    }

    public function __get(string $name)
    {
        return $this->params[$name] ?? null;
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
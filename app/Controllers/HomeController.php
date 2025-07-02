<?php

declare(strict_types=1);

namespace app\Controllers;

use app\Attributes\Get;
use app\View;

class HomeController
{
    #[Get('/')]

    public function index(): View
    {
        return View::make('/test');
    }

    #[Get('/error')]

    public function error(): View
    {
        return View::make('/Errors/Error500');
    }

    #[Get('/register')]

    public function authorization(): View
    {
        return View::make('/TO-DO/Authorization');
    }

    #[Get('/ToDo')]

    public function Todo(): View
    {
        return View::make('/TO-DO/ToDo');
    }
}
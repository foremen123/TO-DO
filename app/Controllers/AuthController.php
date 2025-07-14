<?php

namespace app\Controllers;

use app\Attributes\Get;
use app\Attributes\Post;
use app\interface\AuthRepositoryInterface;
use app\NoteHelper\RedirectResponse;
use app\NoteHelper\ToDoFormatter;
use app\View;
use PDOException;

class AuthController
{
    public function __construct(protected AuthRepositoryInterface $authModel)
    {
    }

    #[Get('/registration')]

    public function registration(): View
    {
        return View::make('/ToDo/Registration');
    }

    #[Get('/authorization')]

    public function authorization(): View
    {
        return View::make('/ToDo/Authorization');
    }

    #[Post('/registrationUser')]

    public function registrationUser(): View|RedirectResponse
    {
        $username = ToDoFormatter::formattedText($_POST['username']);
        $password = ToDoFormatter::formattedText($_POST['password']);

        if ($username === '' || $password === '') {
            return View::make(
                '/ToDo/Registration',
                ['error' => 'Нельзя просто вставить пробелы']
            );

        }

        try {
            if ($this->authModel->registration($username, $password)) {
                return new RedirectResponse('/ToDo');
            }
        } catch (PDOException $e) {
            return View::make(
                '/ToDo/Registration',
                ['error' => 'Имя занято']
            );
        }
        return View::make('/Errors/Error500');
    }

    #[Post('/authorizationUser')]

    public function authorizationUser(): View|RedirectResponse
    {
        try {
            $username = ToDoFormatter::formattedText($_POST['username']);
            $password = ToDoFormatter::formattedText($_POST['password']);

            if ($this->authModel->login($username, $password)) {
                return new RedirectResponse('/ToDo');
            }
            return View::make(
                '/ToDo/Authorization',
                ['error' => 'Неверный логин или пароль']
            );

        } catch (PDOException $e) {
            return View::make('/Errors/Error500');
        }
    }
}
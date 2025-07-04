<?php

namespace app\Controllers;

use app\Attributes\Get;
use app\Attributes\Post;
use app\Models\AuthModel;
use app\View;
use PDOException;

class AuthController
{
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

    public function registrationUser(): void
    {
        $username = trim($_POST['username']) ?? '';
        $password = trim($_POST['password']) ?? '';

        if ($username === '' || $password === '') {
            echo View::make(
                '/ToDo/Registration',
                ['error' => 'Нельзя просто вставить пробелы']
            );
            return;
        }

        $authModel = new AuthModel();

        try {
            if ($authModel->registration($username, $password)) {
                header('Location: /ToDo');
                exit;
            }
        } catch (PDOException $e) {
            echo View::make(
                '/ToDo/Registration',
                ['error' => 'Имя занято']
            );
        }
    }

    #[Post('/authorizationUser')]

    public function authorizationUser(): void
    {
        try {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $authModel = new AuthModel();

            if ($authModel->login($username, $password)) {
                header('Location: /ToDo');
                exit;
            }
            echo View::make(
                '/ToDo/Authorization',
                ['error' => 'Неверный логин или пароль. <a href="/registration">Зарегистрироваться</a>']
            );

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

}
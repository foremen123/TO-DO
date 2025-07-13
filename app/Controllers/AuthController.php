<?php

namespace app\Controllers;

use app\Attributes\Get;
use app\Attributes\Post;
use app\interface\AuthRepositoryInterface;
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

    public function registrationUser(): void
    {
        $username = ToDoFormatter::formattedNote($_POST['username']) ?? '';
        $password = ToDoFormatter::formattedNote($_POST['password']) ?? '';

        if ($username === '' || $password === '') {
            echo View::make(
                '/ToDo/Registration',
                ['error' => 'Нельзя просто вставить пробелы']
            );
            return;
        }

        try {
            if ($this->authModel->registration($username, $password)) {
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
            $username = ToDoFormatter::formattedNote($_POST['username']);
            $password = ToDoFormatter::formattedNote($_POST['password']);



            if ($this->authModel->login($username, $password)) {
                header('Location: /ToDo');
                exit;
            }
            echo View::make(
                '/ToDo/Authorization',
                ['error' => 'Неверный логин или пароль. <a href="/registration">Зарегистрироваться</a>']
            );

        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new PDOException($e->getMessage());
        }
    }

}
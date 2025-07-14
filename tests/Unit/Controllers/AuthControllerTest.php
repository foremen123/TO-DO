<?php

namespace Tests\Unit\Controllers;

use app\Controllers\AuthController;
use app\interface\AuthRepositoryInterface;
use app\NoteHelper\RedirectResponse;
use PDOException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

if (!defined('app\VIEW_PATH')) {
    define('app\VIEW_PATH', __DIR__ . '/../../../views/');
}
class AuthControllerTest extends TestCase
{

    private AuthController $authController;
    private AuthRepositoryInterface $authModel;

    public function setUp(): void
    {
        parent::setUp();

        $this->authModel = $this->createMock(AuthRepositoryInterface::class);
        $this->authController = new AuthController($this->authModel);

        $_POST['username'] = 'test';
        $_POST['password'] = 'testPassword';
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $_POST = [];
    }

    #[Test]
    public function it_register_a_new_user(): void
    {
        $this->authModel
            ->expects($this->once())
            ->method('registration')
            ->with(
                'test',
                'testPassword'
            )
            ->willReturn(true);

        $result = $this->authController->registrationUser();

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/ToDo', $result->location);
    }

    #[Test]
    public function it_return_error_message_when_Post_return_null(): void
    {
        $_POST['username'] = ' ';
        $_POST['password'] = ' ';

        $result =  $this->authController->registrationUser();
        $this->assertStringContainsString('Нельзя просто вставить пробелы', $result);
    }

    #[Test]
    public function it_throw_exception_when_name_is_occupied(): void
    {
        $this->authModel
            ->expects($this->once())
            ->method('registration')
            ->with(
                'test',
                'testPassword'
            )
            ->willThrowException(new PDOException('Имя занято'));

        $result = $this->authController->registrationUser();

        $this->assertStringContainsString('Имя занято', $result);
    }

    #[Test]
    public function  it_authorization_user_into_note(): void
    {
        $this->authModel
            ->expects($this->once())
            ->method('login')
            ->with(
                'test',
                'testPassword'
            )
            ->willReturn(true);

        $result = $this->authController->authorizationUser();

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/ToDo', $result->location);
    }

    #[Test]
    public function it_return_error_message_when_incorrect_password_or_login(): void
    {
        $this->authModel
            ->expects($this->once())
            ->method('login')
            ->with(
                'test',
                'testPassword'
            )
            ->willReturn(false);

        $result = $this->authController->authorizationUser();

        $this->assertStringContainsString('Неверный логин или пароль', $result);
    }

    #[Test]
    public function it_throws_exception_when_there_is_a_pdo_error(): void
    {
        $this->authModel
            ->expects($this->once())
            ->method('login')
            ->willThrowException(new PDOException(''));

        $result = $this->authController->authorizationUser();
        $this->assertStringContainsString('Ошибка на нашей стороне', $result);
    }
}
<?php

namespace Controllers;

use app\Controllers\EmailController;
use app\DB;
use app\DI\Container;
use app\interface\EmailRepositoryInterface;
use app\interface\NoteRepositoryInterface;
use app\Models\AuthModel;
use app\Models\NoteModel;
use app\View;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class EmailControllerTest extends TestCase
{
    private EmailRepositoryInterface $emailModel;
    private NoteRepositoryInterface $noteModel;
    private EmailController $emailController;
    private Container $container;
    public function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();
        $db = $this->createMock(DB::class);

        $this->container->set(EmailRepositoryInterface::class, fn() => new AuthModel($db));
        $this->container->set(NoteRepositoryInterface::class, fn() => new NoteModel($db));

        $this->emailModel = $this->createMock(EmailRepositoryInterface::class);
        $this->noteModel = $this->createMock(NoteRepositoryInterface::class);

        $this->container->set(Environment::class, function (Container $c) {
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../../views');
            return new \Twig\Environment($loader);
        });

        $this->container->set(EmailController::class, function() {
            return new EmailController(
                $this->emailModel,
                $this->noteModel,
                $this->container->get(Environment::class)
            );
        });

        $this->emailController = $this->container->get(EmailController::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $_POST = [];
    }

    #[Test]
    public function it_set_queue_email(): void
    {
        $_POST['id'] = '1';
        $_POST['email'] = 'test';

        $this->noteModel
            ->expects($this->once())
            ->method('getNoteFromEmail')
            ->willReturn(['note']);

        $this->emailModel
            ->expects($this->once())
            ->method('addQueue')
            ->willReturn(true);

        $result = $this->emailController->enqueueEmail();

        $this->assertStringContainsString('Успешно!!! Вернуться', $result);
    }

    #[Test]
    public function it_throws_exception_when_id_or_recipient_is_null(): void
    {
        $_POST['id'] = ' ';
        $_POST['email'] = ' ';

        $result = $this->emailController->enqueueEmail();
        $this->assertInstanceOf(View::class, $result);
        $this->assertStringContainsString('Упс вы не вошли в аккаун', $result);
    }

    #[Test]
    public function it_throws_exception_when_failed_to_set_note_into_queue(): void
    {
        $_POST['id'] = '1';
        $_POST['email'] = 'test';

        $this->noteModel
            ->expects($this->once())
            ->method('getNoteFromEmail')
            ->willReturn(['note']);

        $this->emailModel
            ->expects($this->once())
            ->method('addQueue')
            ->willReturn(false);

        $result = $this->emailController->enqueueEmail();

        $this->assertInstanceOf(View::class, $result);
        $this->assertStringContainsString('Ошибка на нашей стороне', $result);
    }
}
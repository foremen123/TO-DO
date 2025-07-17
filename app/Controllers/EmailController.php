<?php

declare(strict_types=1);

namespace app\Controllers;

use app\Attributes\Get;
use app\Attributes\Post;
use app\interface\EmailRepositoryInterface;
use app\interface\NoteRepositoryInterface;
use app\Models\EmailModel;
use app\NoteHelper\RedirectResponse;
use app\View;
use Exception;
use PDOException;
use Twig\Environment;

class EmailController
{
    public function __construct(
        private readonly EmailRepositoryInterface  $emailModel,
        private readonly NoteRepositoryInterface  $noteModel,
        private readonly Environment $twig,
    )
    {
    }

    #[Get('/email')]
    public function sendEmail(): View
    {
        return View::make('/ToDo/Email/SendEmail');
    }

    #[Post('/mailer/queue')]
    public function enqueueEmail(): View|RedirectResponse
    {
        try {
            $id = $_POST['id'] ?? '';
            $recipient = $_POST['email'] ?? '';

            if (empty(trim($_POST['id'])) || empty(trim($_POST['email'])) ) {
                throw new Exception('Username or recipient not found');
            }

            $subject = 'Ваша заметка';
            $textBody = implode($this->noteModel->getNoteFromEmail($id));
            $htmlBody = $this->twig->render('/ToDo/Email.twig', ['note_text' => $textBody]);

            if (!$this->emailModel->addQueue(
                $recipient,
                $htmlBody,
                $textBody,
                $subject
            )) {
                throw new PDOException('Failed to add a new note into queue');
            }

            return View::make('ToDo/Email/Succeed');
        } catch (PDOException $e) {
            return View::make('/Errors/Error500');
        } catch (Exception $e) {
           return View::make('/Errors/NotUserNameSessionError');

        }
    }
}
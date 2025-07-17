<?php

declare(strict_types=1);

namespace app\Service;

use app\Enums\Queue;
use app\Models\EmailModel;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    public function __construct(
        protected EmailModel $emailModel,
        protected MailerInterface $mailer,
    )
    {
    }

    public function sendQueuedEmails(): void
    {
        $emails = $this->emailModel->getEmailByStatus(Queue::Pending);

        foreach ($emails as $email) {

            try {
                $emailMessage = new Email()
                    ->from('foremen330@gmail.com')
                    ->to($email['recipient'])
                    ->subject('Ваша заметка')
                    ->html($email['html_body']);
                $this->mailer->send($emailMessage);
                $this->emailModel->updateStatusMessage($email['id'], Queue::Sent);
            } catch (TransportExceptionInterface $e) {
                $errorMsg = $e->getMessage();
                $this->emailModel->updateStatusWithError($email['id'], Queue::Failed, $errorMsg);
                continue;
            }
        }
    }
}
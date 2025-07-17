<?php

namespace app\Models;

use app\Enums\Queue;
use app\interface\DatabaseInterface;
use app\interface\EmailRepositoryInterface;
use PDOException;

class EmailModel extends Model implements EmailRepositoryInterface
{
    public function __construct(?DatabaseInterface $db = null)
    {
        parent::__construct($db);
    }

    public function addQueue(
        string $recipient,
        string $htmlBody,
        string $textBody,
        string $subject = '',
    ): bool
    {
        $queue = Queue::Pending;
        $stmt = $this->db->prepare(
            'INSERT INTO mailer_queue(
                         recipient, html_body, text_body, status, created_at)
                        VALUES (:recipient , :htmlBody, :textBody, :status, NOW())');

        if (!$stmt->execute(
            [
                ':recipient' => $recipient,
                ':htmlBody' => $htmlBody,
                ':textBody' => $textBody,
                ':status' => $queue->getQueue(),
            ]))
        {
            return false;
        }

        return true;
    }

    public function getEmailByStatus(Queue $status): array
    {
        $stmt = $this->db->prepare('SELECT * FROM mailer_queue WHERE status = ?');

        if (!$stmt->execute([$status->getQueue()])) {
            throw new PDOException('Not execute this status message');
        }

        return $stmt->fetchAll();
    }

    public function updateStatusMessage(int $id, Queue $status): bool
    {
        $stmt = $this->db->prepare('UPDATE mailer_queue SET status = ?, send_at = NOW() WHERE id = ?');

        if (!$stmt->execute([$status->getQueue(), $id])) {
            return false;
        }
        return true;
    }


    public function setEmailFromUser(string $id, string $email): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET email = ? WHERE id = ?');
        if (!$stmt->execute([$email, $id])) {
            return false;
        }
        return true;
    }

    public function getEmailByUserId(string $id): ?string
    {
        $stmt = $this->db->prepare('SELECT email FROM users WHERE id = ?');
        if ($stmt->execute([$id])) {
            $result = $stmt->fetch();
            return $result['email'] ?? null;
        }

        return null;
    }

    public function updateStatusWithError(int $id, Queue $status, string $errorMessage): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE mailer_queue SET status = ?, error_message = ? WHERE id = ?'
        );

        return $stmt->execute([$status->getQueue(), $errorMessage, $id]);
    }
}
<?php

namespace app\Models;

use app\Enums\Queue;
use app\interface\DatabaseInterface;
use app\interface\EmailRepositoryInterface;
use Doctrine\DBAL\Exception;
use PDOException;
use RuntimeException;
use Throwable;

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
    ): bool
    {
    $status = Queue::Pending;
        try {
            $this->db->createBuilder()
                ->insert('mailer_queue')
                ->values([
                    'recipient' => ':recipient',
                    'html_body' => ':html',
                    'text_body' => ':text',
                    'status' => ':status',
                    'created_at' => ':created_at'
                ])
                ->setParameters([
                    'recipient' => $recipient,
                    'html' => $htmlBody,
                    'text' => $textBody,
                    'status' => $status->value,
                    'created_at' => date('Y-m-d H:i:s')
                ])
                ->executeStatement();;
            return true;
        } catch (Throwable $e) {
            echo $e->getMessage() . $e->getCode() . ' ';
            return false;
        }
    }

    public function getEmailByStatus(Queue $status): array
    {
        try {
            return $this->db->createBuilder()
                ->select('*')
                ->from('mailer_queue')
                ->where('status = :status')
                ->setParameter('status', $status->getQueue())
                ->fetchAllAssociative();
        } catch (Throwable) {
            throw new RuntimeException('Not execute thus status message');
        }
    }

    public function updateStatusMessage(int $id, Queue $status): bool
    {

        try {
            $this->db->createBuilder()
                ->update('mailer_queue')
                ->set('status', ':status')
                ->set('send_at', ':send_at')
                ->where('id = :id')
                ->setParameters([
                    'status'  => $status->value,
                    'send_at' => date('Y-m-d H:i:s'),
                    'id' => $id,
                ])
                ->executeStatement();
            return true;
        } catch (Throwable) {
            return false;
        }
    }


    public function setEmailFromUser(string $id, string $email): bool
    {
        try {
            $this->db->createBuilder()
                ->update('users')
                ->set('email', ':email')
                ->where('id = :id')
                ->setParameters([
                    'email' => $email,
                    'id' => $id
                ])
                ->executeStatement();
            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function getEmailByUserId(string $id): ?string
    {
        try {
             $result = $this->db->createBuilder()
                ->select('email')
                ->from('users')
                ->where('id = :id')
                ->setParameter('id', $id)
                ->fetchAssociative();

            return $result['email'] ?? null;
        } catch (Throwable) {
            return null;
        }
    }

    public function updateStatusWithError(int $id, Queue $status, string $errorMessage): bool
    {
        try {
            $this->db->createBuilder()
                ->update('mailer_queue')
                ->set('status', ':status')
                ->set('error_message', ':error_message')
                ->where('id = :id')
                ->setParameters([
                    'status' => $status->getQueue(),
                    'error_message' => $errorMessage,
                    'id' => $id
                ])
                ->executeStatement();
            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
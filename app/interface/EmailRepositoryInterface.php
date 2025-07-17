<?php

namespace app\interface;

use app\Enums\Queue;

interface EmailRepositoryInterface
{
    public function addQueue(
        string $recipient,
        string $htmlBody,
        string $textBody,
        string $subject = '',
    ): bool;
    public function getEmailByStatus(Queue $status): array;
    public function updateStatusMessage(int $id, Queue $status): bool;
    public function setEmailFromUser(string $id, string $email): bool;
    public function getEmailByUserId(string $id): ?string;
    public function updateStatusWithError(int $id, Queue $status, string $errorMessage): bool;
}
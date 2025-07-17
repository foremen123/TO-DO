<?php

namespace app\Enums;

enum Queue: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';

    public function getQueue(): string
    {
        return match($this) {
             self::Sent => 'sent',
             self::Failed => 'failed',
             self::Pending => 'pending',
        };
    }

    public function checkFromQueue(?string $value): self
    {
        return match($value) {
            'sent' => self::Sent,
            'failed' => self::Failed,
            default => self::Pending,
        };
    }
}
<?php

namespace Models;

use app\DB;
use app\DI\Container;
use app\Enums\Queue;
use app\interface\EmailRepositoryInterface;
use app\Models\EmailModel;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EmailModelTest extends TestCase
{
    private EmailRepositoryInterface $emailModel;
    private DB $db;
    private PDOStatement $stmtMock;

    public function setUp(): void
    {
        $container = new Container();

        $this->db = $this->createMock(DB::class);

        $container->set(EmailRepositoryInterface::class, fn() => new EmailModel($this->db));
        $this->emailModel = $container->get(EmailRepositoryInterface::class);

        $this->stmtMock = $this->createMock(PDOStatement::class);
    }

    #[Test]
    public function it_adding_a_new_note_to_queue(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO mailer_queue(
                         recipient, html_body, text_body, status, created_at)
                        VALUES (:recipient , :htmlBody, :textBody, :status, NOW())')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $result = $this->emailModel->addQueue(
            'test', 'test', 'test', 'test', 'test');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_return_false_when_note_not_adding_to_queue(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO mailer_queue(
                         recipient, html_body, text_body, status, created_at)
                        VALUES (:recipient , :htmlBody, :textBody, :status, NOW())')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        $result = $this->emailModel->addQueue(
            'test', 'test', 'test', 'test', 'test');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_get_email_by_status(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM mailer_queue WHERE status = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->stmtMock
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        $result = $this->emailModel->getEmailByStatus(Queue::Pending);

        $this->assertSame([], $result);
    }

    #[Test]
    public function it_throws_exception_when_failed_get_email_status(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM mailer_queue WHERE status = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        $this->expectException(PDOException::class);
        $this->emailModel->getEmailByStatus(Queue::Pending);
    }

    #[Test]
    public function it_update_status_message(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE mailer_queue SET status = ?, send_at = NOW() WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $result = $this->emailModel->updateStatusMessage(1, Queue::Sent);
        $this->assertTrue($result);
    }

    #[Test]
    public function it_throws_exception_when_failed_update_status_message(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE mailer_queue SET status = ?, send_at = NOW() WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        $result = $this->emailModel->updateStatusMessage(1, Queue::Sent);
        $this->assertFalse($result);
    }

    #[Test]
    public function it_set_email_from_user(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE users SET email = ? WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $result = $this->emailModel->setEmailFromUser(1, 'test');
        $this->assertTrue($result);
    }

    #[Test]
    public function it_return_false_when_failed_sent_email(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE users SET email = ? WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        $result = $this->emailModel->setEmailFromUser(1, 'test');
        $this->assertFalse($result);
    }

    #[Test]
    public function it_get_email_by_user_id(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT email FROM users WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->stmtMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(['email' => 'test']);

        $result = $this->emailModel->getEmailByUserId('1');
        $this->assertSame('test', $result);
    }

    #[Test]
    public function it_return_null_when_not_get_email(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT email FROM users WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        $result = $this->emailModel->getEmailByUserId('1');
        $this->assertNull($result);
    }

    #[Test]
    public function it_update_status_with_error(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE mailer_queue SET status = ?, error_message = ? WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $result = $this->emailModel->updateStatusWithError(1, Queue::Sent, 'test');
        $this->assertTrue($result);
    }

}
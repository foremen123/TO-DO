<?php

namespace Tests\Unit\Models;

use app\Models;
use app\interface\DatabaseInterface;
use app\Models\AuthModel;
use PDOStatement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AuthModelTest extends TestCase
{
    private DatabaseInterface  $db;
    private AuthModel $authModel;
    private PDOStatement $insertStmtMock;
    private PDOStatement $selectStmtMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->insertStmtMock = $this->createMock(PDOStatement::class);
        $this->selectStmtMock = $this->createMock(PDOStatement::class);

        $this->db = $this->createMock(DatabaseInterface::class);
        $this->authModel = new AuthModel($this->db);

    }

    #[Test]
    public function it_registration_new_user(): void
    {

        $this->insertStmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('INSERT INTO users (username, password) VALUES (?, ?)'))
            ->willReturn($this->insertStmtMock);

        $result = $this->authModel->registration('test', 'test');
        $this->assertTrue($result);
    }

    #[Test]
    public function it_return_false_when_not_registration_user()
    {
        $this->insertStmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('INSERT INTO users (username, password) VALUES (?, ?)'))
            ->willReturn($this->insertStmtMock);

        $result = $this->authModel->registration('test', 'test');
        $this->assertFalse($result);
    }

    #[Test]
    public function it_add_new_a_username_into_session(): void
    {
        $_SESSION = [];

        $this->insertStmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('INSERT INTO users (username, password) VALUES (?, ?)'))
            ->willReturn($this->insertStmtMock);

        $result = $this->authModel->registration('test', 'testPassword');

        $this->assertArrayHasKey('username', $_SESSION);
        $this->assertSame('test', $_SESSION['username']);
    }

    #[Test]
    public function it_authorization_user(): void
    {
        $_SESSION = [];

        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM users WHERE username = ?')
            ->willReturn($this->selectStmtMock);

        $this->selectStmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->selectStmtMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(
                [
                    'username' => 'test',
                    'password' => password_hash('testPassword', PASSWORD_DEFAULT)
                ]);

        $actually = $this->authModel->login('test', 'testPassword');

        $this->assertTrue(true);
        $this->assertArrayHasKey('username', $_SESSION);
        $this->assertSame('test', $_SESSION['username']);
    }

    #[Test]
    public function it_return_false_when_not_authorization_user(): void
    {
        $_SESSION = [];

        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM users WHERE username = ?')
            ->willReturn($this->selectStmtMock);

        $this->selectStmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->selectStmtMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(
                [
                    'username' => 'errorUsername',
                    'password' => password_hash('testPassword', PASSWORD_DEFAULT)
                ]);

        $actually = $this->authModel->login('test', 'testPassword');

        $this->assertFalse($actually);
    }
}
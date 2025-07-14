<?php

namespace Tests\Unit\Models;

use app\DI\Container;
use app\Enums\SortNote;
use app\interface\DatabaseInterface;
use app\Models\NoteModel;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class NoteModelTest extends TestCase
{
    private DatabaseInterface $db;
    private NoteModel $noteModel;
    private PDOStatement $stmtMock;
    private SortNote $sortSetting;
    private string $username;
    private int $id;
    private string $note;
    public function setUp(): void
    {
        parent::SetUp();

        $this->db = $this->createMock(DatabaseInterface::class);
        $this->noteModel = new NoteModel($this->db);
        $this->stmtMock = $this->createMock(PDOStatement::class);

        $this->sortSetting = SortNote::DateDESC;
        $this->username = 'test';
        $this->id = 1;
        $this->note = 'test';

    }

    #[Test]
    public function it_create_a_new_note(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('INSERT INTO notes (note, username) VALUES (?, ?)'))
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->assertTrue($this->noteModel->addNote('test', 'test'));
    }

    #[Test]
    public function it_return_false_when_not_created_note(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo('INSERT INTO notes (note, username) VALUES (?, ?)'))
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        $this->assertFalse($this->noteModel->addNote('test', 'test'));
    }

    #[Test]
    public function it_returns_sorted_notes_for_given_user(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM notes WHERE username = ? ORDER BY ' . $this->sortSetting->getSort())
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([$this->username]))
            ->willReturn(true);

        $this->stmtMock
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn([['id' => 1, 'note' => 'Test note', 'username' => 'test']]);

        $result = $this->noteModel->getNotes($this->username, $this->sortSetting);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Test note', $result[0]['note']);
    }

    #[Test]
    public function it_throws_exception_when_getNote_fails(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM notes WHERE username = ? ORDER BY ' . $this->sortSetting->getSort())
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([$this->username]))
            ->willReturn(false);

        $this->expectException(PDOException::class);
        $this->noteModel->getNotes($this->username, $this->sortSetting);
    }

    #[Test]
    public function it_delete_this_note(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM notes WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([$this->id]))
            ->willReturn(true);

        $result = $this->noteModel->deleteNote($this->id);
        $this->assertTrue($result);
    }

    #[Test]
    public function it_return_false_when_not_delete_this_note(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM notes WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([$this->id]))
            ->willReturn(false);

        $this->expectException(PDOException::class);
        $this->noteModel->deleteNote($this->id);
    }

    #[Test]
    public function it_get_this_noteId(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM notes WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([$this->id]))
            ->willReturn(true);

        $this->stmtMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => $this->id]);

        $result = $this->noteModel->getNoteId($this->id);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($this->id, $result['id']);
    }

    #[Test]
    public function it_throws_exception_when_getNoteId_fails(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM notes WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([$this->id]))
            ->willReturn(false);

        $this->expectException(PDOException::class);
        $this->noteModel->getNoteId($this->id);
    }

    #[Test]
    public function it_edit_this_note(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE notes SET note = :note, date = CURRENT_TIMESTAMP WHERE id = :id')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([':id' => $this->id,  ':note' => $this->note]))
            ->willReturn(true);

        $this->stmtMock
            ->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $result = $this->noteModel->editNote($this->id, $this->note);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_throws_exception_when_editNote_fails(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE notes SET note = :note, date = CURRENT_TIMESTAMP WHERE id = :id')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([':id' => $this->id,  ':note' => $this->note]))
            ->willReturn(false);

        $this->expectException(PDOException::class);
        $this->noteModel->editNote($this->id, $this->note);
    }

    #[Test]
    public function it_throws_exception_when_note_was_not_found_or_updated()
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE notes SET note = :note, date = CURRENT_TIMESTAMP WHERE id = :id')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([':id' => $this->id,  ':note' => $this->note]))
            ->willReturn(true);

        $this->stmtMock
            ->expects($this->once())
            ->method('rowCount')
            ->willReturn(0);

        $this->expectException(PDOException::class);
        $this->noteModel->editNote($this->id, $this->note);
    }

    #[Test]
    public function it_set_done_note(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE notes SET completed = ? WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([$this->id, true]))
            ->willReturn(true);

        $result = $this->noteModel->setDoneNote($this->id, '1');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_throws_exception_when_set_done_note_fails(): void
    {
        $this->db
            ->expects($this->once())
            ->method('prepare')
            ->with('UPDATE notes SET completed = ? WHERE id = ?')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo([$this->id, '1']))
            ->willReturn(false);

        $this->expectException(PDOException::class);
        $result = $this->noteModel->setDoneNote($this->id, true);
    }
}
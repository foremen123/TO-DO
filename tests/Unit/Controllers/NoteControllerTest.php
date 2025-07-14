<?php

namespace Controllers;

use app\Controllers\NoteController;
use app\Enums\SortNote;
use app\interface\NoteRepositoryInterface;
use app\Models\Model;
use app\Models\NoteModel;
use app\NoteHelper\RedirectResponse;
use app\NoteHelper\ToDoFormatter;
use app\View;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;


class NoteControllerTest extends TestCase
{
    private NoteRepositoryInterface $noteModel;
    private NoteController $controller;
    public function setUp(): void
    {
        parent::setUp();

        $this->noteModel = $this->createMock(NoteRepositoryInterface::class);

        $this->controller = new NoteController($this->noteModel);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $_SESSION = [];
        $_GET = [];
    }

    #[Test]
    public function it_return_view_note(): void
    {
        $_SESSION['username'] = 'test';
        $_GET['sort'] = 'note DESC';

        $this->noteModel
            ->expects($this->once())
            ->method('getNotes')
            ->willReturn([]);

        $result = $this->controller->Todo();
        $this->assertStringContainsString('Ваши Заметки:', $result);
        $this->assertStringContainsString('"Форма создания новой заметки', $result);
    }

    #[Test]
    public function it_return_error_view_when_throws_error(): void
    {
        $_SESSION['username'] = 'test';
        $_GET['sort'] = 'note DESC';

        $this->noteModel
            ->expects($this->once())
            ->method('getNotes')
            ->willThrowException(new Exception());

        $result = $this->controller->Todo();
        $this->assertStringContainsString('Ошибка на нашей стороне', $result);
    }

    #[Test]
    public function it_return_view_edit_note(): void
    {
        $_GET['id'] = 1;

        $this->noteModel
            ->expects($this->once())
            ->method('getNoteId')
            ->willReturn(['id' => 1, 'note' => 'testNote']);

        $result = $this->controller->getEditId();

        $this->assertStringContainsString('note', $result);
        $this->assertStringContainsString('Что-бы вы хотели изменить', $result);
    }

    #[Test]
    public function it_return_throw_exception_when_id_not_exists_or_not_numeric(): void
    {
        $_GET = '';

        $result = $this->controller->getEditId();

        $this->assertStringContainsString('Ошибка на нашей стороне', $result);
    }

    #[Test]
    public function it_return_a_new_note_into_database(): void
    {
        $_POST['note'] = 'note';
        $_SESSION['username'] = 'testName';

        $this->noteModel
            ->expects($this->once())
            ->method('addNote')
            ->with(
                $this->equalTo('note'),
                $this->equalTo('testName'),
            )
            ->willReturn(true);

        $this->noteModel
            ->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(true);

        $result = $this->controller->createNote();

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/ToDo', $result->location);
    }

    #[Test]
    public function it_deleted_Note()
    {
        $_POST['id'] = 1;

        $this->noteModel
            ->expects($this->once())
            ->method('deleteNote')
            ->with(
                $this->equalTo(1),
            )
            ->willReturn(true);

        $result = $this->controller->deleteNote();

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/ToDo', $result->location);
    }

    #[Test]
    public function it_return_error_view_when_id_Delete_Note_is_empty()
    {
        $_POST['id'] = null;

        $result = $this->controller->deleteNote();
        $html = $result->render();

        $this->assertInstanceOf(View::class, $result);
        $this->assertStringContainsString('Ошибка на нашей стороне', $html);
    }

    #[Test]
    public function it_return_error_view_when_failed_deleted_note()
    {
        $_POST['id'] = 1;

        $this->noteModel
            ->expects($this->once())
            ->method('deleteNote')
            ->with($this->equalTo($_POST['id']))
            ->willReturn(false);

        $result = $this->controller->deleteNote();
        $html = $result->render();

        $this->assertInstanceOf(View::class, $result);
        $this->assertStringContainsString('Ошибка на нашей стороне', $html);
    }

    #[Test]
    public function it_edited_Note()
    {
        $_POST['id'] = 1;
        $_POST['editedNote'] = 'test';

        $this->noteModel
            ->expects($this->once())
            ->method('editNote')
            ->with($this->equalTo($_POST['id']), $_POST['editedNote'])
            ->willReturn(true);

        $result = $this->controller->editNote();

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/ToDo', $result->location);
    }

    #[Test]
    public function it_return_error_view_when_id_or_note_EditNote_is_empty()
    {
        $_POST['id'] = null;
        $_POST['editedNote'] = null;

        $result = $this->controller->editNote();

        $this->assertInstanceOf(View::class, $result);
        $this->assertStringContainsString('Ошибка на нашей стороне', $result);
    }

    #[Test]
    public function it_return_error_view_when_failed_edited_note()
    {
        $_POST['id'] = 1;
        $_POST['editedNote'] = 'testNote';

        $this->noteModel
            ->expects($this->once())
            ->method('editNote')
            ->with(
                $this->equalTo($_POST['id']),
                $this->equalTo($_POST['editedNote'])
            )
            ->willReturn(false);

        $result = $this->controller->editNote();
        $html =

        $this->assertInstanceOf(View::class, $result);
        $this->assertStringContainsString('Ошибка на нашей стороне', $result);
    }

    #[Test]
    public function it_note_is_done()
    {
        $_POST['id'] = 1;

        $this->noteModel
            ->expects($this->once())
            ->method('getNoteId')
            ->with($this->equalTo($_POST['id']))
            ->willReturn(['id' => 1, 'note' => 'testNote']);

        $this->noteModel
            ->expects($this->once())
            ->method('setDoneNote')
            ->with($this->equalTo($_POST['id']), $this->equalTo(1))
            ->willReturn(true);


        $result = $this->controller->doneNote();

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame('/ToDo', $result->location);
    }

    #[Test]
    public function it_return_error_view_when_id_DoneNote_is_empty()
    {
        $_POST['id'] = null;

        $result = $this->controller->doneNote();
        $html = $result->render();

        $this->assertInstanceOf(View::class, $result);
        $this->assertStringContainsString('Ошибка на нашей стороне', $html);
    }

    #[Test]
    public function it_return_error_view_when_note_DoneNote_is_empty()
    {
        $_POST['id'] = 1;

        $this->noteModel
            ->expects($this->once())
            ->method('getNoteId')
            ->with($this->equalTo($_POST['id']))
            ->willReturn([]);

        $result = $this->controller->doneNote();
        $html = $result->render();

        $this->assertInstanceOf(View::class, $result);
        $this->assertStringContainsString('Ошибка на нашей стороне', $html);
    }

    #[Test]
    public function  it_return_error_view_when_note__DoneNote_is_not_found()
    {
        $_POST['id'] = 1;

        $this->noteModel
            ->expects($this->once())
            ->method('getNoteId')
            ->with($this->equalTo($_POST['id']))
            ->willReturn(['id' => 1, 'note' => 'testNote']);

        $this->noteModel
            ->expects($this->once())
            ->method('setDoneNote')
            ->with($this->equalTo($_POST['id']), $this->equalTo(1))
            ->willReturn(false);

        $result = $this->controller->doneNote();
        $html = $result->render();

        $this->assertInstanceOf(View::class, $result);
        $this->assertStringContainsString('Ошибка на нашей стороне', $html);

    }
    #[Test]
    public function it_user_is_logOut_your_acc()
    {
        $_SESSION['username'] = 'testName';

        $result = $this->controller->logOut();

        $this->assertSame('/', $result->location);
    }
}

<?php

namespace app\Controllers;

use app\Attributes\Get;
use app\Attributes\Post;
use app\Enums\SortNote;
use app\Models\NoteModel;
use app\NoteHelper\ToDoFormatter;
use app\View;
use Exception;


class NoteController
{

    public function __construct(protected NoteModel $noteModel)
    {
    }

    #[Get('/ToDo')]

    public function Todo(): View
    {
        try {
            $username = $_SESSION['username'];
            $sortParam = $_GET['sort'] ?? null;
            $sort = SortNote::checkFromSort($sortParam);

            $notes = $this->noteModel->getNotes($username, $sort);

            $formattedNote = [];

            foreach ($notes as $note) {
                $note['date'] = ToDoFormatter::formattedDate($note['date']);

                $formattedNote[] = $note;
            }
            return View::make('/ToDo/ToDo',
                [
                    'username' => $_SESSION['username'],
                    'notes' => $formattedNote,
                    'sort' => $sort,
                    'sorts' => SortNote::cases()
                ]);

        } catch (Exception $e) {

            error_log($e->getMessage());
            return View::make('/Error/Error500');
        }
    }

    #[Get('/getEditId')]

    public function getEditId(): void
    {
        try {
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('Not found id');
            }

            $id = $_GET['id'];

            $note = $this->noteModel->getNoteId($id);

            echo View::make('/ToDo/EditedNote', ['note' => $note]);
        } catch (Exception $e) {
            http_response_code(502);
            error_log($e->getMessage());

            echo View::make('Error/Error500');
        }
    }

    #[Post('/createNote')]
    public function createNote(): void
    {
        try {
            $note = ToDoFormatter::formattedNote($_POST['note']) ?? '';
            $username = ToDoFormatter::formattedNote($_SESSION['username']) ?? '';

            if ($note === '' || $username === '') {
                throw new Exception('Not found note or username');
            }

            $this->noteModel->addNote($note, $username);

            if (!$this->noteModel->isLoggedIn()) {
                throw new Exception('You are not logged in');
            }
            header('Location:/ToDo');

        } catch (\Exception $e) {
            error_log($e->getMessage());
            echo View::make('/ErrorTest');
        }
    }

    #[Post('/deleteNote')]

    public function deleteNote(): void
    {
        try {
            $id = $_POST['id'] ??'';

            if ($id === '') {
                throw new Exception('Not found id');
            }

            $this->noteModel->deleteNote($id);

            header('Location: /ToDo');
        } catch (Exception) {
            echo View::make('/Error/Error500');
        }
    }

    #[Post('/editNote')]

    public function editNote(): void
    {
        try {
            $id = $_POST['id'] ?? '';
            $note = $_POST['editedNote'] ?? '';

            if ($id === '' || $note === '') {
                throw new Exception('Not found id or note');
            }

            $this->noteModel->editNote($id, $note);

            header('Location: /ToDo');
        } catch (Exception $e) {

            error_log($e->getMessage());
            echo View::make('/Error/Error500');
        }
    }

    #[Post('/doneNote')]

    public function doneNote(): void
    {
        try {
            $id = $_POST['id'] ?? '';
            if ($id === '') {
                throw new Exception('Not found id');
            }

            $note = $this->noteModel->getNoteId($id);
            if (empty($note)) {
                throw new Exception('Not found note');
            }
            $completed = $note['completed'] ?? 0;
            $this->noteModel->setDoneNote($id, !$completed);

            header('Location: /ToDo');
        } catch (Exception $e) {
            error_log($e->getMessage());
            echo View::make('/Error/Error500');
        }
    }

    #[Get('/logOut')]

    public function logOut(): void
    {
        if (isset($_SESSION['username'])) {
            unset($_SESSION['username']);

            header('Location: /');
            exit;
        }

        header('Location: /');
        exit;
    }
}
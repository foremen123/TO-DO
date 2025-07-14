<?php

namespace app\Controllers;

use app\Attributes\Get;
use app\Attributes\Post;
use app\Enums\SortNote;
use app\Exceptions\NotCreatedNoteException;
use app\interface\NoteRepositoryInterface;
use app\Models\NoteModel;
use app\NoteHelper\RedirectResponse;
use app\NoteHelper\ToDoFormatter;
use app\View;
use Exception;


class NoteController
{

    public function __construct(private readonly NoteRepositoryInterface $noteModel,)
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

            return View::make('/Errors/Error500');
        }
    }

    #[Get('/getEditId')]

    public function getEditId(): View
    {
        try {
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                throw new Exception('Not found id');
            }

            $id = $_GET['id'];

            $note = $this->noteModel->getNoteId($id);

            return View::make('/ToDo/EditedNote', ['note' => $note]);
        } catch (Exception $e) {
            http_response_code(502);

            return View::make('Errors/Error500');
        }
    }

    #[Post('/createNote')]
    public function createNote(): View|RedirectResponse
    {
        try {
            $note = ToDoFormatter::formattedText($_POST['note']);
            $username = ToDoFormatter::formattedText($_SESSION['username']);

            if ($note === '' || $username === '') {
                throw new Exception('Not found note or username');
            }

            if (!$this->noteModel->addNote($note, $username)) {
                throw new NotCreatedNoteException('This note is not created');
            }

            if (!$this->noteModel->isLoggedIn()) {
                throw new Exception('You are not logged in');
            }
            return new RedirectResponse('/ToDo');
        } catch (NotCreatedNoteException $e) {
            return View::make('/Errors/Error500');
        } catch (\Exception $e) {
            return View::make('/Errors/Error404');
        }
    }

    #[Post('/deleteNote')]

    public function deleteNote(): View|RedirectResponse
    {
        try {
            $id = $_POST['id'] ??'';

            if ($id === '') {
                throw new Exception('Not found id');
            }

            if (!$this->noteModel->deleteNote($id)) {
                throw new Exception('Not deleted your note');
            }

            return new RedirectResponse('/ToDo');
        } catch (Exception) {
            return View::make('/Errors/Error500');
        }
    }

    #[Post('/editNote')]

    public function editNote(): View|RedirectResponse
    {
        try {
            $id = $_POST['id'] ?? '';
            $note = $_POST['editedNote'] ?? '';

            if ($id === '' || $note === '') {
                throw new Exception('Not found id or note');
            }

            if (!$this->noteModel->editNote($id, $note)) {
                throw new Exception('This note is not edited');
            }

            return new RedirectResponse('/ToDo');
        } catch (Exception $e) {
            return View::make('/Errors/Error500');
        }
    }

    #[Post('/doneNote')]

    public function doneNote(): View|RedirectResponse
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
            if (! $this->noteModel->setDoneNote($id, !$completed)) {
                throw new Exception('This note is not completed');
            }

            return new RedirectResponse('/ToDo');
        } catch (Exception $e) {
            return View::make('/Errors/Error500');
        }
    }

    #[Get('/logOut')]

    public function logOut(): RedirectResponse
    {
        if (isset($_SESSION['username'])) {
            unset($_SESSION['username']);

            return new RedirectResponse('/');
        }
        return new RedirectResponse('/');
    }
}
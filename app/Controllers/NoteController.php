<?php

namespace app\Controllers;

use app\Attributes\Get;
use app\Attributes\Post;
use app\Models\NoteModel;
use app\NoteHelper\ToDoFormatter;
use app\View;
use Exception;


class NoteController
{
    #[Get('/ToDo')]

    public function Todo(): View
    {
        $username = $_SESSION['username'];

        $noteModel = new NoteModel();
        $notes = $noteModel->getNotes($username);

        return View::make('/ToDo/ToDo',
            [
                'username' => $_SESSION['username'],
                'notes' => $notes
            ]);
    }

    #[Post('/createNote')]
    public function createNote():void
    {
        try {
            $note = ToDoFormatter::formatterNote($_POST['note']) ?? '';
            $username = ToDoFormatter::formatterNote($_SESSION['username']) ?? '';

            if ($note === '' || $username === '') {
                throw new Exception('Not found note or username');
            }

            $noteModel = new NoteModel();
            $noteModel->addNote($note, $username);

            if (!$noteModel->isLoggedIn()) {
                throw new Exception('You are not logged in');
            }
            header('Location:/ToDo');

        } catch (\Exception $e) {
            echo View::make('/ErrorTest');
        }
    }
}
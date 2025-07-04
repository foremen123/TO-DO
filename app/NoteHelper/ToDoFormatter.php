<?php

namespace app\NoteHelper;

class ToDoFormatter
{
    static public function formatterNote(string $note): string
    {
        return trim($note);
    }
}
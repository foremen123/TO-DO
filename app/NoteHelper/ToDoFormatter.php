<?php

namespace app\NoteHelper;

use DateTime;
use DateTimeZone;

class ToDoFormatter
{
    static public function formattedText(string $note): string
    {
        return trim($note) ?? '';
    }

    static public function formattedDate(string $date): string
    {
        $timeZone = new DateTimeZone('Asia/Yekaterinburg');
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $date, new DateTimeZone('UTC'));

        $date->setTimezone($timeZone);
        return $date->format('F j, Y H:i');
    }
}
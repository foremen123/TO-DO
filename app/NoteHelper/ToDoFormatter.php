<?php

namespace app\NoteHelper;

use DateTime;
use DateTimeZone;
use mysql_xdevapi\Exception;

class ToDoFormatter
{
    static public function formattedText(string $note): string
    {
        return trim($note) ?? '';
    }

    static public function formattedDate(string $date): string
    {
        $timeZone = new DateTimeZone('Asia/Yekaterinburg');
        if (! $date = DateTime::createFromFormat('Y-m-d H:i:s', $date, new DateTimeZone('UTC'))) {
            throw new Exception('This format date is not correction');
        }

        $date->setTimezone($timeZone);
        return $date->format('F j, Y H:i');
    }
}
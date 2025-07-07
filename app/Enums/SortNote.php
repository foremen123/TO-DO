<?php

namespace app\Enums;

enum SortNote: string
{
    case DateDESC = 'date DESC';
    case DateASC = 'date ASC';
    case NoteDESC = 'note DESC';
    case NoteASC = 'note ASC';
    case CompletedNote = 'completed DESC';


    public function getSort(): string
    {
        return match($this) {
            self::DateDESC => 'date DESC',
            self::DateASC => 'date ASC',
            self::NoteDESC => 'note DESC',
            self::NoteASC => 'note ASC',
            self::CompletedNote => 'completed DESC'
        };
    }

    static public function checkFromSort(?string $value): self
    {
        return match ($value) {
            'date ASC'  => self::DateASC,
            'note DESC' => self::NoteDESC,
            'note ASC'  => self::NoteASC,
            'completed DESC' => self::CompletedNote,
            default => self::DateDESC,
    };
    }

    public function label(): string
    {
        return match ($this) {
            self::DateDESC   => 'По дате (сначала новые)',
            self::DateASC    => 'По дате (сначала старые)',
            self::NoteASC      => 'По алфавиту (A-Z)',
            self::CompletedNote  => 'Сначала выполненные',
            self::NoteDESC => 'По алфавиту (Z-A)'
        };
    }
}
<?php

namespace app\interface;

use app\Enums\SortNote;

interface NoteRepositoryInterface
{
    public function addNote(string $note, string $username): bool;
    public function getNotes(string $username, SortNote $sortSetting): array;
    public function deleteNote(string $id,): bool;
    public function getNoteId(string $id): array;
    public function editNote(string $id, string $note): bool;
    public function setDoneNote(string $id, bool $completed): bool;
    public function isLoggedIn(): bool;
}
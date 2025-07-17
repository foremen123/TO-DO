<?php

namespace app\Models;

use app\interface\DatabaseInterface;
use app\Models;
use app\Enums\SortNote;
use app\interface\NoteRepositoryInterface;
use PDOException;

class NoteModel extends Model implements NoteRepositoryInterface
{
    public function __construct(?DatabaseInterface $db = null)
    {
        parent::__construct($db);
    }

    public function addNote(string $note, string $username): bool
    {
        $stmt = $this->db->prepare('INSERT INTO notes (note, username) VALUES (?, ?)');
        if (!$stmt->execute([$note, $username])) {
            return false;
        }
        return true;
    }

    public function getNotes(?string $username, SortNote $sortSetting): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM notes WHERE username = ? ORDER BY ' . $sortSetting->getSort()
            );
        if (!$stmt->execute([$username])) {
            throw new PDOException('note receipt failed');
        }
            return $stmt->fetchAll();


    }

    public function deleteNote(string $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM notes WHERE id = ?');
        if (!$stmt->execute([$id])){
            throw new PDOException('note could\'nt be deleted');
        }

        return true;
    }

    public function getNoteId(string $id): array
    {
        $stmt = $this->db->prepare('SELECT * FROM notes WHERE id = ?');

        if(!$stmt->execute([$id])) {
            throw new PDOException('note could\'nt be received');
        }

        return $stmt->fetch();
    }

    public function editNote(string $id, string $note): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE notes SET note = :note, date = CURRENT_TIMESTAMP WHERE id = :id'
        );

        if (!$stmt->execute([':note' => $note,  ':id' => $id])) {
            throw new PDOException('Note could\'nt be updated');
        }

        if ($stmt->rowCount() === 0) {
            throw new PDOException('Note not found or not updated');
        }
        return true;
    }

    public function setDoneNote(string $id, bool $completed): bool
    {
        $stmt = $this->db->prepare('UPDATE notes SET completed = ? WHERE id = ?');

        if (!$stmt->execute([(int) $completed, $id])) {
            throw new PDOException('Failed to update completed');
        }

        return true;
    }

    public function getNoteFromEmail(string $id): array
    {
        $stmt = $this->db->prepare('SELECT note FROM notes WHERE id = ? ');
        $stmt->execute([$id]);

        return $stmt->fetch();
    }
}
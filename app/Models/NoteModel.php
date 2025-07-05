<?php

namespace app\Models;

use app\View;
use PDOException;

class NoteModel extends Model
{
    public function addNote(string $note, string $username): void
    {
        $stmt = $this->db->prepare('INSERT INTO notes (note, username) VALUES (?, ?)');
        if (!$stmt->execute([$note, $username])) {
            throw new PDOException('note could\'nt be sent');
        }
    }

    public function getNotes(string $username): array
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM notes WHERE username = ?');
            if (!$stmt->execute([$username])) {
                throw new PDOException('note receipt failed');
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            http_response_code(502);
            error_log($e->getMessage());
            throw new PDOException($e->getMessage());
        }
    }

    public function deleteNote(string $id,): void
    {
        $stmt = $this->db->prepare('DELETE FROM notes WHERE id = ?');
        if (!$stmt->execute([$id])){
            throw new PDOException('note could\'nt be deleted');
        }
    }

    public function getNoteId(string $id): array
    {
        $stmt = $this->db->prepare('SELECT * FROM notes WHERE id = ?');

        if(!$stmt->execute([$id])) {
            throw new PDOException('note could\'nt be received');
        }

        return $stmt->fetch();
    }

    public function editNote(string $id, string $note): void
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
    }

    public function setDoneNote(string $id, bool $completed): void
    {
        $stmt = $this->db->prepare('UPDATE notes SET completed = ? WHERE id = ?');

        if (!$stmt->execute([(int) $completed, $id])) {
            throw new PDOException('Failed to update completed');
        }
    }
}
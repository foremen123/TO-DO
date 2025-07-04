<?php

namespace app\Models;

use PDOException;

class NoteModel extends Model
{
    public function addNote(string $note, string $username): void
    {
        $stmt = $this->db->prepare('INSERT INTO notes (note, username) VALUES (?, ?)');
        if (! $stmt->execute([$note, $username])) {
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
            throw new PDOException($e->getMessage());
        }
    }
}
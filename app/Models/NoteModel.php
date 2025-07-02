<?php

namespace app\Models;

use PDOException;
use Ramsey\Uuid\Test\TestCase;

class NoteModel extends Model
{
    public function addNote(): void
    {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare('INSERT INTO notes (note, username) VALUE (?, ?)');

            $note = $_POST['note'];
            $username = $_SESSION['username'];

            if (! $stmt->execute([$note, $username])) {
                throw new PDOException('note could\'nt be sent');
            }
            $this->db->commit();
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            http_response_code(501);
            throw new PDOException($e->getMessage());
        }
    }

    public function getNotes(): array
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM notes WHERE username = ?');
            $username = $_SESSION['username'];
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
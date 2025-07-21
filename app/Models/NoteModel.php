<?php

namespace app\Models;

use app\interface\DatabaseInterface;
use app\Models;
use app\Enums\SortNote;
use app\interface\NoteRepositoryInterface;
use Doctrine\DBAL\Exception;
use PDOException;
use RuntimeException;

class NoteModel extends Model implements NoteRepositoryInterface
{
    public function __construct(?DatabaseInterface $db = null)
    {
        parent::__construct($db);
    }

    public function addNote(string $note, string $username): bool
    {
        try {
            $stmt = $this->db->createBuilder()
                ->insert('notes')
                ->values([
                    'note' => ':note',
                    'username' => ':username'
                ])
                ->setParameters([
                    'note' => $note,
                    'username' => $username
                ])
                ->executeStatement();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getNotes(?string $username, SortNote $sortSetting): array
    {
        try {
            return $this->db->createBuilder()
                ->select('*')
                ->from('notes')
                ->where('username = :username')
                ->setParameter('username', $username)
                ->orderBy($sortSetting->getSort())
                ->fetchAllAssociative();
        } catch (Exception $e) {
            throw new RuntimeException('note receipt failed: ' . $e->getMessage());
        }
    }

    public function deleteNote(string $id): bool
    {
        try {
            $this->db->createBuilder()
                ->delete('notes')
                ->where('id = :id')
                ->setParameter('id', $id)
                ->executeStatement();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getNoteId(string $id): array
    {
        try {
            return $this->db->createBuilder()
                ->select('*')
                ->from('notes',)
                ->where('id = :id')
                ->setParameter('id', $id)
                ->fetchAssociative();

        } catch (Exception $e) {
            throw new RuntimeException('Note not found: ' . $e->getMessage());
        }
    }

    public function editNote(string $id, string $note): bool
    {
        try {
            $stmt = $this->db->createBuilder()
                ->update('notes', 'n')
                ->set('note', ':note')
                ->set('date', 'CURRENT_TIMESTAMP')
                ->where('id', ':id')
                ->setParameters([
                    'note' => $note,
                    'id' => $id
                ])
                ->executeStatement();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function setDoneNote(string $id, bool $completed): bool
    {
        try {
            $this->db->createBuilder()
                ->update('notes')
                ->set('completed', ':completed')
                ->where('id = :id')
                ->setParameters([
                    'completed' => (int) $completed,
                    'id' => $id
                ])
                ->executeStatement();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getNoteFromEmail(string $id): array
    {
        try {
            return $this->db->createBuilder()
                ->select('note')
                ->from('notes', )
                ->where('id = :id')
                ->setParameter('id', $id)
                ->fetchAssociative();
        } catch (Exception $e) {

            throw new RuntimeException('Note not found: ' . $e->getMessage());
        }
    }
}
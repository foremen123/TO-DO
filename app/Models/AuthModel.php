<?php

namespace app\Models;

use app\DB;
use app\interface\DatabaseInterface;
use app\Models;
use app\interface\AuthRepositoryInterface;
use Doctrine\DBAL\Exception;
use PDOException;
use function PHPUnit\Framework\isNull;

class AuthModel extends Model implements AuthRepositoryInterface
{
    public function __construct(?DatabaseInterface $db = null)
    {
        parent::__construct($db);
    }

    public function registration(string $username, string $password): bool
    {
        try {
            $stmt = $this->db->createBuilder()
                ->insert('users')
                ->values([
                    'username' => ':username',
                    'password' => ':password'
                ])
                ->setParameters(
                    [   'username' => $username,
                        'password' => password_hash($password, PASSWORD_BCRYPT)
                    ])
                ->executeQuery();

            $_SESSION['username'] = $username;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function login(string $username, string $password): bool
    {
        try {
            $user = $this->db->createBuilder()
                ->select('*')
                ->from('users')
                ->where('username = :username')
                ->setParameter('username', $username)
                ->fetchAssociative();

            if ($user && ($user['username'] === $username && password_verify($password, $user['password']))) {
                $_SESSION['username'] = $user['username'];
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }
}
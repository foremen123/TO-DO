<?php

namespace app\Models;

use app\DB;
use app\interface\DatabaseInterface;
use app\Models;
use app\interface\AuthRepositoryInterface;
use PDOException;

class AuthModel extends Model implements AuthRepositoryInterface
{
    public function __construct(?DatabaseInterface $db = null)
    {
        parent::__construct($db);
    }

    public function registration(string $username, string $password): bool
    {
        $stmt = $this->db->prepare('INSERT INTO users (username, password) VALUES (?, ?)');

        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        if (!$stmt->execute([$username, $hashPassword])) {
            return false;
        }
        $_SESSION['username'] = $username;
        return true;

    }

    public function login(string $username, string $password): bool
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ?');

        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && ($user['username'] === $username && password_verify($password, $user['password']))) {
            $_SESSION['username'] = $user['username'];
            return true;
        }

        return false;
    }
}
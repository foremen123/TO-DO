<?php

namespace app\Models;

use PDOException;

class AuthModel extends Model
{
    public function registration(string $username, string $password): bool
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO users (username, password) VALUES (?, ?)');

            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            if (!$stmt->execute([$username, $hashPassword])) {
                return false;
            }
            $_SESSION['username'] = $username;
            return true;

        } catch (PDOException $e) {
            throw  new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function login(string $username, string $password): bool
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ?');

            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && ($user['username'] === $username && password_verify($password, $user['password']))) {
                $_SESSION['username'] = $user['username'];
                return true;
            }

            return false;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function test()
    {
        $stmt = $this->db->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->execute([123, 123]);

    }
}
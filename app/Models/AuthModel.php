<?php

namespace app\Models;

use PDOException;

class AuthModel extends Model
{
    public function registration(): void
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO users (username, password) VALUE (?, ?)');
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->execute([$username, $password]);
        } catch (PDOException $e) {
            throw  new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function login(): bool
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM users WHERE ?');

            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && ($user['username'] === $username && $user['password'] === $password)) {
                $_SESSION['userId'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['userId']);
    }
}
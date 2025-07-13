<?php

namespace app\interface;

interface AuthRepositoryInterface
{
    public function registration(string $username, string $password): bool;
    public function login(string $username, string $password): bool;
}
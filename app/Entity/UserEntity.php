<?php

declare(strict_types=1);

namespace app\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table]
class UserEntity
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    private int $id;

    #[Column(type: 'string', length: 25, unique: true)]
    private string $username;

    #[Column(type: 'string', length: 255)]
    private string $password;
}
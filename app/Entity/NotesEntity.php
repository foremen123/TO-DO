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

class NotesEntity
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    private int $id;

    #[Column(type: 'string', length: 255)]
    private string $note;

    #[Column(type: 'datetime')]
    private string $createdAt = 'CURRENT_TIMESTAMP';

    #[Column(length: 25)]
    private string $username;

    #[Column(type: 'smallint')]
    private int $completed = 0;
}
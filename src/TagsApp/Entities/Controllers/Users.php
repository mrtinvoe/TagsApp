<?php

namespace TagsApp\Entities\Controllers;

use TagsApp\Entities\Execute\User;
use WBoX\Classes\Entity\EntityController;
use WBoX\Classes\Entity\EntityHandler;

class Users extends EntityController implements EntityHandler
{
    static public function getEntity(): User
    {
        return new User();
    }

    public function current(): ?User
    {
        return $this->records[$this->position];
    }

    public function find(array $where = [], array $order = []): ?User
    {
        return $this->fetchOne($where, $order);
    }

    public function findById(int $id, bool $isDeleted = false): ?User
    {
        return $this->fetchById($id, $isDeleted);
    }
}

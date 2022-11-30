<?php

namespace TagsApp\Entities;

use WBoX\Classes\Main\Entity;


/**
 * @Entity()
 * @Table(name="tagsapp_user",engine="InnoDB")
 */
class User extends Entity
{

    /**
     * @Column(type="integer",length=11)
     * @Id()
     * @GeneratedValue(strategy="AUTO")
     */
    protected int $id;

    /**
     * @Column(type="string",length=64)
     */
    protected string $name;

    /**
     * @Column(type="integer",rowIndex=true,caption="id",hidden=true)
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @Column(type="string",caption="Jméno",column="name")
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChangeSetRepository")
 */
class ChangeSet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(name="change_set", type="string", length=255)
     */
    private $changeSet;

    /**
     * @ORM\Column(name="name", type="string", length=0, nullable=false, columnDefinition="ENUM('contestant', 'official', 'registration', 'transport')")
     */
    private $name;

    /**
     * @ORM\Column(name="type", type="string", length=0, nullable=false, columnDefinition="ENUM('ADD', 'DROP', 'UPDATE')")
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $name_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChangeSet(): ?string
    {
        return $this->changeSet;
    }

    public function setChangeSet(string $changeSet): self
    {
        $this->changeSet = $changeSet;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getNameId(): ?int
    {
        return $this->name_id;
    }

    public function setNameId(?int $name_id): self
    {
        $this->name_id = $name_id;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceItemRepository")
 */
class InvoiceItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $amountEuro;

    /**
     * @ORM\Column(type="smallint")
     */
    private $amountCent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAmountEuro(): ?int
    {
        return $this->amountEuro;
    }

    public function setAmountEuro(int $amountEuro): self
    {
        $this->amountEuro = $amountEuro;

        return $this;
    }

    public function getAmountCent(): ?int
    {
        return $this->amountCent;
    }

    public function setAmountCent(int $amountCent): self
    {
        $this->amountCent = $amountCent;

        return $this;
    }
}

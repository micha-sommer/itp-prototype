<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoicePositionRepository")
 */
class InvoicePosition
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Invoice", inversedBy="invoicePositions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $invoice;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $multiplier;

    /**
     * In cents.
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * In cents.
     * @ORM\Column(type="integer")
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getMultiplier()
    {
        return $this->multiplier;
    }

    public function setMultiplier($multiplier): self
    {
        $this->multiplier = $multiplier;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function calculateTotal(): self
    {
        return $this->setTotal($this->getPrice() * $this->getMultiplier());
    }
}

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
     * @ORM\ManyToOne(targetEntity="App\Entity\InvoiceItem")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

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
     * @ORM\Column(type="boolean")
     */
    private $isAdd;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalEuro;

    /**
     * @ORM\Column(type="smallint")
     */
    private $totalCent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItem(): ?InvoiceItem
    {
        return $this->item;
    }

    public function setItem(?InvoiceItem $item): self
    {
        $this->item = $item;

        return $this;
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

    public function getIsAdd(): ?bool
    {
        return $this->isAdd;
    }

    public function setIsAdd(bool $isAdd): self
    {
        $this->isAdd = $isAdd;

        return $this;
    }

    public function getTotalEuro(): ?int
    {
        return $this->totalEuro;
    }

    public function setTotalEuro(int $totalEuro): self
    {
        $this->totalEuro = $totalEuro;

        return $this;
    }

    public function getTotalCent(): ?int
    {
        return $this->totalCent;
    }

    public function setTotalCent(int $totalCent): self
    {
        $this->totalCent = $totalCent;

        return $this;
    }
}

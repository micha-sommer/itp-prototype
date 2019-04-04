<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 */
class Invoice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Registration", inversedBy="invoice", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $registration;

    /**
     * @ORM\Column(type="integer")
     */
    private $paidCashEuro;

    /**
     * @ORM\Column(type="smallint")
     */
    private $paidCashCent;

    /**
     * @ORM\Column(type="integer")
     */
    private $paidBankEuro;

    /**
     * @ORM\Column(type="smallint")
     */
    private $paidBankCent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InvoicePosition", mappedBy="invoice", orphanRemoval=true)
     */
    private $invoicePositions;

    public function __construct()
    {
        $this->invoicePositions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegistration(): ?Registration
    {
        return $this->registration;
    }

    public function setRegistration(Registration $registration): self
    {
        $this->registration = $registration;

        return $this;
    }

    public function getPaidCashEuro(): ?int
    {
        return $this->paidCashEuro;
    }

    public function setPaidCashEuro(int $paidCashEuro): self
    {
        $this->paidCashEuro = $paidCashEuro;

        return $this;
    }

    public function getPaidCashCent(): ?int
    {
        return $this->paidCashCent;
    }

    public function setPaidCashCent(int $paidCashCent): self
    {
        $this->paidCashCent = $paidCashCent;

        return $this;
    }

    public function getPaidBankEuro(): ?int
    {
        return $this->paidBankEuro;
    }

    public function setPaidBankEuro(int $paidBankEuro): self
    {
        $this->paidBankEuro = $paidBankEuro;

        return $this;
    }

    public function getPaidBankCent(): ?int
    {
        return $this->paidBankCent;
    }

    public function setPaidBankCent(int $paidBankCent): self
    {
        $this->paidBankCent = $paidBankCent;

        return $this;
    }

    /**
     * @return Collection|InvoicePosition[]
     */
    public function getInvoicePositions(): Collection
    {
        return $this->invoicePositions;
    }

    public function addInvoicePosition(InvoicePosition $invoicePosition): self
    {
        if (!$this->invoicePositions->contains($invoicePosition)) {
            $this->invoicePositions[] = $invoicePosition;
            $invoicePosition->setInvoice($this);
        }

        return $this;
    }

    public function removeInvoicePosition(InvoicePosition $invoicePosition): self
    {
        if ($this->invoicePositions->contains($invoicePosition)) {
            $this->invoicePositions->removeElement($invoicePosition);
            // set the owning side to null (unless already changed)
            if ($invoicePosition->getInvoice() === $this) {
                $invoicePosition->setInvoice(null);
            }
        }

        return $this;
    }
}

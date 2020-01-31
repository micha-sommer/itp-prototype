<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 */
class Invoice implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $subId;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InvoicePosition", mappedBy="invoice", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $invoicePositions;

    /**
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Registration", inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $registration;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invoiceAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timestamp;

    public function __construct()
    {
        $this->invoicePositions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function calculateTotal(): self
    {
        $total = 0;
        foreach ($this->getInvoicePositions() as $invoicePosition)
        {
            $total += $invoicePosition->getTotal();
        }
        return $this->setTotal($total);
    }

    public function getRegistration(): ?Registration
    {
        return $this->registration;
    }

    public function setRegistration(?Registration $registration): self
    {
        $this->registration = $registration;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getInvoiceAddress(): ?string
    {
        return $this->invoiceAddress;
    }

    public function setInvoiceAddress(?string $invoiceAddress): self
    {
        $this->invoiceAddress = $invoiceAddress;

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

    public function getSubId(): ?int
    {
        return $this->subId;
    }

    public function setSubId(int $subId): self
    {
        $this->subId = $subId;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}

<?php /** @noinspection PhpUnused */

namespace App\Entity;

use App\Repository\InvoicePositionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoicePositionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class InvoicePosition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $amountInHundreds = null;

    #[ORM\Column]
    private ?int $priceInHundreds = null;

    #[ORM\Column]
    private ?int $totalInHundreds = null;

    #[ORM\ManyToOne(inversedBy: 'invoicePositions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Invoice $invoice = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $modifiedAt = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAmountInHundreds(): ?int
    {
        return $this->amountInHundreds;
    }

    public function setAmountInHundreds(int $amountInHundreds): self
    {
        $this->amountInHundreds = $amountInHundreds;

        return $this;
    }

    public function getPriceInHundreds(): ?int
    {
        return $this->priceInHundreds;
    }

    public function setPriceInHundreds(int $priceInHundreds): self
    {
        $this->priceInHundreds = $priceInHundreds;

        return $this;
    }

    public function getTotalInHundreds(): ?int
    {
        return $this->totalInHundreds;
    }

    public function setTotalInHundreds(int $totalInHundreds): self
    {
        $this->totalInHundreds = $totalInHundreds;

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

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(DateTimeImmutable $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new DateTimeImmutable();
        $this->modifiedAt = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->modifiedAt = new DateTimeImmutable();
    }
}

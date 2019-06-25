<?php

namespace App\Entity;

use App\Enum\GenderEnum;
use App\Enum\ITCEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Registration
 *
 * @ORM\Table(name="registrations")
 * @ORM\Entity
 * @UniqueEntity("email")
 */
class Registration implements UserInterface, \Serializable, \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="club", type="string", length=255, nullable=false)
     */
    private $club;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=false)
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @Assert\Email()
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telephone", type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @var string | null
     * @Assert\Length(max=24)
     */
    private $plainPassword;

    /**
     * @var string|null
     *
     * @ORM\Column(name="password", type="string", length=64)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transport", mappedBy="registration", orphanRemoval=true)
     */
    private $transports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Official", mappedBy="registration", orphanRemoval=true)
     */
    private $officials;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Contestant", mappedBy="registration", orphanRemoval=true)
     */
    private $contestants;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Invoice", mappedBy="registration", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $invoice;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $invoice_address;

    public function __construct()
    {
        $this->transports = new ArrayCollection();
        $this->officials = new ArrayCollection();
        $this->contestants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClub(): ?string
    {
        return $this->club;
    }

    public function setClub(string $club): self
    {
        $this->club = $club;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }


    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[] The user roles
     */
    public function getRoles(): array
    {
        if (\in_array($this->getEmail(), ['m.remmos@gmail.com', 'info@thueringer-judoverband.de', 'webmaster@thueringer-judoverband.de'], true)) {
            return ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH'];
        }
        return ['ROLE_USER'];
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt(): ?string
    {
        // we use bcrypt, therefore no salt needed
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(): ?string
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void
    {
        $this->setPlainPassword('');
    }

    /**
     * @return Collection|Transport[]
     */
    public function getTransports(): Collection
    {
        return $this->transports;
    }

    public function addTransport(Transport $transport): self
    {
        if (!$this->transports->contains($transport)) {
            $this->transports[] = $transport;
            $transport->setRegistration($this);
        }

        return $this;
    }

    public function removeTransport(Transport $transport): self
    {
        if ($this->transports->contains($transport)) {
            $this->transports->removeElement($transport);
            // set the owning side to null (unless already changed)
            if ($transport->getRegistration() === $this) {
                $transport->setRegistration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Official[]
     */
    public function getOfficials(): Collection
    {
        return $this->officials;
    }

    public function addOfficial(Official $official): self
    {
        if (!$this->officials->contains($official)) {
            $this->officials[] = $official;
            $official->setRegistration($this);
        }

        return $this;
    }

    public function removeOfficial(Official $official): self
    {
        if ($this->officials->contains($official)) {
            $this->officials->removeElement($official);
            // set the owning side to null (unless already changed)
            if ($official->getRegistration() === $this) {
                $official->setRegistration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contestant[]
     */
    public function getContestants(): Collection
    {
        return $this->contestants;
    }

    public function addContestant(Contestant $contestant): self
    {
        if (!$this->contestants->contains($contestant)) {
            $this->contestants[] = $contestant;
            $contestant->setRegistration($this);
        }

        return $this;
    }

    public function removeContestant(Contestant $contestant): self
    {
        if ($this->contestants->contains($contestant)) {
            $this->contestants->removeElement($contestant);
            // set the owning side to null (unless already changed)
            if ($contestant->getRegistration() === $this) {
                $contestant->setRegistration(null);
            }
        }

        return $this;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(
            [
                $this->id,
                $this->firstName,
                $this->lastName,
                $this->email,
                $this->club,
                $this->password,
            ]
        );
    }

    /** @param $serialized
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->firstName,
            $this->lastName,
            $this->email,
            $this->club,
            $this->password,
        ] = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

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
        return \get_object_vars($this);
    }

    public function getArrival(): ?Transport
    {
        $first = $this->getTransports()->filter(function (Transport $transport) {
            return $transport->getIsArrival();
        });
        if ($first->isEmpty())
            return null;
        return $first->first();
    }

    public function getDeparture(): ?Transport
    {
        $first = $this->getTransports()->filter(function (Transport $transport) {
            return !$transport->getIsArrival();
        });
        if ($first->isEmpty())
            return null;
        return $first->first();
    }

    public function getOvernightFridayCount(): int
    {
        $numOfficials = $this->officials->filter(function (Official $official) {
            return $official->getFriday();
        })->count();
        $numContestants = $this->contestants->filter(function (Contestant $contestant) {
            return $contestant->getFriday();
        })->count();
        return $numOfficials + $numContestants;
    }

    public function getOvernightSaturndayCount(): int
    {
        $numOfficials = $this->officials->filter(function (Official $official) {
            return $official->getSaturday();
        })->count();
        $numContestants = $this->contestants->filter(function (Contestant $contestant) {
            return $contestant->getSaturday();
        })->count();
        return $numOfficials + $numContestants;

    }

    public function getITCToTuesdayCount(): int
    {
        $numOfficials = $this->officials->filter(function (Official $official) {
            return $official->getItc() === ITCEnum::tillTuesday;
        })->count();
        $numContestants = $this->contestants->filter(function (Contestant $contestant) {
            return $contestant->getItc() === ITCEnum::tillTuesday;
        })->count();
        return $numOfficials + $numContestants;
    }

    public function getITCToWednesdayCount(): int
    {
        $numOfficials = $this->officials->filter(function (Official $official) {
            return $official->getItc() === ITCEnum::tillWednesday;
        })->count();
        $numContestants = $this->contestants->filter(function (Contestant $contestant) {
            return $contestant->getItc() === ITCEnum::tillWednesday;
        })->count();
        return $numOfficials + $numContestants;
    }

    public function setInvoice(Invoice $invoice): self
    {
        $this->invoice = $invoice;

        // set the owning side of the relation if necessary
        if ($this !== $invoice->getRegistration()) {
            $invoice->setRegistration($this);
        }

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function getInvoiceAddress(): ?string
    {
        return $this->invoice_address;
    }

    public function setInvoiceAddress(?string $invoice_address): self
    {
        $this->invoice_address = $invoice_address;

        return $this;
    }

}

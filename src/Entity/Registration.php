<?php /** @noinspection PhpUnused */

namespace App\Entity;

use App\Repository\RegistrationRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Registration implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $club = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $timestamp = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\OneToMany(
        mappedBy: 'registration',
        targetEntity: Contestant::class,
        cascade: ["persist", "remove"],
        orphanRemoval: true,
    )]
    private Collection $contestants;

    #[ORM\OneToMany(
        mappedBy: 'registration',
        targetEntity: Official::class,
        cascade: ["persist", "remove"],
        orphanRemoval: true
    )]
    private Collection $officials;

    public function __construct()
    {
        $this->contestants = new ArrayCollection();
        $this->officials = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getTimestamp(): ?DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTimeInterface $timestamp): self
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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Contestant>
     */
    public function getContestants(): Collection
    {
        return $this->contestants;
    }

    public function addContestant(Contestant $contestant): self
    {
        if (!$this->contestants->contains($contestant)) {
            $this->contestants->add($contestant);
            $contestant->setRegistration($this);
        }

        return $this;
    }

    public function removeContestant(Contestant $contestant): self
    {
        if ($this->contestants->removeElement($contestant)) {
            // set the owning side to null (unless already changed)
            if ($contestant->getRegistration() === $this) {
                $contestant->setRegistration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Official>
     */
    public function getOfficials(): Collection
    {
        return $this->officials;
    }

    public function addOfficial(Official $official): self
    {
        if (!$this->officials->contains($official)) {
            $this->officials->add($official);
            $official->setRegistration($this);
        }

        return $this;
    }

    public function removeOfficial(Official $official): self
    {
        if ($this->officials->removeElement($official)) {
            // set the owning side to null (unless already changed)
            if ($official->getRegistration() === $this) {
                $official->setRegistration(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Enum\GenderEnum;
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
class Registration implements UserInterface, \Serializable
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
     * @Assert\Length(max=4096)
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
        if ($this->getEmail() === 'm.remmos@gmail.com') {
            return array('ROLE_USER', 'ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH');
        }
        return array('ROLE_USER');
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
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
    public function getSalt()
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

    public function getInvoice(): string
    {
        $officials = $this->getOfficials();
        $femaleOfficials = $officials->filter(function (Official $official) {
            return $official->getGender() === GenderEnum::female;
        });
        $maleOfficials = $officials->filter(function (Official $official) {
            return $official->getGender() === GenderEnum::female;
        });
        $contestant = $this->getContestants();
        return $officials->count() . '
        ' . $femaleOfficials->count() . '
        ' . $maleOfficials->count() . '
        ' . $contestant->count();
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

    /** @see \Serializable::unserialize()
     * @param $serialized
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
}

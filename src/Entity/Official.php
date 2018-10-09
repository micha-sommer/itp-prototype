<?php

namespace App\Entity;

use App\Enum\GenderEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * Official
 *
 * @ORM\Table(name="officials")
 * @ORM\Entity(repositoryClass="OfficialRepository")
 */
class Official
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
     * @ORM\Column(name="role", type="string", length=0, nullable=false, columnDefinition="ENUM('trainer', 'physio/psychotherapist', 'referee', 'others')") )
     */
    private $role;

    /**
     * @var GenderEnum
     *
     * @ORM\Column(name="gender", type="string", length=0, nullable=false, columnDefinition="ENUM('male', 'female')") )
     */
    private $gender;

    /**
     * @var boolean|null
     *
     * @ORM\Column(name="itc", type="boolean", nullable=false)
     */
    private $itc;

    /**
     * @var boolean|null
     *
     * @ORM\Column(name="friday", type="boolean", nullable=false)
     */
    private $friday;

    /**
     * @var boolean|null
     *
     * @ORM\Column(name="saturday", type="boolean", nullable=false)
     */
    private $saturday;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Registration", inversedBy="officials")
     * @ORM\JoinColumn(nullable=true)
     */
    private $registration;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function setRegistrationId(int $registration): self
    {
        $this->registration = $registration;

        return $this;
    }

    public function getItc(): ?bool
    {
        return $this->itc;
    }

    public function setItc(bool $itc): self
    {
        $this->itc = $itc;

        return $this;
    }

    public function getFriday(): ?bool
    {
        return $this->friday;
    }

    public function setFriday(bool $friday): self
    {
        $this->friday = $friday;

        return $this;
    }

    public function getSaturday(): ?bool
    {
        return $this->saturday;
    }

    public function setSaturday(bool $saturday): self
    {
        $this->saturday = $saturday;

        return $this;
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
}

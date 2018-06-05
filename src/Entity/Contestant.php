<?php

namespace App\Entity;

use App\Entity\Enum\AgeCategoryEnum;
use App\Entity\Enum\WeightCategoryEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * Contestant
 *
 * @ORM\Table(name="contestants")
 * @ORM\Entity
 */
class Contestant
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
     * @var \DateTime
     *
     * @ORM\Column(name="year", type="date", nullable=false)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="weight_category", type="string", length=0, nullable=false)
     */
    private $weightCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="age_category", type="string", length=0, nullable=false)
     */
    private $ageCategory;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="arrival", type="date", nullable=true)
     */
    private $arrival;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="departure", type="date", nullable=true)
     */
    private $departure;

    /**
     * @ORM\ManyToOne(targetEntity="Registration")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="id")
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

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(\DateTimeInterface $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getWeightCategory(): ?string
    {
        return $this->weightCategory;
    }

    public function setWeightCategory(string $weightCategory): self
    {
        $this->weightCategory = new WeightCategoryEnum($weightCategory);

        return $this;
    }

    public function getAgeCategory(): ?string
    {
        return $this->ageCategory;
    }

    public function setAgeCategory(string $ageCategory): self
    {
        $this->ageCategory = new AgeCategoryEnum($ageCategory);

        return $this;
    }

    public function getArrival(): ?\DateTimeInterface
    {
        return $this->arrival;
    }

    public function setArrival(?\DateTimeInterface $arrival): self
    {
        $this->arrival = $arrival;

        return $this;
    }

    public function getDeparture(): ?\DateTimeInterface
    {
        return $this->departure;
    }

    public function setDeparture(?\DateTimeInterface $departure): self
    {
        $this->departure = $departure;

        return $this;
    }

    public function getRegistration(): ?int
    {
        return $this->registration;
    }

    public function setRegistration(int $registration): self
    {
        $this->registration = $registration;

        return $this;
    }


}

<?php

namespace App\Entity;

use App\Enum\AgeCategoryEnum;
use App\Enum\WeightCategoryEnum;
use App\Enum\ITCEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contestant
 *
 * @ORM\Table(name="contestants")
 * @ORM\Entity
 */
class Contestant implements \JsonSerializable
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
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=4, nullable=false)
     */
    private $year;

    /**
     * @var WeightCategoryEnum
     *
     * @ORM\Column(name="weight_category", type="string", length=0, nullable=false, columnDefinition="ENUM('-40','-44','-48','-52','-57','-63','-70','+70','-78','+78','camp_only')")
     */
    private $weightCategory;

    /**
     * @var AgeCategoryEnum
     *
     * @ORM\Column(name="age_category", type="string", length=0, nullable=false, columnDefinition="ENUM('cadet', 'junior')")
     */
    private $ageCategory;

    /**
     * @var ITCEnum
     *
     * @ORM\Column(name="itc", type="string", length=0, nullable=false, columnDefinition="ENUM('no', 'su-tu', 'su-we')")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Registration", inversedBy="contestants")
     * @ORM\JoinColumn(nullable=true)
     */
    private $registration;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

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

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
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
        $this->weightCategory = $weightCategory;

        return $this;
    }

    public function getAgeCategory(): ?string
    {
        return $this->ageCategory;
    }

    public function setAgeCategory(string $ageCategory): self
    {
        $this->ageCategory = $ageCategory;

        return $this;
    }

    public function getItc(): ?string
    {
        return $this->itc;
    }

    public function setItc(string $itc): self
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

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @Assert\IsTrue(message="Year of birth does not match age category.", payload="year")
     * @return bool
     */
    public function isValidYearAgeCombination(): bool
    {
        if ($this->weightCategory === WeightCategoryEnum::camp_only) {
            return true;
        }
        return \in_array($this->year, AgeCategoryEnum::getYears($this->ageCategory), true);
    }

    /**
     * @Assert\IsTrue(message="Age category does not match weight category.", payload="weight")
     * @return bool
     */
    public function isValidAgeWeightCombination(): bool
    {
        if ($this->weightCategory === WeightCategoryEnum::camp_only) {
            return true;
        }
        return \in_array($this->weightCategory, AgeCategoryEnum::getWeightCategories($this->ageCategory), true);
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
}

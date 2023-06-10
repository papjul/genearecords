<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * We try to keep most fields as string, as we can have sometimes special characters
 * such as 28? with a question mark if someone is not sure about the age written
 */
#[ORM\Entity(repositoryClass: LocationRepository::class)]
#[ORM\Table(name: 'location')]
#[ORM\UniqueConstraint(name: "location_idx", columns: ["place", "province", "country"])]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator("doctrine.ulid_generator")]
    #[ORM\Column(length: 26)]
    private ?string $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $place = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $province = null;

    #[ORM\Column(length: 30, nullable: false)]
    private ?string $country = null;

    #[ORM\Column(type: 'decimal', precision: 9, scale: 6, nullable: true)]
    private ?string $lon = null;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 6, nullable: true)]
    private ?string $lat = null;

    #[ORM\Column(nullable: false)]
    private int $all_records_total = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $all_records_year_from = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $all_records_year_to = 0;

    #[ORM\Column(nullable: false)]
    private int $birth_records_total = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $birth_records_year_from = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $birth_records_year_to = 0;

    #[ORM\Column(nullable: false)]
    private int $baptism_records_total = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $baptism_records_year_from = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $baptism_records_year_to = 0;

    #[ORM\Column(nullable: false)]
    private int $marriage_records_total = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $marriage_records_year_from = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $marriage_records_year_to = 0;

    #[ORM\Column(nullable: false)]
    private int $death_records_total = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $death_records_year_from = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $death_records_year_to = 0;

    #[ORM\Column(nullable: false)]
    private int $burial_records_total = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $burial_records_year_from = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $burial_records_year_to = 0;

    #[ORM\Column(nullable: false)]
    private int $other_records_total = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $other_records_year_from = 0;

    #[ORM\Column(length: 4, nullable: true)]
    private ?int $other_records_year_to = 0;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: Record::class)]
    protected $records;

    public function __construct()
    {
        $this->records = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getPlace(): ?string
    {
        return $this->place;
    }

    /**
     * @param string|null $place
     * @return Location
     */
    public function setPlace(?string $place): Location
    {
        $this->place = $place;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProvince(): ?string
    {
        return $this->province;
    }

    /**
     * @param string|null $province
     * @return Location
     */
    public function setProvince(?string $province): Location
    {
        $this->province = $province;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     * @return Location
     */
    public function setCountry(?string $country): Location
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLon(): ?string
    {
        return $this->lon;
    }

    /**
     * @param string|null $lon
     * @return Location
     */
    public function setLon(?string $lon): Location
    {
        $this->lon = $lon;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLat(): ?string
    {
        return $this->lat;
    }

    /**
     * @param string|null $lat
     * @return Location
     */
    public function setLat(?string $lat): Location
    {
        $this->lat = $lat;
        return $this;
    }

    /**
     * @return int
     */
    public function getAllRecordsTotal(): int
    {
        return $this->all_records_total;
    }

    /**
     * @param int $all_records_total
     * @return Location
     */
    public function setAllRecordsTotal(int $all_records_total): Location
    {
        $this->all_records_total = $all_records_total;
        return $this;
    }

    /**
     * @return int
     */
    public function getBirthRecordsTotal(): int
    {
        return $this->birth_records_total;
    }

    /**
     * @param int $birth_records_total
     * @return Location
     */
    public function setBirthRecordsTotal(int $birth_records_total): Location
    {
        $this->birth_records_total = $birth_records_total;
        return $this;
    }

    /**
     * @return int
     */
    public function getBaptismRecordsTotal(): int
    {
        return $this->baptism_records_total;
    }

    /**
     * @param int $baptism_records_total
     * @return Location
     */
    public function setBaptismRecordsTotal(int $baptism_records_total): Location
    {
        $this->baptism_records_total = $baptism_records_total;
        return $this;
    }

    /**
     * @return int
     */
    public function getMarriageRecordsTotal(): int
    {
        return $this->marriage_records_total;
    }

    /**
     * @param int $marriage_records_total
     * @return Location
     */
    public function setMarriageRecordsTotal(int $marriage_records_total): Location
    {
        $this->marriage_records_total = $marriage_records_total;
        return $this;
    }

    /**
     * @return int
     */
    public function getDeathRecordsTotal(): int
    {
        return $this->death_records_total;
    }

    /**
     * @param int $death_records_total
     * @return Location
     */
    public function setDeathRecordsTotal(int $death_records_total): Location
    {
        $this->death_records_total = $death_records_total;
        return $this;
    }

    /**
     * @return int
     */
    public function getBurialRecordsTotal(): int
    {
        return $this->burial_records_total;
    }

    /**
     * @param int $burial_records_total
     * @return Location
     */
    public function setBurialRecordsTotal(int $burial_records_total): Location
    {
        $this->burial_records_total = $burial_records_total;
        return $this;
    }

    /**
     * @return int
     */
    public function getOtherRecordsTotal(): int
    {
        return $this->other_records_total;
    }

    /**
     * @param int $other_records_total
     * @return Location
     */
    public function setOtherRecordsTotal(int $other_records_total): Location
    {
        $this->other_records_total = $other_records_total;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAllRecordsYearFrom(): ?int
    {
        return $this->all_records_year_from;
    }

    /**
     * @param int|null $all_records_year_from
     * @return Location
     */
    public function setAllRecordsYearFrom(?int $all_records_year_from): Location
    {
        $this->all_records_year_from = $all_records_year_from;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAllRecordsYearTo(): ?int
    {
        return $this->all_records_year_to;
    }

    /**
     * @param int|null $all_records_year_to
     * @return Location
     */
    public function setAllRecordsYearTo(?int $all_records_year_to): Location
    {
        $this->all_records_year_to = $all_records_year_to;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBirthRecordsYearFrom(): ?int
    {
        return $this->birth_records_year_from;
    }

    /**
     * @param int|null $birth_records_year_from
     * @return Location
     */
    public function setBirthRecordsYearFrom(?int $birth_records_year_from): Location
    {
        $this->birth_records_year_from = $birth_records_year_from;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBirthRecordsYearTo(): ?int
    {
        return $this->birth_records_year_to;
    }

    /**
     * @param int|null $birth_records_year_to
     * @return Location
     */
    public function setBirthRecordsYearTo(?int $birth_records_year_to): Location
    {
        $this->birth_records_year_to = $birth_records_year_to;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBaptismRecordsYearFrom(): ?int
    {
        return $this->baptism_records_year_from;
    }

    /**
     * @param int|null $baptism_records_year_from
     * @return Location
     */
    public function setBaptismRecordsYearFrom(?int $baptism_records_year_from): Location
    {
        $this->baptism_records_year_from = $baptism_records_year_from;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBaptismRecordsYearTo(): ?int
    {
        return $this->baptism_records_year_to;
    }

    /**
     * @param int|null $baptism_records_year_to
     * @return Location
     */
    public function setBaptismRecordsYearTo(?int $baptism_records_year_to): Location
    {
        $this->baptism_records_year_to = $baptism_records_year_to;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMarriageRecordsYearFrom(): ?int
    {
        return $this->marriage_records_year_from;
    }

    /**
     * @param int|null $marriage_records_year_from
     * @return Location
     */
    public function setMarriageRecordsYearFrom(?int $marriage_records_year_from): Location
    {
        $this->marriage_records_year_from = $marriage_records_year_from;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMarriageRecordsYearTo(): ?int
    {
        return $this->marriage_records_year_to;
    }

    /**
     * @param int|null $marriage_records_year_to
     * @return Location
     */
    public function setMarriageRecordsYearTo(?int $marriage_records_year_to): Location
    {
        $this->marriage_records_year_to = $marriage_records_year_to;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDeathRecordsYearFrom(): ?int
    {
        return $this->death_records_year_from;
    }

    /**
     * @param int|null $death_records_year_from
     * @return Location
     */
    public function setDeathRecordsYearFrom(?int $death_records_year_from): Location
    {
        $this->death_records_year_from = $death_records_year_from;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDeathRecordsYearTo(): ?int
    {
        return $this->death_records_year_to;
    }

    /**
     * @param int|null $death_records_year_to
     * @return Location
     */
    public function setDeathRecordsYearTo(?int $death_records_year_to): Location
    {
        $this->death_records_year_to = $death_records_year_to;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBurialRecordsYearFrom(): ?int
    {
        return $this->burial_records_year_from;
    }

    /**
     * @param int|null $burial_records_year_from
     * @return Location
     */
    public function setBurialRecordsYearFrom(?int $burial_records_year_from): Location
    {
        $this->burial_records_year_from = $burial_records_year_from;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getBurialRecordsYearTo(): ?int
    {
        return $this->burial_records_year_to;
    }

    /**
     * @param int|null $burial_records_year_to
     * @return Location
     */
    public function setBurialRecordsYearTo(?int $burial_records_year_to): Location
    {
        $this->burial_records_year_to = $burial_records_year_to;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOtherRecordsYearFrom(): ?int
    {
        return $this->other_records_year_from;
    }

    /**
     * @param int|null $other_records_year_from
     * @return Location
     */
    public function setOtherRecordsYearFrom(?int $other_records_year_from): Location
    {
        $this->other_records_year_from = $other_records_year_from;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOtherRecordsYearTo(): ?int
    {
        return $this->other_records_year_to;
    }

    /**
     * @param int|null $other_records_year_to
     * @return Location
     */
    public function setOtherRecordsYearTo(?int $other_records_year_to): Location
    {
        $this->other_records_year_to = $other_records_year_to;
        return $this;
    }

    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function __toString()
    {
        return $this->place . ', ' . $this->province . ', '.$this->country;
    }
}

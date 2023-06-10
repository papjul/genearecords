<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * We try to keep most fields as string, as we can have sometimes special characters
 * such as 28? with a question mark if someone is not sure about the age written
 */
#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: 'event')]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator("doctrine.ulid_generator")]
    #[ORM\Column(length: 26)]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Record::class, inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Record $record = null;

    #[ORM\Column(length: 30, nullable: false)]
    private ?string $type = null;

    #[ORM\Column(length: 4, nullable: false)]
    #[Assert\Range(
        notInRangeMessage: 'Year must be between {{ min }} and {{ max }}',
        min: 1000,
        max: 2100,
    )]
    private ?int $year = null;

    #[ORM\Column(length: 2, nullable: true)]
    #[Assert\Range(
        notInRangeMessage: 'Month must be between {{ min }} and {{ max }}',
        min: 1,
        max: 12,
    )]
    private ?int $month = null;

    #[ORM\Column(length: 2, nullable: true)]
    #[Assert\Range(
        notInRangeMessage: 'Day must be between {{ min }} and {{ max }}',
        min: 1,
        max: 31,
    )]
    private ?int $day = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $place = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $province = null;

    // FIXME: Is mandatory
    #[ORM\Column(length: 30, nullable: false)]
    private ?string $country = null;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return Record|null
     */
    public function getRecord(): ?Record
    {
        return $this->record;
    }

    /**
     * @param Record|null $record
     * @return Event
     */
    public function setRecord(?Record $record): Event
    {
        $this->record = $record;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return Event
     */
    public function setType(?string $type): Event
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int|null $year
     * @return Event
     */
    public function setYear(?int $year): Event
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMonth(): ?int
    {
        return $this->month;
    }

    /**
     * @param int|null $month
     * @return Event
     */
    public function setMonth(?int $month): Event
    {
        $this->month = $month;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDay(): ?int
    {
        return $this->day;
    }

    /**
     * @param int|null $day
     * @return Event
     */
    public function setDay(?int $day): Event
    {
        $this->day = $day;
        return $this;
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
     * @return Event
     */
    public function setPlace(?string $place): Event
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
     * @return Event
     */
    public function setProvince(?string $province): Event
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
     * @return Event
     */
    public function setCountry(?string $country): Event
    {
        $this->country = $country;
        return $this;
    }

    public function __toString()
    {
        return $this->id;
    }
}

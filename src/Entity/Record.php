<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\RecordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * We try to keep most fields as string, as we can have sometimes special characters
 * such as 28? with a question mark if someone is not sure about the age written
 */
#[ORM\Entity(repositoryClass: RecordRepository::class)]
#[ORM\Table(name: 'record')]
class Record
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator("doctrine.ulid_generator")]
    #[ORM\Column(length: 26)]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Import::class, inversedBy: 'records')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Import $import = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $record_number = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $page_number = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $indexed_by = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $source = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $link = null;

    #[ORM\OneToMany(mappedBy: 'record', targetEntity: Person::class, cascade: ['persist', 'remove'])]
    protected $persons;

    #[ORM\OneToMany(mappedBy: 'record', targetEntity: Event::class, cascade: ['persist', 'remove'])]
    protected $events;

    public function __construct()
    {
        $this->persons = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return Import|null
     */
    public function getImport(): ?Import
    {
        return $this->import;
    }

    /**
     * @param Import|null $import
     * @return Record
     */
    public function setImport(?Import $import): Record
    {
        $this->import = $import;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecordNumber(): ?string
    {
        return $this->record_number;
    }

    /**
     * @param string|null $record_number
     * @return Record
     */
    public function setRecordNumber(?string $record_number): Record
    {
        $this->record_number = $record_number;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPageNumber(): ?string
    {
        return $this->page_number;
    }

    /**
     * @param string|null $page_number
     * @return Record
     */
    public function setPageNumber(?string $page_number): Record
    {
        $this->page_number = $page_number;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     * @return Record
     */
    public function setNotes(?string $notes): Record
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIndexedBy(): ?string
    {
        return $this->indexed_by;
    }

    /**
     * @param string|null $indexed_by
     * @return Record
     */
    public function setIndexedBy(?string $indexed_by): Record
    {
        $this->indexed_by = $indexed_by;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param string|null $source
     * @return Record
     */
    public function setSource(?string $source): Record
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     * @return Record
     */
    public function setLink(?string $link): Record
    {
        $this->link = $link;
        return $this;
    }

    public function getPersons()
    {
        return $this->persons;
    }

    public function addPerson(Person $person): void {
        $this->persons->add($person);
        $person->setRecord($this);
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function addEvent(Event $event): void {
        $this->events->add($event);
        $event->setRecord($this);
    }

    public function __toString()
    {
        return $this->id;
    }
}

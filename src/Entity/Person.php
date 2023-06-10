<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * We try to keep most fields as string, as we can have sometimes special characters
 * such as 28? with a question mark if someone is not sure about the age written
 */
#[ORM\Entity]
#[ORM\Table(name: 'person')]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator("doctrine.ulid_generator")]
    #[ORM\Column(length: 26)]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Record::class, inversedBy: 'persons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Record $record = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $surname = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $given_names = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $age = null;

    #[ORM\Column(length: 30, nullable: false)]
    private ?string $relationship = null;
    // one of: 'individual', 'spouse', 'father', 'mother', 'father_father', 'father_mother', 'mother_father', 'mother_mother', 'spouse_father_father', 'spouse_father_mother', 'spouse_mother_father', 'spouse_mother_mother'
    // can be extended later (godfather, witness, etc)

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
     * @return Person
     */
    public function setRecord(?Record $record): Person
    {
        $this->record = $record;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string|null $surname
     * @return Person
     */
    public function setSurname(?string $surname): Person
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGivenNames(): ?string
    {
        return $this->given_names;
    }

    /**
     * @param string|null $given_names
     * @return Person
     */
    public function setGivenNames(?string $given_names): Person
    {
        $this->given_names = $given_names;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAge(): ?string
    {
        return $this->age;
    }

    /**
     * @param string|null $age
     * @return Person
     */
    public function setAge(?string $age): Person
    {
        $this->age = $age;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    /**
     * @param string|null $relationship
     * @return Person
     */
    public function setRelationship(?string $relationship): Person
    {
        $this->relationship = $relationship;
        return $this;
    }

    public function __toString()
    {
        return $this->surname . ' ' . $this->given_names . ': '.$this->relationship;
    }
}

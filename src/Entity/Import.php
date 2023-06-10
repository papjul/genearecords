<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImportRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Save any import attempt
 */
#[ORM\Entity(repositoryClass: ImportRepository::class)]
#[ORM\Table(name: 'import')]
class Import
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator("doctrine.ulid_generator")]
    #[ORM\Column(length: 26, nullable: false)]
    private ?string $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $csv_filename = null;

    #[ORM\Column(nullable: true)]
    private ?DateTime $created_on = null;

    #[ORM\Column(nullable: true)]
    private ?User $created_by = null;

    #[ORM\Column(nullable: true)]
    private ?User $imported_by = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $place = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $province = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $country = null;

    #[ORM\OneToMany(mappedBy: 'import', targetEntity: ImportField::class, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['number' => 'ASC'])]
    protected $fields;

    #[ORM\OneToMany(mappedBy: 'import', targetEntity: ImportFieldAdditional::class, cascade: ['persist', 'remove'])]
    protected $fieldsAdditional;

    #[ORM\OneToMany(mappedBy: 'import', targetEntity: Record::class, cascade: ['persist', 'remove'])]
    protected $records;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
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
    public function getCsvFilename(): ?string
    {
        return $this->csv_filename;
    }

    /**
     * @param string|null $csv_filename
     * @return Import
     */
    public function setCsvFilename(?string $csv_filename): Import
    {
        $this->csv_filename = $csv_filename;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedOn(): ?DateTime
    {
        return $this->created_on;
    }

    /**
     * @param DateTime|null $created_on
     * @return Import
     */
    public function setCreatedOn(?DateTime $created_on): Import
    {
        $this->created_on = $created_on;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getCreatedBy(): ?User
    {
        return $this->created_by;
    }

    /**
     * @param User|null $created_by
     * @return Import
     */
    public function setCreatedBy(?User $created_by): Import
    {
        $this->created_by = $created_by;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getImportedBy(): ?User
    {
        return $this->imported_by;
    }

    /**
     * @param User|null $imported_by
     * @return Import
     */
    public function setImportedBy(?User $imported_by): Import
    {
        $this->imported_by = $imported_by;
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
     * @return Import
     */
    public function setPlace(?string $place): Import
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
     * @return Import
     */
    public function setProvince(?string $province): Import
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
     * @return Import
     */
    public function setCountry(?string $country): Import
    {
        $this->country = $country;
        return $this;
    }

    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function getFieldsAsArray(): array
    {
        $fieldsArray = [];
        foreach ($this->fields as $field) {
            $fieldsArray[$field->getNumber()] = $field->getDatabaseField();
        }
        return $fieldsArray;
    }

    public function getFieldsAdditional(): Collection
    {
        return $this->fieldsAdditional;
    }

    public function getFieldsAdditionalAsArray(): array
    {
        $fieldsArray = [];
        foreach ($this->fieldsAdditional as $field) {
            $fieldsArray[$field->getDatabaseField()] = $field->getValue();
        }
        return $fieldsArray;
    }

    public function addFieldsAdditional(ImportFieldAdditional $importFieldAdditional): void
    {
        $importFieldAdditional->setImport($this);
        $this->fieldsAdditional->add($importFieldAdditional);
    }

    public function removeFieldsAdditional(ImportFieldAdditional $importFieldAdditional): void
    {
        $this->fieldsAdditional->removeElement($importFieldAdditional);
    }

    /**
     * Returns all fields, the ones from the CSV + the additional ones
     */
    public function getFieldsCategorized(): ?array
    {
        $fieldsArray = [];
        foreach ($this->fields as $field) {
            $fieldSplit = explode('.', $field->getDatabaseField());
            if (!in_array($fieldSplit[0], ['record', 'event', 'person'])) {
                continue;
            }

            if ($fieldSplit[0] === 'record') {
                $fieldsArray['record'][$fieldSplit[1]] = $field->getDatabaseField();
            } else {
                $fieldsArray[$fieldSplit[0]][$fieldSplit[1]][$fieldSplit[2]] = $field->getDatabaseField();
            }
        }
        foreach ($this->fieldsAdditional as $fieldAdditional) {
            $fieldAdditionalSplit = explode('.', $fieldAdditional->getDatabaseField());
            if (!in_array($fieldAdditionalSplit[0], ['record', 'event', 'person'])) {
                continue;
            }

            if ($fieldAdditionalSplit[0] === 'record') {
                $fieldsArray['record'][$fieldAdditionalSplit[1]] = $fieldAdditional->getDatabaseField();
            } else {
                $fieldsArray[$fieldAdditionalSplit[0]][$fieldAdditionalSplit[1]][$fieldAdditionalSplit[2]] = $fieldAdditional->getDatabaseField();
            }
        }

        // Consistency check
        if (!array_key_exists('record', $fieldsArray) || !array_key_exists('event', $fieldsArray) || !array_key_exists('person', $fieldsArray)) {
            return null;
        }
        // TODO: Check other inconsistencies

        // Complete data if required
        // TODO: Check existence of fields we copy, before
        if (array_key_exists('father', $fieldsArray['person'])) {
            if (!array_key_exists('surname', $fieldsArray['person']['father'])) {
                $fieldsArray['person']['father']['surname'] = $fieldsArray['person']['individual']['surname'];
            }
        }
        if (array_key_exists('father_father', $fieldsArray['person'])) {
            if (!array_key_exists('surname', $fieldsArray['person']['father_father'])) {
                $fieldsArray['person']['father_father']['surname'] = $fieldsArray['person']['individual']['surname'];
            }
        }
        if (array_key_exists('mother_father', $fieldsArray['person'])) {
            if (!array_key_exists('surname', $fieldsArray['person']['mother_father'])) {
                $fieldsArray['person']['mother_father']['surname'] = $fieldsArray['person']['mother']['surname'];
            }
        }
        // Redundant for spouse
        if (array_key_exists('spouse_father', $fieldsArray['person'])) {
            if (!array_key_exists('surname', $fieldsArray['person']['spouse_father'])) {
                $fieldsArray['person']['spouse_father']['surname'] = $fieldsArray['person']['spouse']['surname'];
            }
        }
        if (array_key_exists('spouse_father_father', $fieldsArray['person'])) {
            if (!array_key_exists('surname', $fieldsArray['person']['spouse_father_father'])) {
                $fieldsArray['person']['spouse_father_father']['surname'] = $fieldsArray['person']['spouse']['surname'];
            }
        }
        if (array_key_exists('spouse_mother_father', $fieldsArray['person'])) {
            if (!array_key_exists('surname', $fieldsArray['person']['spouse_mother_father'])) {
                $fieldsArray['person']['spouse_mother_father']['surname'] = $fieldsArray['person']['spouse_mother']['surname'];
            }
        }

        return $fieldsArray;
    }

    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function isReadyToBeImported(): bool
    {
        if ($this->csv_filename !== null) {
            $fieldsFound = [];
            foreach ($this->fields as $field) {
                if ($field->getDatabaseField()) {
                    // If we find twice the same field, it's an error
                    if (in_array($field->getDatabaseField(), $fieldsFound)) {
                        return false;
                    }
                    $fieldsFound[] = $field->getDatabaseField();
                }
            }
            foreach ($this->fieldsAdditional as $fieldAdditional) {
                if ($fieldAdditional->getDatabaseField()) {
                    // If we find twice the same field, it's an error
                    if (in_array($fieldAdditional->getDatabaseField(), $fieldsFound)) {
                        return false;
                    }
                    $fieldsFound[] = $fieldAdditional->getDatabaseField();
                }
            }

            // Surname + given names mandatory
            if (in_array('person.individual.surname', $fieldsFound) && in_array('person.individual.given_names', $fieldsFound)) {
                // Year + country mandatory + if "other" type, type is mandatory
                if(in_array('event.birth.year', $fieldsFound) && in_array('event.birth.place', $fieldsFound)
                    || in_array('event.baptism.year', $fieldsFound) && in_array('event.baptism.country', $fieldsFound)
                    || in_array('event.marriage.year', $fieldsFound) && in_array('event.marriage.country', $fieldsFound)
                    || in_array('event.death.year', $fieldsFound) && in_array('event.death.country', $fieldsFound)
                    || in_array('event.burial.year', $fieldsFound) && in_array('event.burial.country', $fieldsFound)
                    || (in_array('event.other.year', $fieldsFound) && in_array('event.other.country', $fieldsFound) && in_array('event.other.type', $fieldsFound))
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    public function __toString()
    {
        return $this->csv_filename;
    }
}

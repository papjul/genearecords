<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'import_field_additional')]
class ImportFieldAdditional
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator("doctrine.ulid_generator")]
    #[ORM\Column(length: 26, nullable: false)]
    private ?string $id = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $database_field = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $value = null;

    #[ORM\ManyToOne(targetEntity: Import::class, inversedBy: 'fieldsAdditional')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Import $import = null;

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
    public function getDatabaseField(): ?string
    {
        return $this->database_field;
    }

    /**
     * @param string|null $database_field
     * @return ImportFieldAdditional
     */
    public function setDatabaseField(?string $database_field): ImportFieldAdditional
    {
        $this->database_field = $database_field;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     * @return ImportFieldAdditional
     */
    public function setValue(?string $value): ImportFieldAdditional
    {
        $this->value = $value;
        return $this;
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
     * @return ImportFieldAdditional
     */
    public function setImport(?Import $import): ImportFieldAdditional
    {
        $this->import = $import;
        return $this;
    }

    public function __toString()
    {
        return $this->database_field;
    }
}

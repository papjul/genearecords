<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImportRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Save any import attempt
 */
#[ORM\Entity]
#[ORM\Table(name: 'import_field')]
class ImportField
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator("doctrine.ulid_generator")]
    #[ORM\Column(length: 26, nullable: false)]
    private ?string $id = null;

    #[ORM\Column(length: 2, nullable: false)]
    private ?int $number = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $database_field = null;

    #[ORM\ManyToOne(targetEntity: Import::class, inversedBy: 'fields')]
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
     * @return int|null
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @param int|null $number
     * @return ImportField
     */
    public function setNumber(?int $number): ImportField
    {
        $this->number = $number;
        return $this;
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
     * @return ImportField
     */
    public function setDatabaseField(?string $database_field): ImportField
    {
        $this->database_field = $database_field;
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
     * @return ImportField
     */
    public function setImport(?Import $import): ImportField
    {
        $this->import = $import;
        return $this;
    }

    public function __toString()
    {
        return $this->number . (($this->database_field) ? ': ' . $this->database_field : '');
    }
}

<?php

namespace App\Entity;

use App\Repository\MedicamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedicamentRepository::class)]
class Medicament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $formePharmaceutique = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $dosageStandard = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $contreIndications = null;

    #[ORM\OneToMany(mappedBy: 'medicament', targetEntity: LignePrescription::class)]
    private Collection $lignesPrescription;

    public function __construct()
    {
        $this->lignesPrescription = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getFormePharmaceutique(): ?string { return $this->formePharmaceutique; }
    public function setFormePharmaceutique(?string $f): static { $this->formePharmaceutique = $f; return $this; }

    public function getDosageStandard(): ?string { return $this->dosageStandard; }
    public function setDosageStandard(?string $d): static { $this->dosageStandard = $d; return $this; }

    public function getContreIndications(): ?string { return $this->contreIndications; }
    public function setContreIndications(?string $c): static { $this->contreIndications = $c; return $this; }

    public function getLignesPrescription(): Collection { return $this->lignesPrescription; }

    public function __toString(): string { return (string) $this->nom; }
}

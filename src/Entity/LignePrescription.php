<?php

namespace App\Entity;

use App\Repository\LignePrescriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LignePrescriptionRepository::class)]
class LignePrescription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prescription $prescription = null;

    #[ORM\ManyToOne(inversedBy: 'lignesPrescription')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medicament $medicament = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $dosage = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $frequence = null;

    #[ORM\Column(nullable: true)]
    private ?int $dureeJours = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $instructions = null;

    #[ORM\OneToMany(mappedBy: 'lignePrescription', targetEntity: AdministrationMedicament::class, cascade: ['persist'])]
    private Collection $administrations;

    public function __construct()
    {
        $this->administrations = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getPrescription(): ?Prescription { return $this->prescription; }
    public function setPrescription(?Prescription $p): static { $this->prescription = $p; return $this; }

    public function getMedicament(): ?Medicament { return $this->medicament; }
    public function setMedicament(?Medicament $m): static { $this->medicament = $m; return $this; }

    public function getDosage(): ?string { return $this->dosage; }
    public function setDosage(?string $d): static { $this->dosage = $d; return $this; }

    public function getFrequence(): ?string { return $this->frequence; }
    public function setFrequence(?string $f): static { $this->frequence = $f; return $this; }

    public function getDureeJours(): ?int { return $this->dureeJours; }
    public function setDureeJours(?int $d): static { $this->dureeJours = $d; return $this; }

    public function getInstructions(): ?string { return $this->instructions; }
    public function setInstructions(?string $i): static { $this->instructions = $i; return $this; }

    public function getAdministrations(): Collection { return $this->administrations; }
}

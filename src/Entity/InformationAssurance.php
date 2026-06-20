<?php

namespace App\Entity;

use App\Repository\InformationAssuranceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InformationAssuranceRepository::class)]
class InformationAssurance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'assurances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\Column(length: 150)]
    private ?string $compagnie = null;

    #[ORM\Column(length: 100)]
    private ?string $numeroPolice = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $statutRemboursement = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateExpiration = null;

    public function getId(): ?int { return $this->id; }

    public function getPatient(): ?Patient { return $this->patient; }
    public function setPatient(?Patient $patient): static { $this->patient = $patient; return $this; }

    public function getCompagnie(): ?string { return $this->compagnie; }
    public function setCompagnie(string $compagnie): static { $this->compagnie = $compagnie; return $this; }

    public function getNumeroPolice(): ?string { return $this->numeroPolice; }
    public function setNumeroPolice(string $numeroPolice): static { $this->numeroPolice = $numeroPolice; return $this; }

    public function getStatutRemboursement(): ?string { return $this->statutRemboursement; }
    public function setStatutRemboursement(?string $s): static { $this->statutRemboursement = $s; return $this; }

    public function getDateExpiration(): ?\DateTimeInterface { return $this->dateExpiration; }
    public function setDateExpiration(?\DateTimeInterface $d): static { $this->dateExpiration = $d; return $this; }
}

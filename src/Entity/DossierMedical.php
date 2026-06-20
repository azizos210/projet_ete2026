<?php

namespace App\Entity;

use App\Repository\DossierMedicalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DossierMedicalRepository::class)]
#[ORM\HasLifecycleCallbacks]
class DossierMedical
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'dossierMedical', targetEntity: Patient::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $antecedentsMedicaux = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $antecedentsFamiliaux = null;

    #[ORM\OneToMany(mappedBy: 'dossierMedical', targetEntity: Consultation::class)]
    #[ORM\OrderBy(['date' => 'DESC'])]
    private Collection $consultations;

    #[ORM\OneToMany(mappedBy: 'dossierMedical', targetEntity: DocumentMedical::class)]
    private Collection $documents;

    #[ORM\OneToMany(mappedBy: 'dossierMedical', targetEntity: AvisSpecialise::class)]
    private Collection $avisSpecialises;

    public function __construct()
    {
        $this->consultations   = new ArrayCollection();
        $this->documents       = new ArrayCollection();
        $this->avisSpecialises = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void { $this->dateCreation = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }

    public function getPatient(): ?Patient { return $this->patient; }
    public function setPatient(?Patient $patient): static { $this->patient = $patient; return $this; }

    public function getDateCreation(): ?\DateTimeImmutable { return $this->dateCreation; }

    public function getAntecedentsMedicaux(): ?string { return $this->antecedentsMedicaux; }
    public function setAntecedentsMedicaux(?string $a): static { $this->antecedentsMedicaux = $a; return $this; }

    public function getAntecedentsFamiliaux(): ?string { return $this->antecedentsFamiliaux; }
    public function setAntecedentsFamiliaux(?string $a): static { $this->antecedentsFamiliaux = $a; return $this; }

    public function getConsultations(): Collection { return $this->consultations; }
    public function getDocuments(): Collection { return $this->documents; }
    public function getAvisSpecialises(): Collection { return $this->avisSpecialises; }
}

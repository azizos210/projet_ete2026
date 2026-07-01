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
    private ?string $diagnostic = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $traitement = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $observations = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateMiseAJour = null;

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

    #[ORM\PreUpdate]
    public function onPreUpdate(): void { $this->dateMiseAJour = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }

    public function getPatient(): ?Patient { return $this->patient; }
    public function setPatient(?Patient $patient): static { $this->patient = $patient; return $this; }

    public function getDateCreation(): ?\DateTimeImmutable { return $this->dateCreation; }

    public function getDiagnostic(): ?string { return $this->diagnostic; }
    public function setDiagnostic(?string $diagnostic): static { $this->diagnostic = $diagnostic; return $this; }

    public function getTraitement(): ?string { return $this->traitement; }
    public function setTraitement(?string $traitement): static { $this->traitement = $traitement; return $this; }

    public function getObservations(): ?string { return $this->observations; }
    public function setObservations(?string $observations): static { $this->observations = $observations; return $this; }

    public function getDateMiseAJour(): ?\DateTimeImmutable { return $this->dateMiseAJour; }
    public function setDateMiseAJour(?\DateTimeImmutable $dateMiseAJour): static { $this->dateMiseAJour = $dateMiseAJour; return $this; }

    public function getAntecedentsMedicaux(): ?string { return $this->antecedentsMedicaux; }
    public function setAntecedentsMedicaux(?string $a): static { $this->antecedentsMedicaux = $a; return $this; }

    public function getAntecedentsFamiliaux(): ?string { return $this->antecedentsFamiliaux; }
    public function setAntecedentsFamiliaux(?string $a): static { $this->antecedentsFamiliaux = $a; return $this; }

    public function getConsultations(): Collection { return $this->consultations; }
    public function getDocuments(): Collection { return $this->documents; }
    public function getAvisSpecialises(): Collection { return $this->avisSpecialises; }
}

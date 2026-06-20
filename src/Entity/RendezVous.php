<?php

namespace App\Entity;

use App\Enum\StatutRendezVousEnum;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
#[ORM\Table(name: 'rendez_vous')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(columns: ['date_heure'], name: 'idx_rdv_date')]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateHeure = null;

    #[ORM\Column(length: 20, enumType: StatutRendezVousEnum::class, options: ['default' => 'en_attente'])]
    private StatutRendezVousEnum $statut = StatutRendezVousEnum::EN_ATTENTE;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $rappelEnvoye = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVous')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVous')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medecin $medecin = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVousCrees')]
    private ?SecretaireMedicale $secretaire = null;

    #[ORM\OneToOne(mappedBy: 'rendezVous', targetEntity: Consultation::class)]
    private ?Consultation $consultation = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void { $this->dateCreation = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }

    public function getDateHeure(): ?\DateTimeInterface { return $this->dateHeure; }
    public function setDateHeure(\DateTimeInterface $dateHeure): static { $this->dateHeure = $dateHeure; return $this; }

    public function getStatut(): StatutRendezVousEnum { return $this->statut; }
    public function setStatut(StatutRendezVousEnum $statut): static { $this->statut = $statut; return $this; }

    public function getMotif(): ?string { return $this->motif; }
    public function setMotif(?string $motif): static { $this->motif = $motif; return $this; }

    public function isRappelEnvoye(): bool { return $this->rappelEnvoye; }
    public function setRappelEnvoye(bool $rappelEnvoye): static { $this->rappelEnvoye = $rappelEnvoye; return $this; }

    public function getDateCreation(): ?\DateTimeImmutable { return $this->dateCreation; }

    public function getPatient(): ?Patient { return $this->patient; }
    public function setPatient(?Patient $patient): static { $this->patient = $patient; return $this; }

    public function getMedecin(): ?Medecin { return $this->medecin; }
    public function setMedecin(?Medecin $medecin): static { $this->medecin = $medecin; return $this; }

    public function getSecretaire(): ?SecretaireMedicale { return $this->secretaire; }
    public function setSecretaire(?SecretaireMedicale $secretaire): static { $this->secretaire = $secretaire; return $this; }

    public function getConsultation(): ?Consultation { return $this->consultation; }
    public function setConsultation(?Consultation $consultation): static { $this->consultation = $consultation; return $this; }
}

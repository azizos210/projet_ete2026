<?php

namespace App\Entity;

use App\Repository\MedecinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedecinRepository::class)]
class Medecin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'medecin', targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 150)]
    private ?string $specialite = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $numeroOrdre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $signatureNumerique = null;

    #[ORM\Column(options: ['default' => true])]
    private bool $actif = true;

    #[ORM\OneToMany(mappedBy: 'medecin', targetEntity: RendezVous::class)]
    private Collection $rendezVous;

    #[ORM\OneToMany(mappedBy: 'medecin', targetEntity: DisponibiliteMedecin::class, cascade: ['persist', 'remove'])]
    private Collection $disponibilites;

    #[ORM\OneToMany(mappedBy: 'medecin', targetEntity: Consultation::class)]
    private Collection $consultations;

    #[ORM\OneToMany(mappedBy: 'medecin', targetEntity: Prescription::class)]
    private Collection $prescriptions;

    #[ORM\OneToMany(mappedBy: 'medecinDemandeur', targetEntity: AvisSpecialise::class)]
    private Collection $avisDemandesEnvoyes;

    #[ORM\OneToMany(mappedBy: 'medecinSpecialiste', targetEntity: AvisSpecialise::class)]
    private Collection $avisDemandesRecus;

    public function __construct()
    {
        $this->rendezVous          = new ArrayCollection();
        $this->disponibilites      = new ArrayCollection();
        $this->consultations       = new ArrayCollection();
        $this->prescriptions       = new ArrayCollection();
        $this->avisDemandesEnvoyes = new ArrayCollection();
        $this->avisDemandesRecus   = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getSpecialite(): ?string { return $this->specialite; }
    public function setSpecialite(string $specialite): static { $this->specialite = $specialite; return $this; }

    public function getNumeroOrdre(): ?string { return $this->numeroOrdre; }
    public function setNumeroOrdre(string $numeroOrdre): static { $this->numeroOrdre = $numeroOrdre; return $this; }

    public function getSignatureNumerique(): ?string { return $this->signatureNumerique; }
    public function setSignatureNumerique(?string $sig): static { $this->signatureNumerique = $sig; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    public function getRendezVous(): Collection { return $this->rendezVous; }
    public function getDisponibilites(): Collection { return $this->disponibilites; }
    public function getConsultations(): Collection { return $this->consultations; }
    public function getPrescriptions(): Collection { return $this->prescriptions; }
    public function getAvisDemandesEnvoyes(): Collection { return $this->avisDemandesEnvoyes; }
    public function getAvisDemandesRecus(): Collection { return $this->avisDemandesRecus; }

    public function __toString(): string { return 'Dr ' . $this->utilisateur?->getNomComplet() . ' (' . $this->specialite . ')'; }
}

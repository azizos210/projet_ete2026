<?php

namespace App\Entity;

use App\Enum\GenreEnum;
use App\Repository\PatientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
class Patient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'patient', targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 10, enumType: GenreEnum::class, nullable: true)]
    private ?GenreEnum $genre = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $groupeSanguin = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $allergies = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contactUrgence = null;

    #[ORM\Column(length: 50, nullable: true, unique: true)]
    private ?string $numeroSecuriteSociale = null;

    #[ORM\OneToOne(mappedBy: 'patient', targetEntity: DossierMedical::class, cascade: ['persist'])]
    private ?DossierMedical $dossierMedical = null;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: RendezVous::class)]
    private Collection $rendezVous;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: Facture::class)]
    private Collection $factures;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: InformationAssurance::class)]
    private Collection $assurances;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: Evaluation::class)]
    private Collection $evaluations;

    #[ORM\ManyToMany(targetEntity: RessourceEducative::class, mappedBy: 'patients')]
    private Collection $ressourcesEducatives;

    #[ORM\OneToMany(mappedBy: 'demandeur', targetEntity: TicketAssistance::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->rendezVous           = new ArrayCollection();
        $this->factures             = new ArrayCollection();
        $this->assurances           = new ArrayCollection();
        $this->evaluations          = new ArrayCollection();
        $this->ressourcesEducatives = new ArrayCollection();
        $this->tickets              = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getDateNaissance(): ?\DateTimeInterface { return $this->dateNaissance; }
    public function setDateNaissance(?\DateTimeInterface $date): static { $this->dateNaissance = $date; return $this; }

    public function getGenre(): ?GenreEnum { return $this->genre; }
    public function setGenre(?GenreEnum $genre): static { $this->genre = $genre; return $this; }

    public function getGroupeSanguin(): ?string { return $this->groupeSanguin; }
    public function setGroupeSanguin(?string $g): static { $this->groupeSanguin = $g; return $this; }

    public function getAllergies(): ?string { return $this->allergies; }
    public function setAllergies(?string $a): static { $this->allergies = $a; return $this; }

    public function getContactUrgence(): ?string { return $this->contactUrgence; }
    public function setContactUrgence(?string $c): static { $this->contactUrgence = $c; return $this; }

    public function getNumeroSecuriteSociale(): ?string { return $this->numeroSecuriteSociale; }
    public function setNumeroSecuriteSociale(?string $n): static { $this->numeroSecuriteSociale = $n; return $this; }

    public function getDossierMedical(): ?DossierMedical { return $this->dossierMedical; }
    public function setDossierMedical(?DossierMedical $d): static { $this->dossierMedical = $d; return $this; }

    public function getRendezVous(): Collection { return $this->rendezVous; }
    public function getFactures(): Collection { return $this->factures; }
    public function getAssurances(): Collection { return $this->assurances; }
    public function getEvaluations(): Collection { return $this->evaluations; }
    public function getRessourcesEducatives(): Collection { return $this->ressourcesEducatives; }
    public function getTickets(): Collection { return $this->tickets; }

    public function __toString(): string { return $this->utilisateur?->getNomComplet() ?? ''; }
}

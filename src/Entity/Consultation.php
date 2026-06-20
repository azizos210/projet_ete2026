<?php

namespace App\Entity;

use App\Enum\StatutConsultationEnum;
use App\Repository\ConsultationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $symptomes = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $examenClinique = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $diagnostic = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $recommandations = null;

    #[ORM\Column(length: 20, enumType: StatutConsultationEnum::class, options: ['default' => 'en_cours'])]
    private StatutConsultationEnum $statut = StatutConsultationEnum::EN_COURS;

    // Relations
    #[ORM\OneToOne(inversedBy: 'consultation', targetEntity: RendezVous::class)]
    private ?RendezVous $rendezVous = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DossierMedical $dossierMedical = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medecin $medecin = null;

    #[ORM\ManyToOne(inversedBy: 'consultationsAssistees')]
    private ?Infirmier $infirmier = null;

    #[ORM\ManyToOne(inversedBy: 'consultationsValidees')]
    private ?DirecteurMedical $validateur = null;

    #[ORM\OneToMany(mappedBy: 'consultation', targetEntity: SignesVitaux::class, cascade: ['persist', 'remove'])]
    private Collection $signesVitaux;

    #[ORM\OneToMany(mappedBy: 'consultation', targetEntity: DocumentMedical::class)]
    private Collection $documents;

    #[ORM\OneToMany(mappedBy: 'consultation', targetEntity: Prescription::class, cascade: ['persist'])]
    private Collection $prescriptions;

    #[ORM\OneToOne(mappedBy: 'consultation', targetEntity: Facture::class)]
    private ?Facture $facture = null;

    #[ORM\OneToOne(mappedBy: 'consultation', targetEntity: Evaluation::class)]
    private ?Evaluation $evaluation = null;

    public function __construct()
    {
        $this->signesVitaux  = new ArrayCollection();
        $this->documents     = new ArrayCollection();
        $this->prescriptions = new ArrayCollection();
        $this->date          = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $date): static { $this->date = $date; return $this; }

    public function getSymptomes(): ?string { return $this->symptomes; }
    public function setSymptomes(?string $s): static { $this->symptomes = $s; return $this; }

    public function getExamenClinique(): ?string { return $this->examenClinique; }
    public function setExamenClinique(?string $e): static { $this->examenClinique = $e; return $this; }

    public function getDiagnostic(): ?string { return $this->diagnostic; }
    public function setDiagnostic(?string $d): static { $this->diagnostic = $d; return $this; }

    public function getRecommandations(): ?string { return $this->recommandations; }
    public function setRecommandations(?string $r): static { $this->recommandations = $r; return $this; }

    public function getStatut(): StatutConsultationEnum { return $this->statut; }
    public function setStatut(StatutConsultationEnum $statut): static { $this->statut = $statut; return $this; }

    public function getRendezVous(): ?RendezVous { return $this->rendezVous; }
    public function setRendezVous(?RendezVous $rdv): static { $this->rendezVous = $rdv; return $this; }

    public function getDossierMedical(): ?DossierMedical { return $this->dossierMedical; }
    public function setDossierMedical(?DossierMedical $d): static { $this->dossierMedical = $d; return $this; }

    public function getMedecin(): ?Medecin { return $this->medecin; }
    public function setMedecin(?Medecin $m): static { $this->medecin = $m; return $this; }

    public function getInfirmier(): ?Infirmier { return $this->infirmier; }
    public function setInfirmier(?Infirmier $i): static { $this->infirmier = $i; return $this; }

    public function getValidateur(): ?DirecteurMedical { return $this->validateur; }
    public function setValidateur(?DirecteurMedical $v): static { $this->validateur = $v; return $this; }

    public function getSignesVitaux(): Collection { return $this->signesVitaux; }
    public function getDocuments(): Collection { return $this->documents; }
    public function getPrescriptions(): Collection { return $this->prescriptions; }

    public function getFacture(): ?Facture { return $this->facture; }
    public function setFacture(?Facture $f): static { $this->facture = $f; return $this; }

    public function getEvaluation(): ?Evaluation { return $this->evaluation; }
    public function setEvaluation(?Evaluation $e): static { $this->evaluation = $e; return $this; }
}

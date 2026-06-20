<?php

namespace App\Entity;

use App\Enum\StatutFactureEnum;
use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'facture', targetEntity: Consultation::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Consultation $consultation = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(inversedBy: 'facturesEmises')]
    private ?SecretaireMedicale $secretaire = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $numero = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $montant = null;

    #[ORM\Column(length: 20, enumType: StatutFactureEnum::class, options: ['default' => 'en_attente'])]
    private StatutFactureEnum $statutPaiement = StatutFactureEnum::EN_ATTENTE;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateEmission = null;

    #[ORM\OneToMany(mappedBy: 'facture', targetEntity: Paiement::class, cascade: ['persist'])]
    private Collection $paiements;

    public function __construct()
    {
        $this->paiements = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->dateEmission = new \DateTime();
        if (!$this->numero) {
            $this->numero = 'FAC-' . date('Ymd') . '-' . uniqid();
        }
    }

    public function getId(): ?int { return $this->id; }

    public function getConsultation(): ?Consultation { return $this->consultation; }
    public function setConsultation(?Consultation $c): static { $this->consultation = $c; return $this; }

    public function getPatient(): ?Patient { return $this->patient; }
    public function setPatient(?Patient $p): static { $this->patient = $p; return $this; }

    public function getSecretaire(): ?SecretaireMedicale { return $this->secretaire; }
    public function setSecretaire(?SecretaireMedicale $s): static { $this->secretaire = $s; return $this; }

    public function getNumero(): ?string { return $this->numero; }
    public function setNumero(string $numero): static { $this->numero = $numero; return $this; }

    public function getMontant(): ?string { return $this->montant; }
    public function setMontant(string $montant): static { $this->montant = $montant; return $this; }

    public function getStatutPaiement(): StatutFactureEnum { return $this->statutPaiement; }
    public function setStatutPaiement(StatutFactureEnum $s): static { $this->statutPaiement = $s; return $this; }

    public function getDateEmission(): ?\DateTimeInterface { return $this->dateEmission; }

    public function getPaiements(): Collection { return $this->paiements; }
}

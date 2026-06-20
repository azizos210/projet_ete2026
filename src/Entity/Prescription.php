<?php

namespace App\Entity;

use App\Enum\StatutPrescriptionEnum;
use App\Repository\PrescriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrescriptionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Prescription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'prescriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Consultation $consultation = null;

    #[ORM\ManyToOne(inversedBy: 'prescriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medecin $medecin = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateEmission = null;

    #[ORM\Column(length: 30, enumType: StatutPrescriptionEnum::class, options: ['default' => 'active'])]
    private StatutPrescriptionEnum $statut = StatutPrescriptionEnum::ACTIVE;

    #[ORM\Column(options: ['default' => false])]
    private bool $pdfGenere = false;

    #[ORM\OneToMany(mappedBy: 'prescription', targetEntity: LignePrescription::class, cascade: ['persist', 'remove'])]
    private Collection $lignes;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->dateEmission = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getConsultation(): ?Consultation { return $this->consultation; }
    public function setConsultation(?Consultation $c): static { $this->consultation = $c; return $this; }

    public function getMedecin(): ?Medecin { return $this->medecin; }
    public function setMedecin(?Medecin $m): static { $this->medecin = $m; return $this; }

    public function getDateEmission(): ?\DateTimeInterface { return $this->dateEmission; }

    public function getStatut(): StatutPrescriptionEnum { return $this->statut; }
    public function setStatut(StatutPrescriptionEnum $statut): static { $this->statut = $statut; return $this; }

    public function isPdfGenere(): bool { return $this->pdfGenere; }
    public function setPdfGenere(bool $pdf): static { $this->pdfGenere = $pdf; return $this; }

    public function getLignes(): Collection { return $this->lignes; }

    public function addLigne(LignePrescription $ligne): static
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setPrescription($this);
        }
        return $this;
    }

    public function removeLigne(LignePrescription $ligne): static
    {
        $this->lignes->removeElement($ligne);
        return $this;
    }
}

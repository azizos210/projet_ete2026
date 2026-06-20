<?php

namespace App\Entity;

use App\Enum\MethodePaiementEnum;
use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'paiements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Facture $facture = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $montant = null;

    #[ORM\Column(length: 20, enumType: MethodePaiementEnum::class)]
    private ?MethodePaiementEnum $methode = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateTransaction = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $reference = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (!$this->dateTransaction) {
            $this->dateTransaction = new \DateTime();
        }
    }

    public function getId(): ?int { return $this->id; }

    public function getFacture(): ?Facture { return $this->facture; }
    public function setFacture(?Facture $f): static { $this->facture = $f; return $this; }

    public function getMontant(): ?string { return $this->montant; }
    public function setMontant(string $montant): static { $this->montant = $montant; return $this; }

    public function getMethode(): ?MethodePaiementEnum { return $this->methode; }
    public function setMethode(MethodePaiementEnum $methode): static { $this->methode = $methode; return $this; }

    public function getDateTransaction(): ?\DateTimeInterface { return $this->dateTransaction; }
    public function setDateTransaction(\DateTimeInterface $d): static { $this->dateTransaction = $d; return $this; }

    public function getReference(): ?string { return $this->reference; }
    public function setReference(?string $ref): static { $this->reference = $ref; return $this; }
}

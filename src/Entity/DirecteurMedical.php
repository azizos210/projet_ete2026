<?php

namespace App\Entity;

use App\Repository\DirecteurMedicalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DirecteurMedicalRepository::class)]
class DirecteurMedical
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'directeurMedical', targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $specialiteSupervision = null;

    #[ORM\OneToMany(mappedBy: 'directeurMedical', targetEntity: ProtocoleMedical::class)]
    private Collection $protocoles;

    #[ORM\OneToMany(mappedBy: 'validateur', targetEntity: Consultation::class)]
    private Collection $consultationsValidees;

    public function __construct()
    {
        $this->protocoles            = new ArrayCollection();
        $this->consultationsValidees = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getSpecialiteSupervision(): ?string { return $this->specialiteSupervision; }
    public function setSpecialiteSupervision(?string $s): static { $this->specialiteSupervision = $s; return $this; }

    public function getProtocoles(): Collection { return $this->protocoles; }
    public function getConsultationsValidees(): Collection { return $this->consultationsValidees; }
}

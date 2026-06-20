<?php

namespace App\Entity;

use App\Enum\NiveauAccesEnum;
use App\Repository\AdministrateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]
class Administrateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'administrateur', targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 30, enumType: NiveauAccesEnum::class, options: ['default' => 'standard'])]
    private NiveauAccesEnum $niveauAcces = NiveauAccesEnum::STANDARD;

    public function getId(): ?int { return $this->id; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getNiveauAcces(): NiveauAccesEnum { return $this->niveauAcces; }
    public function setNiveauAcces(NiveauAccesEnum $niveauAcces): static { $this->niveauAcces = $niveauAcces; return $this; }
}

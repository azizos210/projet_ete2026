<?php

namespace App\Entity;

use App\Repository\InfirmierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InfirmierRepository::class)]
class Infirmier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'infirmier', targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 50)]
    private ?string $matricule = null;

    #[ORM\Column(length: 100)]
    private ?string $service = null;

    #[ORM\OneToMany(mappedBy: 'infirmier', targetEntity: SignesVitaux::class)]
    private Collection $signesVitaux;

    #[ORM\OneToMany(mappedBy: 'infirmier', targetEntity: AdministrationMedicament::class)]
    private Collection $administrations;

    #[ORM\OneToMany(mappedBy: 'infirmier', targetEntity: Consultation::class)]
    private Collection $consultationsAssistees;

    public function __construct()
    {
        $this->signesVitaux           = new ArrayCollection();
        $this->administrations        = new ArrayCollection();
        $this->consultationsAssistees = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getMatricule(): ?string { return $this->matricule; }
    public function setMatricule(string $matricule): static { $this->matricule = $matricule; return $this; }

    public function getService(): ?string { return $this->service; }
    public function setService(string $service): static { $this->service = $service; return $this; }

    public function getSignesVitaux(): Collection { return $this->signesVitaux; }
    public function getAdministrations(): Collection { return $this->administrations; }
    public function getConsultationsAssistees(): Collection { return $this->consultationsAssistees; }

    public function __toString(): string { return $this->getUtilisateur()?->getNomComplet() ?? 'Infirmier #' . $this->getId(); }
}

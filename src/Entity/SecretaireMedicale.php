<?php

namespace App\Entity;

use App\Repository\SecretaireMedicaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecretaireMedicaleRepository::class)]
class SecretaireMedicale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'secretaireMedicale', targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $posteAccueil = null;

    #[ORM\OneToMany(mappedBy: 'secretaire', targetEntity: RendezVous::class)]
    private Collection $rendezVousCrees;

    #[ORM\OneToMany(mappedBy: 'secretaire', targetEntity: Facture::class)]
    private Collection $facturesEmises;

    #[ORM\OneToMany(mappedBy: 'secretaire', targetEntity: TicketAssistance::class)]
    private Collection $ticketsTraites;

    public function __construct()
    {
        $this->rendezVousCrees = new ArrayCollection();
        $this->facturesEmises  = new ArrayCollection();
        $this->ticketsTraites  = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getPosteAccueil(): ?string { return $this->posteAccueil; }
    public function setPosteAccueil(?string $posteAccueil): static { $this->posteAccueil = $posteAccueil; return $this; }

    public function getRendezVousCrees(): Collection { return $this->rendezVousCrees; }
    public function getFacturesEmises(): Collection { return $this->facturesEmises; }
    public function getTicketsTraites(): Collection { return $this->ticketsTraites; }
}

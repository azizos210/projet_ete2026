<?php

namespace App\Entity;

use App\Enum\PrioriteTicketEnum;
use App\Enum\StatutTicketEnum;
use App\Repository\TicketAssistanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketAssistanceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class TicketAssistance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $demandeur = null;

    #[ORM\ManyToOne(inversedBy: 'ticketsTraites')]
    private ?SecretaireMedicale $secretaire = null;

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(length: 20, enumType: StatutTicketEnum::class, options: ['default' => 'ouvert'])]
    private StatutTicketEnum $statut = StatutTicketEnum::OUVERT;

    #[ORM\Column(length: 20, enumType: PrioriteTicketEnum::class, options: ['default' => 'normale'])]
    private PrioriteTicketEnum $priorite = PrioriteTicketEnum::NORMALE;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateResolution = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getDemandeur(): ?Patient { return $this->demandeur; }
    public function setDemandeur(?Patient $p): static { $this->demandeur = $p; return $this; }

    public function getSecretaire(): ?SecretaireMedicale { return $this->secretaire; }
    public function setSecretaire(?SecretaireMedicale $s): static { $this->secretaire = $s; return $this; }

    public function getSujet(): ?string { return $this->sujet; }
    public function setSujet(string $sujet): static { $this->sujet = $sujet; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $d): static { $this->description = $d; return $this; }

    public function getStatut(): StatutTicketEnum { return $this->statut; }
    public function setStatut(StatutTicketEnum $s): static { $this->statut = $s; return $this; }

    public function getPriorite(): PrioriteTicketEnum { return $this->priorite; }
    public function setPriorite(PrioriteTicketEnum $p): static { $this->priorite = $p; return $this; }

    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }

    public function getDateResolution(): ?\DateTimeInterface { return $this->dateResolution; }
    public function setDateResolution(?\DateTimeInterface $d): static { $this->dateResolution = $d; return $this; }
}

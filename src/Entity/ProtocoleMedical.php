<?php

namespace App\Entity;

use App\Enum\StatutProtocoleEnum;
use App\Repository\ProtocoleMedicalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProtocoleMedicalRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ProtocoleMedical
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'protocoles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DirecteurMedical $directeurMedical = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $version = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\Column(length: 20, enumType: StatutProtocoleEnum::class, options: ['default' => 'brouillon'])]
    private StatutProtocoleEnum $statut = StatutProtocoleEnum::BROUILLON;

    #[ORM\PrePersist]
    public function onPrePersist(): void { $this->dateCreation = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }

    public function getDirecteurMedical(): ?DirecteurMedical { return $this->directeurMedical; }
    public function setDirecteurMedical(?DirecteurMedical $d): static { $this->directeurMedical = $d; return $this; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): static { $this->titre = $titre; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): static { $this->description = $d; return $this; }

    public function getVersion(): ?string { return $this->version; }
    public function setVersion(?string $v): static { $this->version = $v; return $this; }

    public function getDateCreation(): ?\DateTimeImmutable { return $this->dateCreation; }

    public function getStatut(): StatutProtocoleEnum { return $this->statut; }
    public function setStatut(StatutProtocoleEnum $statut): static { $this->statut = $statut; return $this; }
}

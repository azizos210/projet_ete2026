<?php

namespace App\Entity;

use App\Repository\AuditLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuditLogRepository::class)]
#[ORM\Table(name: 'audit_log')]
#[ORM\Index(columns: ['date_action'], name: 'idx_audit_date')]
#[ORM\HasLifecycleCallbacks]
class AuditLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'auditLogs')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 100)]
    private ?string $action = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $entiteCible = null;

    #[ORM\Column(nullable: true)]
    private ?int $entiteId = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $adresseIp = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateAction = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $donneesAvant = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $donneesApres = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->dateAction = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $u): static { $this->utilisateur = $u; return $this; }

    public function getAction(): ?string { return $this->action; }
    public function setAction(string $action): static { $this->action = $action; return $this; }

    public function getEntiteCible(): ?string { return $this->entiteCible; }
    public function setEntiteCible(?string $e): static { $this->entiteCible = $e; return $this; }

    public function getEntiteId(): ?int { return $this->entiteId; }
    public function setEntiteId(?int $id): static { $this->entiteId = $id; return $this; }

    public function getAdresseIp(): ?string { return $this->adresseIp; }
    public function setAdresseIp(?string $ip): static { $this->adresseIp = $ip; return $this; }

    public function getDateAction(): ?\DateTimeInterface { return $this->dateAction; }

    public function getDonneesAvant(): ?array { return $this->donneesAvant; }
    public function setDonneesAvant(?array $d): static { $this->donneesAvant = $d; return $this; }

    public function getDonneesApres(): ?array { return $this->donneesApres; }
    public function setDonneesApres(?array $d): static { $this->donneesApres = $d; return $this; }
}

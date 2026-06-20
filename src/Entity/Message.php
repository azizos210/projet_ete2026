<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'messagesEnvoyes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $expediteur = null;

    #[ORM\ManyToOne(inversedBy: 'messagesRecus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $destinataire = null;

    #[ORM\Column(type: 'text')]
    private ?string $contenu = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateEnvoi = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $lu = false;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->dateEnvoi = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getExpediteur(): ?Utilisateur { return $this->expediteur; }
    public function setExpediteur(?Utilisateur $u): static { $this->expediteur = $u; return $this; }

    public function getDestinataire(): ?Utilisateur { return $this->destinataire; }
    public function setDestinataire(?Utilisateur $u): static { $this->destinataire = $u; return $this; }

    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(string $contenu): static { $this->contenu = $contenu; return $this; }

    public function getDateEnvoi(): ?\DateTimeInterface { return $this->dateEnvoi; }

    public function isLu(): bool { return $this->lu; }
    public function setLu(bool $lu): static { $this->lu = $lu; return $this; }
}

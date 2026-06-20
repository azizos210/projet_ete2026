<?php

namespace App\Entity;

use App\Enum\JourSemaineEnum;
use App\Repository\DisponibiliteMedecinRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DisponibiliteMedecinRepository::class)]
class DisponibiliteMedecin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'disponibilites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medecin $medecin = null;

    #[ORM\Column(length: 15, enumType: JourSemaineEnum::class)]
    private ?JourSemaineEnum $jourSemaine = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $heureDebut = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $heureFin = null;

    #[ORM\Column(options: ['default' => true])]
    private bool $recurrent = true;

    public function getId(): ?int { return $this->id; }

    public function getMedecin(): ?Medecin { return $this->medecin; }
    public function setMedecin(?Medecin $medecin): static { $this->medecin = $medecin; return $this; }

    public function getJourSemaine(): ?JourSemaineEnum { return $this->jourSemaine; }
    public function setJourSemaine(JourSemaineEnum $jour): static { $this->jourSemaine = $jour; return $this; }

    public function getHeureDebut(): ?\DateTimeInterface { return $this->heureDebut; }
    public function setHeureDebut(\DateTimeInterface $h): static { $this->heureDebut = $h; return $this; }

    public function getHeureFin(): ?\DateTimeInterface { return $this->heureFin; }
    public function setHeureFin(\DateTimeInterface $h): static { $this->heureFin = $h; return $this; }

    public function isRecurrent(): bool { return $this->recurrent; }
    public function setRecurrent(bool $recurrent): static { $this->recurrent = $recurrent; return $this; }
}

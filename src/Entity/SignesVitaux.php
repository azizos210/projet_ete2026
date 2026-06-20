<?php

namespace App\Entity;

use App\Repository\SignesVitauxRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SignesVitauxRepository::class)]
#[ORM\Table(name: 'signes_vitaux')]
#[ORM\HasLifecycleCallbacks]
class SignesVitaux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'signesVitaux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Consultation $consultation = null;

    #[ORM\ManyToOne(inversedBy: 'signesVitaux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Infirmier $infirmier = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $tensionArterielle = null;

    #[ORM\Column(nullable: true)]
    private ?int $frequenceCardiaque = null;

    #[ORM\Column(type: 'decimal', precision: 4, scale: 1, nullable: true)]
    private ?string $temperature = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $saturationOxygene = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateMesure = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $alerteDeclenchee = false;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (!$this->dateMesure) {
            $this->dateMesure = new \DateTime();
        }
    }

    public function getId(): ?int { return $this->id; }

    public function getConsultation(): ?Consultation { return $this->consultation; }
    public function setConsultation(?Consultation $c): static { $this->consultation = $c; return $this; }

    public function getInfirmier(): ?Infirmier { return $this->infirmier; }
    public function setInfirmier(?Infirmier $i): static { $this->infirmier = $i; return $this; }

    public function getTensionArterielle(): ?string { return $this->tensionArterielle; }
    public function setTensionArterielle(?string $t): static { $this->tensionArterielle = $t; return $this; }

    public function getFrequenceCardiaque(): ?int { return $this->frequenceCardiaque; }
    public function setFrequenceCardiaque(?int $f): static { $this->frequenceCardiaque = $f; return $this; }

    public function getTemperature(): ?string { return $this->temperature; }
    public function setTemperature(?string $t): static { $this->temperature = $t; return $this; }

    public function getSaturationOxygene(): ?string { return $this->saturationOxygene; }
    public function setSaturationOxygene(?string $s): static { $this->saturationOxygene = $s; return $this; }

    public function getDateMesure(): ?\DateTimeInterface { return $this->dateMesure; }
    public function setDateMesure(\DateTimeInterface $d): static { $this->dateMesure = $d; return $this; }

    public function isAlerteDeclenchee(): bool { return $this->alerteDeclenchee; }
    public function setAlerteDeclenchee(bool $a): static { $this->alerteDeclenchee = $a; return $this; }
}

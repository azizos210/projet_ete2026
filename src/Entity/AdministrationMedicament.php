<?php

namespace App\Entity;

use App\Repository\AdministrationMedicamentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdministrationMedicamentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AdministrationMedicament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'administrations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LignePrescription $lignePrescription = null;

    #[ORM\ManyToOne(inversedBy: 'administrations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Infirmier $infirmier = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateHeure = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $doseAdministree = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $observations = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $contreIndicationSignalee = false;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (!$this->dateHeure) {
            $this->dateHeure = new \DateTime();
        }
    }

    public function getId(): ?int { return $this->id; }

    public function getLignePrescription(): ?LignePrescription { return $this->lignePrescription; }
    public function setLignePrescription(?LignePrescription $l): static { $this->lignePrescription = $l; return $this; }

    public function getInfirmier(): ?Infirmier { return $this->infirmier; }
    public function setInfirmier(?Infirmier $i): static { $this->infirmier = $i; return $this; }

    public function getDateHeure(): ?\DateTimeInterface { return $this->dateHeure; }
    public function setDateHeure(\DateTimeInterface $d): static { $this->dateHeure = $d; return $this; }

    public function getDoseAdministree(): ?string { return $this->doseAdministree; }
    public function setDoseAdministree(?string $d): static { $this->doseAdministree = $d; return $this; }

    public function getObservations(): ?string { return $this->observations; }
    public function setObservations(?string $o): static { $this->observations = $o; return $this; }

    public function isContreIndicationSignalee(): bool { return $this->contreIndicationSignalee; }
    public function setContreIndicationSignalee(bool $c): static { $this->contreIndicationSignalee = $c; return $this; }
}

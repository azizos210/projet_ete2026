<?php

namespace App\Entity;

use App\Enum\StatutAvisEnum;
use App\Repository\AvisSpecialiseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisSpecialiseRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AvisSpecialise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'avisDemandesEnvoyes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medecin $medecinDemandeur = null;

    #[ORM\ManyToOne(inversedBy: 'avisDemandesRecus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medecin $medecinSpecialiste = null;

    #[ORM\ManyToOne(inversedBy: 'avisSpecialises')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DossierMedical $dossierMedical = null;

    #[ORM\Column(type: 'text')]
    private ?string $question = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reponse = null;

    #[ORM\Column(length: 20, enumType: StatutAvisEnum::class, options: ['default' => 'en_attente'])]
    private StatutAvisEnum $statut = StatutAvisEnum::EN_ATTENTE;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateDemande = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateReponse = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void { $this->dateDemande = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }

    public function getMedecinDemandeur(): ?Medecin { return $this->medecinDemandeur; }
    public function setMedecinDemandeur(?Medecin $m): static { $this->medecinDemandeur = $m; return $this; }

    public function getMedecinSpecialiste(): ?Medecin { return $this->medecinSpecialiste; }
    public function setMedecinSpecialiste(?Medecin $m): static { $this->medecinSpecialiste = $m; return $this; }

    public function getDossierMedical(): ?DossierMedical { return $this->dossierMedical; }
    public function setDossierMedical(?DossierMedical $d): static { $this->dossierMedical = $d; return $this; }

    public function getQuestion(): ?string { return $this->question; }
    public function setQuestion(string $q): static { $this->question = $q; return $this; }

    public function getReponse(): ?string { return $this->reponse; }
    public function setReponse(?string $r): static { $this->reponse = $r; return $this; }

    public function getStatut(): StatutAvisEnum { return $this->statut; }
    public function setStatut(StatutAvisEnum $s): static { $this->statut = $s; return $this; }

    public function getDateDemande(): ?\DateTimeImmutable { return $this->dateDemande; }

    public function getDateReponse(): ?\DateTimeImmutable { return $this->dateReponse; }
    public function setDateReponse(?\DateTimeImmutable $d): static { $this->dateReponse = $d; return $this; }
}

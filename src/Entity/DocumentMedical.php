<?php

namespace App\Entity;

use App\Enum\TypeDocumentEnum;
use App\Repository\DocumentMedicalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentMedicalRepository::class)]
#[ORM\HasLifecycleCallbacks]
class DocumentMedical
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DossierMedical $dossierMedical = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?Consultation $consultation = null;

    #[ORM\Column(length: 30, enumType: TypeDocumentEnum::class)]
    private ?TypeDocumentEnum $type = null;

    #[ORM\Column(length: 500)]
    private ?string $cheminFichier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateUpload = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->dateUpload = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getDossierMedical(): ?DossierMedical { return $this->dossierMedical; }
    public function setDossierMedical(?DossierMedical $d): static { $this->dossierMedical = $d; return $this; }

    public function getConsultation(): ?Consultation { return $this->consultation; }
    public function setConsultation(?Consultation $c): static { $this->consultation = $c; return $this; }

    public function getType(): ?TypeDocumentEnum { return $this->type; }
    public function setType(TypeDocumentEnum $type): static { $this->type = $type; return $this; }

    public function getCheminFichier(): ?string { return $this->cheminFichier; }
    public function setCheminFichier(string $chemin): static { $this->cheminFichier = $chemin; return $this; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(?string $titre): static { $this->titre = $titre; return $this; }

    public function getDateUpload(): ?\DateTimeInterface { return $this->dateUpload; }
}

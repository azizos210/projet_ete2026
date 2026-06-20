<?php

namespace App\Entity;

use App\Repository\RessourceEducativeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RessourceEducativeRepository::class)]
class RessourceEducative
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text')]
    private ?string $contenu = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $categorie = null;

    #[ORM\ManyToMany(targetEntity: Patient::class, inversedBy: 'ressourcesEducatives')]
    #[ORM\JoinTable(name: 'ressource_educative_patient')]
    private Collection $patients;

    public function __construct()
    {
        $this->patients = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): static { $this->titre = $titre; return $this; }

    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(string $contenu): static { $this->contenu = $contenu; return $this; }

    public function getCategorie(): ?string { return $this->categorie; }
    public function setCategorie(?string $c): static { $this->categorie = $c; return $this; }

    public function getPatients(): Collection { return $this->patients; }

    public function addPatient(Patient $patient): static
    {
        if (!$this->patients->contains($patient)) {
            $this->patients->add($patient);
        }
        return $this;
    }

    public function removePatient(Patient $patient): static
    {
        $this->patients->removeElement($patient);
        return $this;
    }
}

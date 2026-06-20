<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateur')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(options: ['default' => true])]
    private bool $actif = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $derniereConnexion = null;

    // ===== Relations OneToOne vers profils =====

    #[ORM\OneToOne(mappedBy: 'utilisateur', targetEntity: Administrateur::class, cascade: ['persist'])]
    private ?Administrateur $administrateur = null;

    #[ORM\OneToOne(mappedBy: 'utilisateur', targetEntity: DirecteurMedical::class, cascade: ['persist'])]
    private ?DirecteurMedical $directeurMedical = null;

    #[ORM\OneToOne(mappedBy: 'utilisateur', targetEntity: Medecin::class, cascade: ['persist'])]
    private ?Medecin $medecin = null;

    #[ORM\OneToOne(mappedBy: 'utilisateur', targetEntity: Infirmier::class, cascade: ['persist'])]
    private ?Infirmier $infirmier = null;

    #[ORM\OneToOne(mappedBy: 'utilisateur', targetEntity: SecretaireMedicale::class, cascade: ['persist'])]
    private ?SecretaireMedicale $secretaireMedicale = null;

    #[ORM\OneToOne(mappedBy: 'utilisateur', targetEntity: Patient::class, cascade: ['persist'])]
    private ?Patient $patient = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: AuditLog::class)]
    private Collection $auditLogs;

    #[ORM\OneToMany(mappedBy: 'destinataire', targetEntity: Notification::class)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'expediteur', targetEntity: Message::class)]
    private Collection $messagesEnvoyes;

    #[ORM\OneToMany(mappedBy: 'destinataire', targetEntity: Message::class)]
    private Collection $messagesRecus;

    public function __construct()
    {
        $this->auditLogs       = new ArrayCollection();
        $this->notifications   = new ArrayCollection();
        $this->messagesEnvoyes = new ArrayCollection();
        $this->messagesRecus   = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->dateCreation = new \DateTimeImmutable();
    }

    // ===== UserInterface =====

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function eraseCredentials(): void {}

    // ===== Getters / Setters =====

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): static { $this->prenom = $prenom; return $this; }

    public function getNomComplet(): string { return $this->prenom . ' ' . $this->nom; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): static { $this->telephone = $telephone; return $this; }

    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    public function getDateCreation(): ?\DateTimeImmutable { return $this->dateCreation; }

    public function getDerniereConnexion(): ?\DateTimeImmutable { return $this->derniereConnexion; }
    public function setDerniereConnexion(?\DateTimeImmutable $derniereConnexion): static { $this->derniereConnexion = $derniereConnexion; return $this; }

    public function getAdministrateur(): ?Administrateur { return $this->administrateur; }
    public function setAdministrateur(?Administrateur $administrateur): static { $this->administrateur = $administrateur; return $this; }

    public function getDirecteurMedical(): ?DirecteurMedical { return $this->directeurMedical; }
    public function setDirecteurMedical(?DirecteurMedical $directeurMedical): static { $this->directeurMedical = $directeurMedical; return $this; }

    public function getMedecin(): ?Medecin { return $this->medecin; }
    public function setMedecin(?Medecin $medecin): static { $this->medecin = $medecin; return $this; }

    public function getInfirmier(): ?Infirmier { return $this->infirmier; }
    public function setInfirmier(?Infirmier $infirmier): static { $this->infirmier = $infirmier; return $this; }

    public function getSecretaireMedicale(): ?SecretaireMedicale { return $this->secretaireMedicale; }
    public function setSecretaireMedicale(?SecretaireMedicale $secretaireMedicale): static { $this->secretaireMedicale = $secretaireMedicale; return $this; }

    public function getPatient(): ?Patient { return $this->patient; }
    public function setPatient(?Patient $patient): static { $this->patient = $patient; return $this; }

    public function getAuditLogs(): Collection { return $this->auditLogs; }
    public function getNotifications(): Collection { return $this->notifications; }
    public function getMessagesEnvoyes(): Collection { return $this->messagesEnvoyes; }
    public function getMessagesRecus(): Collection { return $this->messagesRecus; }

    public function __toString(): string { return $this->getNomComplet(); }
}

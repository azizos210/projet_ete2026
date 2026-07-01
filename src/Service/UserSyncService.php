<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Utilisateur;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserSyncService
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    public function syncUtilisateurFromUser(User $user): Utilisateur
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail($user->getEmail());
        $utilisateur->setNom($user->getLastName());
        $utilisateur->setPrenom($user->getFirstName());
        $utilisateur->setRoles($user->getRoles());

        return $utilisateur;
    }

    public function syncUserFromUtilisateur(Utilisateur $utilisateur): User
    {
        $user = new User();
        $user->setEmail($utilisateur->getEmail());
        $user->setFirstName($utilisateur->getPrenom());
        $user->setLastName($utilisateur->getNom());
        $user->setRoles($utilisateur->getRoles());

        return $user;
    }

    public function hashAndSetPassword(User $user, Utilisateur $utilisateur, string $plainPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
        $utilisateur->setPassword($hashedPassword);
    }
}

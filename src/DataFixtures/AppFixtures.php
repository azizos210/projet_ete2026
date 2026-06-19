<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // ===== Utilisateur admin =====
        $admin = new User();
        $admin->setEmail('admin@exemple.fr')
              ->setFirstName('Admin')
              ->setLastName('App')
              ->setRoles(['ROLE_ADMIN'])
              ->setPassword($this->hasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        // ===== Utilisateur standard =====
        $user = new User();
        $user->setEmail('user@exemple.fr')
             ->setFirstName('Jean')
             ->setLastName('Dupont')
             ->setPassword($this->hasher->hashPassword($user, 'password'));
        $manager->persist($user);

        // ===== Ajoutez vos fixtures ici =====

        $manager->flush();
    }
}

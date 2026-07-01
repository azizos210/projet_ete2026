<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\UserSyncService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserSyncService $userSyncService,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@exemple.fr')
              ->setFirstName('Admin')
              ->setLastName('App')
              ->setRoles(['ROLE_ADMIN']);
        $adminUtilisateur = $this->userSyncService->syncUtilisateurFromUser($admin);
        $this->userSyncService->hashAndSetPassword($admin, $adminUtilisateur, 'password');
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@exemple.fr')
             ->setFirstName('Jean')
             ->setLastName('Dupont');
        $userUtilisateur = $this->userSyncService->syncUtilisateurFromUser($user);
        $this->userSyncService->hashAndSetPassword($user, $userUtilisateur, 'password');
        $manager->persist($user);

        $manager->flush();
    }
}

<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin', description: 'Crée un utilisateur admin')]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Email', 'admin@hopital.fr')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Mot de passe', 'admin123')
            ->addOption('nom', null, InputOption::VALUE_REQUIRED, 'Nom', 'Admin')
            ->addOption('prenom', null, InputOption::VALUE_REQUIRED, 'Prénom', 'Super');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getOption('email');
        $password = $input->getOption('password');

        $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existing) {
            $output->writeln("<comment>L'utilisateur $email existe déjà.</comment>");
            return Command::SUCCESS;
        }

        $user = (new User())
            ->setEmail($email)
            ->setFirstName($input->getOption('prenom'))
            ->setLastName($input->getOption('nom'))
            ->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln("<info>Admin créé : $email / $password</info>");
        return Command::SUCCESS;
    }
}

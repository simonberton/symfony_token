<?php

namespace App\Command;

use App\Entity\ApiUser;
use App\Repository\ApiUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'create-user',
    description: 'Create test user for API',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ApiUserRepository $apiUserRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        // Optionally, add arguments here to customize user creation
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $existingUser = $this->apiUserRepository->findOneBy(['name' => 'api']);
        if (!$existingUser) {
            $user = new ApiUser();
            $user->setName('api');
            $user->setRoles(['ROLE_API']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'test'));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $io->success('User "api" created successfully.');
        } else {
            $io->success('User "api" already exists.');
        }

        return Command::SUCCESS;
    }
}

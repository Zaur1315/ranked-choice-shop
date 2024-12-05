<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Console\Exception\RuntimeException;

#[AsCommand(
    name: 'app:add-user',
    description: 'Add a short description for your command',
)]
class AddUserCommand extends Command
{

    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $hasherInterface, private readonly UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', 'em', InputArgument::REQUIRED, 'Email')
            ->addOption('password','p', InputArgument::REQUIRED, 'Password')
            ->addOption('isAdmin', 'a', InputOption::VALUE_REQUIRED, 'If set the user is created as Admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $stopwatch = new Stopwatch();
        $stopwatch->start('add-user-command');
        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $isAdmin = $input->getOption('isAdmin');

        $io->title('Add User Command Wizard');
        $io->text(['Please, enter some information']);

        if (!$email){
            $email = $io->ask('Email');
        }

        if (!$password){
            $password = $io->askHidden('Password (your type will be hidden');
        }

        if (!$isAdmin){
            $question = new Question('Is admin? (1 or 0)');
            $isAdmin = $io->askQuestion($question);
        }

        $isAdmin = boolval($isAdmin);

        try{
            $user = $this->createUser($email, $password, $isAdmin);
        }catch (RuntimeException $e){
            $io->comment($e->getMessage());
            return Command::FAILURE;
        }


        $successMSG = sprintf('%s was seccessfully created: %s', $isAdmin ? 'Administrator' : 'User', $email);

        $io->success($successMSG);

        $event = $stopwatch->stop('add-user-command');
        $stopwatchMSG = sprintf('New user\'s id: %s / Elapsed timw: %.2f ms / Consumed memory: %.2f MB',
            $user->getId(),
            $event->getDuration(),
            $event->getMemory() /1024/1024
        );
        $io->comment($stopwatchMSG);

        return Command::SUCCESS;
    }
    private function createUser(string $email, string $password, bool $isAdmin): User
    {
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);

        if($existingUser) {
            throw new RuntimeException("User already exist");
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER']);

        $encodedPassowrd = $this->hasherInterface->hashPassword($user, $password);
        $user->setPassword($encodedPassowrd);

        $user->setVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}

<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';
    private $entityManager;
    private $encoder;
    public function __construct(EntityManagerInterface $entityManager , UserPasswordEncoderInterface
    $encoder)
    {
        $this->entityManager = $entityManager ;
        $this->encoder = $encoder;
        parent::__construct();
    }
    protected function configure()
    {
        $this
            ->setDescription('Create Admin account')
            ->addArgument( 'email', InputArgument:: REQUIRED, 'email')
            ->addArgument( 'password', InputArgument:: REQUIRED, 'password
description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument( 'email');
        $password = $input->getArgument( 'password');
        $io->note(sprintf('Create a User for email: %s', $email));
        $user = new User();
        $user->setFirstname('Admin');
        $user->setLastname('Admin');
        $user->setEmail($email);
        $passwordEncoded = $this->encoder->encodePassword( $user, $password);
        $user->setPassword( $passwordEncoded );
        $user->setRoles(['ROLE_ADMIN']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $io->success(sprintf('You have created a User with email: %s', $email));
    }
}

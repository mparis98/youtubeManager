<?php

namespace App\Command;

use App\Entity\Video;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
class CountVideosCommand extends Command
{
    protected static $defaultName = 'app:count-videos';
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
            ->setDescription('Count videos of a user')
            ->addArgument( 'email', InputArgument:: REQUIRED, 'email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument( 'email');
        $io->note(sprintf('Count videos of a User for email: %s', $email));
        $user = $this->entityManager->getRepository(User::class)->findOneBy(array('email'=>$email));
        if ($user){
        $videos = $this->entityManager->getRepository(Video::class)->findBy(array('user'=>$user));
        $io->success(sprintf('The user created '.count($videos).' videos'));
        }
        else{
            $io->error(sprintf('The user does not exist !'));
        }
    }
}

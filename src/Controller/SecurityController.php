<?php

namespace App\Controller;

use App\Event\UserRegisteredEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Form\RegisterUserType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\LoginUserType;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface
    $passwordEncoder, EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $logger->info('User registered now !');
            $this->addFlash('success', 'Registered !');
            $event = new UserRegisteredEvent($user);
            $eventDispatcher->dispatch(UserRegisteredEvent::NAME, $event);
            return $this->redirectToRoute('home');
        }
        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, LoggerInterface $logger)
    {
        $user = new User();
        $form = $this->createForm(LoginUserType::class, $user);
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/login.html.twig', [
            'error' => $error ? $error->getMessage() : null,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(AuthenticationUtils $authenticationUtils, LoggerInterface $logger)
    {
        if($this->getUser()) {
            $this->get('security.token_storage')->setToken(null);
            $this->get('session')->invalidate();
        }
        $this->addFlash('success', 'User disconnected!');
        $this->redirectToRoute('home');
    }
}

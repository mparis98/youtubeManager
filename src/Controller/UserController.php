<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ProfileUserType;
use App\Form\VideoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_USER')")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(Request $request, UserRepository $userRepository)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
// $this->redirectToRoute(‘register_sucess’);
        }

        $users = $userRepository ->findAll();
        return $this->render('user/index.html.twig', array(
            'form' => $form->createView(),
            'users'=>$users
        ));
    }

    /**
     * @Route("/user/profile-{byId}", name="user_profile")
     * @ParamConverter("user", options={"mapping"={"byId"="id"}})
     */
    public function firstname(Request $request, UserRepository $userRepository, User $user){

        return $this->render('user/profile.html.twig', array('user'=>$user));
    }

    /**
     * @Route("/user/profile", name="profile")
     */
    public function profile(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('security/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/remove/{id}", name="user_remove")
     */
    public function remove(User $user, EntityManagerInterface $entityManager)
    {
        $articles = $user->getArticles();
        foreach ($articles as $article){
            $article->setUser(null);
        }
        $entityManager->remove($user);
        $entityManager ->flush();
        $this->addFlash('success', 'User supprimé!');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/user/list", name="user_list")
     */
    public function list(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $videos = $this->getDoctrine()->getRepository(Video::class)->findBy(array('user'=>$user));
        return $this->render('user/list.html.twig', [
            'videos' => $videos,
        ]);
    }

    /**
     * @Route("/user/video/profile-{byId}", name="user_video_profile_update")
     * @ParamConverter("video", options={"mapping"={"byId"="id"}})
     */
    public function updateVideo(Video $video, Request $request, EntityManagerInterface $entityManager){
        if ($video->getUser() === $this->getUser()){
            $user=$this->getUser();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $url = $user->getYoutubeUrl();
            preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
            $embed = $matches[1];
            $user->setUrlEmbed('https://www.youtube.com/embed/'.$embed);
            $entityManager->persist($video);
            $entityManager->flush();
            $this->redirectToRoute('admin_users');
        }

        return $this->render('video/video.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    else{
        $this->redirectToRoute('home');

    }
    }
}

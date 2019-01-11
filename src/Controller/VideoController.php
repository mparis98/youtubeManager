<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    /**
     * @Route("/video", name="video")
     */
    public function index(Request $request)
    {
        $user = new Video();
        $form = $this->createForm(VideoType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
// $this->redirectToRoute(‘register_sucess’);
        }
        $articles= $this->getDoctrine()->getRepository(Video::class)->findByDate();

        return $this->render('article/index.html.twig', [
            'form' => $form->createView(),
            'articles'=>$articles,
        ]);
    }

    /**
     * @Route("/user/profile-{byName}", name="video_profile")
     * @ParamConverter("article", options={"mapping"={"byName"="name"}})
     */
    public function name(Video $article){

        return $this->render('article/article.html.twig', array('article'=>$article));
    }

    /**
     * @Route("/video/remove/{id}", name="video_remove")
     * @ParamConverter("article", options={"mapping"={"id"="id"}})
     */
    public function remove(Video $article, EntityManagerInterface
    $entityManager )
    {
        $entityManager ->remove($article);
        $entityManager ->flush();
        $this->addFlash('success', 'Video supprimé!');
        return $this->redirectToRoute( 'home');
    }
}

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
            $url = $user->getYoutubeUrl();
            preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
            $embed = $matches[1];
            $user->setUrlEmbed('https://www.youtube.com/embed/'.$embed);
            $entityManager->persist($user);
            $entityManager->flush();
// $this->redirectToRoute(‘register_sucess’);
        }
        $videos= $this->getDoctrine()->getRepository(Video::class)->findByDate();

        return $this->render('video/index.html.twig', [
            'form' => $form->createView(),
            'video'=>$videos,
        ]);
    }

    /**
     * @Route("/video/remove/{id}", name="video_remove")
     * @ParamConverter("video", options={"mapping"={"id"="id"}})
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

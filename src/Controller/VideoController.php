<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoAdminType;
use App\Form\VideoType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Event\VideoEvent;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("has_role('ROLE_USER') or has_role('ROLE_ADMIN')")
 */
class VideoController extends AbstractController
{
    /**
     * @Route("/video", name="video")
     */
    public function index(Request $request, LoggerInterface $logger, EventDispatcherInterface $eventDispatcher)
    {
        $user = new Video();
        if (in_array('ROLE_ADMIN',$this->getUser()->getRoles())){
            $form = $this->createForm(VideoAdminType::class, $user);
        }
        else{
        $form = $this->createForm(VideoType::class, $user);
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $url = $user->getYoutubeUrl();
            if (filter_var($url, FILTER_VALIDATE_URL)) {

            preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
            $embed = $matches[1];
            $user->setUrlEmbed('https://www.youtube.com/embed/'.$embed);
            if (in_array('ROLE_USER',$this->getUser()->getRoles())){
            $user->setUser($this->getUser());
            }
            $entityManager->persist($user);
            $entityManager->flush();
                $this->addFlash('success', 'Video created !');

                $logger->info('Video created ! User email :'.$this->getUser()->getEmail().', title :'.$user->getTitle().', id :'.$user->getId());
            $event = new VideoEvent($user);
            $eventDispatcher->dispatch(VideoEvent::CREATED, $event);
            $this->redirectToRoute('home');
            }
            else{
                $this->addFlash('error', 'Wrong URL !');

            }
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
    $entityManager, LoggerInterface $logger, EventDispatcherInterface $eventDispatcher )
    {
        $entityManager ->remove($article);
        $entityManager ->flush();
        $logger->info('Video removed ! User email :'.$this->getUser()->getEmail().', title :'.$article->getTitle().', id :'.$article->getId());
        $event = new VideoEvent($article);
        $eventDispatcher->dispatch(VideoEvent::REMOVED, $event);
        $this->addFlash('success', 'Video removed!');
        return $this->redirectToRoute( 'home');
    }
}

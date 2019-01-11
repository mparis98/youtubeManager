<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Video;
use App\Form\VideoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {

        $videos= $this->getDoctrine()->getRepository(Video::class)->findByDate();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('home/index.html.twig', [
            'videos'=>$videos,
            'categories'=>$categories,
            'countCat'=>count($categories),
        ]);
    }

    /**
     * @Route("/video/profile-{byId}", name="video_profile")
     * @ParamConverter("video", options={"mapping"={"byId"="id"}})
     */
    public function name(Video $video){

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('video/profile.html.twig', array('video'=>$video, 'categories'=>$categories));
    }
}

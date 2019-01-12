<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Video;
use App\Form\VideoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\User;

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

    /**
     * @Route("/category/profile-{byId}", name="category_profile")
     * @ParamConverter("category", options={"mapping"={"byId"="id"}})
     */
    public function category(Category $category){

      //  $categories = $this->getDoctrine()->getRepository(Video::class)->findBy(array('category'=>$category));
        return $this->render('category/index.html.twig', array('categories'=>$category->getVideos(),'category'=>$category));
    }

    /**
     * @Route("/user/profile-{byId}", name="home_user_profile")
     * @ParamConverter("user", options={"mapping"={"byId"="id"}})
     */
    public function user(Request $request, User $user){

        $videos = $this->getDoctrine()->getRepository(Video::class)->findBy(array('user'=>$user));

        return $this->render('user/profile.html.twig', array('user'=>$user, 'videos'=>$videos));
    }
}

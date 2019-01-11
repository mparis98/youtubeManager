<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Video;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\VideoType;
use App\Form\ProfileUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/category/create", name="create_category")
     */
    public function createCategory(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
// $this->redirectToRoute(‘register_sucess’);
        }
        return $this->render('admin/create_category.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user", name="admin_users")
     */
    public function user()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $videos = $this->getDoctrine()->getRepository(Video::class)->findAll();
        return $this->render('admin/user.html.twig', [
            'users'=>$users,
            'videos'=>$videos,
        ]);
    }

    /**
     * @Route("/admin/video/profile-{byId}", name="video_profile_update")
     * @ParamConverter("video", options={"mapping"={"byId"="id"}})
     */
    public function updateVideo(Video $video, Request $request, EntityManagerInterface $entityManager){
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($video);
            $entityManager->flush();
            $this->redirectToRoute('admin_users');
        }

        return $this->render('video/video.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/profile-{byId}", name="user_profile_update")
     * @ParamConverter("user", options={"mapping"={"byId"="id"}})
     */
    public function updateUser(Request $request, User $user, EntityManagerInterface $entityManager){

        $form = $this->createForm(ProfileUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('admin_users');
        }
        return $this->render('security/profile.html.twig', [
            'form' => $form->createView()
        ]);    }

    /**
     * @Route("/admin/category/{byId}", name="category_profile_update")
     * @ParamConverter("category", options={"mapping"={"byId"="id"}})
     */
    public function updateCategory(Category $category, Request $request, EntityManagerInterface $entityManager){
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            $this->redirectToRoute('admin_category');
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("admin/video/remove/{id}", name="admin_video_remove")
     * @ParamConverter("video", options={"mapping"={"id"="id"}})
     */
    public function removeVideo
    (Video $article, EntityManagerInterface
    $entityManager )
    {
        $entityManager ->remove($article);
        $entityManager ->flush();
        $this->addFlash('success', 'Video supprimé!');
        return $this->redirectToRoute( 'home');
    }

    /**
     * @Route("admin/user/remove/{id}", name="admin_user_remove")
     * @ParamConverter("user", options={"mapping"={"id"="id"}})

     */
    public function removeUser(User $user, EntityManagerInterface $entityManager)
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
     * @Route("admin/category/remove/{id}", name="admin_category_remove")
     * @ParamConverter("category", options={"mapping"={"id"="id"}})
     */
    public function removeCategory(Category $category, EntityManagerInterface $entityManager)
    {

        $videos = $category->getVideos();

        $entityManager->remove($category);
        $entityManager ->flush();
        $this->addFlash('success', 'Catégorie supprimé!');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/admin/category", name="admin_category")
     */
    public function category()
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('admin/category.html.twig', [
            'category'=>$category,
        ]);
    }

    /**
     * @Route("/admin/videos", name="admin_videos")
     */
    public function videos()
    {
        $videos = $this->getDoctrine()->getRepository(Video::class)->findAll();
        return $this->render('admin/video.html.twig', [
            'videos'=>$videos,
        ]);
    }
}

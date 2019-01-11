<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function index(Request $request)
    {
        $user = new Article();
        $form = $this->createForm(ArticleType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
// $this->redirectToRoute(‘register_sucess’);
        }
        $articles= $this->getDoctrine()->getRepository(Article::class)->findByDate();

        return $this->render('article/index.html.twig', [
            'form' => $form->createView(),
            'articles'=>$articles,
        ]);
    }

    /**
     * @Route("/user/profile-{byName}", name="article_profile")
     * @ParamConverter("article", options={"mapping"={"byName"="name"}})
     */
    public function name(Article $article){

        return $this->render('article/article.html.twig', array('article'=>$article));
    }

    /**
     * @Route("/article/remove/{id}", name="article_remove")
     * @ParamConverter("article", options={"mapping"={"id"="id"}})
     */
    public function remove(Article $article, EntityManagerInterface
    $entityManager )
    {
        $entityManager ->remove($article);
        $entityManager ->flush();
        $this->addFlash('success', 'Article supprimé!');
        return $this->redirectToRoute( 'home');
    }
}

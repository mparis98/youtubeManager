<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function index()
    {
        $articles= $this->getDoctrine()->getRepository(Article::class)->findAll();

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles'=>$articles,
        ]);
    }
}

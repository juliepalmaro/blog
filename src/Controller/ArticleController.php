<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }
    /**
     * @Route("/article/home", name="Accueil")
     */
    public function home(): Response
    {
        return $this->render('article/home.html.twig', []);
    }
    /**
     * @Route("/article/articles", name="Accueil")
     */
    public function articles(): Response
    {
        return $this->render('article/articles.html.twig', []);
    }

    /**
     * @Route("/article/articleComments", name="Accueil")
     */
    public function articleComments(): Response
    {
        return $this->render('article/articleComments.html.twig', []);
    }


    public function getAllArticles(ArticleRepository $articleRepository)
    {
        //   $articles = $articleRepository->findAllArticle();
    }
}

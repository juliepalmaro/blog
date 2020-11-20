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
     * @Route("/article/home", name="home")
     */
    public function home(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllArticles(0, 10, null, null, null, null, null);
        dump($articles);

        return $this->render('article/home.html.twig', []);
    }
    /**
     * @Route("/article/articles", name="articles")
     */
    public function articles(): Response
    {
        return $this->render('article/articles.html.twig', []);
    }

    /**
     * @Route("/article/articleComments", name="Comments")
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

<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/home", name="admin_home")
     */
    public function home(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllArticles(0, 10, null, null, null, null, null);
        dump($articles);

        return $this->render('admin_dashboard/index.html.twig', []);
    }

    /**
     * @Route("/admin/articles", name="admin_articles")
     */
    public function articles(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllArticles(0, 10, null, null, null, null, null);
        dump($articles);

        return $this->render('admin_dashboard/articles.html.twig', []);
    }



    /**
     * @Route("/admin/articlesComments", name="admin_articlesComments")
     */
    public function articlesComments(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllArticles(0, 10, null, null, null, null, null);
        dump($articles);

        return $this->render('admin_dashboard/articlesComments.html.twig', []);
    }
}

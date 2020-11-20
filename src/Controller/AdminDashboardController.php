<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ArticleNewType;

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
     * @Route("/admin/articles/new", name="admin_article_new")
     */
    public function createArticle(Request $request, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleNewType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->createOrUpdateArticle($article);
        }

        return $this->render('admin_dashboard/newArticle.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ArticleNewType;
use App\Repository\CommentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/admin")
 */
class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/", name="admin_home")
     */
    public function home(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllArticles(0, 5, 'creationDate', 'DESC', null, null);

        return $this->render('admin_dashboard/index.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/articles", name="admin_articles")
     */
    public function articles(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllArticles(0, 10, null, null, null, null);

        return $this->render('admin_dashboard/articles.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/articles/new", name="admin_article_new")
     */
    public function createArticle(Request $request, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleNewType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $article->setUser($user);
            $article = $articleRepository->setDefaultValues($article);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Article crée !'
            );
        }

        return $this->render('admin_dashboard/newArticle.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/comments", name="admin_comments")
     */
    public function comments(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findAllComments(0, 10, null, null, null, null);
        return $this->render('admin_dashboard/comments.html.twig', ['comments' => $comments]);
    }
}

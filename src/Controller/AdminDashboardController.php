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
use Doctrine\ORM\Mapping\Id;
use Knp\Component\Pager\PaginatorInterface;
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
     * @Route("/article/{id}", name="admin_edit_article")
     */
    public function editArticle(ArticleRepository $articleRepository, $id, Request $request): Response
    {
        $article = $articleRepository->findOneArticle($id);
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
                'Article modifié !'
            );
        }

        return $this->render('admin_dashboard/newArticle.html.twig', [
            'form' => $form->createView(),
        ]);
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
     * @Route("/articles/delete", name="admin_article_delete")
     */
    public function deleteArticle(Request $request, ArticleRepository $articleRepository): Response
    {
        $ids = $request->request->get('idCheck');
        $entityManager = $this->getDoctrine()->getManager();
        foreach($ids as $id) {
            $article = $articleRepository->find($id);
            $entityManager->remove($article);
        }
        $entityManager->flush();     
        return $this->redirectToRoute('admin_articles');
    }

    /**
     * @Route("/comments", name="admin_comments")
     */
    public function comments(CommentRepository $commentRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $comments = $commentRepository->findAllComments(0, 10, null, null, null, null);
        
        // Paginate the results of the query
        $commentsToLoad = $paginator->paginate(
            // Doctrine Query, not results
            $comments,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        
        return $this->render('admin_dashboard/comments.html.twig', ['comments' => $commentsToLoad]);
    }
    /**
     * @Route("/approuve-comment/{id}", name="approuveComment")
     */
    public function approuveComment(CommentRepository $commentRepository, $id)
    {
        $comment = $commentRepository->findOneComment($id);
        $comment->setState('approved');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();
    }
}

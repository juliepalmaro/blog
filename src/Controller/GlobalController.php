<?php

namespace App\Controller;

use App\Entity\Bookmark;
use App\Entity\Share;
use App\Repository\ArticleRepository;
use App\Repository\BookmarkRepository;
use App\Repository\CommentRepository;
use App\Repository\ShareRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class GlobalController extends AbstractController
{
    /**
     * @Route("/add-bookmark/{id}", name="add-bookmark")
     */
    public function addBookmark(ArticleRepository $articleRepository, $id, Request $request, BookmarkRepository $bookmarkRepository): Response
    {
        $referer = $request->headers->get('referer');
        if (!\is_string($referer) || !$referer) {
            echo 'Referer is invalid or empty.';
        }
        $refererPathInfo = Request::create($referer)->getPathInfo();

        $currentUser = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        $article = $articleRepository->findOneArticle($id);

        $oldBookmark = $bookmarkRepository->findOneBookmark($currentUser, $article);

        if ($oldBookmark) {
            $entityManager->remove($oldBookmark,);
        }

        $bookmark = new Bookmark();
        $bookmark->setUser($currentUser);
        $bookmark->setArticle($article);
        $bookmark->setCreationDate(new DateTime());

        $entityManager->persist($bookmark);

        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Article aimé !'
        );

        return $this->redirect($refererPathInfo);
    }

    /**
     * @Route("/remove-bookmark/{id}", name="remove-bookmark")
     */
    public function removeBookmark(ArticleRepository $articleRepository, $id, Request $request): Response
    {
        $referer = $request->headers->get('referer');
        if (!\is_string($referer) || !$referer) {
            echo 'Referer is invalid or empty.';
        }
        $refererPathInfo = Request::create($referer)->getPathInfo();

        $currentUser = $this->getUser();

        $article = $articleRepository->findOneArticle($id);

        $bookmark = new Bookmark();
        $bookmark->setUser($currentUser);
        $bookmark->setArticle($article);
        $bookmark->setCreationDate(new DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($bookmark);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Article retiré !'
        );

        return $this->redirectToRoute($refererPathInfo);
    }

    /**
     * @Route("/add-shared/{id}", name="add-shared")
     */
    public function addShared(ArticleRepository $articleRepository, $id, Request $request, ShareRepository $shareRepository): Response
    {
        $referer = $request->headers->get('referer');
        if (!\is_string($referer) || !$referer) {
            echo 'Referer is invalid or empty.';
        }
        $refererPathInfo = Request::create($referer)->getPathInfo();

        $currentUser = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        $article = $articleRepository->findOneArticle($id);

        $oldShare = $shareRepository->findOneShared($currentUser, $article);

        if ($oldShare) {
            $entityManager->remove($oldShare);
        }

        $share = new Share();
        $share->setUser($currentUser);
        $share->setArticle($article);
        $share->setCreationDate(new DateTime());

        $entityManager->persist($share);

        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Article partagé !'
        );

        return $this->redirect($refererPathInfo);
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(): Response
    {
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_home');
        } elseif ($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('user_home');
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @Route("admin/filters/", name="admin_filters")
     */

    public function filterArticle(ArticleRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        if (isset($_GET['filter'])) {
            $filter = $_GET['filter'];
        } else {
            throw $this->createNotFoundException('Cette page n\'existe pas');
        }

        $article = $repository->articleFilter($filter);

        // Paginate the results of the query
        $articles = $paginator->paginate(
        // Doctrine Query, not results
            $article,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10);

        if (!$article) {
            throw $this->createNotFoundException('Cette page n\'existe pas');
        }

        return $this->render('admin_dashboard/articles.html.twig', [
            'article' => $articles,
        ]);

    }

    /**
     * @Route("admin/filtersComment/", name="admin_filtersCom")
     */

    public function filterCommentary(CommentRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        if (isset($_GET['filter'])) {
            $filter = $_GET['filter'];
        } else {
            throw $this->createNotFoundException('Cette page n\'existe pas');
        }

        $comment = $repository->commentFilter($filter);

        // Paginate the results of the query
        $comments = $paginator->paginate(
        // Doctrine Query, not results
            $comment,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10);

        if (!$comment) {
            throw $this->createNotFoundException('Cette page n\'existe pas');
        }

        return $this->render('admin_dashboard/comments.html.twig', [
            'comments' => $comments,
        ]);
    }
}

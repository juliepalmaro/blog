<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\BookmarkRepository;
use App\Repository\CommentRepository;
use App\Repository\ShareRepository;
use App\Form\UserUpdateType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\HttpFoundation\Request;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/user")
 */
class UserDashboardController extends AbstractController
{
    /**
     * @Route("/", name="user_home")
     */
    public function index(BookmarkRepository $bookmarkRepository, ShareRepository $shareRepository, CommentRepository $commentRepository): Response
    {
        $currentUser = $this->getUser();
        $bookmarks = $bookmarkRepository->findAllBookmarks(0, 10, null, null, $currentUser, null);
        $shareds = $shareRepository->findAllShared(0, 10, null, null, $currentUser, null);
        $comments = $commentRepository->findAllComments(0, 10, null, null, $currentUser, null);

        return $this->render('user_dashboard/index.html.twig', [
            'bookmarks' => $bookmarks,
            'shareds' => $shareds,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/update", name="user_update")
     */
    public function update(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été modifié');
        }

        return $this->render('user_dashboard/updateUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/bookmarks", name="user_bookmarks")
     */
    public function bookmarks(BookmarkRepository $bookmarkRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $currentUser = $this->getUser();
        $bookmarks = $bookmarkRepository->findAllBookmarks(0, 10, null, null, $currentUser, null);

        // Paginate the results of the query
        $bookmarksToLoad = $paginator->paginate(
            // Doctrine Query, not results
            $bookmarks,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            1
        );

        return $this->render('user_dashboard/bookmarks.html.twig', ['bookmarks' => $bookmarksToLoad]);
    }

    /**
     * @Route("/shared", name="user_shared")
     */
    public function shared(ShareRepository $shareRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $currentUser = $this->getUser();
        $shared = $shareRepository->findAllShared(0, 10, null, null, $currentUser, null);

        // Paginate the results of the query
        $sharedToLoad = $paginator->paginate(
            // Doctrine Query, not results
            $shared,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            1
        );

        return $this->render('user_dashboard/shared.html.twig', ['shared' => $sharedToLoad]);
    }

    /**
     * @Route("/comments", name="user_comments")
     */
    public function comments(CommentRepository $commentRepository, PaginatorInterface $paginator,Request $request): Response
    {
        $currentUser = $this->getUser();
        $comments = $commentRepository->findAllComments(0, 10, null, null, $currentUser, null);

        $articles = [];

        for ($i = 0; $i < count($comments); $i++) {
            $index = array_search($comments[$i]->getArticle(), $articles);
            dump($index);

            if (!$index) {
                array_push($articles, $comments[$i]->getArticle());
            }
        }

        // Paginate the results of the query
        $commentsToLoad = $paginator->paginate(
            // Doctrine Query, not results
            $articles,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );


        return $this->render('user_dashboard/comments.html.twig', ['articles' => $commentsToLoad]);
    }
}

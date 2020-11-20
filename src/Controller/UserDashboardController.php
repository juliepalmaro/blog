<?php

namespace App\Controller;

use App\Repository\BookmarkRepository;
use App\Repository\CommentRepository;
use App\Repository\ShareRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Length;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/user")
 */
class UserDashboardController extends AbstractController
{
    /**
     * @Route("/", name="user_home")
     */
    public function index(): Response
    {
        return $this->render('user_dashboard/index.html.twig');
    }

    /**
     * @Route("/update", name="user_update")
     */
    public function update(): Response
    {
        return $this->render('user_dashboard/index.html.twig');
    }

    /**
     * @Route("/bookmarks", name="user_bookmarks")
     */
    public function bookmarks(BookmarkRepository $bookmarkRepository): Response
    {
        $currentUser = $this->getUser();
        $bookmarks = $bookmarkRepository->findAllBookmarks(0, 10, null, null, $currentUser, null);

        return $this->render('user_dashboard/bookmarks.html.twig', ['bookmarks' => $bookmarks]);
    }

    /**
     * @Route("/shared", name="user_shared")
     */
    public function shared(ShareRepository $shareRepository): Response
    {
        $currentUser = $this->getUser();
        $shared = $shareRepository->findAllShared(0, 10, null, null, $currentUser, null);

        return $this->render('user_dashboard/shared.html.twig', ['shared' => $shared]);
    }

    /**
     * @Route("/comments", name="user_comments")
     */
    public function comments(CommentRepository $commentRepository): Response
    {
        $currentUser = $this->getUser();
        $comments = $commentRepository->findAllComments(0, 10, null, null, $currentUser);

        return $this->render('user_dashboard/comments.html.twig', ['comments' => $comments]);
    }
}

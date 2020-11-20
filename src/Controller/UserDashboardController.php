<?php

namespace App\Controller;

use App\Repository\BookmarkRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @Route("/bookmarks", name="bookmarks")
     */
    public function bookmarks(BookmarkRepository $bookmarkRepository): Response
    {
        $currentUser = $this->getUser();
        $bookmarks = $bookmarkRepository->findAllBookmarks(0, 10, null, null, $currentUser, null);
        dump($bookmarks);

        return $this->render('user_dashboard/bookmarks.html.twig');
    }
}

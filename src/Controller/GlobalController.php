<?php

namespace App\Controller;

use App\Entity\Bookmark;
use App\Entity\Share;
use App\Repository\ArticleRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class GlobalController extends AbstractController
{
    /**
     * @Route("/add-bookmark/{id}", name="add-bookmark")
     */
    public function addBookmark(ArticleRepository $articleRepository, $id, Request $request): Response
    {
        $referer = $request->headers->get('referer');
        if (!\is_string($referer) || !$referer) {
            echo 'Referer is invalid or empty.';
        }

        $refererPathInfo = Request::create($referer)->getPathInfo();
        dump($refererPathInfo);

        $currentUser = $this->getUser();

        $article = $articleRepository->findOneArticle($id);

        $bookmark = new Bookmark();
        $bookmark->setUser($currentUser);
        $bookmark->setArticle($article);
        $bookmark->setCreationDate(new DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($bookmark);
        $entityManager->flush();

        return $this->redirect($refererPathInfo);
    }

    /**
     * @Route("/remove-bookmark/{id}", name="remove-bookmark")
     */
    public function removeBookmark(ArticleRepository $articleRepository, $id, Request $request): Response
    {
        $currentRoute = $request->attributes->get('_route');
        dump($currentRoute);

        $currentUser = $this->getUser();

        $article = $articleRepository->findOneArticle($id);

        $bookmark = new Bookmark();
        $bookmark->setUser($currentUser);
        $bookmark->setArticle($article);
        $bookmark->setCreationDate(new DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($bookmark);
        $entityManager->flush();

        return $this->redirectToRoute($currentRoute);
    }

    /**
     * @Route("/add-shared/{id}", name="add-shared")
     */
    public function addShared(ArticleRepository $articleRepository, $id, Request $request): Response
    {
        $currentRoute = $request->attributes->get('_route');
        dump($currentRoute);

        $currentUser = $this->getUser();

        $article = $articleRepository->findOneArticle($id);

        $share = new Share();
        $share->setUser($currentUser);
        $share->setArticle($article);
        $share->setCreationDate(new DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($share);
        $entityManager->flush();

        return $this->redirectToRoute($currentRoute);
    }
}

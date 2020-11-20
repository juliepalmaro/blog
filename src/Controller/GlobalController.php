<?php

namespace App\Controller;

use App\Entity\Bookmark;
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
    public function index(ArticleRepository $articleRepository, $id, Request $request): Response
    {
        $currentRoute = $request->attributes->get('_route');
        dump($currentRoute);

        // $request = $this->container->get('request_stack');
        // $routeName = $request->get('_route');
        // dump($routeName);

        $currentUser = $this->getUser();

        $article = $articleRepository->findOneArticle($id);

        $bookmark = new Bookmark();
        $bookmark->setUser($currentUser);
        $bookmark->setArticle($article);
        $bookmark->setCreationDate(new DateTime());

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard() : Response
    {
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('admin_home');
        } elseif($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
            return $this->redirectToRoute('user_home');
        } else {
            return $this->redirectToRoute('homepage');
        }
        
    }
}

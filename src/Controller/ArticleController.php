<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/azfazf", name="article")
     */
    public function index(): Response
    {
        return $this->render('home.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }


}

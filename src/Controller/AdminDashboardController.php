<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/admin")
 */
class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/home", name="admin_home")
     */
    public function home(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllArticles(0, 10, null, null, null, null, null);
        dump($articles);

        return $this->render('admin_dashboard/index.html.twig', []);
    }
}

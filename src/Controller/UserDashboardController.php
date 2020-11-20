<?php

namespace App\Controller;

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
}

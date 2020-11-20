<?php

namespace App\Controller;

use App\Form\UserUpdateType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(): Response
    {
        return $this->render('user_dashboard/index.html.twig');
    }

     /**
     * @Route("/update", name="user_update")
     */
    public function update(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été modifié');
        }

        return $this->render('user_dashboard/updateUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

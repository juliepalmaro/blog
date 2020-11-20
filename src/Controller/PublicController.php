<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Role;
use App\Entity\User;
use App\Notification\ContactNotification;
use App\Repository\ArticleRepository;
use App\Security\UserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\RegistrationFormType;
use App\Form\ContactType;

class PublicController extends AbstractController
{

    /**
     * @Route("/about", name="about")
     */
    public function about(): Response
    {
        return $this->render('public/about.html.twig', [
            'controller_name' => 'AboutController',
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, ContactNotification $notification): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Merci de nous avoir contacté nous vous répondrons bientôt :)');

            return $this->render('public/contact.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        return $this->render('public/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $role = $entityManager->getRepository(Role::class)->findOneBy(['label' => 'ROLE_USER']);
            $user->addUserRole($role);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été créé');

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('public/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('public/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/", name="homepage")
     */
    public function getAllArticles(ArticleRepository $repository, Request $request){

        if(isset($page)) {
            $x = ($page - 1) * 10;
        }else{
            $x = 0;
        }

        $datas = $repository->findAllArticles($x,10, 'creationDate' , 'DESC', NULL, null );
        return $this->render('home.html.twig', [
            'datas' => $datas,
        ]);
    }

    /**
     * @Route("/article/{id]", name="article")
     */
    public function findOneArticle(ArticleRepository $repository, $id): Response
    {
        $article = $repository->findOneArticle($id);

        if (!$article){
            throw $this->createNotFoundException('Cet arricle n\existe pas');
        }

        return $this->render('_article.html.twig', [
            'article' => $article,
        ]);
    }
}

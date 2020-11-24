<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Article;
use App\Notification\ContactNotification;
use App\Repository\ArticleRepository;
use App\Security\UserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\ContactType;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\RegistrationType;
use App\Repository\CommentRepository;

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
    public function register(Request $request, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
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
    public function getAllArticles(ArticleRepository $repository, PaginatorInterface $paginator, Request $request)
    {

        if (isset($page)) {
            $x = ($page - 1) * 10;
        } else {
            $x = 0;
        }

        $datas = $repository->findAllArticles($x, 20, 'creationDate', 'DESC', NULL, null);

        // Paginate the results of the query
        $articles = $paginator->paginate(
            // Doctrine Query, not results
            $datas,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );

        return $this->render('public/home.html.twig', [
            'datas' => $articles,
        ]);
    }

    /**
     * @Route("/article/{id}", name="article")
     */
    public function findOneArticle(ArticleRepository $repository, $id, CommentRepository $commentsRepository): Response
    {
        $article = $repository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Cet article n\existe pas');
        }

        if (isset($page)) {
            $x = ($page - 1) * 10;
        } else {
            $x = 0;
        }

        $comments = $commentsRepository->findAllComments($x, 20, 'creationDate', 'DESC', NULL, $article);

        return $this->render('public/infoarticle.html.twig', [
            'article' => $article,
            'comments' => $comments
        ]);
    }

    // LES COMMENTAIRES NE FONCTIONNENT PAS ENCORE (IACO)

    /**
     * @Route("/article/{id}", name="infoarticle")
     */
    public function getAllComments(CommentRepository $repository, PaginatorInterface $paginator, Request $request)
    {

        if (isset($page)) {
            $x = ($page - 1) * 10;
        } else {
            $x = 0;
        }

        $commentDatas = $repository->findAllComments($x, 20, 'creationDate', 'DESC', NULL, null);

        // Paginate the results of the query
        $comments = $paginator->paginate(
            // Doctrine Query, not results
            $commentDatas,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            6
        );

        return $this->render('public/infoarticle.html.twig', [
            'commentDatas' => $comments,
        ]);
    }

    /**
     * @Route("/comment/{id}", name="comment")
     */
    public function findOneComment(CommentRepository $repository, $id): Response
    {
        $comment = $repository->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('Ce commentaire n\existe pas');
        }

        return $this->render('public/comment.html.twig', [
            'article' => $comment,
        ]);
    }

    /**
     * @Route("/search/", name="search")
     */
    public function searchArticle(ArticleRepository $repository): Response
    {
        if (isset($_POST['search'])) {
            $search = $_POST['search'];
        } else {
            throw $this->createNotFoundException('Cet article n\'existe pas');
        }

        $article = $repository->findSpecificArticle(0, 10, 'creationDate', 'DESC', $search);

        if (!$article) {
            throw $this->createNotFoundException('Cet article n\'existe pas 2');
        }

        return $this->render('public/articlefind.html.twig', [
            'articleSearched' => $article,
        ]);
    }
}

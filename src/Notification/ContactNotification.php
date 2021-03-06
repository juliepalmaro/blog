<?php


namespace App\Notification;

//Gestion de l'envoi des mails du formulaire de contact
use App\Entity\Contact;
use Twig\Environment;

class ContactNotification
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $renderer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    public function notify(Contact $contact)
    {
        $message = (new \Swift_Message('Contact blog :'))
            ->setFrom($contact->getMail())
            ->setTo('laraj.symfony@gmail.com')
            ->setReplyTo($contact->getMail())
            ->setBody($this->renderer->render('public/mail.html.twig', [
                'contact' => $contact
            ]), 'text/html');
        $this->mailer->send($message);
    }
}

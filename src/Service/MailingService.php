<?php

namespace App\Service;

use App\Entity\User;
use Twig\Environment;
use App\Entity\Contact;

class MailingService{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $renderer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer){
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    /**
     * Envoi de mail pour le formulaire de contact
     *
     * @param Contact $contact
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function contactSend(Contact $contact){
        $message = (new \Swift_Message('Formulaire de contact - '.$contact->getEmail()))
                ->setFrom($contact->getEmail())
                ->setTo('piquardanthony@gmail.com')
                ->setReplyTo($contact->getEmail())
                ->setBody($this->renderer->render('emails/contact.html.twig', [
                    'contact' => $contact
                ]), 'text/html');
        
        $this->mailer->send($message);
    }

    /**
     * Envoi de mail pour message forum avec mention
     *
     * @param User $sender
     * @param User $receiver
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function notifSend(User $sender, User $receiver){
        $message = (new \Swift_Message('Notification recu - LeTrading.fr'))
            ->setFrom($sender->getEmail())
            ->setTo($receiver->getEmail())
            ->setReplyTo($sender->getEmail())
            ->setBody($this->renderer->render('emails/notif.html.twig'), 'text/html');

        $this->mailer->send($message);
    }

}

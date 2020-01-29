<?php

namespace App\Service;

use Twig\Environment;
use App\Entity\Contact;

class ContactService{

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

    public function notify(Contact $contact){
        $message = (new \Swift_Message('Formulaire de contact - '.$contact->getEmail()))
                ->setFrom($contact->getEmail())
                ->setTo('piquardanthony@gmail.com')
                ->setReplyTo($contact->getEmail())
                ->setBody($this->renderer->render('emails/contact.html.twig', [
                    'contact' => $contact
                ]), 'text/html');
        
                $this->mailer->send($message);
    }
}
<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Votre nom", "Donnez votre nom"))
            ->add('lastName', TextType::class, $this->getConfiguration("Votre prénom", "Donnez votre prénom"))
            ->add('email', EmailType::class, $this->getConfiguration("Votre email", "Donnez votre email"))
            ->add('message', TextareaType::class, $this->getConfiguration("Votre message", "Veuillez renseigner un message"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

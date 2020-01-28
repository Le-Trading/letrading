<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom", "Votre prénom ..."))
            ->add('lastName' , TextType::class, $this->getConfiguration("Nom", "Votre nom de famille ..."))
            ->add('pseudo' , TextType::class, $this->getConfiguration("Pseudo", "Votre pseudo ..."))
            ->add('email' , EmailType::class, $this->getConfiguration("Email", "Votre adresse email"))
            ->add('media', MediaType::class, [
                'attr' => ['placeholder' => 'Choisissez votre fichier'],
                'required' => false,
                'label' => 'Avatar'
            ])
            ->add('hash' , PasswordType::class, $this->getConfiguration("Mot de passe", "Choisissez votre mot de passe"))
            ->add('passwordConfirm' , PasswordType::class, $this->getConfiguration("Confirmer mot de passe", "Confirmer votre mot de passe"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

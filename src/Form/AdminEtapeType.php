<?php

namespace App\Form;

use App\Entity\EtapeFormation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminEtapeType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => "Titre"
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => "Description de l'étape"
            ])
            ->add('media', MediaType::class, [
                'attr' => ['placeholder' => 'Choisissez votre fichier'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EtapeFormation::class,
        ]);
    }
}

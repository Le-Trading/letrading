<?php

namespace App\Form;

use App\Entity\Offers;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminOfferType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['required' => true ])
            ->add('description', CKEditorType::class, [
                'required' => true,
                'label' => "Description de l'offre",
                'config' => array(
                    'toolbar' => "basic"
                )
            ])
            ->add('media', MediaType::class, [
                'label' => "Image de l'offre",
                'required' => false
            ])
            ->add('price', IntegerType::class, ['required' => true ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Offers::class,
        ]);
    }
}

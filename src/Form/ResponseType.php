<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ResponseType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', CKEditorType::class, ['required' => false, 'label' => false, 'config' => array(
                'toolbar' => "basic"
            )])
            ->add('media', MediaType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Choisissez votre fichier'],
                'required' => false
            ]);

        if ($options['isResponse']) {
            $builder->add('respond', HiddenType::class, ['mapped' => false, 'required' => false, 'label' => false]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'isResponse' => true,
            'isAdmin' => false,
            "allow_extra_fields" => true
        ]);
    }
}

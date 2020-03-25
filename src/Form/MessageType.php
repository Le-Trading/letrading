<?php

namespace App\Form;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Form\Type\EntityHiddenType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conversation', EntityHiddenType::class, [
                'class' => Conversation::class, 'label' => false ])
            ->add('author', EntityHiddenType::class, [
                'class' => User::class, 'label' => false  ])
            ->add('content', CKEditorType::class, ['required' => false, 'label' => false])
            ->add('media', MediaType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Choisissez votre fichier'],
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
                $form = $event->getForm();

                //récupération provenance entité
                $entityParent = $event->getForm()->getParent()->getConfig()->getDataClass();

                if($entityParent=="App\Entity\User"){
                    $form->add('avatarFile', VichImageType::class, [
                        'label' => false,
                        'attr' => ['placeholder' => 'Choisissez votre fichier'],
                        'required' => false,
                        'constraints' => [
                            new Image([
                                'maxSize' => '5M',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/gif',
                                    'image/png',
                                ]
                            ])
                        ]
                    ]);
                } elseif($entityParent=="App\Entity\Post"){
                    $form->add('forumFile', VichImageType::class, [
                        'label' => false,
                        'attr' => ['placeholder' => 'Choisissez votre fichier'],
                        'required' => false
                    ]);
                }elseif($entityParent=="App\Entity\Formation"){
                    $form->add('formationFile', VichImageType::class, [
                        'label' => false,
                        'attr' => ['placeholder' => 'Choisissez votre fichier'],
                        'required' => false
                    ]);
                }elseif($entityParent=="App\Entity\SectionFormation"){
                    $form->add('sectionFormationFile', VichImageType::class, [
                        'label' => false,
                        'attr' => ['placeholder' => 'Choisissez votre fichier'],
                        'required' => false
                    ]);
                }elseif($entityParent=="App\Entity\EtapeFormation"){
                    $form->add('etapeFormationFile', VichImageType::class, [
                        'label' => false,
                        'attr' => ['placeholder' => 'Choisissez votre fichier'],
                        'required' => false
                    ]);
                }else{
                    $form->add('defaultFile', VichImageType::class, [
                        'label' => false,
                        'attr' => ['placeholder' => 'Choisissez votre fichier'],
                        'required' => false
                    ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}

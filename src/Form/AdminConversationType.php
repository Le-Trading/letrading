<?php

namespace App\Form;

use App\Entity\Conversation;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminConversationType extends AbstractType
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('participants', CollectionType::class, [
                    'entry_type' => ChoiceType::class,
                    'entry_options' => array(
                        'choices' => $this->prepareUsersForChoices(),
                        'attr' => array('class' => 'form-control selectpicker'),
                        'label' => '',
                        'required' => true
                    ),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => ''
                ]
            )
            ->add('messages', CollectionType::class,
                [
                    'entry_type' => MessageType::class,
                    'allow_add' => true
                ]);
    }

    /**
     *
     *
     * @return array Formatted users
     */
    protected function prepareUsersForChoices()
    {
        $users = $this->userRepository->findAll();
        $choices = [];
        foreach ($users as $user) {
            $choices[$user->getPseudo()] = $user;
        }

        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Conversation::class,
        ]);
    }
}

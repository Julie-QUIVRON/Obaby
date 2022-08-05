<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',TextType::class, [
                'label' => '* Email',
                ])
            ->add('roles', ChoiceType::class,
            [   'label' => '* Rôles',
            'help' => '* Champs obligatoires',
                'choices' => [
                    'utilisateur' => 'ROLE_USER',
                    'modérateur' => 'ROLE_MODERATOR',
                    'administrateur' => 'ROLE_ADMIN',
                ],
                "multiple" => true,
                // radio buttons or checkboxes
                "expanded" => true,
            ])
            ->add('pseudo',TextType::class, [
                'label' => '* Pseudonyme',
                ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
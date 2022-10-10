<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class UserUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // EMAIL
            ->add('email', TextType::class, [
                'label' => '* Email',
            ])
        // PSEUDO
            ->add('pseudo', TextType::class, [
                'label' => '* Pseudonyme',
            ])
        // PASSWORD
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'label' => 'Entrez un mot de passe',
                'first_options'  => [
                        'label' => '* Mot de passe',
                        'constraints' => new NotBlank(),
                        'constraints' => new NotNull(),
                        'constraints' => new Regex(
                            "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/",
                            "Le mot de passe doit contenir au minimum 12 caractères, une majuscule, un chiffre et un caractère spécial"
                    ),
                        'attr' => array(
                                'type' => 'password',
                                'placeholder' => 'Password'
                        ),
                ],
                'second_options' => [
                        'label' => '* Confirmez le mot de passe',
                        'constraints' => new NotBlank(),
                        'constraints' => new NotNull(),
                        'attr' => array(
                                'type' => 'password',
                                'placeholder' => 'Password'
                        ),
                ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

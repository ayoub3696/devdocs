<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudonyme', TextType::class, [
                'label' => 'Pseudonyme',
                'constraints' => [
                    new Assert\NotBlank(message: 'Le pseudonyme est obligatoire.'),
                    new Assert\Length(
                        min: 3,
                        max: 30,
                        minMessage: 'Le pseudonyme doit contenir au moins 3 caractères.',
                        maxMessage: 'Le pseudonyme ne peut pas dépasser 30 caractères.'
                    ),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'constraints' => [
                    new Assert\NotBlank(message: "L'adresse email est obligatoire."),
                    new Assert\Email(message: "Veuillez saisir une adresse email valide."),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
                'invalid_message' => 'Les deux mots de passe ne correspondent pas.',
                'constraints' => [
                    new Assert\NotBlank(message: 'Le mot de passe est obligatoire.'),
                    new Assert\Length(
                        min: 8,
                        minMessage: 'Le mot de passe doit contenir au moins 8 caractères.'
                    ),
                    new Assert\Regex(
                        pattern: '/^(?=.*[A-Za-z])(?=.*\d).+$/',
                        message: 'Le mot de passe doit contenir au moins une lettre et un chiffre.'
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
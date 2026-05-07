<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Nom de l'invité affiché sur le site
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])

            // Email utilisé pour la connexion
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])

            // Mot de passe saisi en clair dans le formulaire,
            // mais il sera hashé dans le contrôleur avant l'enregistrement.
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
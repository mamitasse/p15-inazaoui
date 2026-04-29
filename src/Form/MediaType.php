<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Image',

                // Le champ est obligatoire lors de l’ajout d’un média.
                'required' => true,

                // Validation du fichier uploadé.
                // Objectif : accepter uniquement des images de 2 Mo maximum.
                'constraints' => [
                    new File([
                        // Taille maximale autorisée : 2 Mégaoctets.
                        'maxSize' => '2M',

                        // Vérification par MIME type, plus fiable que l’extension du fichier.
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],

                        // Message affiché si le fichier n’est pas une image autorisée.
                        'mimeTypesMessage' => 'Veuillez choisir une image valide : JPG, PNG ou WEBP.',

                        // Message affiché si le fichier dépasse 2 Mo.
                        'maxSizeMessage' => 'L’image ne doit pas dépasser 2 Mo.',
                    ]),
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ]);

        if ($options['is_admin']) {
            $builder
                ->add('user', EntityType::class, [
                    'label' => 'Utilisateur',
                    'required' => false,
                    'class' => User::class,
                    'choice_label' => 'name',
                ])
                ->add('album', EntityType::class, [
                    'label' => 'Album',
                    'required' => false,
                    'class' => Album::class,
                    'choice_label' => 'name',
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'is_admin' => false,
        ]);
    }
}
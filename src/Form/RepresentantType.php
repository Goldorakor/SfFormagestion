<?php

namespace App\Form;

use App\Entity\Representant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RepresentantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('prenom', TextType::class, [
                'label' => 'Prénom', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('sexe', TextType::class, [
                'label' => 'Civilité', // texte qui s'affiche devant le rectangle de saisie
            ])
            
            ->add('fonction', TextType::class, [
                'label' => 'Fonction', // texte qui s'affiche devant le rectangle de saisie
            ])

            // https://symfony.com/doc/current/controller/upload_file.html -> on récupère ce bout de code dans la doc officielle
            ->add('tampon', FileType::class, [
                'label' => 'Tampon et signature du représentant légal (.PNG, .JPG)',

                'mapped' => false, // Ne pas mapper à l'entité (comme on ne stocke pas le fichier directement en BDD, on ne mappe pas ce champ à l'entité ) -> on ne stocke que le chemin (ça empêche une injection directe en BDD)

                'required' => false, // on rend le champ optionnel -> pas besoin de re-uploader le fichier à chaque edit du représentant

                'constraints' => [ // pour s'assurer que l'utilisateur n'upload que des fichiers conformes.
                    new File([
                        'maxSize' => '2M', // Limite la taille du fichier à 2 mégaoctets.
                        'mimeTypes' => ['image/png', 'image/jpeg'], // Accepte uniquement les fichiers PNG et JPG.
                        'mimeTypesMessage' => 'Veuillez uploader une image PNG ou JPG', //  Affiche ce message si le fichier n'est pas du bon type.
                    ]),
                ],
            ])

            ->add('enregistrer', SubmitType::class, [
                'label' => '<i class="fa-solid fa-download"></i> Enregistrer',
                'label_html' => true,
                'attr' => ['class' => 'btn btn-rose'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Representant::class,
        ]);
    }
}

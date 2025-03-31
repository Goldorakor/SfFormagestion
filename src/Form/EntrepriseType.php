<?php

namespace App\Form;

use App\Entity\Entreprise;
use App\Entity\Representant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('raisonSociale', TextType::class, [
                'label' => 'Nom de la société', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('statutJuri', TextType::class, [
                'label' => 'Statut juridique', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('adresseSiege', TextType::class, [
                'label' => 'Rue', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('codePostalSiege', TextType::class, [
                'label' => 'Code Postal', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('villeSiege', TextType::class, [
                'label' => 'Ville', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('paysSiege', TextType::class, [
                'label' => 'Pays', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('tel', TextType::class, [
                'label' => 'Téléphone', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('email', TextType::class, [
                'label' => 'Adresse e-mail', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('numSiret', TextType::class, [
                'label' => 'Numéro SIRET', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('numRCS', TextType::class, [
                'label' => 'Numéro RCS', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('numTVA', TextType::class, [
                'label' => 'Numéro de TVA intracommunautaire', // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('numDeclaActivite', TextType::class, [
                'label' => "Numéro de déclaration d'activité", // texte qui s'affiche devant le rectangle de saisie
            ])

            ->add('prefectureRegion', TextType::class, [
                'label' => 'Auprès de quelle préfeture de région', // texte qui s'affiche devant le rectangle de saisie
            ])
            
            ->add('tribunal', TextType::class, [
                'label' => 'Tribunal de commerce compétent en cas de litige', // texte qui s'affiche devant le rectangle de saisie
            ])

            // https://symfony.com/doc/current/controller/upload_file.html -> on récupère ce bout de code dans la doc officielle
            ->add('logo', FileType::class, [
                'label' => "Logo de l'entreprise (PNG, JPG)",

                'mapped' => false, // Ne pas mapper à l'entité (comme on ne stocke pas le fichier directement en BDD, on ne mappe pas ce champ à l'entité ) -> on ne stocke que le chemin (ça empêche une injection directe en BDD)

                'required' => false, // on rend le champ optionnel
                
                'constraints' => [ // pour s'assurer que l'utilisateur n'upload que des fichiers conformes.
                    new File([
                        'maxSize' => '2M', // Limite la taille du fichier à 2 mégaoctets.
                        'mimeTypes' => ['image/png', 'image/jpeg'], // Accepte uniquement les fichiers PNG et JPG. => serveur analyse le fichier uploadé pour contrer la faille upload
                        'mimeTypesMessage' => 'Veuillez uploader une image PNG ou JPG', //  Affiche ce message si le fichier n'est pas du bon type.
                    ]),
                ],
            ])

            /*->add('representant', EntityType::class, [
                'class' => Representant::class,
                // 'choice_label' => 'id', : on supprime cette ligne pour qu'il affiche le __toString de 
                'placeholder' => 'Sélectionner un représentant',
                'label' => 'Représentant légal de la société',
                //'required' => false,  Rendre le champ optionnel : pas ici car on exige que le représentant légal soit défini pour l'entreprise
            ])*/

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
            'data_class' => Entreprise::class,
        ]);
    }
}

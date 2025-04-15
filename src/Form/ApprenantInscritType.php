<?php

namespace App\Form;

use App\Entity\Apprenant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ApprenantInscritType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('apprenant', EntityType::class, [
                'class' => Apprenant::class,
                'placeholder' => 'Sélectionner un apprenant',// champ de type EntityType (ou ChoiceType) : l'option placeholder n’est pas un attribut HTML mais une vraie option Symfony
                //'choice_label' => 'nom',  Ajuste selon le champ à afficher
                'label' => 'Apprenant inscrit',
                'attr' => ['class' => 'form-cell apprenant-inscrit'] /* pour décider de la taille du champ en css */
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix en € HT',
                'required' => false,
                /* 'attr' => ['placeholder' => 'Prix optionnel'],  à rajouter ?? */
                'attr' => [
                    'placeholder' => 'Prix optionnel (en € HT)',  // ici, placeholder est un attribut HTML : tous les attributs HTML personnalisés (comme class, placeholder, style, etc.) doivent être mis dans le tableau attr dans un formulaire Symfony
                    'class' => 'form-cell apprenant-prix' /* pour décider de la taille du champ en css */
                ], 
                'constraints' => [
                    new Assert\PositiveOrZero(), // pas de nombre négatif pour un prix
                    new Assert\LessThan(10000), // pas de nombre trop grand non plus ! 
                ]
            ]);
    }


    //  LIGNE À AJOUTER POUR PERMETTRE LE PASSAGE D’UN TABLEAU EN INPUT
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // ici on dit que ce formulaire n’est pas basé sur une entité 
            // => Symfony ne doit pas chercher une classe à instancier avec les données envoyées.
            // Il traitera donc apprenant et prix comme un simple tableau associatif.
        ]);
    }

}

/*

ce fichier est une formulaire qui sera employé dans un formulaire plus global (le formulaire de Session).

*/

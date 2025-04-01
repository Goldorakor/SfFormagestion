<?php

namespace App\Form;

use App\Entity\Apprenant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ApprenantInscritType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('apprenant', EntityType::class, [
                'class' => Apprenant::class,
                'placeholder' => 'Sélectionner un apprenant',
                //'choice_label' => 'nom',  Ajuste selon le champ à afficher
                'label' => 'Apprenant inscrit',
                'attr' => ['class' => 'form-cell apprenant-inscrit'] /* pour décider de la taille du champ en css */
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix en € HT',
                'required' => false,
                /* 'attr' => ['placeholder' => 'Prix optionnel'],  à rajouter ?? */
                'attr' => ['class' => 'form-cell apprenant-prix'] /* pour décider de la taille du champ en css */
            ]);
    }
}

/*

ce fichier est une formulaire qui sera employé dans un formulaire plus global (le formulaire de Session).

*/

<?php

namespace App\Form;

use App\Entity\Societe;
use App\Entity\Responsable;
use App\Entity\Responsabilite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/*
class ResponsabiliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('type_responsable') -> pas besoin, les types sont déjà connus et limités à 3
            ->add('responsable', EntityType::class, [
                'class' => Responsable::class,
                'choice_label' => 'nom', // Affiche le nom du responsable dans la liste déroulante -> peut-être préférer __toString en enlevant cette ligne
                'placeholder' => 'Sélectionner un responsable', // pour afficher cela comme titre du menu déroulant.
            ])
            ->add('type_responsable', ChoiceType::class, [
                'choices' => [
                    'Légal' => 'légal',
                    'Administratif' => 'administratif',
                    'RH' => 'RH',
                ],
                'placeholder' => 'Sélectionner un type',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Responsabilite::class,
        ]);
    }
}
*/

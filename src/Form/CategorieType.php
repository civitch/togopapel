<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Rubrique;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Nom categorie:', 'attr' => ['placeholder' => 'Saisir un titre']])
            ->add('rubrique', EntityType::class, [
                'class'         => Rubrique::class,
                'placeholder'   => 'Sélectionner une rubrique',
                'choice_label'  => 'title',
                'required'      => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter', 'attr' => ['class' => 'btn btn-sm btn-brand']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}

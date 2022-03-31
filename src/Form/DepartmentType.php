<?php

namespace App\Form;

use App\Entity\Department;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'titre','attr' => ['placeholder' => 'Saisir un titre']])
            ->add('role', TextType::class, ['label' => 'rôle','attr' => ['placeholder' => 'Saisir un rôle']])
            ->add('save', SubmitType::class, ['label' => 'Ajouter','attr' => ['class' => 'btn btn-sm btn-brand']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Department::class,
        ]);
    }
}

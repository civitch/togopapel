<?php

namespace App\Form;

use App\Entity\AnnonceSearch;
use App\Entity\Categorie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('priceMin', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['min' => 1, 'placeholder' => "Prix minimum"]
            ])
            ->add('priceMax', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['min' => 1, 'placeholder' => "Prix maximum"]
            ])
            ->add('title', TextType::class, ['required' => false, 'label' => false,'attr' => [ 'placeholder' => "Rechercher annonce"]])
            ->add('type', ChoiceType::class, [
                'choices'  => AnnonceType::getChoicesType(),
                'expanded' => true,
                'placeholder' => false,
                'required' => false,
                'data' => true,
                'label' => false
            ])
            ->add('particulier', CheckboxType::class, ['required' => false, 'label' => 'Particulier '])
            ->add('profesional', CheckboxType::class, ['required' => false, 'label' => 'Professionnel '])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'required' => false,
                'placeholder' => 'Toutes les villes',
                'choice_label' => 'title',
                'label' => false,
                'attr' => ['class' => 'js-select-single']
            ])
            ->add('categorie', EntityType::class, [
                'class'     => Categorie::class,
                'required'  => false,
                'placeholder'=> 'Toutes les catégories',
                'choice_label' => 'title',
                'label' => false,
                'attr' => ['class' => 'js-select-single']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AnnonceSearch::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

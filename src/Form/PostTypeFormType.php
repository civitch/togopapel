<?php

namespace App\Form;

use App\Entity\Etiquette;
use App\Entity\PostType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostTypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('content', TextareaType::class, ['label' => 'Contenu', 'attr' => ['rows' => 8]])
            ->add('etiquette', EntityType::class, [
                'class' => Etiquette::class,
                'choice_label' => 'title',
                'placeholder' => 'Sélectionner une étiquette',
                'label' => 'Etiquette'
            ])
            ->add('save', SubmitType::class, ['label' => 'Sauvegarder'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PostType::class,
        ]);
    }
}

<?php

namespace App\Form\Option;

use App\Entity\Option;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Activé'  => true,
                    'Désactivé' => false
                ],
                'placeholder' => 'Définir le status',
                'required' => true,
                'label'   => 'Statut'
            ])
            ->add('content', TextareaType::class, ['label' => 'Contenu','required' => true])
            ->add('label', ChoiceType::class, [
                'choices' => Option::LABEL,
                'placeholder' => 'Définir le status',
                'required' => true
            ])
            ->add('title', TextType::class, ['label' => 'Titre', 'required' => false])
            ->add('link', TextType::class, ['label' => 'Lien', 'required' => false])
            ->add('save', SubmitType::class, ['attr' => ['class'=>'btn btn-sm btn-brand'], 'label' => 'Sauvegarder'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Option::class,
        ]);
    }
}

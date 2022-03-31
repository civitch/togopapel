<?php

namespace App\Form;

use App\Entity\Pack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Ajouter un titre', 'attr' => ['class' => 'm-input']])
            ->add('description', TextareaType::class, ['label' => 'Ajouter une description', 'attr' => ['maxlength' => 255, 'rows' => 5, 'class' => 'm-input']])
            ->add('price', IntegerType::class, ['label' => 'Ajouter un prix', 'attr' => ['class' => 'm-input']])
            ->add('duration', IntegerType::class, ['label' => 'Ajouter une durée', 'attr' => ['class' => 'm-input']])
            ->add('role', ChoiceType::class, [
                'choices' => $this->getChoices(),
                'placeholder' => 'Sélectionner un rôle',
                'attr' => ['class' => 'm-input']
            ])
            ->add('save', SubmitType::class, ['attr' => ['class'=>'btn btn-sm btn-brand'], 'label' => 'Sauvegarder'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pack::class,
        ]);
    }

    private function getChoices()
    {
        $choices = Pack::ROLES_PACK;
        $output = [];
        foreach($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }
}

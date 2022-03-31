<?php

namespace App\Form;

use App\Entity\Region;
use App\Entity\Ville;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nom de la ville:',
                'attr' => [
                    'placeholder' => 'Saisir le nom de la ville'
                ]])
            ->add('region', EntityType::class, [
                'class' => Region::class,

                'choice_label' => 'title',
                'placeholder' => 'Sélectionner la région'
            ])
            ->add('save', SubmitType::class, ['attr' => ['class' => 'btn btn-sm btn-brand'], 'label' => 'Ajouter'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}

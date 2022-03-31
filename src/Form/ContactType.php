<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Votre nom et prénom:',
                'attr'  => [
                    'placeholder' => 'Saisir votre nom et prénom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse mail:',
                'attr'  => [
                    'placeholder' => 'Saisir votre adresse mail'
                ]
            ])
            ->add('tel', TelType::class, [
                'label' => 'Votre numéro de téléphone:',
                'attr'  => [
                    'placeholder' => 'Saisir votre numéro de télephone'
                ],
                'required' => false
            ])
            ->add('option', ChoiceType::class, [
                'choices' => [
                    'Direction' => 'Direction',
                    'Service Technique' => 'Service Technique',
                    'Service commercial' => 'Service commercial'
                ],
                'label' => 'Sujet:',
                'placeholder' => 'Sélectionner une option:',
                'attr' => ['class' => 'js-select-single']
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message:',
                'attr' => ['rows' => 8, 'placeholder' => 'Saisir votre message']
            ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn-authform']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}

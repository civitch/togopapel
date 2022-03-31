<?php

namespace App\Form\Register;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationParticularType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email:',
                'attr' => ['placeholder' => 'Saisir votre adresse mail']
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas!.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe:',
                    'attr' => [
                        'placeholder' => 'Saisir votre mot de passe',
                        'data-toggle' => "tooltip",
                        'data-placement' => "top",
                        'title' => "Le mot de passe doit comporter au moins 1 chiffre et 8 caractères minimum!"
                    ]],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe:',
                    'attr' => [
                        'placeholder' => 'Saisir à nouveau votre mot de passe',
                        ]],
            ])
            ->add('condition', CheckboxType::class, [
                'mapped'   => false,
                'required' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Créer mon compte', 'attr' => ['class' => 'btn-authform']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }


}

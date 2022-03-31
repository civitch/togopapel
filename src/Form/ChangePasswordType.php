<?php

namespace App\Form;


use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, ['attr' => ['placeholder' => "Saisir votre mot de passe actuel"]])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Nouveau mot de passe', 'attr' => ['placeholder' => "Saisir votre nouveau mot de passe"]],
                'second_options' => ['label' => 'Retaper votre nouveau mot de passe', 'attr' => ['placeholder' => "Confirmez votre nouveau mot de passe"]],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => ['class' => 'btn-authform']
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChangePassword::class,
        ]);
    }
}

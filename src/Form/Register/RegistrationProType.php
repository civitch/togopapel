<?php

namespace App\Form\Register;

use App\Entity\IndicatifPays;
use App\Entity\Rubrique;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationProType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Saisir votre adresse mail',]])
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
            ->add('civility', ChoiceType::class, [
                'choices' => [
                    'Madame' => 'madame',
                    'Monsieur' => 'monsieur',
                ],
                'required' => true,
                'expanded' => true
            ])
            ->add('adresse', TextType::class, ['attr' => ['placeholder' => 'Saisir votre adresse']])
            ->add('indicatifPays', EntityType::class, [
                'class' => IndicatifPays::class,
                'label' => 'Indicatif du pays:',
                'choice_label' => 'title',
                'required' => true,
                'placeholder' => 'Sélectionner un indicatif pour votre numéro',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('i')
                        ->orderBy('i.title', 'ASC');
                },
            ])
            ->add('tel', TelType::class, ['attr' => ['placeholder' => 'Saisir votre numéro de télephone']])
            ->add('name', TextType::class, ['attr' => ['placeholder' => 'Saisir votre nom']])
            ->add('firstname', TextType::class, ['attr' => ['placeholder' => 'Saisir votre prénom']])
            ->add('society', TextType::class, ['attr' => ['placeholder' => 'Saisir la société']])
            ->add('siren', TextType::class, ['attr' => ['placeholder' => 'Saisir numéro de société']])
            ->add('ville', EntityType::class, [
                'class'         => Ville::class,
                'placeholder'   => 'Sélectionner une ville ',
                'choice_label'  => 'title',
                'required'      => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('v')
                        ->orderBy('v.title', 'ASC');
                },
                'attr'          => ['class' => 'ville-select js-select-single']
            ])
            ->add('rubrique', EntityType::class, [
                'class'         => Rubrique::class,
                'placeholder'   => 'Sélectionner une rubrique',
                'choice_label'  => 'title',
                'required'      => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.title', 'ASC');
                },
                'attr' => ['class' => 'js-select-single']
            ])
            ->add('condition', CheckboxType::class, [
                'mapped'   => false,
                'required' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Créer mon compte','attr' => ['class' => 'btn-authform']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

<?php

namespace App\Form\Register;

use App\Entity\Department;
use App\Entity\User;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email :*',
                'attr' => [
                    'placeholder' => 'Saisir l\'adresse mail',
                    'class' => 'm-input m-input--square'
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom :',
                'attr' => [
                    'placeholder' =>'Saisir le nom si possible',
                    'class' => 'm-input m-input--square'
                ],
                "required" => false
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom :',
                'attr' => [
                    'placeholder' =>'Saisir le nom si possible',
                    'class' => 'm-input m-input--square'
                ],
                "required" => false
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'label' => 'Département :*',
                'choice_label' => 'title',
                'placeholder' => 'Sélectionner le département *',
                'required' => true,
                'query_builder' => function(DepartmentRepository $departmentRepository){
                    return $departmentRepository->listeAdmin();
                },
                ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

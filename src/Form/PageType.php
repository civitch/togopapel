<?php

namespace App\Form;

use App\Entity\Page;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre:'])
            ->add('content', CKEditorType::class, [
                'label' => 'Contenu:',
                'config' => [
                    'uiColor' => '#cecece',
                 ],
            ])
            ->add('label', TextType::class, ['label' => 'Label:'])
            ->add('save', SubmitType::class, ['attr' => ['class' =>'btn btn-brand'], 'label' => 'Ajouter'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class
        ]);
    }
}

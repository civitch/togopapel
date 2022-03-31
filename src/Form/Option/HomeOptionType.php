<?php


namespace App\Form\Option;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class HomeOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('label')
            ->remove('title')
        ;
    }

    public function getParent()
    {
        return OptionType::class;
    }

}
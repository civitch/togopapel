<?php

namespace App\DataTables\Column;

use Symfony\Component\OptionsResolver\OptionsResolver;

trait ColumnTrait
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'search' => null
            ])
            ->setAllowedTypes('search', ['null', 'callable'])
        ;

        return $this;
    }

    public function getSearch()
    {
        return $this->options['search'];
    }
}

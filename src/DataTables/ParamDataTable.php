<?php

namespace App\DataTables;

use App\DataTables\Column\{TextColumn, TwigColumn};
use App\DataTables\Search\CriteriaProvider;
use App\Entity\Param;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ParamDataTable implements DataTableTypeInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    )
    {}

    public function configure(DataTable $dataTable, array $options = array())
    {
        $dataTable
        ->setName($options['dataTableName'])
        ->add('label', TextColumn::class, [
            'label'                 => 'Nom',
            'orderable'             => true,
            'searchable'            => true,
            'globalSearchable'      => true,
            'className'             => 'text-center',
        ])
        ->add('value', TextColumn::class, [
            'label'                 => 'Valeur',
            'orderable'             => true,
            'searchable'            => true,
            'globalSearchable'      => true,
        ])
        // ->add('status', TextColumn::class, [
        //     'label'                 => 'Etat',
        //     'orderable'             => false,
        //     'searchable'            => false,
        //     'globalSearchable'      => false,
        //     'className'             => 'status',
        //     'render'                => function($value, Param $param){
                
        //         $url = $this->urlGenerator->generate('app_tiers_status_edit', ['id' => $param->getId()]);
        //         if($param->getStatus() === true ){
        //             return '<a data-title="Désactiver le tiers" data-texte="Etes vous sur de désactiver le tiers" href="'.$url.'" class="btn btn-outline-success enableTiers">Active</a>';
        //         } else {
        //             return '<a data-title="Activer le tiers" data-texte="Etes vous sur d\'activer le tiers" href="'.$url.'" class="btn btn-outline-danger enableTiers">Inactive</a>';
        //         }

        //     }
        // ])
        ->add('actions', TwigColumn::class, [
            'label' => 'Actions',
            'orderable' => false,
            'template' => 'Core/Actions/param_action.html.twig'
        ]) 
        // ->addOrderBy('createdAt', 'ASC')
        ;

        $dataTable->createAdapter(ORMAdapter::class, [
            'entity' => Param::class,
            'criteria' => [
                new CriteriaProvider()
            ]
        ]);
    }
}
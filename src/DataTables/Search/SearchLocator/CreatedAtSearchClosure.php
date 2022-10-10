<?php
namespace App\DataTables\Search\SearchLocator;

use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\DataTableState;

final class CreatedAtSearchClosure
{
    public function __construct(
        private QueryBuilder $qb,
        private DataTableState $state, 
        private $search
    ) {
    }

    public function __invoke(QueryBuilder $qb, DataTableState $state, $search)
    {
        (function(QueryBuilder $qb, DataTableState $state, $search){
                // $search is like "26/11/2021 12:59 -27/11/2021 23:59"
                $dataTable = $state->getDataTable();
                $field = $dataTable->getColumnByName('createdAt')->getField();
                $explode = array_map('trim', explode('-', $search));
                $startAt = $explode[0];
                if(isset($explode[1])){
                    $endAt = $explode[1];
                }
                if($startAt != ''){
                    $startAtDate = \DateTime::createFromFormat('d/m/Y H:i', $startAt);
                    if(false !== $startAtDate){
                        $qb->andWhere($qb->expr()->gte($field, ':createdAtStart'))
                            ->setParameter('createdAtStart', $startAtDate);
                    }
                }
                if(isset($endAt) && $endAt != ''){
                    $endAtDate = \DateTime::createFromFormat('d/m/Y H:i', $endAt);
                    if(false !== $endAtDate){
                        $qb->andWhere($qb->expr()->lte($field, ':createdAtEnd'))
                            ->setParameter('createdAtEnd', $endAtDate);
                    }
                }
            })();
    }
}
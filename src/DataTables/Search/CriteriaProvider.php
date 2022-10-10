<?php

namespace App\DataTables\Search;

use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORM\QueryBuilderProcessorInterface;
use Omines\DataTablesBundle\DataTableState;

class CriteriaProvider implements QueryBuilderProcessorInterface
{
    public function process(QueryBuilder $queryBuilder, DataTableState $state)
    {
        $this->processSearchColumns($queryBuilder, $state);
        $this->processGlobalSearch($queryBuilder, $state);
    }

    public function processSearchColumns(QueryBuilder $queryBuilder, DataTableState $state)
    {
        foreach ($state->getSearchColumns() as $searchInfo) {
            $column = $searchInfo['column'];
            $search = $searchInfo['search'];
            if ('' !== trim($search) && $search !== false && $search !== null) {
                if(null !== $column->getSearch()){
                    call_user_func($column->getSearch(), $queryBuilder, $state, $search);
                }else{
                    $rightExpr = $column->getRightExpr($search);
                    if($rightExpr !== null && '' !== trim($rightExpr)){
                        $rightExpr = $queryBuilder->expr()->literal($rightExpr);
                        $com = new Comparison( $column->getLeftExpr(), $column->getOperator(), $rightExpr);
                        $queryBuilder->andWhere($com);
                    }
                }
            }
            
        }  
    }

    private function processGlobalSearch(QueryBuilder $queryBuilder, DataTableState $state)
    {
        if (!empty($globalSearch = $state->getGlobalSearch())) {
            $expr = $queryBuilder->expr();
            $comparisons = $expr->orX();
            foreach ($state->getDataTable()->getColumns() as $column) {
                if ($column->isGlobalSearchable() && !empty($column->getField()) 
                && $column->isValidForSearch($globalSearch) && $column->getRightExpr($globalSearch) !== null) {
                    $comparisons->add(new Comparison($column->getLeftExpr(), $column->getOperator(),
                        $expr->literal($column->getRightExpr($globalSearch))));
                }
            }
            $queryBuilder->andWhere($comparisons);
        }
    }
}

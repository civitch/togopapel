<?php
namespace App\DataTables\Search\RigthExprLocator;

use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use Omines\DataTablesBundle\DataTable;

final class CreatedAtRigthExprClosure
{
    public function __construct(
        private $searchValue,
        private DataTable $dataTable
    )
    { }
    
    public function __invoke($searchValue, $dataTable)
    {
        if(empty($searchValue)){
            return null;
        }
        $column = $dataTable->getColumnByName('createdAt');
        $baseLeftExpr = $column->getLeftExpr();
        $leftExpr = '';
        $rigthExpr = '';
        $start = trim($searchValue);
        $end = '';
        if(strpos(trim($searchValue), '-')){
            $dates = array_map('trim', explode('-', trim($searchValue)));
            $start = $dates[0];
            $end = $dates[1];
        }
        $startDate = \DateTime::createFromFormat('d/m/Y', $start);
        $endDate = \DateTime::createFromFormat('d/m/Y', $end);

        if($startDate === false && $endDate === false){
            return null;
        }
        if($startDate !== false){
            $rigthExpr .= $start;
        }
        $qb = new ORMQueryBuilder($this->em);
        if($endDate !== false){
            if($startDate !== false){
                $leftExpr .= $baseLeftExpr;
                $leftExpr .= ' ' .$column->getOperator();
                $leftExpr .= ' ' .(string)$qb->expr()->literal($rigthExpr);
                $operator = 'and ' .$baseLeftExpr .' <=';
                $rigthExpr = $start;
                $column->setOption('operator', $operator);
                $column->setOption('leftExpr', $leftExpr);
            }else{
                $column->setOption('operator', '<=');
                $rigthExpr .= $start;
            }
        }

        return $rigthExpr;
    }
}
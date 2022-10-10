<?php

namespace App\DataTables\Column;

use Omines\DataTablesBundle\Column\DateTimeColumn as ColumnDateTimeColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeColumn extends ColumnDateTimeColumn
{
    use ColumnTrait;
}

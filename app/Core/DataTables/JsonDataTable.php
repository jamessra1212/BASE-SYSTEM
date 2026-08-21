<?php

namespace App\Core\DataTables;

use App\Core\DataTables\BaseDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;

abstract class JsonDataTable extends BaseDataTable
{
    abstract public function query(): QueryBuilder;

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return $this->buildTable($query);
    }

    public function make()
    {
        return $this->dataTable(
            $this->query()
        )->toJson();
    }
}

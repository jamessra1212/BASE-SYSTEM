<?php

namespace App\Core\DataTables;


use App\Core\DataTables\Traits\HasColumns;
use App\Core\DataTables\Traits\InteractsWithDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

abstract class BaseDataTable extends DataTable
{
    use InteractsWithDataTable;
    use HasColumns;

    protected string $tableId;

    abstract public function getColumns(): array;

    protected bool $processing = true;
    protected bool $serverSide = true;
    protected bool $responsive = true;

    protected array $filters = [];

    protected function buildTable(QueryBuilder $query): EloquentDataTable
    {
        $table = new EloquentDataTable($query);

        $this->configureDataTable($table);

        $this->configureColumns($table);

        $this->configureFormatting($table);

        $this->configureFilters($table);

        $this->configureOrdering($table);

        return $table;
    }

    protected function configureDataTable(EloquentDataTable $table): void
    {
        $table->setRowId($this->rowId);

        if (! empty($this->rawColumns)) {
            $table->rawColumns($this->rawColumns);
        }

        $table->escapeColumns([]);
    }

    protected function configureFilters(EloquentDataTable $table): void
    {
        //
    }

    protected function configureOrdering(EloquentDataTable $table): void
    {
        //
    }

    public function filter(string $key, mixed $value): static
    {
        $this->filters[$key] = $value;

        return $this;
    }

    protected function getFilter(string $key, mixed $default = null): mixed
    {
        return $this->filters[$key] ?? $default;
    }

}

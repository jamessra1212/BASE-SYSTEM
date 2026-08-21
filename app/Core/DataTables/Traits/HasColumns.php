<?php

namespace App\Core\DataTables\Traits;

use Yajra\DataTables\Html\Column;

trait HasColumns
{
    protected function actionColumn(
        string $title = 'Action',
        int $width = 80,
        string $class = 'text-center'
    ): Column {

        return Column::computed('action')
            ->title($title)
            ->exportable(false)
            ->printable(false)
            ->searchable(false)
            ->orderable(false)
            ->width($width)
            ->addClass($class);
    }
}

<?php

namespace App\Core\DataTables;

use App\Core\Models\Menu;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MenusDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('parent_name', fn (Menu $menu) => $menu->parent?->name ?? '—')
            ->addColumn('status', fn (Menu $menu) => $menu->is_active
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-secondary">Inactive</span>')
            ->addColumn('action', fn (Menu $menu) => view('admin.menus._actions', compact('menu'))->render())
            ->rawColumns(['status', 'action'])
            ->setRowId('id');
    }

    public function query(Menu $model)
    {
        return $model->newQuery()->with('parent')->orderBy('order');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('tblMenus')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(3)
            // ->selectStyleSingle()
            ;
    }

    protected function getColumns(): array
    {
        return [
            Column::make('name'),
            Column::make('parent_name')->title('Parent')->orderable(false)->searchable(false),
            Column::make('route'),
            Column::make('order'),
            Column::make('status')->orderable(false)->searchable(false),
            Column::computed('action')->exportable(false)->printable(false)->width(140)->addClass('text-end'),
        ];
    }

    protected function filename(): string
    {
        return 'Menus_' . date('YmdHis');
    }
}

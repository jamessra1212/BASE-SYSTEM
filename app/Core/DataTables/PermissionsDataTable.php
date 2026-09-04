<?php

namespace App\Core\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PermissionsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('roles_count', fn (Permission $permission) => $permission->roles_count)
            ->addColumn('action', fn (Permission $permission) => view('admin.permissions._actions', compact('permission'))->render())
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    public function query(Permission $model): Builder
    {
        return $model->newQuery()
            ->where('name', 'not like', 'menu.%')
            ->withCount('roles')
            ->orderBy('group')
            ->orderBy('name');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('tblPermissions')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->selectStyleSingle();
    }

    protected function getColumns(): array
    {
        return [
            Column::make('group')->title('Group'),
            Column::make('name'),
            Column::make('roles_count')->title('# Roles')->orderable(false)->searchable(false),
            Column::computed('action')->exportable(false)->printable(false)->width(120)->addClass('text-end'),
        ];
    }

    protected function filename(): string
    {
        return 'Permissions_' . date('YmdHis');
    }
}

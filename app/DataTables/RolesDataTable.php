<?php

namespace App\DataTables;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RolesDataTable extends DataTable
{
    public function dataTable($query)
    {
        return (new EloquentDataTable($query))
            ->addColumn('permissions_count', fn (Role $role) => $role->permissions_count)
            ->addColumn('users_count', fn (Role $role) => $role->users_count)
            ->addColumn('action', fn (Role $role) => view('admin.roles._actions', compact('role'))->render())
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    public function query(Role $model): Builder
    {
        return $model->newQuery()->withCount(['permissions', 'users']);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('tblRoles')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->selectStyleSingle();
    }

    protected function getColumns(): array
    {
        return [
            Column::make('name'),
            Column::make('permissions_count')->title('# Permissions')->orderable(false)->searchable(false),
            Column::make('users_count')->title('# Users')->orderable(false)->searchable(false),
            Column::computed('action')->exportable(false)->printable(false)->width(140)->addClass('text-end'),
        ];
    }

    protected function filename(): string
    {
        return 'Roles_' . date('YmdHis');
    }
}

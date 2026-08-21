<?php

namespace App\DataTables\Menu;

use App\Core\DataTables\BaseDataTable;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;

class MenuDataTable extends BaseDataTable
{

    protected string $tableId = 'tblMenu';

    protected string $rowId = 'id';

    protected string|array|null $ajax = null;

    protected array $language = [
        'search' => 'Search Menu:',
        'zeroRecords' => 'No menu found.',
        'lengthMenu' => '',
        'info' => 'Showing _START_ to _END_ of _TOTAL_ Menus',
        'infoEmpty' => 'No records',
        'paginate' => [
            'next' => 'Next',
            'previous' => 'Previous',
        ],
        'processing' => 'Loading...',
    ];

    protected int $pageLength = 10;
    protected array $lengthMenu = [
        [10, 25, 50],
        ['10', '25', '50']
    ];

    protected array $order = [
        0,
        'asc'
    ];

    protected array $buttons = [
        'print'
    ];

    protected array $rawColumns = [
        'action',
        'submenus',
    ];

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return $this->buildTable($query);
    }

    protected function configureColumns(EloquentDataTable $table): void
    {
        $table
            ->addColumn('submenus', function($data) {
                return view('BackEnd.content.menu.extras.dtSubmenus', [
                    'data' => $data
                ])->render();
            })
            ->addColumn('action', function($row) {
                return view('BackEnd.content.menu.action', [
                    'menu' => $row
                ])->render();
            });
    }

    protected function configureFormatting(EloquentDataTable $table): void
    {
        //
    }

    public function query(Menu $model): QueryBuilder
    {
        return $model->newQuery()->with('submenus');
    }

    public function getColumns(): array
    {
        return [
            Column::make('name')->title('Name'),
            Column::make('route')->title('Route')->orderable(false),
            Column::make('category')->title('Category'),
            Column::computed('submenus')->title('Sub-Menus')->orderable(false),
            $this->actionColumn(
                title: '',
                width: 50,
                class: 'text-center',
            ),
        ];
    }

    protected function filename(): string
    {
        return 'Menu_' . date('YmdHis');
    }
}

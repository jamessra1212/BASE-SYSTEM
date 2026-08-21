<?php

namespace App\DataTables\Menu;

use App\Core\DataTables\JsonDataTable;
use App\Models\Submenu;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;

class SubmenuDataTable extends JsonDataTable
{

    protected string $tableId = 'tblSubMenu';

    protected string $rowId = 'id';

    protected string|array|null $ajax = null;

    protected array $language = [
        'search' => 'Search Submenu:',
        'zeroRecords' => 'No submenu found.',
        'lengthMenu' => '',
        'info' => 'Showing _START_ to _END_ of _TOTAL_ Submenus',
        'infoEmpty' => 'No records',
        'processing' => 'Loading...',
    ];

    protected int $pageLength = 10;
    protected array $lengthMenu = [
        [10, 25, 50],
        ['10', '25', '50']
    ];

    protected array $order = [
        1,
        'asc'
    ];

    protected array $buttons = [
        'print'
    ];

    protected array $rawColumns = [
        'action',
    ];

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return $this->buildTable($query);
    }

    protected function configureColumns(EloquentDataTable $table): void
    {
        $table
            ->addColumn('action', function($row) {
                return view('BackEnd.content.menu.Submenu.su_action', [
                    'menu' => $row
                ])->render();
            });
    }
    protected function configureFormatting(EloquentDataTable $table): void
    {
        //
    }

    public function query(): QueryBuilder
    {
        return Submenu::query()
            ->where(
                'x_menu_id',
                $this->getFilter('menu')
            );
    }

    public function getColumns(): array
    {
        return [
            Column::make('name'),
            Column::make('nav_name'),
            Column::make('route')->orderable(false),
            Column::make('is_nav')->orderable(false),
            Column::make('sort')->orderable(false),
            Column::make('public')->orderable(false),
            $this->actionColumn(
                title: '',
                width: 50,
                class: 'text-center',
            ),
        ];
    }

    protected function filename(): string
    {
        return 'Submenu_' . date('YmdHis');
    }
}

<?php

namespace App\DataTables\Menu;

use App\Models\Submenu;
use App\Traits\HasModernDataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
// use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
// use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SubmenuDataTable extends DataTable
{

use HasModernDataTable;

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Submenu> $query Results from query() method.
     */

    public ?string $slug = null;

    protected string $tableId = 'tblSubMenu';

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function($row) {
                return view('BackEnd.content.menu.Submenu.su_action', [
                    'submenu' => $row
                ])->render();
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Submenu>
     */
    public function query(Submenu $model): QueryBuilder
    {
        return $model->newQuery()
            ->where('x_menu_id', $this->slug);
    }
    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->applyModernHtmlSettings($this->builder())
            ->ajax([
                'url' => route('sida.menu.show', ['slug' => $this->slug ?? '']),
                'type' => 'GET',
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('name')->title('Name'),
            Column::make('nav_name')->title('Navigation Name'),
            Column::make('route')->title('Route'),
            Column::make('is_nav')->title('Is Nav'),
            Column::make('public')->title('Public'),
            Column::computed('action')->title('Action')->addClass('text-center')->width(50),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Submenu_' . date('YmdHis');
    }
}

<?php

namespace App\Core\DataTables\Traits;

use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;

trait InteractsWithDataTable
{

    public function html(): HtmlBuilder
    {
        $builder = $this->builder();

        $this->configureTable($builder);
        $this->configureAjax($builder);
        $this->configureLayout($builder);
        $this->configureLanguage($builder);
        $this->configureButtons($builder);

        return $builder;
    }

    protected function buttons(): array
    {
        return [
            Button::make('excel'),
            Button::make('csv'),
            Button::make('pdf'),
            Button::make('print'),
            Button::make('reset'),
            Button::make('reload'),
        ];
    }

    protected function configureTable(HtmlBuilder $builder): void
    {
        $builder
            ->setTableId($this->tableId)
            ->columns($this->getColumns())
            ->orderBy(
                $this->order[0],
                $this->order[1]
            );
    }

    protected function configureAjax(HtmlBuilder $builder): void
    {
        if ($this->ajax) {
            $builder->minifiedAjax($this->ajax, null, $this->ajaxData);
        } else {
            $builder->minifiedAjax();
        }
    }

    protected function configureLayout(HtmlBuilder $builder): void
    {
        $builder
            ->responsive($this->responsive)
            ->pageLength($this->pageLength)
            ->lengthMenu($this->lengthMenu);
    }

    protected function configureButtons(HtmlBuilder $builder): void
    {
        if (! empty($this->buttons)) {
            $builder->buttons($this->buttons);
        }
    }

    protected function configureLanguage(HtmlBuilder $builder): void
    {
        if (!empty($this->language)) {
            $builder->language($this->language);
        }
    }

}

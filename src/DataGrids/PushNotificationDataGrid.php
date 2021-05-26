<?php

namespace Thecodebunny\PWA\DataGrids;

use Thecodebunny\Ui\DataGrid\DataGrid;
use DB;

/**
 * OrderDataGrid Class
 *
 * @author Thecodebunny Software Pvt. Ltd. <support@thecodebunny.com>
 * @copyright 2018 Thecodebunny Software Pvt Ltd (http://www.thecodebunny.com)
 */
class PushNotificationDataGrid extends DataGrid
{
    protected $index = 'id';            // the column that needs to be treated as index column

    protected $sortOrder = 'desc';      // asc or desc

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('push_notifications')
                ->select('id','title','description','targeturl','imageurl');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('pwa::app.admin.datagrid.id'),
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'title',
            'label' => trans('pwa::app.admin.datagrid.title'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'description',
            'label' => trans('pwa::app.admin.datagrid.description'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => false,
            'filterable' => false
        ]);

        $this->addColumn([
            'index' => 'targeturl',
            'label' => trans('pwa::app.admin.datagrid.target-url'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => false,
            'filterable' => false
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'type' => 'View',
            'route' => 'pwa.pushnotification.edit',
            'icon' => 'icon pencil-lg-icon',
            'method' => 'GET'
        ]);

        $this->addAction([
            'type' => 'Delete',
            'route' => 'pwa.pushnotification.delete',
            'icon' => 'icon trash-icon',
            'method' => 'GET'
        ]);

        $this->addAction([
            'type' => 'send',
            'route' => 'pwa.pushnotification.pushtofirebase',
            'icon' => 'icon bell-icon',
            'method' => 'GET'
        ]);
    }
}
?>
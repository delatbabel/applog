<?php

/**
 * Applog model config -- for viewing log entries
 *
 * @link https://github.com/ddpro/admin/blob/master/docs/model-configuration.md
 */

return [

    'title' => 'Log',

    'single' => 'log',

    'model' => '\Delatbabel\Applog\Models\Applog',

    'server_side' => true,

    /**
     * The display columns
     */
    'columns'     => [
        'id'   => [
            'title' => 'ID',
        ],
        'type'         => [
            'title' => 'Type',
        ],
        'classname'    => [
            'title'    => 'Class Name',
            'sortable' => false,
        ],
        'functionname' => [
            'title'    => 'Function Name',
            'sortable' => false,
        ],
        'linenumber'   => [
            'title'    => 'Line Number',
            'sortable' => false,
        ],
        'message'      => [
            'title'    => 'Message',
            'sortable' => false,
        ],
        'created_at'   => [
            'type'  => 'date',
            'title' => 'Date/Time',
        ],
    ],

    /**
     * The filter set
     */
    'filters'     => [
        'type'       => [
            'title' => 'Type',
        ],
        'classname'  => [
            'title'    => 'Class Name',
            'sortable' => false,
        ],
        'created_at' => [
            'title' => 'Date/Time',
            'type'  => 'datetime',
        ],
    ],

    /**
     * The editable fields
     */
    'edit_fields' => [
        'type'         => [
            'title'    => 'Type',
            'type'     => 'text',
            'editable' => false,
        ],
        'modelname'    => [
            'title'    => 'Model Name',
            'type'     => 'text',
            'editable' => false,
        ],
        'foreign_id'   => [
            'title'    => 'Model ID',
            'type'     => 'number',
            'editable' => false,
        ],
        'classname'    => [
            'title'    => 'Class Name',
            'type'     => 'text',
            'editable' => false,
        ],
        'traitname'    => [
            'title'    => 'Trait Name',
            'type'     => 'text',
            'editable' => false,
        ],
        'functionname' => [
            'title'    => 'Function Name',
            'type'     => 'text',
            'editable' => false,
        ],
        'filename'     => [
            'title'    => 'File Name',
            'type'     => 'text',
            'editable' => false,
        ],
        'linenumber'   => [
            'title'    => 'Line Number',
            'type'     => 'text',
            'editable' => false,
        ],
        'ipaddr'       => [
            'title'    => 'IP Address',
            'type'     => 'text',
            'editable' => false,
        ],
        'message'      => [
            'title'    => 'Message',
            'type'     => 'text',
            'editable' => false,
        ],
        'details'      => [
            'title'    => 'Message',
            'type'     => 'textarea',
            'editable' => false,
        ],
        'created_by'   => [
            'title'    => 'User Name',
            'type'     => 'text',
            'editable' => false,
        ],
        'created_at'   => [
            'title'    => 'Date/Time',
            'type'     => 'datetime',
            'editable' => false,
        ],
    ],

    'action_permissions' => [
        'create' => '\Delatbabel\Applog\Helpers\ApplogHelper::detectCreatePermission',
        'update' => '\Delatbabel\Applog\Helpers\ApplogHelper::detectUpdatePermission',
        'delete' => '\Delatbabel\Applog\Helpers\ApplogHelper::detectDeletePermission',
    ],

    'form_width' => 400,
];

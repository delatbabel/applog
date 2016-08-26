<?php

/**
 * Applog model config -- for viewing log entries
 *
 * @link https://github.com/ddpro/admin/blob/master/docs/model-configuration.md
 */

return array(

    'title' => 'Log',

    'single' => 'log',

    'model' => '\Delatbabel\Applog\Models\Applog',

    /**
     * The display columns
     */
    'columns' => array(
        'id',
        'type' => array(
            'title' => 'Type',
        ),
        'classname' => array(
            'title' => 'Class Name',
            'sortable' => false,
        ),
        'functionname' => array(
            'title' => 'Function Name',
            'sortable' => false,
        ),
        'linenumber' => array(
            'title' => 'Line Number',
            'sortable' => false,
        ),
        'message' => array(
            'title' => 'Message',
            'sortable' => false,
        ),
        'created_at' => array(
            'title' => 'Date/Time',
        ),
    ),

    /**
     * The filter set
     */
    'filters' => array(
        'type' => array(
            'title' => 'Type',
        ),
        'classname' => array(
            'title' => 'Class Name',
            'sortable' => false,
        ),
        'created_at' => array(
            'title' => 'Date/Time',
            'type' => 'datetime',
        ),
    ),

    /**
     * The editable fields
     */
    'edit_fields' => array(
        'type' => array(
            'title' => 'Type',
            'type' => 'text',
            'editable' => false,
        ),
        'model' => array(
            'title' => 'Model Name',
            'type' => 'text',
            'editable' => false,
        ),
        'foreign_id' => array(
            'title' => 'Model ID',
            'type' => 'number',
            'editable' => false,
        ),
        'name' => array(
            'title' => 'Name',
            'type' => 'text',
            'editable' => false,
        ),
        'classname' => array(
            'title' => 'Class Name',
            'type' => 'text',
            'editable' => false,
        ),
        'traitname' => array(
            'title' => 'Trait Name',
            'type' => 'text',
            'editable' => false,
        ),
        'functionname' => array(
            'title' => 'Function Name',
            'type' => 'text',
            'editable' => false,
        ),
        'filename' => array(
            'title' => 'File Name',
            'type' => 'text',
            'editable' => false,
        ),
        'linenumber' => array(
            'title' => 'Line Number',
            'type' => 'text',
            'editable' => false,
        ),
        'ipaddr' => array(
            'title' => 'IP Address',
            'type' => 'text',
            'editable' => false,
        ),
        'message' => array(
            'title' => 'Message',
            'type' => 'text',
            'editable' => false,
        ),
        'details' => array(
            'title' => 'Message',
            'type' => 'textarea',
            'editable' => false,
        ),
        'created_by' => array(
            'title' => 'User Name',
            'type' => 'text',
            'editable' => false,
        ),
        'created_at' => array(
            'title' => 'Date/Time',
            'type' => 'datetime',
            'editable' => false,
        ),
    ),

    'action_permissions' => array(
        'create' => function($model) { return false; },
        'update' => function($model) { return false; },
        'delete' => function($model) { return false; },
    ),

    'form_width' => 400,
);

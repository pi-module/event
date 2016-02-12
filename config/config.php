<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return array(
    'category' => array(
        array(
            'title' => _a('Admin'),
            'name' => 'admin'
        ),
        array(
            'title' => _a('View'),
            'name' => 'view'
        ),
        array(
            'title' => _a('Manage'),
            'name' => 'manage'
        ),
        array(
            'title' => _a('Order'),
            'name' => 'order'
        ),
    ),
    'item' => array(
        // Admin
        'admin_perpage' => array(
            'category' => 'admin',
            'title' => _a('Perpage'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 10
        ),
        // View
        'view_perpage' => array(
            'category' => 'view',
            'title' => _a('Perpage'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 10
        ),
        // Manage
        'manage_active' => array(
            'category' => 'manage',
            'title' => _a('Manage event by users'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'manage_approval' => array(
            'category' => 'manage',
            'title' => _a('Auto approval user events'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // Order
        'order_active' => array(
            'category' => 'order',
            'title' => _a('Active order'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
    ),
);
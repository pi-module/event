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
    // Front section
    'front' => array(
        'manage' => array(
            'label' => _a('Manage events'),
            'permission' => array(
                'resource' => 'manage',
            ),
            'route' => 'event',
            'module' => 'event',
            'controller' => 'manage',
        ),
    ),
    // Admin section
    'admin' => array(
        'event' => array(
            'label' => _a('Event'),
            'permission' => array(
                'resource' => 'event',
            ),
            'route' => 'admin',
            'module' => 'event',
            'controller' => 'event',
            'action' => 'index',
            'pages' => array(
                'event' => array(
                    'label' => _a('Event'),
                    'permission' => array(
                        'resource' => 'event',
                    ),
                    'route' => 'admin',
                    'module' => 'event',
                    'controller' => 'event',
                    'action' => 'index',
                ),
                'update' => array(
                    'label' => _a('New event'),
                    'permission' => array(
                        'resource' => 'event',
                    ),
                    'route' => 'admin',
                    'module' => 'event',
                    'controller' => 'event',
                    'action' => 'update',
                ),
            ),
        ),
        'order' => array(
            'label' => _a('List of order'),
            'permission' => array(
                'resource' => 'order',
            ),
            'route' => 'admin',
            'module' => 'event',
            'controller' => 'order',
            'action' => 'index',
        ),
        'tools' => array(
            'label' => _a('Tools'),
            'permission' => array(
                'resource' => 'tools',
            ),
            'route' => 'admin',
            'module' => 'event',
            'controller' => 'tools',
            'action' => 'index',
        ),
    ),
);
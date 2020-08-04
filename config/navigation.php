<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return [
    // Front section
    'front' => [
        'manage' => [
            'label'      => _a('Manage events'),
            'permission' => [
                'resource' => 'manage',
            ],
            'route'      => 'event',
            'module'     => 'event',
            'controller' => 'manage',
        ],
    ],
    // Admin section
    'admin' => [
        'event' => [
            'label'      => _a('Event'),
            'permission' => [
                'resource' => 'event',
            ],
            'route'      => 'admin',
            'module'     => 'event',
            'controller' => 'event',
            'action'     => 'index',
            'pages'      => [
                'event'  => [
                    'label'      => _a('All Events'),
                    'permission' => [
                        'resource' => 'event',
                    ],
                    'route'      => 'admin',
                    'module'     => 'event',
                    'controller' => 'event',
                    'action'     => 'index',
                ],
                'update' => [
                    'label'      => _a('New event'),
                    'permission' => [
                        'resource' => 'event',
                    ],
                    'route'      => 'admin',
                    'module'     => 'event',
                    'controller' => 'event',
                    'action'     => 'update',
                ],
            ],
        ],
        'order' => [
            'label'      => _a('List of order'),
            'permission' => [
                'resource' => 'order',
            ],
            'route'      => 'admin',
            'module'     => 'event',
            'controller' => 'order',
            'action'     => 'index',
        ],
        'tools' => [
            'label'      => _a('Tools'),
            'permission' => [
                'resource' => 'tools',
            ],
            'route'      => 'admin',
            'module'     => 'event',
            'controller' => 'tools',
            'action'     => 'index',
        ],
    ],
];

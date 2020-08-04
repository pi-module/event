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
        [
            'title'      => _a('Index page'),
            'controller' => 'index',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Category'),
            'controller' => 'category',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Event detail'),
            'controller' => 'detail',
            'permission' => 'public',
            'block'      => 1,
        ],
        [
            'title'      => _a('Manage'),
            'controller' => 'manage',
            'permission' => 'manage',
            'block'      => 1,
        ],
        [
            'title'      => _a('Register'),
            'controller' => 'register',
            'permission' => 'public',
            'block'      => 0,
        ],
        [
            'label'      => _a('Json output'),
            'controller' => 'json',
            'permission' => 'public',
            'block'      => 0,
        ],
    ],
    // Admin section
    'admin' => [
        [
            'title'      => _a('Event'),
            'controller' => 'event',
            'permission' => 'event',
        ],
        [
            'title'      => _a('List of order'),
            'controller' => 'order',
            'permission' => 'order',
        ],
        [
            'title'      => _a('Tools'),
            'controller' => 'tools',
            'permission' => 'tools',
        ],
    ],
];

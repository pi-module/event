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
return [
    // Front section
    'front' => [
        'public' => [
            'title'  => _a('Global public resource'),
            'access' => [
                'guest',
                'member',
            ],
        ],
        'manage' => [
            'title'  => _a('Manage'),
            'access' => [
                'member',
            ],
        ],
    ],
    // Admin section
    'admin' => [
        'event' => [
            'title'  => _a('Event'),
            'access' => [],
        ],
        'order' => [
            'title'  => _a('List of order'),
            'access' => [],
        ],
        'tools' => [
            'title'  => _a('Tools'),
            'access' => [],
        ],
    ],
];
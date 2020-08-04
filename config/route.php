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
    // route name
    'event' => [
        'name'    => 'event',
        'type'    => 'Module\Event\Route\Event',
        'options' => [
            'route'    => '/event',
            'defaults' => [
                'module'     => 'event',
                'controller' => 'index',
                'action'     => 'index',
            ],
        ],
    ],
];

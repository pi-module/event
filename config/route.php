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
    // route name
    'event' => array(
        'name' => 'event',
        'type' => 'Module\Event\Route\Event',
        'options' => array(
            'route' => '/event',
            'defaults' => array(
                'module' => 'event',
                'controller' => 'index',
                'action' => 'index'
            )
        ),
    )
);
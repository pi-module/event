<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return array(
    // Front section
    'front' => array(
        array(
            'title' => _a('Index page'),
            'controller' => 'index',
            'permission' => 'public',
            'block' => 1,
        ),
        array(
            'title' => _a('Category'),
            'controller' => 'category',
            'permission' => 'public',
            'block' => 1,
        ),
        array(
            'title' => _a('Event detail'),
            'controller' => 'detail',
            'permission' => 'public',
            'block' => 1,
        ),
        array(
            'title' => _a('Manage'),
            'controller' => 'manage',
            'permission' => 'manage',
            'block' => 1,
        ),
        array(
            'title' => _a('Register'),
            'controller' => 'register',
            'permission' => 'public',
            'block' => 0,
        ),
        array(
            'label' => _a('Json output'),
            'controller' => 'json',
            'permission' => 'public',
            'block' => 0,
        ),
    ),
    // Admin section
    'admin' => array(
        array(
            'title' => _a('Event'),
            'controller' => 'event',
            'permission' => 'event',
        ),
        array(
            'title' => _a('List of order'),
            'controller' => 'order',
            'permission' => 'order',
        ),
        array(
            'title' => _a('Tools'),
            'controller' => 'tools',
            'permission' => 'tools',
        ),
    ),
);
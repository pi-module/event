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
    // Module meta
    'meta'       => [
        'title'       => _a('Event'),
        'description' => _a('Manage events'),
        'version'     => '2.1.0',
        'license'     => 'New BSD',
        'logo'        => 'image/logo.png',
        'readme'      => 'docs/readme.txt',
        'demo'        => 'http://pialog',
        'icon'        => 'fa-calendar',
    ],
    'dependency' => [
        'news',
        'media',
    ],
    // Author information
    'author'     => [
        'Name'    => 'Hossein Azizabadi',
        'email'   => 'azizabadi@faragostaresh.com',
        'website' => 'http://pialog',
        'credits' => 'Pi Engine Team',
    ],
    // Resource
    'resource'   => [
        'database'   => 'database.php',
        'config'     => 'config.php',
        'permission' => 'permission.php',
        'page'       => 'page.php',
        'navigation' => 'navigation.php',
        'block'      => 'block.php',
        'route'      => 'route.php',
        'comment'    => 'comment.php',
    ],
];
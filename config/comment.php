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
    'event' => [
        'title'    => _a('Event comments'),
        'icon'     => 'icon-post',
        'callback' => 'Module\Event\Api\Comment',
        'locator'  => 'Module\Event\Api\Comment',
    ],
];

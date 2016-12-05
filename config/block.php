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
    'recent-event' => array(
        'title' => _a('Recent events'),
        'description' => _a('Recent events list'),
        'render' => array('block', 'recentEvent'),
        'template' => 'recent-event',
        'config' => array(
            'topic-id' => array(
                'title' => _a('Category'),
                'description' => '',
                'edit' => array(
                    'type' => 'Module\News\Form\Element\Topic',
                    'options' => array(
                        'type' => 'event',
                    ),
                ),
                'filter' => 'string',
                'value' => 0,
            ),
            'number' => array(
                'title' => _a('Number'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'number_int',
                'value' => 5,
            ),
            'show-time' => array(
                'title' => _a('Show time'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'show-topic' => array(
                'title' => _a('Show category'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'show-price' => array(
                'title' => _a('Show price'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'show-hits' => array(
                'title' => _a('Show hits'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'show-summary' => array(
                'title' => _a('Show summary'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
            'list-type' => array(
                'title' => _a('Event list type'),
                'description' => '',
                'edit' => array(
                    'type' => 'select',
                    'options' => array(
                        'options' => array(
                            'vertical' => _a('Vertical'),
                            'horizontal' => _a('Horizontal'),
                        ),
                    ),
                ),
                'filter' => 'text',
                'value' => 'vertical',
            ),
            'show-morelink' => array(
                'title' => _a('Show more link'),
                'description' => '',
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 0,
            ),
            'link-blockmore' => array(
                'title' => _a('Set more link'),
                'description' => '',
                'edit' => 'text',
                'filter' => 'string',
                'value' => '',
            ),
            'block-effect' => array(
                'title' => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit' => 'checkbox',
                'filter' => 'number_int',
                'value' => 1,
            ),
        ),
    ),
);
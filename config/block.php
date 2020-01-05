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
    'recent-event' => [
        'title'       => _a('Recent events'),
        'description' => _a('Recent events list'),
        'render'      => ['block', 'recentEvent'],
        'template'    => 'recent-event',
        'config'      => [
            'topic-id'       => [
                'title'       => _a('Category'),
                'description' => '',
                'edit'        => [
                    'type'    => 'Module\News\Form\Element\Topic',
                    'options' => [
                        'type' => 'event',
                    ],
                ],
                'filter'      => 'string',
                'value'       => 0,
            ],
            'number'         => [
                'title'       => _a('Number'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'number_int',
                'value'       => 5,
            ],
            'show-time'      => [
                'title'       => _a('Show time'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'show-topic'     => [
                'title'       => _a('Show category'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'show-price'     => [
                'title'       => _a('Show price'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'show-hits'      => [
                'title'       => _a('Show hits'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'show-summary'   => [
                'title'       => _a('Show summary'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'show-days'      => [
                'title'       => _a('Show days'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
            'list-type'      => [
                'title'       => _a('Event list type'),
                'description' => '',
                'edit'        => [
                    'type'    => 'select',
                    'options' => [
                        'options' => [
                            'vertical'   => _a('Vertical'),
                            'horizontal' => _a('Horizontal'),
                            'table'      => _a('Table'),
                        ],
                    ],
                ],
                'filter'      => 'text',
                'value'       => 'vertical',
            ],
            'show-morelink'  => [
                'title'       => _a('Show more link'),
                'description' => '',
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 0,
            ],
            'link-blockmore' => [
                'title'       => _a('Set more link'),
                'description' => '',
                'edit'        => 'text',
                'filter'      => 'string',
                'value'       => '',
            ],
            'block-effect'   => [
                'title'       => _a('Use block effects'),
                'description' => _a('Use block effects or set custom effect on theme'),
                'edit'        => 'checkbox',
                'filter'      => 'number_int',
                'value'       => 1,
            ],
        ],
    ],
];
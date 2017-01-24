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
    'category' => array(
        array(
            'title' => _a('Admin'),
            'name' => 'admin'
        ),
        array(
            'title' => _a('News module'),
            'name' => 'news'
        ),
        array(
            'title' => _a('Guide module'),
            'name' => 'guide'
        ),

        array(
            'title' => _a('View'),
            'name' => 'view'
        ),
        array(
            'title' => _a('Social'),
            'name' => 'social'
        ),
        array(
            'title' => _a('Manage'),
            'name' => 'manage'
        ),
        array(
            'title' => _a('Order'),
            'name' => 'order'
        ),
    ),
    'item' => array(
        // Admin
        'admin_perpage' => array(
            'category' => 'admin',
            'title' => _a('Perpage'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 10
        ),
        // News
        'use_news_topic' => array(
            'category' => 'news',
            'title' => _a('Use news topics'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // Guide
        'use_guide_category' => array(
            'category' => 'guide',
            'title' => _a('Use guide category'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'use_guide_location' => array(
            'category' => 'guide',
            'title' => _a('Use guide location'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'filter_location_level' => array(
            'title' => _a('Select location level for show on filter form'),
            'description' => '',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        1 => _a('level 1'),
                        2 => _a('level 2'),
                        3 => _a('level 3'),
                        4 => _a('level 4'),
                        5 => _a('level 5'),
                    ),
                ),
            ),
            'filter' => 'number_int',
            'value' => 1,
            'category' => 'guide',
        ),
        // View
        'text_summary_index' => array(
            'category' => 'view',
            'title' => _a('Summary for index page'),
            'description' => '',
            'edit' => 'textarea',
            'filter' => 'string',
            'value' => ''
        ),

        'text_description_index' => array(
            'category' => 'view',
            'title' => _a('Description for index page'),
            'description' => '',
            'edit' => 'textarea',
            'filter' => 'string',
            'value' => ''
        ),

        'wide_content' => array(
            'category' => 'view',
            'title' => _a('Active wide front image for this module'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 0
        ),

        'view_list_type' => array(
            'title' => _a('Event list type'),
            'description' => '',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'line' => _a('Line : each event on one line'),
                        'box' => _a('Box : each event as box and 3 box on one line'),
                    ),
                ),
            ),
            'filter' => 'text',
            'value' => 'line',
            'category' => 'view',
        ),
        'view_perpage' => array(
            'category' => 'view',
            'title' => _a('Perpage'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 10
        ),
        'related_event' => array(
            'category' => 'view',
            'title' => _a('Show related event'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'related_event_type' => array(
            'title' => _a('Related event type'),
            'description' => '',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'event' => _a('By event module topics'),
                        'guide' => _a('By guide module categories'),
                    ),
                ),
            ),
            'filter' => 'text',
            'value' => 'event',
            'category' => 'view',
        ),
        'image_homepage' => array(
            'category' => 'view',
            'title' => _a('Set wide image for homepage'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => ''
        ),

        'price_filter' => array(
            'category' => 'view',
            'title' => _a('Price filter'),
            'description' => _a('Input method : 100-200,Between 100 to 200|200-300,Between 200 to 300|'),
            'edit' => 'textarea',
            'filter' => 'string',
            'value' => ''
        ),
        // Social
        'social_sharing' => array(
            'title' => _t('Social sharing items'),
            'description' => '',
            'edit' => array(
                'type' => 'multi_checkbox',
                'options' => array(
                    'options' => Pi::service('social_sharing')->getList(),
                ),
            ),
            'filter' => 'array',
            'category' => 'social',
        ),
        // Manage
        'manage_active' => array(
            'category' => 'manage',
            'title' => _a('Manage event by users'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'manage_approval' => array(
            'category' => 'manage',
            'title' => _a('Auto approval user events'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // Order
        'order_active' => array(
            'category' => 'order',
            'title' => _a('Active order'),
            'description' => _a('Manage event registration, ticket stock and activate orders'),
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'order_free' => array(
            'category' => 'order',
            'title' => _a('Show free word for free events'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'order_discount' => array(
            'category' => 'order',
            'title' => _a('Active discount system'),
            'description' => _a('Discount percent for each rule on each event'),
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
    ),
);
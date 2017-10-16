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
        array(
            'title' => _a('Vote'),
            'name' => 'vote'
        ),
        array(
            'title' => _a('Favourite'),
            'name' => 'favourite'
        ),
        array(
            'title'  => _a('Google Map'),
            'name'   => 'map'
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
        'generate_slug' => array(
            'category' => 'admin',
            'title' => _a('Generate slug automatic'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 0
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
        'view_template' => array(
            'title' => _a('View template'),
            'description' =>  _a('Temporary config to finish new angular js template'),
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'angular' => _a('Angular'),
                        'angularnew' => _a('New angular'),
                    ),
                ),
            ),
            'filter' => 'text',
            'value' => 'angular',
            'category' => 'view',
        ),
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
       'image_index' => array(
            'category' => 'view',
            'title' => _a('Set wide image for homepage'),
            'description' => sprintf(__("First make <strong>width</strong> directory on <strong>%s</strong> path, and you need upload images by ftp on it and put main image name on this field, for example if you input <strong>header.jpg</strong> on this field images size and name should be : 
                <br> mobi-header.jpg => width: 479px 
                <br> smart-header.jpg => width: 767px 
                <br> tab-header.jpg => width: 991px 
                <br> lap-header.jpg => width: 1366px 
                <br> xlap-header.jpg => width: 1920px 
                <br> desk-header.jpg => width: 2880px"), Pi::path('upload/event/image/width/index')),
            'edit' => 'text',
            'filter' => 'string',
            'value' => ''
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
                        'table' => _a('Table'),
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
            'title' => _a('OG/Twitter image URL for Homepage'),
            'description' => 'Used in meta (second choice)',
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
        'view_days' => array(
            'category' => 'view',
            'title' => _a('Show days'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'important_organizer' => array(
            'category' => 'view',
            'title' => _a('Make organizer bold'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 0
        ),
        'image_position' => array(
            'title' => _a('Image position'),
            'description' => _a('Image position on single event page'),
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'side' => _a('Show image on sidebar'),
                        'center' => _a('Show image on center'),
                    ),
                ),
            ),
            'filter' => 'text',
            'value' => 'side',
            'category' => 'view',
        ),
        'event_all_hits' => array(
            'category' => 'view',
            'title' => _a('Include all hits'),
            'description' => _a('Include all page refresh as hits, if not check use SESSION check for update hits'),
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 0
        ),
        'view_summary' => array(
            'category' => 'view',
            'title' => _a('Show summary on event page'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1,
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
        'manage_role' => array(
            'category' => 'manage',
            'title' => _a('Allowed user role to access'),
            'description' => _a('Put allowed role here, this option used when guide module not installed'),
            'edit' => 'text',
            'filter' => 'string',
            'value' => 'member'
        ),
        'manage_register' => array(
            'category' => 'manage',
            'title' => _a('Manage register options'),
            'description' => _a('Manage register options by user side. If yes, those extra fields will showed on user form : Address and Registration details/requirements'),
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
        // Vote
        'vote_bar' => array(
            'category' => 'vote',
            'title' => _a('Use vote system'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // favourite
        'favourite_bar' => array(
            'category' => 'favourite',
            'title' => _a('Use favourite system'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // Map
        'map_use' => array(
            'title'        => _a('Use Google Map'),
            'description'  => '',
            'value'        => 0,
            'filter'       => 'number_int',
            'edit'         => 'checkbox',
            'category'     => 'map',
        ),
        /* 'map_position' => array(
            'title'        => _a('Position'),
            'description'  => ' ',
            'edit'         => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'bottom' => _a('Bottom'),
                        'side' => _a('Side'),
                        'wide' => _a('Wide on top'),
                    ),
                ),
            ),
            'filter'       => 'string',
            'value'        => 'bottom',
            'category'     => 'map',
        ), */
        'map_zoom' => array(
            'title'        => _a('Zoom'),
            'description'  => '',
            'edit'         => 'text',
            'filter'       => 'number_int',
            'value'        => 15,
            'category'     => 'map',
        ),
        'map_api_key' => array(
            'category' => 'map',
            'title' => _a('Set Google map API KEY'),
            'description' => _a('For obtaining an API Key please read this document : https://developers.google.com/maps/documentation/javascript/tutorial#api_key'),
            'edit' => 'text',
            'filter' => 'string',
            'value' => ''
        ),
        'map_type' => array(
            'title' => _a('Map type'),
            'description' => '',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'ROADMAP' => _a('ROADMAP'),
                        'SATELLITE' => _a('SATELLITE'),
                        'HYBRID' => _a('HYBRID'),
                        'TERRAIN' => _a('TERRAIN'),
                    ),
                ),
            ),
            'filter' => 'text',
            'value' => 'ROADMAP',
            'category' => 'map',
        ),
    ),
);

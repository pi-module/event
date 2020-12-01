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

namespace Module\Event\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class EventForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        $this->option = $option;
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new EventFilter($this->option);
        }
        return $this->filter;
    }

    public function init()
    {
        // title
        $this->add(
            [
                'name'       => 'title',
                'options'    => [
                    'label' => __('Title'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                    'required'    => true,
                ],
            ]
        );

        // subtitle
        $this->add(
            [
                'name'       => 'subtitle',
                'options'    => [
                    'label' => __('Subtitle'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );

        // slug
        if ($this->option['side'] == 'admin') {
            $this->add(
                [
                    'name'       => 'slug',
                    'options'    => [
                        'label' => __('slug'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => '',
                    ],
                ]
            );
        } else {
            $this->add(
                [
                    'name'       => 'slug',
                    'attributes' => [
                        'type' => 'hidden',
                    ],
                ]
            );
        }

        // extra
        $this->add(
            [
                'name'    => 'extra_time',
                'type'    => 'fieldset',
                'options' => [
                    'label' => __('Time options'),
                ],
            ]
        );

        // Check local
        $local = Pi::service('i18n')->getLocale();
        if ($local == 'fa') {
            // time_start_view
            $this->add(
                [
                    'name'       => 'time_start_moment',
                    'options'    => [
                        'label' => __('Time start'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => '',
                        'required'    => true,
                        'class'       => 'event-time-start-view',
                    ],
                ]
            );
            // time_end_view
            $this->add(
                [
                    'name'       => 'time_end_moment',
                    'options'    => [
                        'label' => __('Time end'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => '',
                        'required'    => false,
                        'class'       => 'event-time-end-view',
                    ],
                ]
            );
            // time_start
            $this->add(
                [
                    'name'       => 'time_start',
                    'attributes' => [
                        'type'  => 'hidden',
                        'class' => 'event-time-start',
                    ],
                ]
            );
            // time_end
            $this->add(
                [
                    'name'       => 'time_end',
                    'attributes' => [
                        'type'  => 'hidden',
                        'class' => 'event-time-end',
                    ],
                ]
            );
        } else {
            // time_start
            $this->add(
                [
                    'name'       => 'time_start',
                    'type'       => 'datepicker',
                    'options'    => [
                        'label'      => __('Time start'),
                        'datepicker' => [
                            'format'         => 'yyyy/mm/dd',
                            'autoclose'      => true,
                            'todayBtn'       => true,
                            'todayHighlight' => true,
                            'weekStart'      => 1,
                            'zIndexOffset'   => 10000,

                        ],
                    ],
                    'attributes' => [
                        'required' => true,
                        'value'    => date('Y-m-d'),
                        'class'    => 'event-time-start',
                    ],
                ]
            );
            // time_end
            $this->add(
                [
                    'name'       => 'time_end',
                    'type'       => 'datepicker',
                    'options'    => [
                        'label'      => __('Time end'),
                        'datepicker' => [
                            'format'         => 'yyyy/mm/dd',
                            'autoclose'      => true,
                            'todayBtn'       => true,
                            'todayHighlight' => true,
                            'weekStart'      => 1,
                            'zIndexOffset'   => 10000,

                        ],
                    ],
                    'attributes' => [
                        'required' => false,
                        'class'    => 'event-time-end',
                    ],
                ]
            );
        }

        // extra
        $this->add(
            [
                'name'    => 'extra_text',
                'type'    => 'fieldset',
                'options' => [
                    'label' => __('Description options'),
                ],
            ]
        );

        // text_summary
        $this->add(
            [
                'name'       => 'text_summary',
                'options'    => [
                    'label' => __('Summary'),
                ],
                'attributes' => [
                    'type'        => 'textarea',
                    'rows'        => '3',
                    'cols'        => '40',
                    'description' => '',
                    'required'    => $this->option['side'] == 'admin' ? false : true,

                ],
            ]
        );

        // text_description
        $this->add(
            [
                'name'       => 'text_description',
                'options'    => [
                    'label'  => __('Description'),
                    'editor' => 'html',
                ],
                'attributes' => [
                    'type'        => 'editor',
                    'description' => '',
                    'required'    => $this->option['side'] == 'admin' ? false : true,

                ],
            ]
        );

        // extra_main
        $this->add(
            [
                'name'    => 'extra_main',
                'type'    => 'fieldset',
                'options' => [
                    'label' => __('Main options'),
                ],
            ]
        );

        // status
        if ($this->option['side'] == 'admin') {
            $this->add(
                [
                    'name'       => 'status',
                    'type'       => 'select',
                    'options'    => [
                        'label'         => __('Status'),
                        'value_options' => [
                            1 => __('Published'),
                            2 => __('Pending review'),
                            3 => __('Draft'),
                            4 => __('Private'),
                            5 => __('Delete'),
                        ],
                    ],
                    'attributes' => [
                        'required' => true,
                    ],
                ]
            );
        }

        // main_image
        $this->add(
            [
                'name'       => 'main_image',
                'type'       => 'Module\Media\Form\Element\Media',
                'options'    => [
                    'label'  => __('Main image'),
                    'module' => 'news',
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]
        );

        // address
        if ($this->option['side'] == 'admin' || ($this->option['side'] == 'front' && $this->option['manage_register'])) {
            $this->add(
                [
                    'name'       => 'address',
                    'options'    => [
                        'label' => __('Address'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => '',
                    ],
                ]
            );
        }

        // organizer_name
        $this->add(
            [
                'name'       => 'organizer_name',
                'options'    => [
                    'label' => __('Organizer name'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );

        // source_url
        $this->add(
            [
                'name'       => 'source_url',
                'options'    => [
                    'label' => __('Organizer Website'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );

        // offer_url
        $this->add(
            [
                'name'       => 'offer_url',
                'options'    => [
                    'label' => __('Offer url'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );

        // register
        $this->add(
            [
                'name'    => 'extra_register',
                'type'    => 'fieldset',
                'options' => [
                    'label' => __('Registration options'),
                ],
            ]
        );

        // register_details
        if ($this->option['side'] == 'admin' || ($this->option['side'] == 'front' && $this->option['manage_register'])) {
            $this->add(
                [
                    'name'       => 'register_details',
                    'options'    => [
                        'label'  => __('Registration details/requirements'),
                        'editor' => 'html',
                    ],
                    'attributes' => [
                        'type'        => 'editor',
                        'description' => '',
                    ],
                ]
            );
        }

        // Check order active
        if ($this->option['order_active']) {
            // register_can
            $this->add(
                [
                    'name'       => 'register_can',
                    'type'       => 'checkbox',
                    'options'    => [
                        'label' => __('Register online?'),
                    ],
                    'attributes' => [
                        'description' => __('User can register directly on the website and proceed to checkout, real availability is displayed'),
                    ],
                ]
            );
        }

        if ($this->option['order_active']) {
            // register_stock
            $this->add(
                [
                    'name'       => 'register_stock',
                    'options'    => [
                        'label' => __('Capacity'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'required'    => true,
                        'description' => __('0 for unlimited'),
                    ],
                ]
            );
        }

        // register_price
        $this->add(
            [
                'name'       => 'register_price',
                'options'    => [
                    'label' => __('Minimum Price'),
                ],
                'attributes' => [
                    'type'        => 'text',
                    'description' => '',
                ],
            ]
        );

        // order_discount
        if ($this->option['order_active'] && isset($this->option['order_discount']) && $this->option['order_discount']) {

            // extra_product
            $this->add(
                [
                    'name'    => 'extra_discount',
                    'type'    => 'fieldset',
                    'options' => [
                        'label' => __('Discount options'),
                    ],
                ]
            );
            // Get role list
            $roles = Pi::service('registry')->Role->read('front');
            unset($roles['webmaster']);
            unset($roles['guest']);
            foreach ($roles as $name => $role) {
                $this->add(
                    [
                        'name'       => $name,
                        'options'    => [
                            'label' => __('Rebate rate for') . ' ' . $role['title'],
                        ],
                        'attributes' => [
                            'type'        => 'text',
                            'description' => __('Number and between 1 to 99'),
                        ],
                    ]
                );
            }
        }

        // Check topic
        if ($this->option['use_news_topic']) {
            $this->add(
                [
                    'name'    => 'extra_topic',
                    'type'    => 'fieldset',
                    'options' => [
                        'label' => __('Topic options'),
                    ],
                ]
            );
            // topic
            $this->add(
                [
                    'name'    => 'topic',
                    'type'    => 'Module\News\Form\Element\Topic',
                    'options' => [
                        'label'    => __('Topic'),
                        'required' => true,
                        'topic'    => 'full',
                        'type'     => 'event',
                    ],
                ]
            );
            // topic_main
            $this->add(
                [
                    'name'       => 'topic_main',
                    'type'       => 'Module\News\Form\Element\Topic',
                    'options'    => [
                        'label'    => __('Main topic'),
                        'required' => true,
                        'topic'    => '',
                        'type'     => 'event',
                    ],
                    'attributes' => [
                        'required'    => true,
                        'size'        => 1,
                        'multiple'    => 0,
                        'description' => __('Just use for breadcrumbs and mobile apps'),
                    ],
                ]
            );
        }

        // extra guide
        if (Pi::service('module')->isActive('guide') && $this->option['side'] == 'admin') {
            $this->add(
                [
                    'name'    => 'extra_guide',
                    'type'    => 'fieldset',
                    'options' => [
                        'label' => __('Guide options'),
                    ],
                ]
            );
            // guide_owner
            if ($this->option['side'] == 'admin') {
                $this->add(
                    [
                        'name'    => 'guide_owner',
                        'type'    => 'Module\Guide\Form\Element\Owner',
                        'options' => [
                            'label'  => __('Owner'),
                            'module' => 'guide',
                            'owner'  => true,
                        ],
                    ]
                );
            }
            // guide_category
            if ($this->option['use_guide_category']) {
                $this->add(
                    [
                        'name'       => 'guide_category',
                        'type'       => 'Module\Guide\Form\Element\Category',
                        'options'    => [
                            'label'    => __('Category'),
                            'category' => [0 => ''],
                            'module'   => 'guide',
                            'type'     => 'external',
                            'status'   => 1,
                        ],
                        'attributes' => [
                            'size'     => 5,
                            'multiple' => 1,
                        ],
                    ]
                );
            }
            // guide_location
            if ($this->option['use_guide_location']) {
                $this->add(
                    [
                        'name'       => 'guide_location',
                        'type'       => 'Module\Guide\Form\Element\Location',
                        'options'    => [
                            'label'  => __('Location'),
                            'module' => 'guide',
                            'type'   => 'external',
                            'status' => 1,
                        ],
                        'attributes' => [
                            'size'     => 5,
                            'multiple' => 1,
                        ],
                    ]
                );
            }
            // guide_item
            if (!isset($this->option['item']) || !$this->option['item']) {
                $this->add(
                    [
                        'name'       => 'guide_item',
                        'type'       => 'Module\Guide\Form\Element\Item',
                        'options'    => [
                            'label'  => __('Item'),
                            'module' => 'guide',
                            'owner'  => isset($this->option['owner']) ? $this->option['owner'] : '',
                            'status' => 1,
                        ],
                        'attributes' => [
                            'size'     => 5,
                            'multiple' => 1,
                        ],
                    ]
                );
            }
        }

        // Check order active
        if ($this->option['map_use']) {
            $this->add(
                [
                    'name'    => 'extra_map',
                    'type'    => 'fieldset',
                    'options' => [
                        'label' => __('Map options'),
                    ],
                ]
            );
            // map_latitude
            $this->add(
                [
                    'name'       => 'map_latitude',
                    'options'    => [
                        'label' => __('Latitude'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => '',
                    ],
                ]
            );
            // map_longitude
            $this->add(
                [
                    'name'       => 'map_longitude',
                    'options'    => [
                        'label' => __('Longitude'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => '',
                    ],
                ]
            );
            // map_zoom
            $this->add(
                [
                    'name'       => 'map_zoom',
                    'options'    => [
                        'label' => __('Zoom'),
                    ],
                    'attributes' => [
                        'type'        => 'text',
                        'description' => '',
                    ],
                ]
            );
        }
        // extra
        if ($this->option['side'] == 'admin') {
            $this->add(
                [
                    'name'    => 'extra_seo',
                    'type'    => 'fieldset',
                    'options' => [
                        'label' => __('SEO options'),
                    ],
                ]
            );
            // seo_title
            $this->add(
                [
                    'name'       => 'seo_title',
                    'options'    => [
                        'label' => __('SEO Title'),
                    ],
                    'attributes' => [
                        'type'        => 'textarea',
                        'rows'        => '2',
                        'cols'        => '40',
                        'description' => '',
                    ],
                ]
            );
            // seo_keywords
            $this->add(
                [
                    'name'       => 'seo_keywords',
                    'options'    => [
                        'label' => __('SEO Keywords'),
                    ],
                    'attributes' => [
                        'type'        => 'textarea',
                        'rows'        => '2',
                        'cols'        => '40',
                        'description' => '',
                    ],
                ]
            );
            // seo_description
            $this->add(
                [
                    'name'       => 'seo_description',
                    'options'    => [
                        'label' => __('SEO Description'),
                    ],
                    'attributes' => [
                        'type'        => 'textarea',
                        'rows'        => '3',
                        'cols'        => '40',
                        'description' => '',
                    ],
                ]
            );
        }

        // Save
        $this->add(
            [
                'name'       => 'submit',
                'type'       => 'submit',
                'attributes' => [
                    'value' => __('Submit'),
                ],
            ]
        );
    }
}

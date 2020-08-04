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
use Laminas\InputFilter\InputFilter;

class EventFilter extends InputFilter
{
    public function __construct($option = [])
    {
        // title
        $this->add(
            [
                'name'       => 'title',
                'required'   => true,
                'filters'    => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    new \Laminas\Validator\Db\NoRecordExists(
                        [
                            'table'   => Pi::model('story', 'news')->getTable(),
                            'field'   => 'title',
                            'adapter' => Pi::model('story', 'news')->getAdapter(),
                            'exclude' => [
                                'field' => 'id',
                                'value' => isset($option['id']) ? $option['id'] : null,
                            ],
                        ]
                    ),
                ],
            ]
        );
        // subtitle
        $this->add(
            [
                'name'     => 'subtitle',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
        // slug
        $this->add(
            [
                'name'     => 'slug',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
        // time_start
        $this->add(
            [
                'name'     => 'time_start',
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
        // time_end
        $this->add(
            [
                'name'     => 'time_end',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
        // text_summary
        $this->add(
            [
                'name'     => 'text_summary',
                'required' => $option['side'] == 'admin' ? false : true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
        // text_description
        $this->add(
            [
                'name'     => 'text_description',
                'required' => $option['side'] == 'admin' ? false : true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
        // Check topic
        if ($option['use_news_topic']) {
            // topic
            $this->add(
                [
                    'name'     => 'topic',
                    'required' => true,
                ]
            );
            // topic_main
            $this->add(
                [
                    'name'     => 'topic_main',
                    'required' => true,
                ]
            );
        }
        // status
        if ($option['side'] == 'admin') {
            $this->add(
                [
                    'name'     => 'status',
                    'required' => true,
                ]
            );
        }
        // image
        $this->add(
            [
                'name'     => 'main_image',
                'required' => false,
            ]
        );
        // image
        $this->add(
            [
                'name'     => 'image',
                'required' => false,
            ]
        );
        // cropping
        $this->add(
            [
                'name'     => 'cropping',
                'required' => false,
            ]
        );
        // address
        if ($option['side'] == 'admin' || ($option['side'] == 'front' && $option['manage_register'])) {
            $this->add(
                [
                    'name'     => 'address',
                    'required' => false,
                    'filters'  => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                ]
            );
        }
        // organizer_name
        $this->add(
            [
                'name'     => 'organizer_name',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
        // source_url
        $this->add(
            [
                'name'       => 'source_url',
                'required'   => false,
                'filters'    => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'Uri',
                        'options' => [
                            'allowRelative' => false,
                        ],
                    ],
                ],
            ]
        );
        // offer_url
        $this->add(
            [
                'name'       => 'offer_url',
                'required'   => false,
                'filters'    => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'Uri',
                        'options' => [
                            'allowRelative' => false,
                        ],
                    ],
                ],
            ]
        );
        // register_details
        if ($option['side'] == 'admin' || ($option['side'] == 'front' && $option['manage_register'])) {
            $this->add(
                [
                    'name'     => 'register_details',
                    'required' => false,
                    'filters'  => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                ]
            );
        }
        // register_price
        $this->add(
            [
                'name'     => 'register_price',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
        // Check order active
        if ($option['order_active']) {
            // register_can
            $this->add(
                [
                    'name'     => 'register_can',
                    'required' => false,
                    'filters'  => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                ]
            );
            // register_stock
            $this->add(
                [
                    'name'     => 'register_stock',
                    'required' => $option['register_can'],
                    'filters'  => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                ]
            );

        }
        // order_discount
        if (isset($option['order_active']) && $option['order_active'] && isset($option['order_discount']) && $option['order_discount']) {
            // Get role list
            $roles = Pi::service('registry')->Role->read('front');
            unset($roles['webmaster']);
            unset($roles['guest']);
            foreach ($roles as $name => $role) {
                $this->add(
                    [
                        'name'     => $name,
                        'required' => false,
                        'filters'  => [
                            [
                                'name' => 'StringTrim',
                            ],
                        ],
                    ]
                );
            }
        }
        // guide option
        if (Pi::service('module')->isActive('guide') && $option['side'] == 'admin') {
            // owner
            if ($option['side'] == 'admin') {
                $this->add(
                    [
                        'name'     => 'guide_owner',
                        'required' => false,
                    ]
                );
            }
            // category
            if ($option['use_guide_category']) {
                $this->add(
                    [
                        'name'     => 'guide_category',
                        'required' => false,
                    ]
                );
            }
            // location
            if ($option['use_guide_location']) {
                $this->add(
                    [
                        'name'     => 'guide_location',
                        'required' => false,
                    ]
                );
            }
            // item
            if (!isset($option['item']) || !$option['item']) {
                $this->add(
                    [
                        'name'     => 'guide_item',
                        'required' => false,
                    ]
                );
            }
        }
        // Check map
        if ($option['map_use']) {
            // map_latitude
            $this->add(
                [
                    'name'     => 'map_latitude',
                    'required' => false,
                ]
            );
            // map_longitude
            $this->add(
                [
                    'name'     => 'map_longitude',
                    'required' => false,
                ]
            );
            // map_zoom
            $this->add(
                [
                    'name'     => 'map_zoom',
                    'required' => false,
                ]
            );
        }
        // Seo options
        if ($option['side'] == 'admin') {
            // seo_title
            $this->add(
                [
                    'name'     => 'seo_title',
                    'required' => false,
                    'filters'  => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                ]
            );
            // seo_keywords
            $this->add(
                [
                    'name'     => 'seo_keywords',
                    'required' => false,
                    'filters'  => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                ]
            );
            // seo_description
            $this->add(
                [
                    'name'     => 'seo_description',
                    'required' => false,
                    'filters'  => [
                        [
                            'name' => 'StringTrim',
                        ],
                    ],
                ]
            );
        }
    }
}

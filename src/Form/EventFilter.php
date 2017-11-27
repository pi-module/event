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
namespace Module\Event\Form;

use Pi;
use Zend\InputFilter\InputFilter;

class EventFilter extends InputFilter
{
    public function __construct($option = array())
    {
        // id
        $this->add(array(
            'name' => 'id',
            'required' => false,
        ));
        // title
        $this->add(array(
            'name' => 'title',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
            'validators' => array(
                new \Zend\Validator\Db\NoRecordExists(
                    array(
                        'table' => Pi::model('story', 'news')->getTable(),
                        'field' => 'title',
                        'adapter' => Pi::model('story', 'news')->getAdapter(),
                        'exclude' => array(
                            'field' => 'id',
                            'value' => isset($option['id']) ? $option['id'] : null
                        )                             
                    )
                )
            ),
        ));
        // subtitle
        $this->add(array(
            'name' => 'subtitle',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // slug
        $this->add(array(
            'name' => 'slug',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // time_start
        $this->add(array(
            'name' => 'time_start',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // time_end
        $this->add(array(
            'name' => 'time_end',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // text_summary
        $this->add(array(
            'name' => 'text_summary',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // text_description
        $this->add(array(
            'name' => 'text_description',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // Check topic
        if ($option['use_news_topic']) {
            // topic
            $this->add(array(
                'name' => 'topic',
                'required' => true,
            ));
            // topic_main
            $this->add(array(
                'name' => 'topic_main',
                'required' => true,
            ));
        }
        // status
        if ($option['side'] == 'admin') {
            $this->add(array(
                'name' => 'status',
                'required' => true,
            ));
        }
        // image
        $this->add(array(
            'name' => 'main_image',
            'required' => false,
        ));
        // image
        $this->add(array(
            'name' => 'image',
            'required' => false,
        ));
        // cropping
        $this->add(array(
            'name' => 'cropping',
            'required' => false,
        ));
        // address
        if ($option['side'] == 'admin' || ($option['side'] == 'front' && $option['manage_register'])) {
            $this->add(array(
                'name' => 'address',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'StringTrim',
                    ),
                ),
            ));
        }
        // organizer_name
        $this->add(array(
            'name' => 'organizer_name',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // source_url
        $this->add(array(
            'name' => 'source_url',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
            'validators' => array(
                array(
                    'name'    => 'Uri',
                    'options' => array(
                        'allowRelative' => false
                    ),
                ),
            ),
        ));
        // offer_url
        $this->add(array(
            'name' => 'offer_url',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
            'validators' => array(
                array(
                    'name'    => 'Uri',
                    'options' => array(
                        'allowRelative' => false
                    ),
                ),
            ),
        ));
        // register_details
        if ($option['side'] == 'admin' || ($option['side'] == 'front' && $option['manage_register'])) {
            $this->add(array(
                'name' => 'register_details',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'StringTrim',
                    ),
                ),
            ));
        }
        // register_price
        $this->add(array(
            'name' => 'register_price',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // Check order active
        if ($option['order_active']) {
            // register_can
            $this->add(array(
                'name' => 'register_can',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'StringTrim',
                    ),
                ),
            ));
            // register_stock
            $this->add(array(
                'name' => 'register_stock',
                'required' => $option['register_can'],
                'filters' => array(
                    array(
                        'name' => 'StringTrim',
                    ),
                ),
            ));
           
        }
        // order_discount
        if (isset($option['order_active']) && $option['order_active'] && isset($option['order_discount']) && $option['order_discount']) {
            // Get role list
            $roles = Pi::service('registry')->Role->read('front');
            unset($roles['webmaster']);
            unset($roles['guest']);
            foreach ($roles as $name => $role) {
                $this->add(array(
                    'name' => $name,
                    'required' => false,
                    'filters' => array(
                        array(
                            'name' => 'StringTrim',
                        ),
                    ),
                ));
            }
        }
        // guide option
        if (Pi::service('module')->isActive('guide') && $option['side'] == 'admin') {
            // owner
            if ($option['side'] == 'admin') {
                $this->add(array(
                    'name' => 'guide_owner',
                    'required' => false,
                ));
            }
            // category
            if ($option['use_guide_category']) {
                $this->add(array(
                    'name' => 'guide_category',
                    'required' => false,
                ));
            }
            // location
            if ($option['use_guide_location']) {
                $this->add(array(
                    'name' => 'guide_location',
                    'required' => false,
                ));
            }
            // item
            if (!isset($option['item']) || !$option['item']) {
                $this->add(array(
                    'name' => 'guide_item',
                    'required' => false,
                ));
            }
        }
        // Check map
        if ($option['map_use']) {
            // map_latitude
            $this->add(array(
                'name' => 'map_latitude',
                'required' => false,
            ));
            // map_longitude
            $this->add(array(
                'name' => 'map_longitude',
                'required' => false,
            ));
            // map_zoom
            $this->add(array(
                'name' => 'map_zoom',
                'required' => false,
            ));
        }
        // Seo options
        if ($option['side'] == 'admin') {
            // seo_title
            $this->add(array(
                'name' => 'seo_title',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'StringTrim',
                    ),
                ),
            ));
            // seo_keywords
            $this->add(array(
                'name' => 'seo_keywords',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'StringTrim',
                    ),
                ),
            ));
            // seo_description
            $this->add(array(
                'name' => 'seo_description',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'StringTrim',
                    ),
                ),
            ));
        }
    }
}

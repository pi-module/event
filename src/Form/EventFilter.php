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
            'validators' => array(
                new \Module\News\Validator\SlugDuplicate(array(
                    'module' => 'news',
                    'table' => 'story',
                )),
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
        // status
        if ($option['side'] == 'admin') {
            $this->add(array(
                'name' => 'status',
                'required' => true,
            ));
        }
        // image
        $this->add(array(
            'name' => 'image',
            'required' => false,
        ));
        // address
        $this->add(array(
            'name' => 'address',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
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
        ));
        // price
        $this->add(array(
            'name' => 'price',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
        // registration_details
        $this->add(array(
            'name' => 'registration_details',
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
        // guide option
        if (Pi::service('module')->isActive('guide')) {
            // owner
            if ($option['side'] == 'admin') {
                $this->add(array(
                    'name' => 'guide_owner',
                    'required' => false,
                ));
            }
            // category
            $this->add(array(
                'name' => 'guide_category',
                'required' => false,
            ));
            // location
            $this->add(array(
                'name' => 'guide_location',
                'required' => false,
            ));
            // item
            $this->add(array(
                'name' => 'guide_item',
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
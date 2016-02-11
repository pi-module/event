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
use Pi\Form\Form as BaseForm;

class EventForm extends BaseForm
{
    public function __construct($name = null, $option = array())
    {
        $this->option = $option;
        $this->thumbUrl = (isset($option['thumbUrl'])) ? $option['thumbUrl'] : '';
        $this->removeUrl = (isset($option['removeUrl'])) ? $option['removeUrl'] : '';
        $this->module = Pi::service('module')->current();
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
        // id
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        // title
        $this->add(array(
            'name' => 'title',
            'options' => array(
                'label' => __('Title'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
                'required' => true,
            )
        ));
        // subtitle
        $this->add(array(
            'name' => 'subtitle',
            'options' => array(
                'label' => __('Subtitle'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // slug
        $this->add(array(
            'name' => 'slug',
            'options' => array(
                'label' => __('slug'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // text_summary
        $this->add(array(
            'name' => 'text_summary',
            'options' => array(
                'label' => __('Summary'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '3',
                'cols' => '40',
                'description' => '',
            )
        ));
        // text_description
        $this->add(array(
            'name' => 'text_description',
            'options' => array(
                'label' => __('Description'),
                'editor' => 'html',
            ),
            'attributes' => array(
                'type' => 'editor',
                'description' => '',
            )
        ));
        // extra_main
        $this->add(array(
            'name' => 'extra_main',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('Mian options'),
            ),
        ));
        // status
        $this->add(array(
            'name' => 'status',
            'type' => 'select',
            'options' => array(
                'label' => __('Status'),
                'value_options' => array(
                    1 => __('Published'),
                    2 => __('Pending review'),
                    3 => __('Draft'),
                    4 => __('Private'),
                    5 => __('Delete'),
                ),
            ),
            'attributes' => array(
                'required' => true,
            )
        ));
        // Image
        if ($this->thumbUrl) {
            $this->add(array(
                'name' => 'imageview',
                'type' => 'Module\News\Form\Element\Image',
                'options' => array(//'label' => __('Image'),
                ),
                'attributes' => array(
                    'src' => $this->thumbUrl,
                ),
            ));
            $this->add(array(
                'name' => 'remove',
                'type' => 'Module\News\Form\Element\Remove',
                'options' => array(
                    'label' => __('Remove image'),
                ),
                'attributes' => array(
                    'link' => $this->removeUrl,
                ),
            ));
            $this->add(array(
                'name' => 'image',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ));
        } else {
            $this->add(array(
                'name' => 'image',
                'options' => array(
                    'label' => __('Image'),
                ),
                'attributes' => array(
                    'type' => 'file',
                    'description' => '',
                )
            ));
        }
        // address
        $this->add(array(
            'name' => 'address',
            'options' => array(
                'label' => __('Address'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // organizer_name
        $this->add(array(
            'name' => 'organizer_name',
            'options' => array(
                'label' => __('Organizer name'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // source_url
        $this->add(array(
            'name' => 'source_url',
            'options' => array(
                'label' => __('Organizer Website'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // offer_url
        $this->add(array(
            'name' => 'offer_url',
            'options' => array(
                'label' => __('Offer url'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // price
        $this->add(array(
            'name' => 'price',
            'options' => array(
                'label' => __('Minimum Price'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // registration_details
        $this->add(array(
            'name' => 'registration_details',
            'options' => array(
                'label' => __('Registration details/requirements'),
                'editor' => 'html',
            ),
            'attributes' => array(
                'type' => 'editor',
                'description' => '',
            )
        ));
        // extra
        $this->add(array(
            'name' => 'extra_time',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('Time options'),
            ),
        ));
        // time_start
        $this->add(array(
            'name' => 'time_start',
            'type' => 'datepicker',
            'options' => array(
                'label' => __('Time start'),
                'datepicker' => array(
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                    'todayBtn' => true,
                    'todayHighlight' => true,
                    'weekStart' => 1,
                ),
            ),
            'attributes' => array(
                'required' => true,
                'value' => date('Y-m-d'),
            )
        ));
        // time_end
        $this->add(array(
            'name' => 'time_end',
            'type' => 'datepicker',
            'options' => array(
                'label' => __('Time end'),
                'datepicker' => array(
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                    'todayBtn' => true,
                    'todayHighlight' => true,
                    'weekStart' => 1,
                ),
            ),
            'attributes' => array(
                'required' => false,
            )
        ));
        // extra guide
        if (Pi::service('module')->isActive('guide')) {
            $this->add(array(
                'name' => 'extra_guide',
                'type' => 'fieldset',
                'options' => array(
                    'label' => __('Guide options'),
                ),
            ));
            // owner
            $this->add(array(
                'name' => 'guide_owner',
                'type' => 'Module\Guide\Form\Element\Owner',
                'options' => array(
                    'label' => __('Owner'),
                    'module' => 'guide',
                ),
            ));
            // category
            $this->add(array(
                'name' => 'guide_category',
                'type' => 'Module\Guide\Form\Element\Category',
                'options' => array(
                    'label' => __('Category'),
                    'category' => array(0 => ''),
                    'module' => 'guide',
                ),
                'attributes' => array(
                    'size' => 5,
                    'multiple' => 1,
                )
            ));
            // location
            $this->add(array(
                'name' => 'guide_location',
                'type' => 'Module\Guide\Form\Element\Location',
                'options' => array(
                    'label' => __('Location'),
                    'module' => 'guide',
                ),
                'attributes' => array(
                    'size' => 5,
                    'multiple' => 1,
                )
            ));
            // item
            $this->add(array(
                'name' => 'guide_item',
                'type' => 'Module\Guide\Form\Element\Item',
                'options' => array(
                    'label' => __('Item'),
                    'module' => 'guide',
                ),
                'attributes' => array(
                    'size' => 5,
                    'multiple' => 1,
                )
            ));
        }
        // extra
        $this->add(array(
            'name' => 'extra_seo',
            'type' => 'fieldset',
            'options' => array(
                'label' => __('SEO options'),
            ),
        ));
        // seo_title
        $this->add(array(
            'name' => 'seo_title',
            'options' => array(
                'label' => __('SEO Title'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '2',
                'cols' => '40',
                'description' => '',
            )
        ));
        // seo_keywords
        $this->add(array(
            'name' => 'seo_keywords',
            'options' => array(
                'label' => __('SEO Keywords'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '2',
                'cols' => '40',
                'description' => '',
            )
        ));
        // seo_description
        $this->add(array(
            'name' => 'seo_description',
            'options' => array(
                'label' => __('SEO Description'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '3',
                'cols' => '40',
                'description' => '',
            )
        ));
        // Save
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => __('Submit'),
            )
        ));
    }
}
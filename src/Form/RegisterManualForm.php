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

class RegisterManualForm extends BaseForm
{
    public function __construct($name = null, $option = [])
    {
        $this->options = $option;
        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new RegisterManualFilter($this->options);
        }
        return $this->filter;
    }

    public function init()
    {
        foreach ($this->options['fields'] as $field) {
            if (intval($field['attributes']['required']) == 1) {
                $this->add($field);
            }
        }

        // main_image
        $this->add(
            [
                'name'    => 'main_image',
                'type'    => 'Module\Media\Form\Element\Media',
                'options' => [
                    'label'  => __('Payment image'),
                    'module' => 'news',
                ],
            ]
        );

        // Save
        $this->add(
            [
                'name'       => 'submit',
                'type'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-success',
                    'value' => __('Complete registration'),
                ],
            ]
        );
    }
}

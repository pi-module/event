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

class RegisterManualFilter extends InputFilter
{
    public function __construct($options = [])
    {
        foreach ($options['filters'] as $filter) {
            if ($filter && intval($filter['required']) == 1) {
                $this->add($filter);
            }
        }

        // main_image
        $this->add(
            [
                'name'     => 'main_image',
                'required' => false,
            ]
        );
    }
}

<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Event\Form;

use Pi;
use Laminas\InputFilter\InputFilter;

class EventSearchFilter extends InputFilter
{
    public function __construct()
    {
        // title
        $this->add(
            [
                'name'     => 'title',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ]
        );
        
        // status
        $this->add(
            [
                'name'     => 'status',
                'required' => false,
            ]
        );
    }
}
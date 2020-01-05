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

class RegisterFilter extends InputFilter
{
    public function __construct($option = [])
    {
        // id
        $this->add(
            [
                'name'     => 'id',
                'required' => true,
            ]
        );
        // number
        $this->add(
            [
                'name'     => 'number',
                'required' => true,
            ]
        );
    }
}
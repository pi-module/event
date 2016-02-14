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
namespace Module\Event\Block;

use Pi;
use Module\Guide\Form\SearchLocationForm;
use Zend\Db\Sql\Predicate\Expression;

class Block
{
    public static function recentEvent($options = array(), $module = null)
    {
        // Set options
        $block = array();
        $block = array_merge($block, $options);
        // Set block array
        $block['resources'] = Pi::api('event', 'event')->getEventLast($block['number']);
        $block['morelink'] = Pi::url(Pi::service('url')->assemble('event', array(
            'module' => $module,
            'controller' => 'index',
            'action' => 'index',
        )));
        return $block;
    }
}
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

namespace Module\Event\Model;

use Pi\Application\Model\Model;

class Order extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns
        = [
            'id',
            'uid',
            'event',
            'order_id',
            'number',
            'price',
            'vat',
            'total',
            'time_order',
            'time_start',
            'time_end',
            'status',
            'type',
            'extra',
        ];
}

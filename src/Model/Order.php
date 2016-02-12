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

namespace Module\Event\Model;

use Pi\Application\Model\Model;

class Order extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns = array(
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
        'code_public',
        'code_private',
        'extra',
    );
}

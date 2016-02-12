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

class Extra extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $columns = array(
        'id',
        'uid',
        'time_start',
        'time_end',
        'source_url',
        'organizer_name',
        'address',
        'offer_url',
        'register_details',
        'register_can',
        'register_capacity',
        'register_price',
        'register_type',
        'guide_owner',
        'guide_category',
        'guide_location',
        'guide_item'
    );
}
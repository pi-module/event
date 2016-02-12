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
        'price',
        'offer_url',
        'registration_details',
        'guide_owner',
        'guide_category',
        'guide_location',
        'guide_item'
    );
}
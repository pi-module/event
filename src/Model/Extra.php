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
    protected $rowClass = 'Module\Event\Model\Extra\RowGateway';

    /**
     * {@inheritDoc}
     */
    protected $columns = array(
        'id',
        'uid',
        'title',
        'slug',
        'status',
        'time_start',
        'time_end',
        'source_url',
        'organizer_name',
        'address',
        'offer_url',
        'register_details',
        'register_can',
        'register_stock',
        'register_sales',
        'register_price',
        'register_type',
        'register_discount',
        'guide_owner',
        'guide_category',
        'guide_location',
        'guide_item',
        'map_latitude',
        'map_longitude',
        'map_zoom',
    );
}
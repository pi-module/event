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
        $where = array(
            'status' => 1,
            'type' => 'event',
        );

        $eventModel = Pi::model('extra', 'event');
        $eventTable = $eventModel->getTable();
        $eventAdapter = $eventModel->getAdapter();
        // Set sql
        $sql = "SELECT * FROM `%s` WHERE  (`status` = 1 AND time_end > %s ) OR (`status` = 1 AND `time_end` = 0 AND time_start > %s ) ORDER BY `time_start` ASC, `id` ASC LIMIT %s";
        // Set sql
        $sql = sprintf(
            $sql,
            $eventTable,
            strtotime("-1 day"),
            strtotime("-1 day"),
            intval($block['number'])
        );
        // query
        try {
            $rowset = $eventAdapter->query($sql, 'execute');
            $rowset = $rowset->toArray();
            foreach ($rowset as $row) {
                $where['id'][$row['id']] = $row['id'];
            }
        } catch (\Exception $exception) {
            $where['time_publish > ?'] = strtotime("-1 day");
        }

        if (isset($block['topic-id']) && !empty($block['topic-id']) && !in_array(0, $block['topic-id'])) {
            $where['topic'] = $block['topic-id'];
            $table = 'link';
        } else {
            $table = 'story';
        }

        $order = array('id ASC');

        // Set event
        $events = Pi::api('event', 'event')->getEventList($where, $order, '', $block['number'], 'full', $table);
        foreach ($events as $event) {
            $block['resources'][$event['time_start'].$event['id']] = $event;
        }

        // Set more link
        $block['morelink'] = Pi::url(Pi::service('url')->assemble('event', array(
            'module' => $module,
            'controller' => 'index',
            'action' => 'index',
        )));
        // Language
        _b('free!');
        // return
        return $block;
    }
}
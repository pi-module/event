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
    public static function recentEvent($options = [], $module = null)
    {
        // Set options
        $block = [];
        $block = array_merge($block, $options);

        /*  $where = array(
            'status' => 1,
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
                $list[] = $row['id'];
                $block['resources'][$row['id']] = array();
            }
        } catch (\Exception $exception) {
            $where['time_publish > ?'] = strtotime("-1 day");
        } */

        $select = Pi::model('extra', 'event')->select()
            ->where(
                function ($where) {
                    $fromWhere = clone $where;
                    $toWhere   = clone $where;
                    $fromWhere->equalTo('status', 1);
                    $fromWhere->greaterThan('time_end', strtotime("-1 day"));
                    $toWhere->equalTo('status', 1);
                    $toWhere->equalTo('time_end', 0);
                    $toWhere->greaterThan('time_start', strtotime("-1 day"));
                    $where->andPredicate($fromWhere)->orPredicate($toWhere);
                }
            )
            ->order('time_start ASC, id ASC')
            ->limit(intval($block['number']));
        $rowset = Pi::model('extra', 'event')->selectWith($select)->toArray();
        foreach ($rowset as $row) {
            $list[]                         = $row['id'];
            $block['resources'][$row['id']] = [];
        }

        $where = [
            'status' => 1,
        ];

        if (isset($block['topic-id']) && !empty($block['topic-id']) && !in_array(0, $block['topic-id'])) {
            $table          = 'link';
            $where['topic'] = $block['topic-id'];
            $where['story'] = $list;
        } else {
            $table       = 'story';
            $where['id'] = $list;
        }

        //$table = 'story';
        //$where['id'] = $list;

        $order = ['time_publish ASC', 'id ASC'];

        // Set event
        $events = Pi::api('event', 'event')->getEventList($where, $order, '', $block['number'], 'full', $table);
        foreach ($events as $event) {
            $block['resources'][$event['id']] = $event;
        }

        // Set more link
        $block['morelink'] = Pi::url(
            Pi::service('url')->assemble(
                'event', [
                'module'     => $module,
                'controller' => 'index',
                'action'     => 'index',
            ]
            )
        );

        // Language
        _b('free!');
        _b('Toman');
        _b('%s days');
        _b('1 days');
        // return
        return $block;
    }
}
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

namespace Module\Event\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Zend\Json\Json;
use Zend\Db\Sql\Predicate\Expression;

class JsonController extends IndexController
{
    public function indexAction()
    {
        // Set return
        $return = array(
            'website' => Pi::url(),
            'module' => $this->params('module'),
        );
        // Set view
        return $return;
    }

    public function filterIndexAction()
    {
        // Set info
        $where = array(
            'status' => 1,
            'type' => 'event'
        );
        $order = array('time_publish DESC', 'id DESC');
        $events = Pi::api('event', 'event')->getEventList($where, $order, '', '', 'full', 'story');
        $listEvent = array();
        foreach ($events as $event) {
            // Set text_summary
            $event['text_summary'] = Pi::service('markup')->render($event['text_summary'], 'html', 'html');
            $event['text_summary'] = strip_tags($event['text_summary'], "<b><strong><i><p><br><ul><li><ol><h2><h3><h4>");
            $event['text_summary'] = str_replace("<p>&nbsp;</p>", "", $event['text_summary']);
            // Set category list
            $categoryList = array();
            if (isset($event['guide_category']) && !empty($event['guide_category'])) {
                foreach ($event['guide_category'] as $category) {
                    $categoryList[$category] = sprintf('category-%s-guide', $category);
                }
            }
            if (isset($event['topic']) && !empty($event['topic'])) {
                foreach ($event['topic'] as $category) {
                    $categoryList[$category] = sprintf('category-%s-news', $category);
                }
            }
            // Set location list
            $locationList = array();
            if (isset($event['guide_location']) && !empty($event['guide_location'])) {
                foreach ($event['guide_location'] as $category) {
                    $locationList[$category] = sprintf('location-%s-guide', $category);
                }
            }
            // Set time view
            if (!empty($event['time_start']) && !empty($event['time_end'])) {
                $timeView = sprintf('%s %s %s %s', __('From'), $event['time_start_view'], __('to'), $event['time_end_view']);
            } elseif (!empty($event['time_start'])) {
                $timeView = $event['time_start_view'];
            }
            // Set time level
            if ($event['time_end'] == 0 && $event['time_start'] < time()) {
                $timeLevel = 'expire';
            } elseif ($event['time_end'] > 0 && $event['time_end'] < time()) {
                $timeLevel = 'expire';
            } elseif ($event['time_start'] < (time() + (60 * 60 * 24 * 7))) {
                $timeLevel = 'thisWeek';
            } elseif ($event['time_start'] < (time() + (60 * 60 * 24 * 14))) {
                $timeLevel = 'nextWeek';
            } elseif ($event['time_start'] < (time() + (60 * 60 * 24 * 30))) {
                $timeLevel = 'thisMonth';
            } elseif ($event['time_start'] < (time() + (60 * 60 * 24 * 60))) {
                $timeLevel = 'nextMonth';
            } else {
                $timeLevel = 'nextAllMonth';
            }
            // Set single event array
            $eventSingle = array(
                'id' => $event['id'],
                'title' => $event['title'],
                'image' => $event['image'],
                'thumbUrl' => $event['thumbUrl'],
                'eventUrl' => $event['eventUrl'],
                'subtitle' => $event['subtitle'],
                'register_price' => $event['register_price'],
                'register_price_view' => $event['register_price_view'],
                'price_currency' => $event['price_currency'],
                'hits' => $event['hits'],
                'text_summary' => $event['text_summary'],
                'time_create' => $event['time_create'],
                'time_publish' => $event['time_publish'],
                'time_update' => $event['time_update'],
                'time_start' => $event['time_start'],
                'time_end' => $event['time_end'],
                'time_start_view' => date("Y-m-d H:i:s", $event['time_start']),
                'time_end_view' => date("Y-m-d H:i:s", $event['time_end']),
                'time_view' => $timeView,
                'time_level' => $timeLevel,
                'category' => implode(' ', $categoryList),
                'location' => implode(' ', $locationList),
            );
            // Add to list
            $listEvent[] = $eventSingle;
        }
        // Set view
        return $listEvent;
    }
}
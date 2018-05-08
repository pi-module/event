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

class JsonController extends IndexController
{
    public function searchAction()
    {
        // Get info from url
        $module = $this->params('module');
        $page = $this->params('page', 1);
        $title = $this->params('title');
        $category = $this->params('category');
        $tag = $this->params('tag');
        $favourite = $this->params('favourite');
        $limit = $this->params('limit');
        $time = $this->params('time');

        //$recommended = $this->params('recommended');
        //$order = $this->params('order');
        //$categorySelect = $this->params('categorySelect');
        //$locationSelect = $this->params('locationSelect');

        // Set has search result
        $hasSearchResult = true;

        // Set table
        $table = 'story';

        // Clean title
        if (Pi::service('module')->isActive('search') && isset($title) && !empty($title)) {
            $title = Pi::api('api', 'search')->parseQuery($title);
        } elseif (isset($title) && !empty($title)) {
            $title = _strip($title);
        } else {
            $title = '';
        }

        // Clean params
        $paramsClean = array();
        foreach ($_GET as $key => $value) {
            $key = _strip($key);
            $value = _strip($value);
            $paramsClean[$key] = $value;
        }

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Set empty result
        $result = array(
            'events' => array(),
            'paginator' => array(),
            'condition' => array(),
        );

        // Set where link
        $whereEvent = array(
            'status' => 1,
            'type' => 'event'
        );
        /* if (!empty($recommended) && $recommended == 1) {
            $whereEvent['recommended'] = 1;
        } */

        // Set page title
        $pageTitle = __('List of events');

        // Set order
        $order = array('time_publish DESC', 'id DESC');
        $orderExtra = array('time_start DESC', 'id DESC');

        // Get location list
        $locationList = Pi::api('event', 'event')->getLocationList();
        if (!empty($locationList)) {
            $locationAll[] = array(
                'id' => 0,
                'title' => __('All'),
                'value' => '',
            );
            $locationList = array_merge($locationAll, $locationList);
        }

        // Get category list
        $categoryList = Pi::api('event', 'event')->getCategoryList();
        if (!empty($categoryList)) {
            $categoryAll[] = array(
                'id' => 0,
                'title' => __('All'),
                'value' => '',
            );
            $categoryList = array_merge($categoryAll, $categoryList);
        }

        // Get category information from model
        if (!empty($category)) {
            // Get category
            $category = Pi::api('topic', 'news')->getTopicFull($category, 'slug');
            // Check category
            if (!$category || $category['status'] != 1) {
                return $result;
            }
            $categoryIDList = array();
            $categoryIDList[] = $category['id'];
            if (isset($category['ids']) && !empty($category['ids'])) {
                foreach ($category['ids'] as $categorySingle) {
                    $categoryIDList[] = $categorySingle;
                }
            }
            // Set page title
            $pageTitle = sprintf(__('List of events on %s category'), $category['title']);
            // Set table
            $table = 'link';
        }

        // Get tag list
        if (!empty($tag)) {
            $eventIDTag = array();
            // Check favourite
            if (!Pi::service('module')->isActive('tag')) {
                return $result;
            }
            // Get id from tag module
            $tagList = Pi::service('tag')->getList($tag, $module);
            foreach ($tagList as $tagSingle) {
                $eventIDTag[] = $tagSingle['item'];
            }
            // Set header and title
            $pageTitle = sprintf(__('All events from %s'), $tag);
        }

        // Get favourite list
        if (!empty($favourite) && $favourite == 1) {
            // Check favourite
            if (!Pi::service('module')->isActive('favourite')) {
                return $result;
            }
            // Get uid
            $uid = Pi::user()->getId();
            // Check user
            if (!$uid) {
                return $result;
            }
            // Get id from favourite module
            $eventIDFavourite = Pi::api('favourite', 'favourite')->userFavourite($uid, 'news');
            // Set page title
            $pageTitle = ('All favourite events by you');
        }

        // Set event ID list
        $checkTitle = false;
        $checkTime = false;
        $eventIDList = array(
            'title' => array(),
            'time' => array(),
        );

        // Check title from event table
        if (isset($title) && !empty($title)) {
            $checkTitle = true;
            $titles = is_array($title) ? $title : array($title);
            $columns = array('id');
            $select = $this->getModel('extra')->select()->columns($columns)->where(function ($where) use ($titles, $recommended) {
                $whereMain = clone $where;
                $whereKey = clone $where;
                $whereMain->equalTo('status', 1);
                //if (!empty($recommended) && $recommended == 1) {
                //    $whereMain->equalTo('recommended', 1);
                //}
                foreach ($titles as $title) {
                    $whereKey->like('title', '%' . $title . '%')->and;
                }
                $where->andPredicate($whereMain)->andPredicate($whereKey);
            })->order($orderExtra);
            $rowset = $this->getModel('extra')->selectWith($select);
            foreach ($rowset as $row) {
                $eventIDList['title'][$row->id] = $row->id;
            }
        }

        // Check time
        $timeArray = array(
            'active',
            'expired',
            'thisWeek',
            'nextWeek',
            'thisMonth',
            'nextMonth',
            'nextTwoMonth',
            'nextThreeMonth',
            'nextAllMonth',
        );
        $time = (in_array($time, $timeArray)) ? $time : '';
        if (!empty($time)) {
            // Get time
            $timeList = Pi::api('time', 'event')->makeTime();
            // Set time where query
            switch ($time) {
                case 'active':
                    $whereExtra1 = array('time_end' => 0, 'time_start > ?' => $timeList['expired']);
                    $whereExtra2 = array('time_end > ?' => 0, 'time_end > ?' => $timeList['expired']);
                    break;

                case 'expired':
                    $whereExtra1 = array('time_end' => 0, 'time_start < ?' => $timeList['expired']);
                    $whereExtra2 = array('time_end > ?' => 0, 'time_end < ?' => $timeList['expired']);
                    break;

                case 'thisWeek':
                    $whereExtra1 = array('time_start >= ?' => $timeList['thisWeek'], 'time_start < ?' => $timeList['nextWeek']);
                    $whereExtra2 = array('time_end > ?' => 0, 'time_start < ?' => $timeList['thisWeek'], 'time_end > ?' => $timeList['thisWeek']);
                    break;

                case 'nextWeek':
                    $whereExtra1 = array('time_start >= ?' => $timeList['nextWeek'], 'time_start < ?' => $timeList['nextTwoWeek']);
                    $whereExtra2 = array('time_end > ?' => 0, 'time_start < ?' => $timeList['nextWeek'], 'time_end > ?' => $timeList['nextWeek']);
                    break;

                case 'thisMonth':
                    $whereExtra1 = array('time_start >= ?' => $timeList['thisMonth'], 'time_start < ?' => $timeList['nextMonth']);
                    $whereExtra2 = array('time_end > ?' => 0, 'time_start < ?' => $timeList['thisMonth'], 'time_end > ?' => $timeList['thisMonth']);
                    break;

                case 'nextMonth':
                    $whereExtra1 = array('time_start >= ?' => $timeList['nextMonth'], 'time_start < ?' => $timeList['nextTwoMonth']);
                    $whereExtra2 = array('time_end > ?' => 0, 'time_start < ?' => $timeList['nextMonth'], 'time_end > ?' => $timeList['nextMonth']);
                    break;

                case 'nextTwoMonth':
                    $whereExtra1 = array('time_start >= ?' => $timeList['nextTwoMonth'], 'time_start < ?' => $timeList['nextThreeMonth']);
                    $whereExtra2 = array('time_end > ?' => 0, 'time_start < ?' => $timeList['nextTwoMonth'], 'time_end > ?' => $timeList['nextTwoMonth']);
                    break;

                case 'nextThreeMonth':
                    $whereExtra1 = array('time_start >= ?' => $timeList['nextThreeMonth'], 'time_start < ?' => $timeList['nextFourMonth']);
                    $whereExtra2 = array('time_end > ?' => 0, 'time_start < ?' => $timeList['nextThreeMonth'], 'time_end > ?' => $timeList['nextThreeMonth']);
                    break;

                case 'nextAllMonth':
                    $whereExtra1 = array('time_start >= ?' => $timeList['nextFourMonth'],);
                    $whereExtra2 = array('time_end > ?' => 0, 'time_end > ?' => $timeList['nextFourMonth']);
                    break;
            }

            // Make query
            $checkTime = true;
            $columns = array('id');
            $select = $this->getModel('extra')->select()->columns($columns)->where(function ($where) use ($whereExtra1, $whereExtra2) {
                $whereMain = clone $where;
                $where1 = clone $where;
                $where2 = clone $where;
                $whereMain->equalTo('status', 1);
                $where1->addPredicates($whereExtra1);
                $where2->addPredicates($whereExtra2);
                $where->addPredicate($whereMain)->addPredicate($where1)->orPredicate($where2);
            })->order($orderExtra);
            $rowset = $this->getModel('extra')->selectWith($select);
            foreach ($rowset as $row) {
                $eventIDList['time'][$row->id] = $row->id;
            }
        }

        // Set info
        $event = array();
        $count = 0;

        $limit = (intval($limit) > 0) ? intval($limit) : intval($config['view_perpage']);
        $offset = (int)($page - 1) * $limit;

        // Set category on where link
        if (isset($categoryIDList) && !empty($categoryIDList)) {
            $whereEvent['topic'] = $categoryIDList;
        }

        // Set event on where link from title and attribute
        if ($checkTitle) {
            if (!empty($eventIDList)) {
                $whereStoryId = $eventIDList;
            } else {
                $hasSearchResult = false;
            }
        }

        // Set story on where link from title and time
        if ($checkTitle && $checkTime) {
            if (!empty($eventIDList['title']) && !empty($eventIDList['time'])) {
                $whereStoryId = array_intersect($eventIDList['title'], $eventIDList['time']);
            } else {
                $hasSearchResult = false;
            }
        } elseif ($checkTitle) {
            if (!empty($eventIDList['title'])) {
                $whereStoryId = $eventIDList['title'];
            } else {
                $hasSearchResult = false;
            }
        } elseif ($checkTime) {
            if (!empty($eventIDList['time'])) {
                $whereStoryId = $eventIDList['time'];
            } else {
                $hasSearchResult = false;
            }
        }

        // Set favourite events on where link
        if (!empty($favourite) && $favourite == 1 && isset($eventIDFavourite)) {
            if (isset($whereStoryId) && !empty($whereStoryId)) {
                $whereStoryId = array_intersect($eventIDFavourite, $whereStoryId);
            } elseif (!isset($whereStoryId) || empty($whereStoryId)) {
                $whereStoryId = $eventIDFavourite;
            } else {
                $hasSearchResult = false;
            }
        }

        // Set tag events on where link
        if (!empty($tag) && isset($eventIDTag)) {
            if (isset($whereStoryId) && !empty($whereStoryId)) {
                $whereStoryId = array_intersect($eventIDTag, $whereStoryId);
            } elseif (!isset($whereStoryId) || empty($whereStoryId)) {
                $whereStoryId = $eventIDTag;
            } else {
                $hasSearchResult = false;
            }
        }

        // Check has Search Result
        if ($hasSearchResult) {
            // Set story id list
            if (isset($whereStoryId)) {
                switch ($table) {
                    case 'story':
                        $whereEvent['id'] = $whereStoryId;
                        break;

                    case 'link':
                        $whereEvent['story'] = $whereStoryId;
                        break;
                }
            }
            // Get event
            $event = Pi::api('event', 'event')->getEventList($whereEvent, $order, $offset, $limit, 'full', $table);
            $count = Pi::api('api', 'news')->getStoryCount($whereEvent, $table);
            $event = array_values($event);
        }

        // Set result
        $result = array(
            'events' => $event,
            'paginator' => array(
                'count' => $count,
                'limit' => $limit,
                'page' => $page,
            ),
            'condition' => array(
                'title' => $pageTitle,
                 'locationList' => $locationList,
                'categoryList' => $categoryList,
                'table' => $table,
                'listType' => $config['view_list_type'],
                'importantOrganizer' => $config['important_organizer'],
            ),
        );

        return $result;
    }

    public function eventSingleAction()
    {
        // Get info from url
        $id = $this->params('id');
        $slug = $this->params('slug');
        // Get
        if (!empty($slug)) {
            $singleEvent = Pi::api('event', 'event')->getEventSingle($slug, 'slug');
        } elseif (!empty($id)) {
            $singleEvent = Pi::api('event', 'event')->getEventSingle($id);
        } else {
            return false;
        }

        // Set text_summary
        $singleEvent['text_summary'] = Pi::service('markup')->render($singleEvent['text_summary'], 'html', 'html');
        $singleEvent['text_summary'] = strip_tags($singleEvent['text_summary'],"<b><strong><i><p><br><ul><li><ol><h2><h3><h4>");
        $singleEvent['text_summary'] = str_replace("<p>&nbsp;</p>", "", $singleEvent['text_summary']);
        // Set text_description
        $singleEvent['text_description'] = Pi::service('markup')->render($singleEvent['text_description'], 'html', 'html');
        $singleEvent['text_description'] = strip_tags($singleEvent['text_description'],"<b><strong><i><p><br><ul><li><ol><h2><h3><h4>");
        $singleEvent['text_description'] = str_replace("<p>&nbsp;</p>", "", $singleEvent['text_description']);

        // Set register_details
        $singleEvent['register_details'] = Pi::service('markup')->render($singleEvent['register_details'], 'html', 'html');
        $singleEvent['register_details'] = strip_tags($singleEvent['register_details'],"<b><strong><i><p><br><ul><li><ol><h2><h3><h4>");
        $singleEvent['register_details'] = str_replace("<p>&nbsp;</p>", "", $singleEvent['register_details']);
        // Set time
        if (!empty($singleEvent['time_start']) && !empty($singleEvent['time_end'])) {
            $singleEvent['time_view'] = sprintf('%s %s %s %s', _b('From'), $singleEvent['time_start_view'], _b('to'), $singleEvent['time_end_view']);
        } elseif (!empty($singleEvent['time_start'])) {
            $singleEvent['time_view'] = $singleEvent['time_start_view'];
        }

        // Set event
        $event = array();
        $event[] = array(
            'id' => $singleEvent['id'],
            'title' => $singleEvent['title'],
            'text_summary' => $singleEvent['text_summary'],
            'text_description' => $singleEvent['text_description'],
            'register_details' => $singleEvent['register_details'],
            'time_update' => $singleEvent['time_update'],
            'time_start' => $singleEvent['time_start'],
            'time_end' => $singleEvent['time_end'],
            'time_view' => $singleEvent['time_view'],
            'hits' => $singleEvent['hits'],
            'recommended' => $singleEvent['recommended'],
            'favourite' => $singleEvent['favourite'],
            'largeUrl' => $singleEvent['largeUrl'],
            'mediumUrl' => $singleEvent['mediumUrl'],
            'thumbUrl' => $singleEvent['thumbUrl'],
            'image' => $singleEvent['image'],
            'eventUrl' => $singleEvent['eventUrl'],
            'source_url' => $singleEvent['source_url'],
            'organizer_name' => $singleEvent['organizer_name'],
            'address' => $singleEvent['address'],
            'offer_url' => $singleEvent['offer_url'],
            'price' => isset($singleEvent['register_price_view']) ? $singleEvent['register_price_view'] : $singleEvent['price_view'],
            'subtitle' => $singleEvent['subtitle'],
            'register_price' => $singleEvent['register_price'],
            'register_price_view' => $singleEvent['register_price_view'],
            'price_currency' => $singleEvent['price_currency'],
            'originalUrl' => isset($singleEvent['originalUrl']) ? $singleEvent['originalUrl'] : '',
        );

        return $event;
    }

    public function filterIndexAction()
    {
        // Get time
        $time = Pi::api('time', 'event')->makeTime();
        // Set info
        $where = array(
            'status' => 1,
            'type' => 'event'
        );
        $order = array('time_publish DESC', 'id DESC');
        $events = Pi::api('event', 'event')->getEventList($where, $order, '', '', 'full', 'story');
        $listEvent = array();
        foreach ($events as $event) {
            $listEvent[] = Pi::api('event', 'event')->canonizeEventJson($event, $time, true);
        }
        // Set view
        return $listEvent;
    }

    public function filterCategoryAction()
    {
        //Get from url
        $slug = $this->params('slug');
        // Get category
        $category = Pi::api('topic', 'news')->getTopicFull($slug, 'slug');
        // Check category
        if (!$category || $category['status'] != 1) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The category not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get time
        $time = Pi::api('time', 'event')->makeTime();
        // Set info
        $where = array(
            'status' => 1,
            'type' => 'event',
            'topic' => $category['ids'],
        );
        $order = array('time_publish DESC', 'id DESC');
        $events = Pi::api('event', 'event')->getEventList($where, $order, '', '', 'full', 'link');
        $listEvent = array();
        foreach ($events as $event) {
            $listEvent[] = Pi::api('event', 'event')->canonizeEventJson($event, $time);
        }
        // Set view
        return $listEvent;
    }

    public function registerClickAction()
    {
        $id = $this->params('id');
        $id = intval($id);
        $result = array();

        if ($id > 0) {
            $this->getModel('extra')->increment('register_click', array('id' => $id));
            $result['status'] = 1;
        } else {
            $result['status'] = 0;
        }

        return $result;
    }

    public function hitAction()
    {
        // Get info from url
        $module = $this->params('module');
        $slug = $this->params('slug');

        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Find event
        $event = Pi::api('event', 'event')->getEventSingle($slug, 'slug', 'full');

        // Update Hits
        if ($config['event_all_hits']) {
            Pi::model('story', 'news')->increment('hits', array('id' => $event['id']));
        } else {
            if(!isset($_SESSION['hits_events'][$event['id']])){
                if(!isset($_SESSION['hits_events'])){
                    $_SESSION['hits_events'] = array();
                }

                $_SESSION['hits_events'][$event['id']] = false;
            }

            if(!$_SESSION['hits_events'][$event['id']]){
                Pi::model('story', 'news')->increment('hits', array('id' => $event['id']));
                $_SESSION['hits_events'][$event['id']] = true;
            }
        }

        /**
         * Get new hit count
         */
        $event = Pi::model('story', 'news')->find($event['id']);

        return array(
            'status' => 1,
            'hits' => (int) $event->hits,
        );
    }
}
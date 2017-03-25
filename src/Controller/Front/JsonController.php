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
        $recommended = $this->params('recommended');
        $limit = $this->params('limit');
        $order = $this->params('order');

        // Set has search result
        $hasSearchResult = true;

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
        $whereLink = array(
            'status' => 1,
            'type' => 'event'
        );
        if (!empty($recommended) && $recommended == 1) {
            $whereLink['recommended'] = 1;
        }

        // Set page title
        $pageTitle = __('List of events');

        $order = array('time_publish DESC', 'id DESC');
        $orderExtra = array('time_start DESC', 'id DESC');

        // Get category information from model
        if (!empty($category)) {
            // Get category
            $category = Pi::api('topic', 'news')->getTopicFull($category, 'slug');
            // Check category
            if (!$category || $category['status'] != 1) {
                return $result;
            }
            // Set page title
            $pageTitle = sprintf(__('List of events on %s category'), $category['title']);
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
            $eventIDFavourite = Pi::api('favourite', 'favourite')->userFavourite($uid, $module);
            // Set page title
            $pageTitle = ('All favourite events by you');
        }

        // Set event ID list
        $checkTitle = false;
        $eventIDList = array();

        // Check title from event table
        if (isset($title) && !empty($title)) {
            $checkTitle = true;
            $titles = is_array($title) ? $title : array($title);
            $columns = array('id');
            $select = $this->getModel('extra')->select()->columns($columns)->where(function ($where) use ($titles, $recommended) {
                $whereMain = clone $where;
                $whereKey = clone $where;
                $whereMain->equalTo('status', 1);
                if (!empty($recommended) && $recommended == 1) {
                    $whereMain->equalTo('recommended', 1);
                }
                foreach ($titles as $title) {
                    $whereKey->like('title', '%' . $title . '%')->and;
                }
                $where->andPredicate($whereMain)->andPredicate($whereKey);
            })->order($orderExtra);
            $rowset = $this->getModel('extra')->selectWith($select);
            foreach ($rowset as $row) {
                $eventIDList[$row->id] = $row->id;
            }
        }

        // Set info
        $event = array();
        $count = 0;

        $offset = (int)($page - 1) * $config['view_perpage'];
        $limit = (intval($limit) > 0) ? intval($limit) : intval($config['view_perpage']);

        // Set category on where link
        if (isset($categoryIDList) && !empty($categoryIDList)) {
            $whereLink['category'] = $categoryIDList;
        }

        // Set event on where link from title and attribute
        if ($checkTitle) {
            if (!empty($eventIDList)) {
                $whereLink['story'] = $eventIDList;
            } else {
                $hasSearchResult = false;
            }
        }

        // Set favourite events on where link
        if (!empty($favourite) && $favourite == 1 && isset($eventIDFavourite)) {
            if (isset($whereLink['story']) && !empty($whereLink['story'])) {
                $whereLink['story'] = array_intersect($eventIDFavourite, $whereLink['story']);
            } elseif (!empty($whereLink['story'])) {
                $whereLink['story'] = $eventIDFavourite;
            } else {
                $hasSearchResult = false;
            }
        }

        // Set tag events on where link
        if (!empty($tag) && isset($eventIDTag)) {
            if (isset($whereLink['story']) && !empty($whereLink['story'])) {
                $whereLink['story'] = array_intersect($eventIDTag, $whereLink['story']);
            } elseif (!empty($whereLink['story'])) {
                $whereLink['story'] = $eventIDTag;
            } else {
                $hasSearchResult = false;
            }
        }

        // Check has Search Result
        if ($hasSearchResult) {
            $event = Pi::api('event', 'event')->getEventList($whereLink, $order, $offset, $limit, 'full', 'link');
            $count = Pi::api('api', 'news')->getStoryCount($whereLink, 'link');
        }

        // Set result
        $result = array(
            'events' => $event,
            'paginator' => array(
                'count' => $count,
                'limit' => intval($config['view_perpage']),
                'page' => $page,
            ),
            'condition' => array(
                'title' => $pageTitle,
            ),
        );

        return $result;
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
}
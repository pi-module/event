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
            $listEvent[] = Pi::api('event', 'event')->canonizeEventJson($event, $time);
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
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

class CategoryController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
        $page = $this->params('page', 1);
        $slug = $this->params('slug');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get category
        $category = Pi::api('topic', 'news')->getTopicFull($slug, 'slug');
        // Check category
        if (!$category || $category['status'] != 1) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The category not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Set info
        $where = array(
            'status' => 1,
            'type' => 'event',
            'topic' => $category['ids'],
        );
        $offset = (int)($page - 1) * $config['view_perpage'];
        $order = array('time_publish DESC', 'id DESC');
        $limit = intval($config['view_perpage']);
        // Get list of event
        $listEvent = Pi::api('event', 'event')->getEventList($where, $order, $offset, $limit, 'full', 'link');
        // Set template
        $template = array(
            'module' => 'event',
            'controller' => 'category',
            'action' => 'index',
            'slug' => $category['slug'],
        );
        // Get paginator
        $paginator = Pi::api('api', 'news')->getStoryPaginator($template, $where, $page, $limit, 'link');
        // Set view
        $this->view()->headTitle($category['seo_title']);
        $this->view()->headdescription($category['seo_description'], 'set');
        $this->view()->headkeywords($category['seo_keywords'], 'set');
        $this->view()->setTemplate('event-list');
        $this->view()->assign('eventList', $listEvent);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('category', $category);
        $this->view()->assign('title', sprintf(__('Event list on %s'), $category['title']));
    }

    public function listAction()
    {
        $this->view()->setTemplate('event-category');
    }
}
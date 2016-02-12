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

class IndexController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
        $page = $this->params('page', 1);
        //$search = $this->params('q');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Set info
        $where = array(
            'status' => 1,
            'type' => 'event'
        );
        $offset = (int)($page - 1) * $config['view_perpage'];
        $order = array('time_publish DESC', 'id DESC');
        $limit = intval($config['view_perpage']);
        // Get list of story
        $listEvent = array();
        $listStory = Pi::api('api', 'news')->getStoryList($where, $order, $offset, $limit, 'full', 'link');
        foreach ($listStory as $singleStory) {
            $listEvent[$singleStory['id']] = Pi::api('event', 'event')->joinExtra($singleStory);
        }
        // Set template
        $template = array(
            'module' => 'event',
            'controller' => 'index',
            'action' => 'index',
        );
        // Get paginator
        $paginator = Pi::api('api', 'news')->getStoryPaginator($template, $where, $page, $limit, 'link');
        // Set view
        $this->view()->setTemplate('event-list');
        $this->view()->assign('eventList', $listEvent);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('title', __('Event list'));
    }
}
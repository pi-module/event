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
        // Get list of event
        $listEvent = Pi::api('event', 'event')->getEventList($where, $order, $offset, $limit, 'full', 'story');
        // Set template
        $template = array(
            'module' => 'event',
            'controller' => 'index',
            'action' => 'index',
        );
        // Get paginator
        $paginator = Pi::api('api', 'news')->getStoryPaginator($template, $where, $page, $limit, 'story');
        // Set view
        $this->view()->setTemplate('event-list');
        $this->view()->assign('eventList', $listEvent);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('title', __('Event list'));
    }
}
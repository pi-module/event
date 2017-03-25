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
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Check homepage type
        switch ($config['view_template']) {
            default:
            case 'angular':
                // Set filter url
                $filterUrl = Pi::url($this->url('', array(
                    'controller' => 'json',
                    'action' => 'filterIndex'
                )));
                // Get location list
                $locationList = Pi::api('event', 'event')->getLocationList();
                // Get category list
                $categoryList = Pi::api('event', 'event')->getCategoryList();
                // Get price filter list
                $priceFilterList = Pi::api('event', 'event')->getPriceFilterList();
                // Set view
                $this->view()->setTemplate('event-angular');
                $this->view()->assign('config', $config);
                $this->view()->assign('filterUrl', $filterUrl);
                $this->view()->assign('locationList', $locationList);
                $this->view()->assign('categoryList', $categoryList);
                $this->view()->assign('priceFilterList', $priceFilterList);
                $this->view()->assign('title', __('Event list'));
                $this->view()->assign('isHomepage', 1);
                $this->view()->assign('isCategoryPage', 0);
                // Language
                __('Toman');
                break;
            case 'angularnew':
                $this->view()->setTemplate('event-angular-new');
                $this->view()->assign('config', $config);
                $this->view()->assign('pageType', 'all');
                break;
        }
    }
}
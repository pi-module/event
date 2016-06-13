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
        // Set filter url
        $filterUrl = Pi::url($this->url('', array(
            'controller' => 'json',
            'action' => 'filterCategory',
            'slug' => $slug,
        )));
        // Get location list
        $locationList = Pi::api('event', 'event')->getLocationList();
        // Get category list
        $categoryList = Pi::api('event', 'event')->getCategoryList();
        // Set view
        $this->view()->headTitle($category['seo_title']);
        $this->view()->headdescription($category['seo_description'], 'set');
        $this->view()->headkeywords($category['seo_keywords'], 'set');
        $this->view()->setTemplate('event-angular');
        $this->view()->assign('config', $config);
        $this->view()->assign('filterUrl', $filterUrl);
        $this->view()->assign('locationList', $locationList);
        $this->view()->assign('categoryList', $categoryList);
        $this->view()->assign('category', $category);
        $this->view()->assign('title', sprintf(__('Event list on %s'), $category['title']));
        $this->view()->assign('isCategoryPage', 1);
        // Language
        __('Toman');
    }
}
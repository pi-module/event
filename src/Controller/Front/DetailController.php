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
use Module\Event\Form\RegisterForm;
use Module\Event\Form\RegisterFilter;

class DetailController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
        $slug = $this->params('slug');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Find event
        $event = Pi::api('event', 'event')->getEvent($slug, 'slug', 'full');
        // Check event
        if (!$event || $event['status'] != 1) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The event not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Update Hits
        Pi::model('story', 'news')->update(
            array('hits' => $event['hits'] + 1),
            array('id' => $event['id'])
        );
        // Set guide module options
        $event['guideItemInfo'] = array();
        $event['guideLocationInfo'] = array();
        $event['guideCategoryInfo'] = array();
        if (Pi::service('module')->isActive('guide')) {
            // Set item info
            if (!empty($event['guide_item'])) {
                foreach ($event['guide_item'] as $item) {
                    $event['guideItemInfo'][$item] = Pi::api('item', 'guide')->getItem($item);
                }
            }
            // Set location info
            if (!empty($event['guide_location'])) {
                foreach ($event['guide_location'] as $location) {
                    $event['guideLocationInfo'][$location] = Pi::api('location', 'guide')->getLocation($location);
                }
            }
            // Set category info
            if (!empty($event['guide_category'])) {
                foreach ($event['guide_category'] as $category) {
                    $event['guideCategoryInfo'][$category] = Pi::api('category', 'guide')->getCategory($category);
                }
            }
        }
        // Set form
        $option = array();
        $option['stock'] = ($event['register_stock'] > 10) ? 10 : $event['register_stock'];
        $form = new RegisterForm('event', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        $form->setAttribute('action', $event['eventOrder']);
        $form->setData($event);
        // Set template
        switch ($config['view_template']) {
            case 'guide':
                $template = 'event-guide';
                break;

            default:
            case 'detail':
                $template = 'event-detail';
                break;
        }
        // Set view
        $this->view()->headTitle($event['seo_title']);
        $this->view()->headDescription($event['seo_description'], 'set');
        $this->view()->headKeywords($event['seo_keywords'], 'set');
        $this->view()->setTemplate($template);
        $this->view()->assign('event', $event);
        $this->view()->assign('config', $config);
        $this->view()->assign('form', $form);
    }
}
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
        $event = Pi::api('event', 'event')->getEventSingle($slug, 'slug', 'full');

        if($slug != $event['slug']){
            return $this->redirect()->toRoute('', array('slug' => $event['slug']))->setStatusCode(301);
        }

        // Check event
        if (!$event || $event['status'] != 1) {
            $this->getResponse()->setStatusCode(404);
            $this->terminate(__('The event not found.'), '', 'error-404');
            $this->view()->setLayout('layout-simple');
            return;
        }

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

        // Set event topic
        $eventTopic = array();
        if (!empty($event['topic'])) {
            $eventTopic = array_merge($eventTopic, $event['topic']);
        }

        // Set guide module options
        $event['guideItemInfo'] = array();
        $event['guideLocationInfo'] = array();
        $event['guideCategoryInfo'] = array();
        if (Pi::service('module')->isActive('guide')) {
            // Set item info
            if (!empty($event['guide_item'])) {
                $event['guideItemInfo'] = array(
                    'commercial' => array(),
                    'free' => array(),
                );
                foreach ($event['guide_item'] as $item) {
                    $guideItem = Pi::api('item', 'guide')->getItem($item);
                    if (Pi::api('item', 'guide')->IsExpired($guideItem)) {
                        continue;         
                    }
                    if (isset($guideItem) && !empty($guideItem) && $guideItem['status'] == 1) {
                        if (in_array($guideItem['item_type'], array('commercial', 'person'))) {
                            $event['guideItemInfo']['commercial'][$item] = $guideItem;
                        } else {
                            $event['guideItemInfo']['free'][$item] = $guideItem;
                        }
                    }
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
                    $eventTopic = array_merge($eventTopic, $event['guide_category']);
                }
            }
        }
        // Set form
        $option = array();
        $option['stock'] = ($event['register_stock'] == 0 || $event['register_stock'] -  $event['register_sales'] > 10) ? 10 : ($event['register_stock'] -  $event['register_sales']);
        $form = new RegisterForm('event', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        $form->setAttribute('action', $event['eventOrder']);
        $form->setData($event);
        // Related
        if ($config['related_event'] && !empty($eventTopic)) {
            $eventTopic = array_unique($eventTopic);
            $relatedEvents = Pi::api('event', 'event')->getEventRelated($event['id'], $eventTopic);
            $this->view()->assign('relatedEvents', $relatedEvents);
        }
        $ended = false;
        $today = strtotime('today');
        if (($event['time_end'] == 0 && $event['time_start'] < $today) || ($event['time_end'] > 0 && $event['time_end'] < $today)) {
            $ended = true;
        }
        // Set vote
        if ($config['vote_bar'] && Pi::service('module')->isActive('vote')) {
            $vote = array();
            $vote['point'] = $event['point'];
            $vote['count'] = $event['count'];
            $vote['item'] = $event['id'];
            $vote['table'] = 'story';
            $vote['module'] = 'news';
            $vote['subModule'] = 'event';
            $vote['type'] = 'star';
            $scoreList = Pi::registry('scoreList', 'vote')->read();
            if (isset($scoreList[$module])) {
                $vote['score'] = $scoreList[$module];
                $vote['points'] = Pi::api('vote', 'vote')->getVoteScore('news', 'story', $event['id']);
            }
            $this->view()->assign('vote', $vote);
        }
        // favourite
        if ($config['favourite_bar'] && Pi::service('module')->isActive('favourite')) {
            $favourite['is'] = Pi::api('favourite', 'favourite')->loadFavourite('event', 'story', $event['id']);
            $favourite['item'] = $event['id'];
            $favourite['table'] = 'story';
            $favourite['module'] = 'event';
            $this->view()->assign('favourite', $favourite);
            
            $configFavourite = Pi::service('registry')->config->read('favourite');
            if ($configFavourite['favourite_list']) {
                $favouriteList = Pi::api('favourite', 'favourite')->listItemFavourite('event', 'story', $event['id']);
                $this->view()->assign('favouriteList', $favouriteList);
            }
        }
        
        // my places
        $uid = Pi::user()->getId();
        $count = 0;
        if ($uid) {
            $where = array(
                'uid' => $uid,
                'module' => 'event',
                'product' =>  $event['id'],
                'status_payment' => 2
            );    
            
            
            $orderTable = Pi::model('order', 'order')->getTable();
            $detailTable = Pi::model("detail", 'order')->getTable();
            $invoiceTable = Pi::model("invoice", 'order')->getTable();
            $invoiceInstallmentTable = Pi::model("invoice_installment", 'order')->getTable();
         
            $select = Pi::db()->select();
            $select
            ->from(array('order' => $orderTable))->columns(array())
            ->join(array('detail' => $detailTable), 'detail.order = order.id', array('count' => new \Zend\Db\Sql\Expression('SUM(number)')))
            ->join(array('invoice' => $invoiceTable), 'invoice.order = order.id', array())
            ->join(array('invoice_installment' => $invoiceInstallmentTable), 'invoice_installment.invoice = invoice.id', array('status_payment'))
            ->where ($where);
        
            $count = Pi::db()->query($select)->current()['count'];
            if (!$count) {
                $count = 0;
            }
        }
        
        
        
        // Set view
        $this->view()->headTitle($event['seo_title']);
        $this->view()->headDescription($event['seo_description'], 'set');
        $this->view()->headKeywords($event['seo_keywords'], 'set');
        $this->view()->setTemplate('event-detail');
        $this->view()->assign('event', $event);
        $this->view()->assign('count', $count);
        $this->view()->assign('config', $config);
        $this->view()->assign('form', $form);
        $this->view()->assign('ended', $ended);
    }
}

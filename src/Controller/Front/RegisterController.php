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

class RegisterController extends ActionController
{
    /* public function indexAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // Check order is active or inactive
        if (!$this->config('order_active')) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('So sorry, At this moment order is inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get info
        $uid = Pi::user()->getId();
        $list = array();
        $order = array('id DESC');
        $where = array('uid' => $uid);
        $select = $this->getModel('order')->select()->where($where)->order($order);
        $rowset = $this->getModel('order')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = Pi::api('order', 'event')->canonizeOrder($row);
        }
        // Set view
        $this->view()->setTemplate('register-list');
        $this->view()->assign('list', $list);
    } */

    public function addAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // Get info from url
        $module = $this->params('module');
        // Check order is active or inactive
        if (!$this->config('order_active')) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('So sorry, At this moment order is inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Check post
        if ($this->request->isPost()) {
            // Get post
            $data = $this->request->getPost()->toArray();
            // Set number
            if (isset($data['number']) && intval($data['number']) > 0) {
                $number = intval($data['number']);
            } else {
                $number = 1;
            }
            // Check id
            if (!intval($data['id'])) {
                $this->getResponse()->setStatusCode(401);
                $this->terminate(__('You need select event'), '', 'error-denied');
                $this->view()->setLayout('layout-simple');
                return;
            }
            // check order module
            if (Pi::service('module')->isActive('order') && !empty($data)) {
                // Find event
                $event = Pi::api('event', 'event')->getEvent(intval($data['id']), 'id', 'full');
                // Check event
                if (!$event || $event['status'] != 1) {
                    $this->getResponse()->setStatusCode(404);
                    $this->terminate(__('The event not found.'), '', 'error-404');
                    $this->view()->setLayout('layout-simple');
                    return;
                }
                // Check can register
                if (!$event['register_can']) {
                    $message = __('This event not available for registration');
                    $this->jump($event['eventUrl'], $message, 'error');
                }
                // Check capacity
                if ($event['register_stock'] === 0 || $event['register_stock'] < $number) {
                    $message = __('This event do not have enough capacity');
                    $this->jump($event['eventUrl'], $message, 'error');
                }
                // Set extra
                $extra = array();
                $extra['view_type'] = 'template';
                $extra['view_template'] = 'order-detail';
                $extra['getDetail'] = true;
                // Set singel Product
                $singleProduct = array(
                    'product' => $event['id'],
                    'product_price' => $event['register_price'],
                    'discount_price' => 0,
                    'shipping_price' => 0,
                    'setup_price' => 0,
                    'packing_price' => 0,
                    'vat_price' => 0,
                    'number' => $number,
                    'title' => $event['title'],
                    'extra' => json::encode($extra),
                );
                // Set order array
                $order = array();
                $order['module_name'] = $module;
                $order['module_item'] = $event['id'];
                $order['type_payment'] = 'recurring';
                $order['type_commodity'] = 'service';
                $order['product'][$event['id']] = $singleProduct;
                // Set and go to order
                $url = Pi::api('order', 'order')->setOrderInfo($order);
                Pi::service('url')->redirect($url);
            } else {
                $this->getResponse()->setStatusCode(401);
                $this->terminate(__('Order module not installed'), '', 'error-denied');
                $this->view()->setLayout('layout-simple');
                return;
            }
        } else {
            $url = array('', 'controller' => 'register', 'action' => 'index');
            return $this->redirect()->toRoute('', $url);
        }
    }

    /* public function detailAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();
        // Check order is active or inactive
        if (!$this->config('order_active')) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('So sorry, At this moment order is inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Get info from url
        $id = $this->params('id');
        $module = $this->params('module');
        // Get order and event
        $orderEvent = Pi::api('order', 'event')->getOrder($id);
        $event = Pi::api('event', 'event')->getEvent($orderEvent['event']);
        $orderOrder = Pi::api('order', 'order')->getOrder($orderEvent['order_id']);
        // Check order
        if (!$orderOrder['id']) {
            $url = array('', 'module' => $module, 'controller' => 'index', 'action' => 'index');
            $this->jump($url, __('Order not set.'));
        }
        // Check user
        if ($orderOrder['uid'] != Pi::user()->getId()) {
            $url = array('', 'module' => $module, 'controller' => 'index', 'action' => 'index');
            $this->jump($url, __('It not your order.'));
        }
        // Check status payment
        if ($orderOrder['status_payment'] != 2) {
            $url = array('', 'module' => $module, 'controller' => 'index', 'action' => 'index');
            $this->jump($url, __('This order not pay'));
        }
        // Check time payment
        $time = time() - 3600;
        if ($time > $orderOrder['time_payment']) {
            $url = array('', 'module' => $module, 'controller' => 'index', 'action' => 'index');
            $this->jump($url, __('This is old order and you pay it before'));
        }
        // Set links
        $orderOrder['order_link'] = Pi::url($this->url('order', array(
            'module' => 'order',
            'controller' => 'detail',
            'action' => 'index',
            'id' => $orderOrder['id']
        )));
        $orderOrder['user_link'] = Pi::url($this->url('order', array(
            'module' => 'order',
            'controller' => 'index',
            'action' => 'index',
        )));
        $orderOrder['list_link'] = Pi::url($this->url('', array(
            'module' => $module,
            'controller' => 'order',
            'action' => 'list',
        )));

        // Get invoice information
        Pi::service('i18n')->load(array('module/order', 'default'));
        $invoices = Pi::api('invoice', 'order')->getInvoiceFromOrder($orderOrder['id']);
        // Set view
        $this->view()->setTemplate('register-detail');
        $this->view()->assign('orderOrder', $orderOrder);
        $this->view()->assign('orderEvent', $orderEvent);
        $this->view()->assign('invoices', $invoices);
        $this->view()->assign('event', $event);
    } */
}
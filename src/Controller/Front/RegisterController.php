<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\Event\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;

class RegisterController extends ActionController
{
    public function addAction()
    {
        // Get uid
        $uid = Pi::user()->getId();
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
                $event = Pi::api('event', 'event')->getEventSingle(intval($data['id']), 'id', 'full');
                // Check event
                if (!$event || $event['status'] != 1) {
                    $this->getResponse()->setStatusCode(404);
                    $this->terminate(__('The event not found.'), '', 'error-404');
                    $this->view()->setLayout('layout-simple');
                    return;
                }

                // Save statistics
                if (Pi::service('module')->isActive('statistics')) {
                    Pi::api('log', 'statistics')->save('event', 'register', $event['id']);
                }

                // Check can register
                if (!$event['register_can']) {
                    $message = __('This event not available for registration');
                    $this->jump($event['eventUrl'], $message, 'error');
                }
                // Check capacity
                if ($event['register_stock'] > 0 && ($event['register_stock'] -  $event['register_sales'] === 0 || $event['register_stock'] -  $event['register_sales']  < $number)) {
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
                    'extra' => json_encode($extra),
                );
                // Set order array
                $order = array();
                $order['module'] = $module;
                $order['product'] = $event['id'];
                $order['type_payment'] = 'onetime';
                $order['type_commodity'] = 'service';
                $order['product'][$event['id']] = $singleProduct;
                // Set session_order if user not login
                if ($uid == 0) {
                    $_SESSION['session_order'] = array(
                        'module' => 'event',
                        'value' => $singleProduct,
                    );
                }
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
}
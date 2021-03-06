<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\Event\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Module\Event\Form\RegisterManualForm;
use Module\Event\Form\RegisterManualFilter;

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
                if ($event['register_stock'] > 0
                    && ($event['register_stock'] - $event['register_sales'] === 0
                        || $event['register_stock'] - $event['register_sales'] < $number)
                ) {
                    $message = __('This event do not have enough capacity');
                    $this->jump($event['eventUrl'], $message, 'error');
                }

                // Set singel Product
                $singleProduct = [
                    'product'        => $event['id'],
                    'product_price'  => $event['register_price'],
                    'discount_price' => 0,
                    'shipping_price' => 0,
                    'setup_price'    => 0,
                    'packing_price'  => 0,
                    'vat_price'      => 0,
                    'number'         => $number,
                    'title'          => $event['title'],
                    'extra'          => json_encode(
                        [
                            'view_type'     => 'template',
                            'view_template' => 'order-detail',
                            'getDetail'     => true,
                        ]
                    ),
                ];

                // Set order array
                $order = [
                    'module_name'    => $module,
                    'module_table'   => 'extra',
                    'type_payment'   => 'onetime',
                    'type_commodity' => 'service',
                    'total_discount' => 0,
                    'total_shipping' => 0,
                    'total_packing'  => 0,
                    'total_setup'    => 0,
                    'total_vat'      => 0,
                    'can_pay'        => 1,
                    'product'        => [
                        $event['id'] => $singleProduct,
                    ],
                ];

                // Set session_order if user not login
                if ($uid == 0) {
                    $_SESSION['session_order'] = [
                        'module' => 'event',
                        'value'  => $singleProduct,
                    ];
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
            $url = ['', 'controller' => 'register', 'action' => 'index'];
            return $this->redirect()->toRoute('', $url);
        }
    }

    public function processAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            if (isset($data['id']) && intval($data['id']) > 0 && isset($data['number']) && intval($data['number']) > 0) {
                $url = [
                    'action' => 'manual',
                    'id'     => $data['id'],
                    'number' => $data['number'],
                ];

                return $this->jump($url);
            }
        }
    }

    public function manualAction()
    {
        // Check user is login or not
        Pi::service('authentication')->requireLogin();

        // Get info from url
        $module = $this->params('module');
        $id     = $this->params('id');
        $number = $this->params('number');
        $uid    = Pi::user()->getId();

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check order is active or inactive
        if (!$this->config('order_active')) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('So sorry, At this moment order is inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Find event
        $event = Pi::api('event', 'event')->getEventSingle(intval($id), 'id', 'full');

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
        if ($event['register_stock'] > 0
            && ($event['register_stock'] - $event['register_sales'] === 0
                || $event['register_stock'] - $event['register_sales'] < $number)
        ) {
            $message = __('This event do not have enough capacity');
            $this->jump($event['eventUrl'], $message, 'error');
        }

        // Set order params
        $orderParams = [
            'uid'        => $uid,
            'event'      => $event['id'],
            'order_id'   => 0,
            'number'     => $number,
            'price'      => $event['register_price'] * $number,
            'vat'        => 0,
            'total'      => 0,
            'time_order' => time(),
            'time_start' => $event['time_start'],
            'time_end'   => $event['time_end'],
            'status'     => 2,
            'extra'      => '',
        ];

        // Get group
        $groups = Pi::registry('display_group', 'user')->read();
        $group  = array_shift($groups);
        list($fields, $filters) = $this->getGroupElements($group['id']);

        // Set option
        $option = [
            'fields'  => $fields,
            'filters' => array_filter($filters),
        ];

        // Set form
        $form = new RegisterManualForm('event', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();

            // Form filter
            $form->setInputFilter(new RegisterManualFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();

                // Update user profile
                Pi::api('user', 'order')->updateUserInformation($values);

                // Set image
                $orderParams['main_image'] = $values['main_image'];

                // Save order
                $row = Pi::model('order', $this->getModule())->createRow();
                $row->assign($orderParams);
                $row->save();

                // jump
                $message = __('You register on event successfully.');
                $this->jump($event['eventUrl'], $message);
            }
        }

        // Set view
        $this->view()->headTitle($event['seo_title']);
        $this->view()->headDescription($event['seo_description'], 'set');
        $this->view()->headKeywords($event['seo_keywords'], 'set');
        $this->view()->setTemplate('register-manual');
        $this->view()->assign('event', $event);
        $this->view()->assign('config', $config);
        $this->view()->assign('form', $form);
        $this->view()->assign('orderParams', $orderParams);
        $this->view()->assign('title', sprintf(__('Register on %s'), $event['title']));
    }

    protected function getGroupElements($groupId)
    {
        $meta     = Pi::registry('field', 'user')->read('', 'edit');
        $fields   = Pi::registry('display_field', 'user')->read($groupId);
        $elements = [];
        $filters  = [];

        foreach ($fields as $field) {
            if (!isset($meta[$field])) {
                continue;
            }
            $element = Pi::api('form', 'user')->getElement($field);
            $filter  = Pi::api('form', 'user')->getFilter($field);
            if ($element) {
                $elements[] = $element;
            }
            if ($filter) {
                $filters[] = $filter;
            }
        }

        return [$elements, $filters];
    }
}

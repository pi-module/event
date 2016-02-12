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
namespace Module\Event\Api;

use Pi;
use Pi\Application\Api\AbstractApi;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Json\Json;
use Zend\Math\Rand;

/*
 * Pi::api('order', 'event')->getProductDetails($product);
 * Pi::api('order', 'event')->postPaymentUpdate($order, $basket);
 * Pi::api('order', 'event')->getOrder($id);
 * Pi::api('order', 'event')->canonizeOrder($order);
 */

class Order extends AbstractApi
{
    public function getProductDetails($product)
    {
        $event = Pi::api('event', 'event')->getEvent($product, 'id', 'light');
        $event['productUrl'] = $event['eventUrl'];
        return $event;
    }

    public function postPaymentUpdate($order, $basket)
    {
        // Update time
        if ($order['module_item'] > 0) {
            // Set number
            $number = 1;
            foreach ($basket as $product) {
                $number = $product['number'];
            }
            // Get event
            $event = Pi::api('event', 'event')->getEvent($order['module_item'], 'id', 'light');
            $setting = Json::decode($event['setting'], true);
            // generate code
            $code = $this->generateCode();
            // Save order
            $values = array(
                'uid' => $order['uid'],
                'event' => $event['id'],
                'order_id' => $order['id'],
                'number' => $number,
                'price' => $order['product_price'],
                'vat'=> $order['vat_price'],
                'total'=> $order['total_price'],
                'time_order' => time(),
                'time_start' => $event['time_start'],
                'time_end' => $event['time_end'],
                'type' => $event['register_type'],
                'code_public' => $code['public'],
                'code_private' => $code['private'],
                'status' => 1,
                'extra' => Json::encode($setting['action']),
            );
            $row = Pi::model('order', $this->getModule())->createRow();
            $row->assign($values);
            $row->save();
            // Set url
            $url = Pi::url(Pi::service('url')->assemble('plans', array(
                'module' => $this->getModule(),
                'controller' => 'order',
                'action' => 'finish',
                'id' => $row->id,
            )));
            // Send notification
            //Pi::api('notification', 'order')->newOrder($order, $event);
            // Set back url
            return $url;
        }
    }

    public function getOrder($id)
    {
        $order = Pi::model('order', $this->getModule())->find($id);
        $order = $this->canonizeOrder($order);
        return $order;
    }

    public function canonizeOrder($order)
    {
        // Check
        if (empty($order)) {
            return '';
        }
        // Order to array
        $order = $order->toArray();
        // Set time
        $order['time_order_view'] = _date($order['time_order']);
        $order['time_start_view'] = _date($order['time_start']);
        $order['time_end_view'] = _date($order['time_end']);
        // return
        return $order;
    }

    public function generateCode()
    {
        // Generate code_public
        $public = Rand::getInteger(100000, 999999);
        // Generate code_private
        // Without 0 o
        $private = Rand::getString(6, 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789', true);
        // Set values
        $result = array(
            'public' => $public,
            'private' => $private,
        );
        return $result;
    }
}
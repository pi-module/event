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
use Zend\Json\Json;

/*
 * Pi::api('event', 'event')->getEvent($parameter, $field, $type);
 * Pi::api('event', 'event')->getExtra($id);
 * Pi::api('event', 'event')->joinExtra($event);
 * Pi::api('event', 'event')->canonizeExtra($extra);
 * Pi::api('event', 'event')->canonizeEvent($event);
 */

class Event extends AbstractApi
{
    public function getEvent($parameter, $field = 'id', $type = 'full')
    {
        $event = Pi::api('api', 'news')->getStorySingle($parameter, $field, $type);
        $event = $this->joinExtra($event);
        return $event;
    }

    public function getExtra($id)
    {
        $extra = Pi::model('extra', $this->getModule())->find($id);
        $extra = $this->canonizeExtra($extra);
        return $extra;
    }

    public function joinExtra($event)
    {
        $extra = $this->getExtra($event['id']);
        $event = array_merge($event, $extra);
        $event = $this->canonizeEvent($event);
        return $event;
    }

    public function canonizeExtra($extra) {
        // Check
        if (empty($extra)) {
            return '';
        }
        // object to array
        if (is_object($extra)) {
            $extra = $extra->toArray();
        }
        // Set registration_details
        $extra['registration_details'] = Pi::service('markup')->render($extra['registration_details'], 'html', 'html');
        // Set time
        $extra['time_start_view'] = (empty($extra['time_start'])) ? '' : _date($extra['time_start'], array('pattern' => 'yyyy-MM-dd'));
        $extra['time_end_view'] = (empty($extra['time_end'])) ? '' : _date($extra['time_end'], array('pattern' => 'yyyy-MM-dd'));
        // register_price
        if (is_numeric($extra['register_price']) && $extra['register_price'] > 0) {
            $configSystem = Pi::service('registry')->config->read('system');
            if (Pi::service('module')->isActive('order')) {
                $extra['register_price_view'] = Pi::api('api', 'order')->viewPrice($extra['register_price']);
            } else {
                $extra['register_price_view'] = _currency($extra['register_price']);
            }
            $extra['price_currency'] = empty($configSystem['number_currency']) ? 'USD' : $configSystem['number_currency'];
        } else {
            $extra['register_price_view'] = __('this event is free!');
        }
        //
        $extra['guide_category'] = Json::decode($extra['guide_category']);
        $extra['guide_location'] = Json::decode($extra['guide_location']);
        $extra['guide_item'] = Json::decode($extra['guide_item']);
        return $extra;
    }

    public function canonizeEvent($event) {
        // Check
        if (empty($event)) {
            return '';
        }
        // Set event url
        $event['eventUrl'] = Pi::url(Pi::service('url')->assemble('event', array(
            'module' => $this->getModule(),
            'controller' => 'index',
            'slug' => $event['slug'],
        )));
        // Set category
        if (isset($event['topics']) && !empty($event['topics'])) {
            $topicList = array();
            foreach ($event['topics'] as $topic) {
                $topicList[] = array(
                    'title' => $topic['title'],
                    'url' => Pi::url(Pi::service('url')->assemble('event', array(
                        'module' => $this->getModule(),
                        'controller' => 'category',
                        'slug' => $topic['slug'],
                    ))),
                );
            }
            $event['topics'] = $topicList;
        }
        return $event;
    }
}

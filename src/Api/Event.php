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
 * Pi::api('event', 'event')->getListFromId($id);
 * Pi::api('event', 'event')->getEventList($where, $order, $offset, $limit, $type, $table);
 * Pi::api('event', 'event')->canonizeExtra($extra);
 * Pi::api('event', 'event')->canonizeEvent($event);
 * Pi::api('event', 'event')->sitemap();
 * Pi::api('event', 'event')->regenerateImage();
 * Pi::api('event', 'event')->getLocationList();
 * Pi::api('event', 'event')->getCategoryList();
 */

class Event extends AbstractApi
{
    public function getEvent($parameter, $field = 'id', $type = 'full')
    {
        $option =  array(
            'imagePath' => 'event/image'
        );
        $event = Pi::api('api', 'news')->getStorySingle($parameter, $field, $type, $option);
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

    public function getListFromId($id)
    {
        $list = array();
        $where = array('id' => $id, 'status' => 1);
        $select = Pi::model('extra', $this->getModule())->select()->where($where);
        $rowset = Pi::model('extra', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $this->canonizeExtra($row);
            $list[$row->id]['eventUrl'] = Pi::url(Pi::service('url')->assemble('event', array(
                'module' => $this->getModule(),
                'controller' => 'index',
                'slug' => $row->slug,
            )));
        }
        return $list;
    }

    public function getEventList($where, $order, $offset = '', $limit = 10, $type = 'full', $table = 'link')
    {
        $option =  array(
            'imagePath' => 'event/image'
        );
        $listEvent = array();
        $listStory = Pi::api('api', 'news')->getStoryList($where, $order, $offset, $limit, $type, $table, $option);
        foreach ($listStory as $singleStory) {
            $listEvent[$singleStory['id']] = Pi::api('event', 'event')->joinExtra($singleStory);
        }
        return  $listEvent;
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
        // Set register_details
        $extra['register_details'] = Pi::service('markup')->render($extra['register_details'], 'html', 'html');
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
            $extra['register_price_view'] = __('free!');
        }
        // canonize guide module details
        $extra['guide_category'] = Json::decode($extra['guide_category'], true);
        $extra['guide_location'] = Json::decode($extra['guide_location'], true);
        $extra['guide_item'] = Json::decode($extra['guide_item'], true);
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
        // Set register url
        $event['eventOrder'] = Pi::url(Pi::service('url')->assemble('event', array(
            'module' => $this->getModule(),
            'controller' => 'register',
            'action' => 'add',
            //'slug' => $event['slug'],
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

    public function sitemap()
    {
        if (Pi::service('module')->isActive('sitemap')) {
            // Remove old links
            Pi::api('sitemap', 'sitemap')->removeAll('event', 'event');
            // find and import
            $columns = array('id', 'slug', 'status');
            $where = array('type' => array(
                'event'
            ));
            $select = Pi::model('story', 'news')->select()->columns($columns)->where($where);
            $rowset = Pi::model('story', 'news')->selectWith($select);
            foreach ($rowset as $row) {
                // Make url
                $loc = Pi::url(Pi::service('url')->assemble('news', array(
                    'module' => 'event',
                    'controller' => 'index',
                    'slug' => $row->slug,
                )));
                // Add to sitemap
                Pi::api('sitemap', 'sitemap')->groupLink($loc, $row->status, 'event', 'event', $row->id);
            }
        }
    }

    public function regenerateImage()
    {
        // Set info
        $columns = array('id', 'image', 'path');
        $where = array('type' => array(
            'event'
        ));
        $order = array('id ASC');
        $select = Pi::model('story', 'news')->select()->columns($columns)->where($where)->order($order);
        $rowset = Pi::model('story', 'news')->selectWith($select);
        foreach ($rowset as $row) {
            if (!empty($row->image) && !empty($row->path)) {
                // Set image original path
                $original = Pi::path(
                    sprintf('upload/event/image/large/%s/%s',
                        $row->path,
                        $row->image
                    ));
                // Set image large path
                $images['large'] = Pi::path(
                    sprintf('upload/event/image/large/%s/%s',
                        $row->path,
                        $row->image
                    ));
                // Set image medium path
                $images['medium'] = Pi::path(
                    sprintf('upload/event/image/medium/%s/%s',
                        $row->path,
                        $row->image
                    ));
                // Set image thumb path
                $images['thumb'] = Pi::path(
                    sprintf('upload/event/image/thumb/%s/%s',
                        $row->path,
                        $row->image
                    ));
                // Check original exist of not
                if (file_exists($original)) {
                    // Remove old images
                    foreach ($images as $image) {
                        if (file_exists($image)) {
                            Pi::service('file')->remove($image);
                        }
                    }
                    // regenerate
                    Pi::api('image', 'news')->process($row->image, $row->path, 'event/image');
                }
            }
        }
    }

    public function getLocationList()
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set list
        $list = array();
        // Check guide module install
        if (Pi::service('module')->isActive('guide')) {
            $listLocation = Pi::api('location', 'guide')->locationListByLevel($config['filter_location_level']);
            foreach ($listLocation as $location) {
                $list[] = array(
                    'id' => $location['id'],
                    'title' => $location['title'],
                    'value' => sprintf('location-%s-guide', $location['id']),
                );
            }
        }
        // return
        return $list;
    }

    public function getCategoryList()
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set list
        $list = array();
        // Check guide module install
        if (Pi::service('module')->isActive('guide')) {
            $listGuide = Pi::api('category', 'guide')->categoryList();
            foreach ($listGuide as $category) {
                $list[] = array(
                    'id' => $category['id'],
                    'title' => $category['title'],
                    'value' => sprintf('category-%s-guide', $category['id']),
                );
            }
        }
        // Check news module use topic
        if ($config['use_topic']) {
            $listNews = Pi::api('topic', 'news')->getTopicList();
            foreach ($listNews as $topic) {
                $list[] = array(
                    'id' => $topic['id'],
                    'title' => $topic['title'],
                    'value' => sprintf('category-%s-news', $topic['id']),
                );
            }
        }
        // return
        return $list;
    }
}

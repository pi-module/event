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

/*
 * Pi::api('event', 'event')->getEventSingle($parameter, $field, $type);
 * Pi::api('event', 'event')->getEventList($where, $order, $offset, $limit, $type, $table);
 * Pi::api('event', 'event')->getEventRelated($id, $topic);
 * Pi::api('event', 'event')->getListFromId($id);
 * Pi::api('event', 'event')->getLocationList();
 * Pi::api('event', 'event')->getCategoryList();
 * Pi::api('event', 'event')->getPriceFilterList();
 * Pi::api('event', 'event')->canonizeEvent($extra, $event);
 * Pi::api('event', 'event')->canonizeEventJson($event, $time);
 * Pi::api('event', 'event')->sitemap();
 * Pi::api('event', 'event')->regenerateImage();
 */

class Event extends AbstractApi
{
    public function getEventSingle($parameter, $field = 'id', $type = 'full')
    {
        // Set option
        $option = array(
            'imagePath' => 'event/image'
        );
        // Get event from news story table
        $event = Pi::api('api', 'news')->getStorySingle($parameter, $field, $type, $option);
        // Get event extra information
        $extra = Pi::model('extra', $this->getModule())->find($event['id']);
        $extra = $this->canonizeEvent($extra, $event);
        $event = array_merge($event, $extra);
        // return
        return $event;
    }

    public function getEventList($where, $order, $offset = '', $limit = 10, $type = 'full', $table = 'link')
    {
        $option = array(
            'imagePath' => 'event/image'
        );
        $listEvent = array();
        $listExtra = array();
        $listEventId = array();
        // Get story list from news story table
        $listStory = Pi::api('api', 'news')->getStoryList($where, $order, $offset, $limit, $type, $table, $option);
        // Set extra id array
        foreach ($listStory as $singleStory) {
            $listEventId[$singleStory['id']] = $singleStory['id'];
        }
        // Get list of extra information
        if (!empty($listEventId)) {
            $whereExtra = array('id' => $listEventId);
            $select = Pi::model('extra', $this->getModule())->select()->where($whereExtra);
            $rowSet = Pi::model('extra', $this->getModule())->selectWith($select);
            foreach ($rowSet as $row) {
                $listExtra[$row->id] = $this->canonizeEvent($row);
            }
            // Join extra information to event
            foreach ($listStory as $event) {
                if(isset($listExtra[$event['id']]) && is_array($listExtra[$event['id']])){
                    $listEvent[$event['id']] = array_merge($event, $listExtra[$event['id']]);
                    $listEvent[$event['id']]['thumbUrl'] = Pi::url((string) Pi::api('doc','media')->getSingleLinkUrl($event['main_image'])->setConfigModule('news')->thumb('thumbnail'));
                    //$listEvent[$event['id']]['mediumUrl'] = Pi::url((string) Pi::api('doc','media')->getSingleLinkUrl($event['main_image'])->thumb(320, 240));
                }
            }
        }
        return $listEvent;
    }

    public function getEventRelated($id, $topic)
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set array
        $listEvent = array();
        $listExtra = array();
        $listEventId = array();
        // Get related story list from news story table
        $order = array('time_publish DESC', 'id DESC');
        // Set related event module
        switch ($config['related_event_type']) {
            case 'event':
                $where = array(
                    'status' => 1,
                    'story != ?' => $id,
                    'topic' => $topic,
                    'type' => 'event',
                    'module' => 'event',
                    'controller' => 'topic',
                );
                break;

            case 'guide':
                $where = array(
                    'status' => 1,
                    'story != ?' => $id,
                    'topic' => $topic,
                    'type' => 'event',
                    'module' => 'guide',
                    'controller' => 'category',
                );
                break;
        }
        $listStory = Pi::api('api', 'news')->getStoryRelated($where, $order);
        // Set extra id array
        foreach ($listStory as $singleStory) {
            $listEventId[$singleStory['id']] = $singleStory['id'];
        }
        // Check id list
        if (!empty($listEventId)) {
            // Get list of extra information
            $whereExtra = array('id' => $listEventId);
            $select = Pi::model('extra', $this->getModule())->select()->where($whereExtra);
            $rowSet = Pi::model('extra', $this->getModule())->selectWith($select);
            foreach ($rowSet as $row) {
                $listExtra[$row->id] = $this->canonizeEvent($row);
            }
            // Join extra information to event
            foreach ($listStory as $event) {
                //if (($listExtra[$event['id']]['time_end'] == 0 && $listExtra[$event['id']]['time_start'] > strtotime("-1 day")) || ($listExtra[$event['id']]['time_end'] > strtotime("-1 day"))) {
                    $listEvent[$event['id']] = array_merge($event, $listExtra[$event['id']]);
                //}
            }
        }
        return $listEvent;
    }

    public function getListFromId($id)
    {
        $list = array();
        $where = array('id' => $id, 'status' => 1);
        $select = Pi::model('extra', $this->getModule())->select()->where($where);
        $rowset = Pi::model('extra', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $list[$row->id] = $this->canonizeEvent($row);
            $list[$row->id]['eventUrl'] = Pi::url(Pi::service('url')->assemble('event', array(
                'module' => $this->getModule(),
                'controller' => 'index',
                'slug' => $row->slug,
            )));
        }
        return $list;
    }

    public function getLocationList()
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        // Set list
        $list = array();
        // Check guide module install
        if ($config['use_guide_location'] && Pi::service('module')->isActive('guide')) {
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
        if ($config['use_guide_category'] && Pi::service('module')->isActive('guide')) {
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
        if ($config['use_news_topic']) {
            $listNews = Pi::api('topic', 'news')->getTopicList('event');
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

    Public function getPriceFilterList()
    {
        // Get config
        $config = Pi::service('registry')->config->read($this->getModule());
        $list = array();
        // Check
        if (!empty($config['price_filter'])) {
            $priceFilter = explode("|", $config['price_filter']);
            foreach ($priceFilter as $priceFilterSingle) {
                $priceFilterSingle = explode(",", $priceFilterSingle);
                $list[$priceFilterSingle[0]] = array(
                    'value' => $priceFilterSingle[0],
                    'title' => $priceFilterSingle[1],
                );
            }
        }
        // return
        return $list;
    }

    public function canonizeEvent($extra, $event = array())
    {
        // Check
        if (empty($extra)) {
            return '';
        }
        // object to array
        if (is_object($extra)) {
            /* @var $extra \Pi\Db\RowGateway\RowGateway */
            $extra = $extra->toArray();
        }
        // Make register_discount
        $extra['register_discount'] = json_decode($extra['register_discount'], true);
        // Set register_details
        $extra['register_details'] = Pi::service('markup')->render($extra['register_details'], 'html', 'html');
        // Set time
        $extra['time_start_view'] = (empty($extra['time_start'])) ? '' : _date($extra['time_start'], array('pattern' => 'yyyy/MM/dd'));
        $extra['time_end_view'] = (empty($extra['time_end'])) ? '' : _date($extra['time_end'], array('pattern' => 'yyyy/MM/dd'));
        // Set register_price
        if (is_numeric($extra['register_price']) && $extra['register_price'] > 0) {
            $uid = Pi::user()->getId();
            $roles = Pi::user()->getRole($uid);
            if (!empty($extra['register_discount'])) {
                $price = $extra['register_price'];
                foreach ($extra['register_discount'] as $role => $percent) {
                    if (isset($percent) && $percent > 0 && in_array($role, $roles)) {
                        $price = ($extra['register_price'] - ($extra['register_price'] * ($percent / 100)));
                    }
                }
                $extra['register_price'] = $price;
            }
            if (Pi::service('module')->isActive('order')) {
                $priceView = Pi::api('api', 'order')->viewPrice($extra['register_price']);
            } else {
                $priceView = _currency($extra['register_price']);
            }
        } else {
            $priceView = _currency($extra['register_price']);
        }
        // Set order
        if(!isset($this->config)){
            $this->config = Pi::service('registry')->config->read($this->getModule());
        }

        $extra['register_price_view_clean'] = $priceView;
        if ($this->config['order_active']) {
            if (($extra['register_price'] > 0 && ($extra['register_stock'] - $extra['register_sales'] > 0 || $extra['register_stock'] == 0))) {
                $extra['register_price_view'] = $priceView;
            } elseif ($extra['register_price'] > 0 && $extra['register_stock'] > 0 && $extra['register_stock'] - $extra['register_sales'] == 0) {
                $extra['register_price_view'] = sprintf(__('Out of stock ( %s )'), $priceView);
            } else {
                $extra['register_price_view'] = __('free!');
            }
        } else {
            if (is_numeric($extra['register_price']) && $extra['register_price'] > 0) {
                $extra['register_price_view'] = _currency($extra['register_price']);
            }  else {
                $extra['register_price_view'] = __('free!');
            }
        }
        // Set currency
        $configSystem = Pi::service('registry')->config->read('system');
        $extra['price_currency'] = empty($configSystem['number_currency']) ? 'USD' : $configSystem['number_currency'];
        // canonize guide module details
        $extra['guide_category'] = json_decode($extra['guide_category'], true);
        $extra['guide_location'] = json_decode($extra['guide_location'], true);
        $extra['guide_item'] = json_decode($extra['guide_item'], true);
        // Set event url
        $extra['eventUrl'] = Pi::url(Pi::service('url')->assemble('event', array(
            'module' => $this->getModule(),
            'controller' => 'index',
            'slug' => $extra['slug'],
        )));
        // Set register url
        $extra['eventOrder'] = Pi::url(Pi::service('url')->assemble('event', array(
            'module' => $this->getModule(),
            'controller' => 'register',
            'action' => 'add',
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
            $extra['topics'] = $topicList;
        }
        // Check guide module
        if (Pi::service('module')->isActive('guide') && !empty($extra['guide_location'])) {

            if(!isset($this->locationList)){
                $this->locationList = Pi::registry('locationList', 'guide')->read();
            }
            $extra['locationInfo'] = $this->locationList[$extra['guide_location'][0]];
        }
        // Set time view
        if (!empty($extra['time_start']) && !empty($extra['time_end'])) {
            $extra['time_view'] = sprintf('%s %s %s %s', __('From'), $extra['time_start_view'], __('to'), $extra['time_end_view']);
        } elseif (!empty($extra['time_start'])) {
            $extra['time_view'] = $extra['time_start_view'];
        }
        // Number of days
        if ($extra['time_end'] > 0) {
            $days = ceil(($extra['time_end'] - $extra['time_start']) / 86400) + 1;
            $extra['days'] = sprintf(__('%s days'), $days);
        } else {
            $extra['days'] = __('1 days');
        }

        return $extra;
    }

    public function canonizeEventJson($event, $time, $largeThumb = false)
    {
        // Get config
        if(!isset($this->config)){
            $this->config = Pi::service('registry')->config->read($this->getModule());
        }

        // Set text_summary
        $event['text_summary'] = Pi::service('markup')->render($event['text_summary'], 'html', 'html');
        $event['text_summary'] = strip_tags($event['text_summary'], "<b><strong><i><p><br><ul><li><ol><h2><h3><h4>");
        $event['text_summary'] = str_replace("<p>&nbsp;</p>", "", $event['text_summary']);
        // Set category list
        $categoryList = array();
        if (isset($event['guide_category']) && !empty($event['guide_category'])) {
            foreach ($event['guide_category'] as $category) {
                $categoryList[$category] = sprintf('category-%s-guide', $category);
            }
        }
        if (isset($event['topic']) && !empty($event['topic'])) {
            foreach ($event['topic'] as $category) {
                $categoryList[$category] = sprintf('category-%s-news', $category);
            }
        }
        // Set location list
        $locationList = array();
        if (isset($event['guide_location']) && !empty($event['guide_location'])) {
            foreach ($event['guide_location'] as $category) {
                $locationList[$category] = sprintf('location-%s-guide', $category);
            }
        }
        // Set time view
        if (!empty($event['time_start']) && !empty($event['time_end'])) {
            $timeView = sprintf('%s %s %s %s', __('From'), $event['time_start_view'], __('to'), $event['time_end_view']);
        } elseif (!empty($event['time_start'])) {
            $timeView = $event['time_start_view'];
        }
        // Set time level
        $timeLevel = '';
        if ($event['time_end'] == 0 && $event['time_start'] < $time['expired']) {
            $timeLevel = ' expired';
        } elseif ($event['time_end'] > 0 && $event['time_end'] < $time['expired']) {
            $timeLevel .= ' expired';
        }

        if ($event['time_start'] >= $time['thisWeek'] && $event['time_start'] < $time['nextWeek']) {
            $timeLevel .= ' thisWeek';
        } elseif ($event['time_end'] > 0 && $event['time_start'] < $time['thisWeek'] && $event['time_end'] > $time['thisWeek']) {
            $timeLevel .= ' thisWeek';
        }

        if ($event['time_start'] >= $time['nextWeek'] && $event['time_start'] < $time['nextTwoWeek']) {
            $timeLevel .= ' nextWeek';
        } elseif ($event['time_end'] > 0 && $event['time_start'] < $time['nextWeek'] && $event['time_end'] > $time['nextWeek']) {
            $timeLevel .= ' nextWeek';
        }

        if ($event['time_start'] >= $time['thisMonth'] && $event['time_start'] < $time['nextMonth']) {
            $timeLevel .= ' thisMonth';
        } elseif ($event['time_end'] > 0 && $event['time_start'] < $time['thisMonth'] && $event['time_end'] > $time['thisMonth']) {
            $timeLevel .= ' thisMonth';
        }

        if ($event['time_start'] >= $time['nextMonth'] && $event['time_start'] < $time['nextTwoMonth']) {
            $timeLevel .= ' nextMonth';
        } elseif ($event['time_end'] > 0 && $event['time_start'] < $time['nextMonth'] && $event['time_end'] > $time['nextMonth']) {
            $timeLevel .= ' nextMonth';
        }

        if ($event['time_start'] >= $time['nextTwoMonth'] && $event['time_start'] < $time['nextThreeMonth']) {
            $timeLevel .= ' nextTwoMonth';
        } elseif ($event['time_end'] > 0 && $event['time_start'] < $time['nextTwoMonth'] && $event['time_end'] > $time['nextTwoMonth']) {
            $timeLevel .= ' nextTwoMonth';
        }

        if ($event['time_start'] >= $time['nextThreeMonth'] && $event['time_start'] < $time['nextFourMonth']) {
            $timeLevel .= ' nextThreeMonth';
        } elseif ($event['time_end'] > 0 && $event['time_start'] < $time['nextThreeMonth'] && $event['time_end'] > $time['nextThreeMonth']) {
            $timeLevel .= ' nextThreeMonth';
        }

        if (empty($timeLevel) || $event['time_start'] >= $time['nextFourMonth'] || ($event['time_end'] > 0 && $event['time_end'] > $time['nextFourMonth'])) {
            $timeLevel .= ' nextAllMonth';
        }

        // Check price filter
        $event['price_filter'] = '';
        if (!empty($this->config['price_filter'])) {
            $priceFilter = explode("|", $this->config['price_filter']);
            foreach ($priceFilter as $priceFilterSingle) {
                $priceFilterSingle = explode(",", $priceFilterSingle);
                $priceFilterSinglePrice = explode("-", $priceFilterSingle[0]);
                if (intval($event['register_price']) >= min($priceFilterSinglePrice) && intval($event['register_price']) < max($priceFilterSinglePrice)) {
                    $event['price_filter'] = $priceFilterSingle[0];
                }
            }
        }

        // Number of days
        if ($event['time_end'] > 0) {
            $days = ceil(($event['time_end'] - $event['time_start']) / 86400) + 1;
            $event['days'] = sprintf(__('%s days'), $days);
        } else {
            $event['days'] = __('1 days');
        }

        //$event['thumbUrl'] = (string) Pi::api('doc','media')->getSingleLinkUrl($event['main_image'])->thumb(150, 100);
        //$event['mediumUrl'] = (string) Pi::api('doc','media')->getSingleLinkUrl($event['main_image'])->thumb(320, 240);

        // Set single event array
        $eventSingle = array(
            'id' => $event['id'],
            'title' => $event['title'],
            'image' => $event['main_image'],
            'thumbUrl' => $largeThumb ? $event['mediumUrl'] : $event['thumbUrl'],
            'eventUrl' => $event['eventUrl'],
            'subtitle' => $event['subtitle'],
            'register_price' => $event['register_price'],
            'register_price_view' => $event['register_price_view'],
            'price_currency' => $event['price_currency'],
            'price_filter' => $event['price_filter'],
            'hits' => $event['hits'],
            'text_summary' => $event['text_summary'],
            'time_create' => $event['time_create'],
            'time_publish' => $event['time_publish'],
            'time_update' => $event['time_update'],
            'time_start' => $event['time_start'],
            'time_end' => $event['time_end'],
            'time_start_view' => date("Y-m-d H:i:s", $event['time_start']),
            'time_end_view' => date("Y-m-d H:i:s", $event['time_end']),
            'time_view' => $timeView,
            'time_level' => $timeLevel,
            'category' => implode(' ', $categoryList),
            'location' => implode(' ', $locationList),
            'days' => $event['days'],
        );

        return $eventSingle;
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

    public function migrateMedia(){
        if (Pi::service("module")->isActive("media")) {
            // Get config
            $config = Pi::service('registry')->config->read($this->getModule());

            $storyModel = Pi::model("story", "news");

            $select = $storyModel->select();
            $storyCollection = $storyModel->selectWith($select);

            foreach($storyCollection as $story){

                /**
                 * Check if media item have already migrate or no image to migrate
                 */
                if($story->main_image || empty($story["image"]) || empty($story["path"])){
                    continue;
                }

                $mediaData = array(
                    'active' => 1,
                    'time_created' => time(),
                    'uid'   => $story->uid,
                    'count' => 0,
                );

                $imagePath = sprintf("upload/%s/original/%s/%s",
                    'event/image',
                    $story["path"],
                    $story["image"]
                );

                $mediaData['title'] = $story->title;
                $mediaId = Pi::api('doc', 'media')->insertMedia($mediaData, $imagePath);

                if($mediaId){
                    $story->main_image = $mediaId;
                    $story->save();
                }
            }
        }
    }

    public function doAfterVote()
    {}
    
    public function favoriteList($uid = null)
    {
        // Get user id
        if ($uid == null) {
            $uid = Pi::user()->getId();
        }
                
        // Check user
        if ($uid > 0) {
            $favoriteIds = Pi::api('favourite', 'favourite')->userFavourite($uid, 'news');
            // Check list of ides
            if (!empty($favoriteIds)) {
                // Get config
                
                // TOPIC
                $select = Pi::model('category', 'guide')->select();
                $rowset = Pi::model('category', 'guide')->selectWith($select);
                $topics = array();
                foreach ($rowset as $row) {
                    $topics[$row->id] = array(
                        'title' => $row->title,
                        'categoryUrl' => Pi::url(Pi::service("url")->assemble("guide", array(
                            "module" => 'guide',
                            "controller" => "category",
                            "slug" => $row->slug,
                        )))
                    );
                }
                //
                
                // LOCATION
                $select = Pi::model('location', 'guide')->select();
                $rowset = Pi::model('location', 'guide')->selectWith($select);
                $locations = array();
                foreach ($rowset as $row) {
                    $locations[$row->id] = $row->title;
                }
                // 
                
                $config = Pi::service('registry')->config->read('news');
                // Set list
                $list = array();
                $where = array(
                    'story.id' => $favoriteIds, 
                    'story.status' => 1,
                    'story.type' => array(
                       'event'
                   )
                );
                
                $storyTable = Pi::model("story", 'news')->getTable();
                $extraTable = Pi::model("extra", 'event')->getTable();
                
                
                $select = Pi::db()->select();
                $select
                ->from(array('story' => $storyTable))
                ->join(array('extra' => $extraTable), 'story.id = extra.id', array('guide_category', 'guide_location', 'time_start', 'time_end' ))
                ->where ($where);
                
                
                $rowset = Pi::db()->query($select);
                
                foreach ($rowset as $row) {
                    $story = $row;
                    $story['url'] = Pi::url(Pi::service('url')->assemble('news', array(
                        'module' => 'event',
                        'controller' => 'index',
                        'slug' => $row['slug'],
                    )));
                    
                    $story['categories'] = array();
                    $storyTopics = json_decode($story['guide_category']);
                    foreach ($storyTopics as $idTopic) {
                        $story['categories'][] = $topics[$idTopic];
                    }
                    
                    $story['locations'] = array();
                    $storyLocationsId = json_decode($story['guide_location']);
                    $storyLocations = array();
                    foreach ($storyLocationsId as $idLocation) {
                        $storyLocations[] = $locations[$idLocation];
                    }
                    $story['location'] = join(' | ', $storyLocations);
                    $story['image'] = '';
                    if ($row['main_image']) {
                        $story["image"] = Pi::url((string) Pi::api('doc','media')->getSingleLinkUrl($row['main_image'])->setConfigModule('news')->thumb('medium'));
                    }
                    
                    if (!empty($story['time_start']) && !empty($story['time_end'])) {
                        $time = sprintf(
                            '%s %s %s %s',
                            __('From'),
                            _date($story['time_start']),
                            __('to'),
                            _date($story['time_end'])
                        );
                    } elseif (!empty($story['time_start'])) {
                        $time = _date($story['time_start']);
                    }
                    $story['period'] = $time;

                    $list[$row['id']] = $story;
                }
                return $list;
            } else {
             return array();
            }
        } else {
            return array();
        }
    }

}

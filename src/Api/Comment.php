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
use Pi\Application\Api\AbstractComment;

class Comment extends AbstractComment
{
    /** @var string */
    protected $module = 'event';

    /**
     * Get target data
     *
     * @param int|int[] $item Item id(s)
     *
     * @return array
     */
    public function get($item)
    {

        $result = array();
        $items = (array)$item;

        // Set options
        $event = Pi::api('event', 'event')->getListFromId($items);

        foreach ($items as $id) {
            $result[$id] = array(
                'id' => $event[$id]['id'],
                'title' => $event[$id]['title'],
                'url' => $event[$id]['eventUrl'],
                'uid' => $event[$id]['uid'],
                'time' => $event[$id]['time_create'],
            );
        }

        if (is_scalar($item)) {
            $result = $result[$item];
        }

        return $result;
    }

    /**
     * Locate source id via route
     *
     * @param RouteMatch|array $params
     *
     * @return mixed|bool
     */
    public function locate($params = null)
    {
        if (null == $params) {
            $params = Pi::engine()->application()->getRouteMatch();
        }
        echo '<pre>';
        print_r($params);
        echo '</pre>';
        if ($params instanceof RouteMatch) {
            $params = $params->getParams();
        }
        if ('event' == $params['module']
            && !empty($params['slug'])
        ) {
            $event = Pi::api('event', 'event')->getEvent($params['slug'], 'slug');
            $item = $event['id'];
        } else {
            $item = false;
        }
        return $item;
    }
}

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
use Pi\Application\Api\AbstractBreadcrumbs;

class Breadcrumbs extends AbstractBreadcrumbs
{
    /**
     * {@inheritDoc}
     */
    public function load()
    {
        // Get params
        $params = Pi::service('url')->getRouteMatch()->getParams();
        // Set module link
        $moduleData = Pi::registry('module')->read($this->getModule());
        // Make tree
        if (!empty($params['controller']) && $params['controller'] != 'index') {
            // Set index
            $result = array(
                array(
                    'label' => $moduleData['title'],
                    'href' => Pi::url(Pi::service('url')->assemble('news', array(
                        'module' => $this->getModule(),
                    ))),
                ),
            );
            // Set
            switch ($params['controller']) {
                case 'detail':
                    $event = Pi::api('event', 'event')->getEvent($params['slug'], 'slug', 'light');
                    // Check topic_mai
                    if ($event['topic_main'] > 0) {
                        $topic = Pi::api('topic', 'news')->getTopic($event['topic_main']);
                        $result[] = array(
                            'label' => $topic['title'],
                            'href' => Pi::url(Pi::service('url')->assemble('event', array(
                                'module' => $this->getModule(),
                                'controller' => 'category',
                                'slug' => $topic['slug'],
                            ))),
                        );
                    }
                    $result[] = array(
                        'label' => $event['title'],
                    );
                    break;

                case 'category':
                    if (!empty($params['slug'])) {
                        // Set link
                        $result[] = array(
                            'label' => __('Category list'),
                            'href' => Pi::url(Pi::service('url')->assemble('event', array(
                                'controller' => 'category',
                            ))),
                        );
                        // Set link
                        $topic = Pi::api('topic', 'news')->getTopic($params['slug'], 'slug');
                        $result[] = array(
                            'label' => $topic['title'],
                        );
                    } else {
                        $result[] = array(
                            'label' => __('Category list'),
                        );
                    }
                    break;

                case 'manage':
                    if ($params['action'] == 'index') {
                        $result[] = array(
                            'label' => __('Manage events'),
                        );
                    } elseif ($params['action'] == 'order') {
                        // Set link
                        $result[] = array(
                            'label' => __('Manage events'),
                            'href' => Pi::url(Pi::service('url')->assemble('event', array(
                                'controller' => 'manage',
                            ))),
                        );
                        // Set link
                        $result[] = array(
                            'label' => __('List of orders'),
                        );
                    } elseif ($params['action'] == 'update') {
                        // Set link
                        $result[] = array(
                            'label' => __('Manage events'),
                            'href' => Pi::url(Pi::service('url')->assemble('event', array(
                                'controller' => 'manage',
                            ))),
                        );
                        // Set link
                        $result[] = array(
                            'label' => __('Add / edit event'),
                        );
                    }
                    break;

                case 'order':
                    if ($params['action'] == 'list') {
                        $result[] = array(
                            'label' => __('List of your registered events'),
                        );
                    } elseif ($params['action'] == 'detail') {
                        // Set link
                        $result[] = array(
                            'label' => __('List of your registered events'),
                            'href' => Pi::url(Pi::service('url')->assemble('event', array(
                                'controller' => 'order',
                                'action' => 'list',
                            ))),
                        );
                        // Set link
                        $result[] = array(
                            'label' => __('Registered event information'),
                        );
                    }
                    break;
            }
        } else {
            $result = array(
                array(
                    'label' => $moduleData['title'],
                ),
            );
        }
        return $result;

    }
}
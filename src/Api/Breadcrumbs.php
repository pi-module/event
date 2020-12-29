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
            $result = [
                [
                    'label' => $moduleData['title'],
                    'href'  => Pi::url(
                        Pi::service('url')->assemble(
                            'event',
                            [
                            'module' => $this->getModule(),
                        ]
                        )
                    ),
                ],
            ];
            // Set
            switch ($params['controller']) {
                case 'detail':
                    $event = Pi::api('event', 'event')->getEventSingle($params['slug'], 'slug', 'light');
                    // Check topic_mai
                    if ($event['topic_main'] > 0) {
                        $topic    = Pi::api('topic', 'news')->getTopic($event['topic_main']);
                        $result[] = [
                            'label' => $topic['title'],
                            'href'  => Pi::url(
                                Pi::service('url')->assemble(
                                    'event',
                                    [
                                    'module'     => $this->getModule(),
                                    'controller' => 'category',
                                    'slug'       => $topic['slug'],
                                ]
                                )
                            ),
                        ];
                    }
                    $result[] = [
                        'label' => $event['title'],
                    ];
                    break;

                case 'category':
                    if (!empty($params['slug'])) {
                        // Set link
                        $topic    = Pi::api('topic', 'news')->getTopic($params['slug'], 'slug');
                        $result[] = [
                            'label' => $topic['title'],
                        ];
                    }
                    break;

                case 'manage':
                    if ($params['action'] == 'index') {
                        $result[] = [
                            'label' => __('Manage events'),
                        ];
                    } elseif ($params['action'] == 'order') {
                        // Set link
                        $result[] = [
                            'label' => __('Manage events'),
                            'href'  => Pi::url(
                                Pi::service('url')->assemble(
                                    'event',
                                    [
                                    'controller' => 'manage',
                                ]
                                )
                            ),
                        ];
                        // Set link
                        $result[] = [
                            'label' => __('List of orders'),
                        ];
                    } elseif ($params['action'] == 'update') {
                        // Set link
                        $result[] = [
                            'label' => __('Manage events'),
                            'href'  => Pi::url(
                                Pi::service('url')->assemble(
                                    'event',
                                    [
                                    'controller' => 'manage',
                                ]
                                )
                            ),
                        ];
                        // Set link
                        $result[] = [
                            'label' => __('Add / edit event'),
                        ];
                    }
                    break;
            }
        } else {
            $result = [
                [
                    'label' => $moduleData['title'],
                ],
            ];
        }
        return $result;
    }
}

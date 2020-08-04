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

namespace Module\Event\Controller\Admin;

use Pi;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Module\Event\Form\EventForm;
use Module\Event\Form\EventFilter;
use Module\Event\Form\EventSearchForm;
use Module\Event\Form\EventSearchFilter;

class EventController extends ActionController
{
    public function indexAction()
    {
        // Get page
        $page   = $this->params('page', 1);
        $title  = $this->params('title');
        $status = $this->params('status');

        // Set info
        $order  = ['id DESC'];
        $limit  = intval($this->config('admin_perpage'));
        $offset = (int)($page - 1) * $limit;
        $where  = [
            'type' => 'event',
        ];
        if (!empty($title)) {
            $where['title LIKE ?'] = '%' . $title . '%';
        }
        if (!empty($status) && intval($status) > 0) {
            $where['status'] = intval($status);
        }

        // Get list of event
        $listEvent = Pi::api('event', 'event')->getEventList($where, $order, $offset, $limit, 'light', 'story');

        // Set template
        $template = [
            'module'     => 'event',
            'controller' => 'event',
            'action'     => 'index',
            'title'      => $title,
            'status'     => $status,
        ];

        // Get paginator
        $paginator = Pi::api('api', 'news')->getStoryPaginator($template, $where, $page, $limit, 'story');

        // Set form
        $values = [
            'title'  => $title,
            'status' => $status,
        ];
        $form   = new EventSearchForm('search');
        $form->setAttribute('action', $this->url('', ['action' => 'process']));
        $form->setData($values);

        // Set view
        $this->view()->setTemplate('event-index');
        $this->view()->assign('list', $listEvent);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('form', $form);
    }

    public function processAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form = new EventSearchForm('search');
            $form->setInputFilter(new EventSearchFilter());
            $form->setData($data);
            if ($form->isValid()) {
                $values  = $form->getData();
                $message = __('View filtered events');
                $url     = [
                    'action' => 'index',
                    'title'  => $values['title'],
                    'status' => $values['status'],
                ];
            } else {
                $message = __('Not valid');
                $url     = [
                    'action' => 'index',
                ];
            }
        } else {
            $message = __('Not set');
            $url     = [
                'action' => 'index',
            ];
        }
        return $this->jump($url, $message);
    }

    public function updateAction()
    {
        // Get id
        $id     = $this->params('id');
        $module = $this->params('module');

        // Set local
        $local = Pi::service('i18n')->getLocale();

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Check category
        $categoryList = Pi::registry('categoryRoute', 'event')->read();
        if (empty($categoryList)) {
            $message = __('No topic created for event system yet !');
            $this->jump(['action' => 'index'], $message, 'error');
        }

        // Find event
        $event = [];
        if ($id) {
            $event = Pi::api('event', 'event')->getEventSingle($id, 'id', 'full');
        }

        // Set option
        $option = [
            'side'               => 'admin',
            'use_news_topic'     => $config['use_news_topic'],
            'use_guide_category' => $config['use_guide_category'],
            'use_guide_location' => $config['use_guide_location'],
            'order_active'       => $config['order_active'],
            'order_discount'     => $config['order_discount'],
            'map_use'            => $config['map_use'],
            'manage_register'    => $config['manage_register'],
            'register_can'       => $this->params('register_can'),
            'id'                 => $id,
        ];


        // Set form
        $form = new EventForm('event', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();

            // Set slug
            $slug         = ($data['slug']) ? $data['slug'] : $data['title'];
            $filter       = new Filter\Slug;
            $data['slug'] = $filter($slug);

            // Form filter
            $form->setInputFilter(new EventFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();

                // Set time
                if ($local == 'fa') {
                    $values['time_publish'] = ($values['time_end']) ? $values['time_end'] : $values['time_start'];
                    $values['time_end']     = ($values['time_end']) ? $values['time_end'] : '';
                } else {
                    $values['time_publish'] = ($values['time_end']) ? strtotime($values['time_end']) : strtotime($values['time_start']);
                    $values['time_start']   = strtotime($values['time_start']);
                    $values['time_end']     = ($values['time_end']) ? strtotime($values['time_end']) : '';
                }

                // Set type
                $values['type'] = 'event';
                $values['image'] = '';

                // Set guide module info
                if (isset($values['guide_category']) && !empty($values['guide_category'])) {
                    $values['guide_category'] = json_encode($values['guide_category']);
                }
                if (isset($values['guide_location']) && !empty($values['guide_location'])) {
                    $values['guide_location'] = json_encode($values['guide_location']);
                }
                if (isset($values['guide_item']) && !empty($values['guide_item'])) {
                    $values['guide_item']     = json_encode($values['guide_item']);
                }

                // Set register_discount
                $discount = [];
                if ($config['order_discount']) {
                    // Get role list
                    $roles = Pi::service('registry')->Role->read('front');
                    unset($roles['webmaster']);
                    unset($roles['guest']);
                    foreach ($roles as $name => $role) {
                        $discount[$name] = $values[$name];
                    }
                }
                $values['register_discount'] = json_encode($discount);

                // Save values on news story table and event extra table
                if (!empty($id) && $id > 0) {
                    $values['id'] = $id;
                    $story = Pi::api('api', 'news')->editStory($values, true, false);
                    if (isset($story) && !empty($story)) {
                        $row = $this->getModel('extra')->find($story['id']);
                    } else {
                        $message = __('Error on save story data on event module.');
                        $this->jump(['action' => 'index'], $message, 'error');
                    }
                } else {
                    $values['uid'] = Pi::user()->getId();
                    $story         = Pi::api('api', 'news')->addStory($values, true);
                    if (isset($story) && !empty($story)) {
                        $row          = $this->getModel('extra')->createRow();
                        $values['id'] = $story['id'];
                    } else {
                        $message = __('Error on save story data on event module.');
                        $this->jump(['action' => 'index'], $message, 'error');
                    }
                }
                // Set map
                if ($config['map_use'] && !empty($values['map_latitude']) && !empty($values['map_longitude'])) {
                    $values['map_zoom'] = empty($values['map_zoom']) ? $config['map_zoom'] : $values['map_zoom'];
                }
                $row->assign($values);
                if ($values['register_stock'] == null) {
                    $row->register_stock = null;
                }
                $row->save();

                // Check topic
                if (!$config['use_news_topic']) {
                    $values['topic'] = [];
                }

                // Set link array
                $link = [
                    'story'        => $story['id'],
                    'time_publish' => $story['time_publish'],
                    'time_update'  => $story['time_update'],
                    'status'       => $story['status'],
                    'uid'          => $story['uid'],
                    'type'         => $story['type'],
                    'module'       => [
                        'event' => [
                            'name'       => 'event',
                            'controller' => [
                                'topic' => [
                                    'name'  => 'topic',
                                    'topic' => $values['topic'],
                                ],
                            ],
                        ],
                    ],
                ];

                // Add guide module info on link
                if (Pi::service('module')->isActive('guide')) {
                    $link['module']['guide'] = [
                        'name'       => 'guide',
                        'controller' => [],
                    ];
                    if ($config['use_guide_category'] && isset($values['guide_category']) && !empty($values['guide_category'])) {
                        $link['module']['guide']['controller']['category'] = [
                            'name'  => 'category',
                            'topic' => json_decode($values['guide_category'], true),
                        ];
                    }

                    if ($config['use_guide_location'] && isset($values['guide_location']) && !empty($values['guide_location'])) {
                        $link['module']['guide']['controller']['location'] = [
                            'name'  => 'location',
                            'topic' => json_decode($values['guide_location'], true),
                        ];
                    }

                    if (isset($values['guide_item']) && !empty($values['guide_item'])) {
                        $link['module']['guide']['controller']['item'] = [
                            'name'  => 'item',
                            'topic' => json_decode($values['guide_item'], true),
                        ];
                    }

                    if (isset($values['guide_owner']) && !empty($values['guide_owner'])) {
                        $link['module']['guide']['controller']['owner'] = [
                            'name'  => 'owner',
                            'topic' => [
                                $values['guide_owner'],
                            ],
                        ];
                    }
                }

                // Setup link
                Pi::api('api', 'news')->setupLink($link);

                // Add / Edit sitemap
                if (Pi::service('module')->isActive('sitemap')) {
                    // Set loc
                    $loc = Pi::url(
                        $this->url(
                            'event', [
                                'module'     => $module,
                                'controller' => 'index',
                                'slug'       => $values['slug'],
                            ]
                        )
                    );
                    // Update sitemap
                    Pi::api('sitemap', 'sitemap')->singleLink($loc, $story['status'], $module, 'event', $story['id']);
                }

                // Add log
                $message = __('Event data saved successfully.');
                $this->jump(['action' => 'index'], $message);
            }
        } else {
            if ($id) {
                // Make discount
                if ($config['order_discount'] && isset($event['register_discount'])) {
                    foreach ($event['register_discount'] as $name => $value) {
                        $event[$name] = $value;
                    }
                }
                // Set time
                if ($local != 'fa') {
                    $event['time_start'] = ($event['time_start']) ? date('Y-m-d', $event['time_start']) : date('Y-m-d');
                    $event['time_end']   = ($event['time_end']) ? date('Y-m-d', $event['time_end']) : '';
                }
                $form->setData($event);
                // Set event to view
                $this->view()->assign('event', $event);
            }
        }

        // Set view
        $this->view()->setTemplate('event-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add event'));
    }
}

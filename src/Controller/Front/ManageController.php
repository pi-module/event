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

namespace Module\Event\Controller\Front;

use Pi;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Module\Event\Form\EventForm;
use Module\Event\Form\EventFilter;
use Laminas\Math\Rand;

class ManageController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $module  = $this->params('module');
        $allowed = 0;
        $where   = [];
        $events  = [];
        // Get user
        $uid = Pi::user()->getId();
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // check
        if (!$config['manage_active']) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Owner dashboard is inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        } else {
            Pi::service('authentication')->requireLogin();
        }

        $roles = Pi::user()->getRole($uid, 'front');
        if (in_array($config['manage_role'], $roles)) {
            $where   = [
                'uid' => $uid,
            ];
            $allowed = 1;
        }

        // Get event list
        if ($allowed) {
            $order  = ['time_start DESC', 'id DESC'];
            $select = $this->getModel('extra')->select()->where($where)->order($order);
            $rowset = $this->getModel('extra')->selectWith($select);
            foreach ($rowset as $row) {
                $events[$row->id] = $row->toArray();
            }
        }

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('event', 'manage');
        }

        // Set view
        $this->view()->setTemplate('manage-index');
        $this->view()->assign('title', __('All your events'));
        $this->view()->assign('config', $config);
        $this->view()->assign('events', $events);
        $this->view()->assign('allowed', $allowed);
        // Language
        __('Search');
    }

    public function updateAction()
    {
        // Get info from url
        $module = $this->params('module');
        $id     = $this->params('id');
        $item   = $this->params('item');

        // Set local
        $local = Pi::service('i18n')->getLocale();

        // Get config
        $config = Pi::service('registry')->config->read($module);

        // Get user
        $uid = Pi::user()->getId();

        // check
        if (!$config['manage_active']) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Owner dashboard is inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        } else {
            Pi::service('authentication')->requireLogin();
        }
        // Check allowed
        $allowed = 0;
        if ($item && Pi::service('module')->isActive('guide')) {
            $owner = $this->canonizeGuideOwner();
            if (isset($owner['id']) && $owner['id'] > 0) {
                $allowed = 1;
            }
        } else {
            $roles = Pi::user()->getRole($uid, 'front');
            if (in_array($config['manage_role'], $roles)) {
                $allowed = 1;
            }
        }
        if (!$allowed) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(
                __(
                    'You are not allowed to submit new event, please contact to website admin and complete your registration, after that you allowed to submit events'
                ), '', 'error-denied'
            );
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Set option
        $option = [
            'side'               => 'front',
            'use_news_topic'     => $config['use_news_topic'],
            'use_guide_category' => $config['use_guide_category'],
            'use_guide_location' => $config['use_guide_location'],
            'order_active'       => $config['order_active'],
            'map_use'            => $config['map_use'],
            'manage_register'    => $config['manage_register'],
            'order_discount'     => $config['order_discount'],
            'register_can'       => $this->params('register_can'),
            'id'                 => $id,
        ];

        // Find event
        if ($id) {
            $event = Pi::api('event', 'event')->getEventSingle($id, 'id', 'full');

            if ($event['time_end'] < strtotime('now midnight')) {
                $this->getResponse()->setStatusCode(401);
                $this->terminate(__('Event is over'), '', 'error-denied');
                $this->view()->setLayout('layout-simple');
                return;
            }
        }
        // Check event uid
        if (isset($event['uid']) && $event['uid'] != $uid) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Its not your event'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Set title
        if ($id) {
            $title = __('Update event');
        } else {
            $title = __('Add event');
        }

        // Check event guide owner
        if ($item && Pi::service('module')->isActive('guide')) {
            $owner           = $this->canonizeGuideOwner();
            $option['owner'] = $owner['id'];
            if (isset($event['guide_owner']) && $event['guide_owner'] != $owner['id']) {
                $this->getResponse()->setStatusCode(401);
                $this->terminate(__('Its not your event'), '', 'error-denied');
                $this->view()->setLayout('layout-simple');
                return;
            }
            // Check item
            $canSubmitEvent = true;
            if ($item) {
                $item           = Pi::api('item', 'guide')->getItemLight($item);
                $option['item'] = $item['id'];
                $title          = sprintf(__('Add event to %s'), $item['title']);

                // Test if pakcage has_event
                if ($item['item_type'] == 'commercial') {
                    $canSubmitEvent = false;
                    $package        = Pi::api('package', 'guide')->getPackageFromPeriod($item['package']);
                    if (!Pi::api('item', 'guide')->isExpired($item)) {
                        $canSubmitEvent = $package['has_event'];
                    }
                }
            }
        } else {
            // Check item
            $canSubmitEvent = true;
        }

        // Set form
        $option['id'] = $id;
        $form         = new EventForm('event', $option);
        $form->setAttribute('action', '#');
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost() && $canSubmitEvent) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();

            // Set slug
            if ($config['generate_slug']) {
                $slug = Rand::getString(16, 'abcdefghijklmnopqrstuvwxyz123456789', true);
            } else {
                $slug = ($data['slug']) ? $data['slug'] : $data['title'];
            }
            $filter       = new Filter\Slug;
            $data['slug'] = $filter($slug);

            // Form filter
            $form->setInputFilter(new EventFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();

                // Unset image
                    if ($values['image'] == '') {
                        unset($values['image']);
                    }

                    // Set time
                    if ($local == 'fa') {
                        $values['time_publish'] = ($values['time_end']) ? $values['time_end'] : $values['time_start'];
                        $values['time_start']   = $values['time_start'];
                        $values['time_end']     = ($values['time_end']) ? $values['time_end'] : '';
                    } else {
                        $values['time_publish'] = ($values['time_end']) ? strtotime($values['time_end']) : strtotime($values['time_start']);
                        $values['time_start']   = strtotime($values['time_start']);
                        $values['time_end']     = ($values['time_end']) ? strtotime($values['time_end']) : '';
                    }

                    // Set type
                    $values['type'] = 'event';
                    // Set status
                    $values['status'] = $config['manage_approval'] ? 1 : 2;
                    
                    // Set guide module info
                    if (isset($owner) && isset($owner['id'])) {
                        $values['guide_owner'] = $owner['id'];
                    }
                    if (isset($values['guide_category']) && !empty($values['guide_category'])) {
                        $values['guide_category'] = Json::encode($values['guide_category']);
                    }

                    if (isset($values['guide_location']) && !empty($values['guide_location'])) {
                        $values['guide_location'] = Json::encode($values['guide_location']);
                    }

                    if (!empty($item)) {
                        $values['guide_item'] = json_encode([$item['id']]);
                    } else {
                        if (isset($values['guide_item'])) {
                            $values['guide_item'] = json_encode($values['guide_item']);
                        }
                    }

                    // Save values on news story table and event extra table
                    $edit = false;
                    if (!empty($id)) {
                        $edit  = true;
                        $values['id'] = $id;
                        $story = Pi::api('api', 'news')->editStory($values, true, false);
                        if (isset($story) && !empty($story)) {
                            $row = $this->getModel('extra')->find($story['id']);
                        } else {
                            $message = __('Error on save story data on news module.');
                            $this->jump(['action' => 'index'], $message, 'error');
                        }
                    } else {
                        $values['uid'] = Pi::user()->getId();
                        $story         = Pi::api('api', 'news')->addStory($values, true);
                        if (isset($story) && !empty($story)) {
                            $row          = $this->getModel('extra')->createRow();
                            $values['id'] = $story['id'];
                        } else {
                            $message = __('Error on save story data on news module.');
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
                    if ($row->status == 1) {
                        $message = __('Thanks for contributing ! Event data saved successfully and was published on public side');
                    } else {
                        $message = __('Thanks for contributing ! Event data saved successfully and we will be validate it soon');
                    }

                    // Set to admin
                    $adminmail = Pi::config('adminmail');
                    $adminname = Pi::config('adminname');
                    $toAdmin   = [
                        $adminmail => $adminname,
                    ];

                    // Set info
                    $information = [
                        'admin-event-url' => Pi::url(
                            Pi::service('url')->assemble('admin', ['module' => 'event', 'controller' => 'event', 'action' => 'index'])
                        ),
                        'title'           => $values['title'],
                    ];

                    // Send mail to admin
                    if ($edit) {
                        Pi::service('notification')->send(
                            $toAdmin,
                            'admin_edit_event',
                            $information,
                            Pi::service('module')->current()
                        );
                    } else {
                        Pi::service('notification')->send(
                            $toAdmin,
                            'admin_add_event',
                            $information,
                            Pi::service('module')->current()
                        );
                    }
                    if ($item['id']) {
                        $this->jump(['module' => 'guide', 'controller' => 'manage', 'action' => 'event-list', 'item' => $item['id']], $message);
                    }
                    $this->jump(['action' => 'index'], $message);
            }
        } else {
            if ($id) {
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

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('event', 'manage');
        }

        // Set view
        $this->view()->setTemplate('manage-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', $title);
        $this->view()->assign('item', $item);
        $this->view()->assign('config', $config);
        $this->view()->assign('canSubmitEvent', $canSubmitEvent);

    }

    public function removeAction()
    {
        // Get info from url
        $module = $this->params('module');
        $id     = $this->params('id');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get user
        $uid = Pi::user()->getId();
        // check
        if (!$config['manage_active']) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Owner dashboard is inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        } else {
            Pi::service('authentication')->requireLogin();
        }
        // Check allowed
        $allowed = 0;
        if (Pi::service('module')->isActive('guide')) {
            $owner = $this->canonizeGuideOwner();
            if (isset($owner['id']) && $owner['id'] > 0) {
                $allowed = 1;
            }
        } else {
            $roles = Pi::user()->getRole($uid, 'front');
            if (in_array($config['manage_role'], $roles)) {
                $allowed = 1;
            }
        }
        if (!$allowed) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(
                __(
                    'You are not allowed to submit new event, please contact to website admin and complete your registration, after that you allowed to submit events'
                ), '', 'error-denied'
            );
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Find event
        $event = Pi::api('event', 'event')->getEventSingle($id, 'id', 'full');
        // Check event uid
        if (isset($event['uid']) && $event['uid'] != $uid) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Its not your event'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Check event guide owner
        if (Pi::service('module')->isActive('guide')) {
            $owner           = $this->canonizeGuideOwner();
            $option['owner'] = $owner['id'];
            if (isset($event['guide_owner']) && $event['guide_owner'] != $owner['id']) {
                $this->getResponse()->setStatusCode(401);
                $this->terminate(__('Its not your event'), '', 'error-denied');
                $this->view()->setLayout('layout-simple');
                return;
            }
        }
        // remove image
        $result = Pi::api('api', 'news')->removeImage($id);
        return $result;
    }

    public function orderAction()
    {
        // Get info from url
        $module = $this->params('module');
        $id     = $this->params('id');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Get user
        $uid = Pi::user()->getId();
        // check
        if (!$config['manage_active']) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Owner dashboard is inactive'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        } else {
            Pi::service('authentication')->requireLogin();
        }
        // Check allowed
        $allowed = 0;
        if (Pi::service('module')->isActive('guide')) {
            $owner = $this->canonizeGuideOwner();
            if (isset($owner['id']) && $owner['id'] > 0) {
                $allowed = 1;
            }
        } else {
            $roles = Pi::user()->getRole($uid, 'front');
            if (in_array($config['manage_role'], $roles)) {
                $allowed = 1;
            }
        }
        if (!$allowed) {
            $this->getResponse()->setStatusCode(403);
            $this->terminate(
                __(
                    'You are not allowed to submit new event, please contact to website admin and complete your registration, after that you allowed to submit events'
                ), '', 'error-denied'
            );
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Find event
        $event = Pi::api('event', 'event')->getEventSingle($id, 'id', 'full');
        // Check event uid
        if (isset($event['uid']) && $event['uid'] != $uid) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Its not your event'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }

        // Get info
        $list   = [];
        $order  = ['id DESC'];
        $where  = ['event' => $event['id']];
        $select = $this->getModel('order')->select()->where($where)->order($order);
        $rowset = $this->getModel('order')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id]         = Pi::api('order', 'event')->canonizeOrder($row, false);
            $list[$row->id]['user'] = Pi::user()->get(
                $row->uid, [
                'id', 'identity', 'name', 'email',
            ]
            );
        }

        // Save statistics
        if (Pi::service('module')->isActive('statistics')) {
            Pi::api('log', 'statistics')->save('event', 'manage');
        }

        // Set view
        $this->view()->setTemplate('manage-order');
        $this->view()->assign('list', $list);
        $this->view()->assign('event', $event);
        $this->view()->assign('title', sprintf('Registration List for %s', $event['title']));
    }

    public function canonizeGuideOwner()
    {
        // Get user
        $uid   = Pi::user()->getId();
        $owner = [];
        // Check guide module
        if (Pi::service('module')->isActive('guide')) {
            $owner = Pi::model('owner', 'guide')->find($uid, 'uid');
            if (empty($owner)) {
                return $this->redirect()->toRoute(
                    '', [
                    'module'     => 'guide',
                    'controller' => 'manage',
                    'action'     => 'index',
                ]
                );
            }
            // Set owner
            $owner = Pi::api('owner', 'guide')->canonizeOwner($owner);
        }
        // return
        return $owner;
    }
}

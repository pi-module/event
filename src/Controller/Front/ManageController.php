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
use Zend\Json\Json;

class ManageController extends ActionController
{
    public function indexAction()
    {
        // Get info from url
        $module = $this->params('module');
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
        // Check owner
        if (Pi::service('module')->isActive('guide')) {
            $owner = $this->canonizeGuideOwner();
            $where = array('guide_owner' => $owner['id']);
        } else {
            $where = array('uid' => Pi::user()->getId());
        }
        // Get ids
        $select = $this->getModel('extra')->select()->where($where);
        $rowset = $this->getModel('extra')->selectWith($select);
        $ids = array();
        foreach ($rowset as $row) {
            $ids[$row->id] = $row->id;
        }
        // Set info
        $listEvent = array();
        if (!empty($ids)) {
            $where = array(
                'status' => 1,
                'type' => 'event',
                'id' => $ids,
            );
            $order = array('time_publish DESC', 'id DESC');
            // Get list of story
            $listStory = Pi::api('api', 'news')->getStoryList($where, $order, '', '', 'light', 'story');
            foreach ($listStory as $singleStory) {
                $listEvent[$singleStory['id']] = Pi::api('event', 'event')->joinExtra($singleStory);
            }
        }
        // Set view
        $this->view()->setTemplate('manage-index');
        $this->view()->assign('title', __('List of your events'));
        $this->view()->assign('owner', $owner);
        $this->view()->assign('config', $config);
        $this->view()->assign('events', $listEvent);
    }

    public function updateAction()
    {
        // Get info from url
        $module = $this->params('module');
        $id = $this->params('id');
        $option = array(
            'side' => 'front'
        );
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
        // Get user
        $uid = Pi::user()->getId();
        // Find event
        if ($id) {
            $event = Pi::api('event', 'event')->getEvent($id, 'id', 'full');
            if ($event['image']) {
                $option['thumbUrl'] = $event['thumbUrl'];
                $option['removeUrl'] = $this->url('', array('action' => 'remove', 'id' => $event['id']));
            }
        }
        // Check event uid
        if (isset($event['uid']) && $event['uid'] != $uid) {
            $this->getResponse()->setStatusCode(401);
            $this->terminate(__('Its not your event'), '', 'error-denied');
            $this->view()->setLayout('layout-simple');
            return;
        }
        // Check event guide owner
        if (Pi::service('module')->isActive('guide')) {
            $owner = $this->canonizeGuideOwner();
            $option['owner'] = $owner['id'];
            if (isset($event['guide_owner']) && $event['guide_owner'] != $owner['id']) {
                $this->getResponse()->setStatusCode(401);
                $this->terminate(__('Its not your event'), '', 'error-denied');
                $this->view()->setLayout('layout-simple');
                return;
            }
        }
        // Set form
        $form = new EventForm('event', $option);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            // Set slug
            $slug = ($data['slug']) ? $data['slug'] : $data['title'];
            $filter = new Filter\Slug;
            $data['slug'] = $filter($slug);
            // Form filter
            $form->setInputFilter(new EventFilter($option));
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // upload image
                $image = Pi::api('api', 'news')->uploadImage($file, 'event-');
                $values = array_merge($values, $image);
                // Set time
                $values['time_start'] = strtotime($values['time_start']);
                $values['time_end'] = ($values['time_end']) ? strtotime($values['time_end']) : '';
                // Set type
                $values['type'] = 'event';
                // Set status
                $values['status'] = $config['manage_approval'] ? 1 : 0;
                // Set guide module info
                if (isset($owner) && isset($owner['id'])) {
                    $values['guide_owner'] = $owner['id'];
                }
                $values['guide_category'] = Json::encode($values['guide_category']);
                $values['guide_location'] = Json::encode($values['guide_location']);
                $values['guide_item'] = Json::encode($values['guide_item']);
                // Save values on news story table and event extra table
                if (!empty($values['id'])) {
                    $story = Pi::api('api', 'news')->editStory($values);
                    if (isset($story) && !empty($story)) {
                        $row = $this->getModel('extra')->find($story['id']);
                    } else {
                        $message = __('Error on save story data on news module.');
                        $this->jump(array('action' => 'index'), $message, 'error');
                    }
                } else {
                    $values['uid'] = Pi::user()->getId();
                    $story = Pi::api('api', 'news')->addStory($values);
                    if (isset($story) && !empty($story)) {
                        $row = $this->getModel('extra')->createRow();
                        $values['id'] = $story['id'];
                    } else {
                        $message = __('Error on save story data on news module.');
                        $this->jump(array('action' => 'index'), $message, 'error');
                    }
                }
                $row->assign($values);
                $row->save();
                // Set link array
                $link = array(
                    'story' => $story['id'],
                    'time_publish' => $story['time_publish'],
                    'time_update' => $story['time_update'],
                    'status' => $story['status'],
                    'uid' => $story['uid'],
                    'type' => $story['type'],
                    'module' => array(
                        'event' => array(
                            'name' => 'event',
                            'controller' => array(
                                'topic' => array(
                                    'name' => 'topic',
                                    'topic' => $values['topic'],
                                ),
                            ),
                        ),
                    ),
                );
                // Add guide module info on link
                if (Pi::service('module')->isActive('guide')) {
                    $link['module']['guide'] = array(
                        'name' => 'guide',
                        'controller' => array(),
                    );
                    if (isset($values['guide_category']) && !empty($values['guide_category'])) {
                        $link['module']['guide']['controller']['category'] = array(
                            'name' => 'category',
                            'topic' => Json::decode($values['guide_category']),
                        );
                    }

                    if (isset($values['guide_location']) && !empty($values['guide_location'])) {
                        $link['module']['guide']['controller']['location'] = array(
                            'name' => 'location',
                            'topic' => Json::decode($values['guide_location']),
                        );
                    }

                    if (isset($values['guide_item']) && !empty($values['guide_item'])) {
                        $link['module']['guide']['controller']['item'] = array(
                            'name' => 'item',
                            'topic' => Json::decode($values['guide_item']),
                        );
                    }

                    if (isset($values['guide_owner']) && !empty($values['guide_owner'])) {
                        $link['module']['guide']['controller']['owner'] = array(
                            'name' => 'owner',
                            'topic' => array(
                                $values['guide_owner'],
                            ),
                        );
                    }
                }
                // Setup link
                Pi::api('api', 'news')->setupLink($link);
                // Add / Edit sitemap
                if (Pi::service('module')->isActive('sitemap')) {
                    // Set loc
                    $loc = Pi::url($this->url('event', array(
                        'module' => $module,
                        'controller' => 'index',
                        'slug' => $values['slug']
                    )));
                    // Update sitemap
                    Pi::api('sitemap', 'sitemap')->singleLink($loc, $story['status'], $module, 'event', $story['id']);
                }
                // Add log
                $message = __('Event data saved successfully.');
                $this->jump(array('action' => 'index'), $message);
            }
        } else {
            if ($id) {
                // Set time
                $event['time_start'] = ($event['time_start']) ? date('Y-m-d', $event['time_start']) : date('Y-m-d');
                $event['time_end'] = ($event['time_end']) ? date('Y-m-d', $event['time_end']) : '';
                $form->setData($event);
            }
        }
        // Set view
        $this->view()->setTemplate('manage-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add event'));
    }

    public function removeAction()
    {
        $id = $this->params('id');
        $result = Pi::api('api', 'news')->removeImage($id);
        return $result;
    }

    public function canonizeGuideOwner()
    {
        // Get user
        $uid = Pi::user()->getId();
        $owner = array();
        // Check guide module
        if (Pi::service('module')->isActive('guide')) {
            $owner = Pi::model('owner', 'guide')->find($uid, 'uid');
            if (empty($owner)) {
                return $this->redirect()->toRoute('', array(
                    'module' => 'guide',
                    'controller' => 'manage',
                    'action' => 'index',
                ));
            }
            // Set owner
            $owner = Pi::api('owner', 'guide')->canonizeOwner($owner);
        }
        // return
        return $owner;
    }
}
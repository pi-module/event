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
namespace Module\Event\Controller\Admin;

use Pi;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Module\Event\Form\EventForm;
use Module\Event\Form\EventFilter;
use Module\News\Form\StorySearchForm;
use Module\News\Form\StorySearchFilter;

class EventController extends ActionController
{
    public function indexAction()
    {
        // Get page
        $page = $this->params('page', 1);
        $title = $this->params('title');
        // Set info
        $where = array(
            'type' => 'event',
        );
        if (!empty($title)) {
            $where['title LIKE ?'] = '%' . $title . '%';
        }
        $order = array('id DESC');
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $limit = intval($this->config('admin_perpage'));
        // Get list of event
        $listEvent = Pi::api('event', 'event')->getEventList($where, $order, $offset, $limit, 'light', 'story');
        // Set template
        $template = array(
            'module' => 'event',
            'controller' => 'event',
            'action' => 'index',
            'title' => $title,
        );
        // Get paginator
        $paginator = Pi::api('api', 'news')->getStoryPaginator($template, $where, $page, $limit, 'story');
        // Set form
        $values = array(
            'title' => $title,
        );
        $form = new StorySearchForm('search');
        $form->setAttribute('action', $this->url('', array('action' => 'process')));
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
            $form = new StorySearchForm('search');
            $form->setInputFilter(new StorySearchFilter());
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                $message = __('View filtered events');
                $url = array(
                    'action' => 'index',
                    'title' => $values['title'],
                );
            } else {
                $message = __('Not valid');
                $url = array(
                    'action' => 'index',
                );
            }
        } else {
            $message = __('Not set');
            $url = array(
                'action' => 'index',
            );
        }
        return $this->jump($url, $message);
    }

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        $module = $this->params('module');
        // Set local
        $local = Pi::service('i18n')->getLocale();
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Set option
        $option = array(
            'side' => 'admin',
            'use_news_topic' => $config['use_news_topic'],
            'use_guide_category' => $config['use_guide_category'],
            'use_guide_location' => $config['use_guide_location'],
            'order_active' => $config['order_active'],
            'order_discount' => $config['order_discount'],
            'map_use' => $config['map_use'],
            'manage_register' => $config['manage_register'],
            'register_can' => $this->params('register_can')
        );
        // Find event
        if ($id) {
            $event = Pi::api('event', 'event')->getEventSingle($id, 'id', 'full');
            if ($event['image']) {
                $event['thumbUrl'] = Pi::url(
                    sprintf('upload/%s/original/%s/%s',
                        'event/image',
                        $event['path'],
                        $event['image']
                    ));

                $option['thumbUrl'] = $event['thumbUrl'];
                $option['removeUrl'] = $this->url('', array('action' => 'remove', 'id' => $event['id']));
            }
        }
        // Set form
        $option['id'] = $id;
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
                $formIsValid = true;

                $values = $form->getData();
                // upload image
                $image = Pi::api('api', 'news')->uploadImage($file, 'event-', 'event/image');

                if($file && !empty($file['image']['name']) && (!$image || is_string($image))){
                    $formIsValid = false;

                    if(is_string($image)){
                        $messenger = $this->plugin('flashMessenger');
                        $messenger->addMessage($image);
                    }
                }

                if($formIsValid){
                    $values = array_merge($values, $image);

                    if ($values['image'] == '') {
                        unset($values['image']);
                    }

                    // Set time
                    if ($local == 'fa') {
                        $values['time_publish'] = ($values['time_end']) ? $values['time_end'] : $values['time_start'];
                        $values['time_start'] = $values['time_start'];
                        $values['time_end'] = ($values['time_end']) ? $values['time_end'] : '';
                    } else {
                        $values['time_publish'] = ($values['time_end']) ? strtotime($values['time_end']) : strtotime($values['time_start']);
                        $values['time_start'] = strtotime($values['time_start']);
                        $values['time_end'] = ($values['time_end']) ? strtotime($values['time_end']) : '';
                    }

                    // Set type
                    $values['type'] = 'event';
                    // Set guide module info
                    $values['guide_category'] = json_encode($values['guide_category']);
                    $values['guide_location'] = json_encode($values['guide_location']);
                    $values['guide_item'] = json_encode($values['guide_item']);
                    // Set register_discount
                    $discount = array();
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
                    if (!empty($values['id'])) {
                        $story = Pi::api('api', 'news')->editStory($values, true);
                        if (isset($story) && !empty($story)) {
                            $row = $this->getModel('extra')->find($story['id']);
                        } else {
                            $message = __('Error on save story data on event module.');
                            $this->jump(array('action' => 'index'), $message, 'error');
                        }
                    } else {
                        $values['uid'] = Pi::user()->getId();
                        $story = Pi::api('api', 'news')->addStory($values, true);
                        if (isset($story) && !empty($story)) {
                            $row = $this->getModel('extra')->createRow();
                            $values['id'] = $story['id'];
                        } else {
                            $message = __('Error on save story data on event module.');
                            $this->jump(array('action' => 'index'), $message, 'error');
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
                        $values['topic'] = array();
                    }
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
                        if ($config['use_guide_category'] && isset($values['guide_category']) && !empty($values['guide_category'])) {
                            $link['module']['guide']['controller']['category'] = array(
                                'name' => 'category',
                                'topic' => json_decode($values['guide_category'], true),
                            );
                        }

                        if ($config['use_guide_location'] && isset($values['guide_location']) && !empty($values['guide_location'])) {
                            $link['module']['guide']['controller']['location'] = array(
                                'name' => 'location',
                                'topic' => json_decode($values['guide_location'], true),
                            );
                        }

                        if (isset($values['guide_item']) && !empty($values['guide_item'])) {
                            $link['module']['guide']['controller']['item'] = array(
                                'name' => 'item',
                                'topic' => json_decode($values['guide_item'], true),
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
            }
        } else {
            if ($id) {
                // Make discount
                if ($config['order_discount']) {
                    foreach ($event['register_discount'] as $name => $value) {
                        $event[$name] = $value;
                    }
                }
                // Set time
                if ($local != 'fa') {
                    $event['time_start'] = ($event['time_start']) ? date('Y-m-d', $event['time_start']) : date('Y-m-d');
                    $event['time_end'] = ($event['time_end']) ? date('Y-m-d', $event['time_end']) : '';
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

    public function removeAction()
    {
        $id = $this->params('id');
        $result = Pi::api('api', 'news')->removeImage($id);
        return $result;
    }
}

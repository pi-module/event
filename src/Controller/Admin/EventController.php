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
use Pi\Paginator\Paginator;
use Pi\File\Transfer\Upload;
use Module\Event\Form\EventForm;
use Module\Event\Form\EventFilter;

class EventController extends ActionController
{
    /**
     * Event Image Prefix
     */
    protected $ImageEventPrefix = 'event_';

    public function indexAction()
    {
        // Get page
        $page = $this->params('page', 1);
        $module = $this->params('module');
        $title = $this->params('title');
        // Get info
        $list = array();
        /* $order = array('id DESC');
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $limit = intval($this->config('admin_perpage'));
        $where = array();
        // Get
        if (!empty($title)) {
            $where['title LIKE ?'] = '%' . $title . '%';
        }
        $select = $this->getModel('event')->select()->where($where)->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('event')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = Pi::api('event', 'guide')->canonizeEvent($row);
        }
        // Set paginator
        $columns = array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)'));
        $select = $this->getModel('event')->select()->where($where)->columns($columns);
        $count = $this->getModel('event')->selectWith($select)->current()->count;
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            'router' => $this->getEvent()->getRouter(),
            'route' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params' => array_filter(array(
                'module' => $this->getModule(),
                'controller' => 'event',
                'action' => 'index',
            )),
        ));
        // Set form
        $values = array(
            'title' => $title,
        );
        $form = new AdminSearchForm('search');
        $form->setAttribute('action', $this->url('', array('action' => 'process')));
        $form->setData($values); */
        // Set view
        $this->view()->setTemplate('event-index');
        $this->view()->assign('list', $list);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('form', $form);
    }

    public function processAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form = new AdminSearchForm('search');
            $form->setInputFilter(new AdminSearchFilter());
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                $message = __('View filtered items');
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
        $option = array();
        // Find event
        /* if ($id) {
            $event = $this->getModel('event')->find($id)->toArray();
            if ($event['image']) {
                $event['thumbUrl'] = sprintf('upload/%s/thumb/%s/%s', $this->config('image_path'), $event['path'], $event['image']);
                $option['thumbUrl'] = Pi::url($event['thumbUrl']);
                $option['removeUrl'] = $this->url('', array('action' => 'remove', 'id' => $event['id']));
            }
        } */
        // Set option
        $option['condition'] = 'admin';
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

            echo '<pre>';
            print_r($data);
            echo '</pre>';

            if ($form->isValid()) {
                $values = $form->getData();

                echo '<pre>';
                print_r($values);
                echo '</pre>';

                // upload image
                /* if (!empty($file['image']['name'])) {
                    // Set upload path
                    $values['path'] = sprintf('%s/%s', date('Y'), date('m'));
                    $originalPath = Pi::path(sprintf('upload/%s/original/%s', $this->config('image_path'), $values['path']));
                    // Image name
                    $imageName = Pi::api('image', 'guide')->rename($file['image']['name'], $this->ImageEventPrefix, $values['path']);
                    // Upload
                    $uploader = new Upload;
                    $uploader->setDestination($originalPath);
                    $uploader->setRename($imageName);
                    $uploader->setExtension($this->config('image_extension'));
                    $uploader->setSize($this->config('image_size'));
                    if ($uploader->isValid()) {
                        $uploader->receive();
                        // Get image name
                        $values['image'] = $uploader->getUploaded('image');
                        // process image
                        Pi::api('image', 'guide')->process($values['image'], $values['path']);
                    } else {
                        $this->jump(array('action' => 'update'), __('Problem in upload image. please try again'));
                    }
                } elseif (!isset($values['image'])) {
                    $values['image'] = '';
                }
                // Set seo_title
                $title = ($values['seo_title']) ? $values['seo_title'] : $values['title'];
                $filter = new Filter\HeadTitle;
                $values['seo_title'] = $filter($title);
                // Set seo_keywords
                $keywords = ($values['seo_keywords']) ? $values['seo_keywords'] : $values['title'];
                $filter = new Filter\HeadKeywords;
                $filter->setOptions(array(
                    'force_replace_space' => (bool)$this->config('force_replace_space'),
                ));
                $values['seo_keywords'] = $filter($keywords);
                // Set seo_description
                $description = ($values['seo_description']) ? $values['seo_description'] : $values['title'];
                $filter = new Filter\HeadDescription;
                $values['seo_description'] = $filter($description);
                // Set time
                if (empty($values['id'])) {
                    $values['time_create'] = time();
                }
                $values['time_update'] = time();
                $values['time_start'] = strtotime($values['time_start']);
                $values['time_end'] = ($values['time_end']) ? strtotime($values['time_end']) : '';
                // Add user info
                $values['uid'] = Pi::user()->getId();
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('event')->find($values['id']);
                } else {
                    $row = $this->getModel('event')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Add / Edit sitemap
                if (Pi::service('module')->isActive('sitemap')) {
                    // Set loc
                    $loc = Pi::url($this->url('guide', array(
                        'module' => $module,
                        'controller' => 'event',
                        'slug' => $values['slug']
                    )));
                    // Update sitemap
                    Pi::api('sitemap', 'sitemap')->singleLink($loc, $row->status, $module, 'event', $row->id);
                }
                // Add log
                $operation = (empty($values['id'])) ? 'add' : 'edit';
                Pi::api('log', 'guide')->addLog('event', $row->id, $operation);
                $message = __('Event data saved successfully.');
                $this->jump(array('action' => 'index'), $message); */
            }
        } else {
            /* if ($id) {
                // Set time
                $event['time_start'] = ($event['time_start']) ? date('Y-m-d', $event['time_start']) : date('Y-m-d');
                $event['time_end'] = ($event['time_end']) ? date('Y-m-d', $event['time_end']) : '';
                $form->setData($event);
            } */
        }
        // Set view
        $this->view()->setTemplate('event-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add event'));
    }

    public function removeAction()
    {
        // Get id and status
        $id = $this->params('id');
        // set event
        $event = $this->getModel('event')->find($id);
        // Check
        if ($event && !empty($id)) {
            // remove file
            /* $files = array(
                Pi::path(sprintf('upload/%s/original/%s/%s', $this->config('image_path'), $event->path, $event->image)),
                Pi::path(sprintf('upload/%s/large/%s/%s', $this->config('image_path'), $event->path, $event->image)),
                Pi::path(sprintf('upload/%s/medium/%s/%s', $this->config('image_path'), $event->path, $event->image)),
                Pi::path(sprintf('upload/%s/thumb/%s/%s', $this->config('image_path'), $event->path, $event->image)),
            );
            Pi::service('file')->remove($files); */
            // clear DB
            $event->path = '';
            $event->image = '';
            // Save
            $event->save();
            // Check
            if ($event->path == '' && $event->image == '') {
                $message = sprintf(__('Image of %s removed'), $event->title);
                $status = 1;
            } else {
                $message = __('Image not remove');
                $status = 0;
            }
        } else {
            $message = __('Please select event');
            $status = 0;
        }
        return array(
            'status' => $status,
            'message' => $message,
        );
    }
}
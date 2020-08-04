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
use Module\Event\Form\SitemapForm;

class ToolsController extends ActionController
{
    public function indexAction()
    {
    }

    public function importAction()
    {
        // Check guide module
        if (Pi::service('module')->isActive('guide')) {
            $module = $this->params('module');
            // Select
            $events = [];
            $select = Pi::model('event', 'guide')->select();
            $rowset = Pi::model('event', 'guide')->selectWith($select);
            foreach ($rowset as $row) {
                $events[$row->id] = Pi::api('event', 'guide')->canonizeEvent($row);
            }
            // Check list
            if (!empty($events)) {
                // Save events
                foreach ($events as $event) {
                    // Set info
                    unset($event['id']);
                    $event['type']         = 'event';
                    $event['time_publish'] = ($event['time_end']) ? $event['time_end'] : $event['time_start'];
                    $event['topic']        = [];
                    // Save as story
                    $story = Pi::api('api', 'news')->addStory($event);
                    // Set id
                    $event['id'] = $story['id'];
                    // Set guide module info
                    $event['guide_category'] = json_encode([$event['category']]);
                    $event['guide_location'] = json_encode([$event['location']]);
                    $event['guide_item']     = json_encode([$event['item']]);
                    $event['guide_owner']    = $event['owner'];
                    // Set extra fields
                    $event['register_details'] = $event['registration_details'];
                    $event['register_price']   = $event['price'];
                    $event['register_can']     = 0;
                    $event['register_stock']   = 0;
                    $event['register_sales']   = 0;

                    // Save event
                    $row = $this->getModel('extra')->createRow();
                    $row->assign($event);
                    $row->save();
                    // move image
                    if (!empty($event['image']) && !empty($event['path'])) {
                        // Move original
                        $originalOld = Pi::path(
                            sprintf('upload/guide/image/original/%s/%s', $event['path'], $event['image'])
                        );
                        $originalNew = Pi::path(
                            sprintf('upload/event/image/original/%s/%s', $event['path'], $event['image'])
                        );
                        if (file_exists($originalOld)) {
                            Pi::service('file')->copy($originalOld, $originalNew);
                            Pi::service('file')->remove($originalOld);
                        }
                        // Move large
                        $largeOld = Pi::path(
                            sprintf('upload/guide/image/large/%s/%s', $event['path'], $event['image'])
                        );
                        $largeNew = Pi::path(
                            sprintf('upload/event/image/large/%s/%s', $event['path'], $event['image'])
                        );
                        if (file_exists($largeOld)) {
                            Pi::service('file')->copy($largeOld, $largeNew);
                            Pi::service('file')->remove($largeOld);
                        }
                        // Move medium
                        $mediumOld = Pi::path(
                            sprintf('upload/guide/image/medium/%s/%s', $event['path'], $event['image'])
                        );
                        $mediumNew = Pi::path(
                            sprintf('upload/event/image/medium/%s/%s', $event['path'], $event['image'])
                        );
                        if (file_exists($mediumOld)) {
                            Pi::service('file')->copy($mediumOld, $mediumNew);
                            Pi::service('file')->remove($mediumOld);
                        }
                        // Move thumb
                        $thumbOld = Pi::path(
                            sprintf('upload/guide/image/thumb/%s/%s', $event['path'], $event['image'])
                        );
                        $thumbNew = Pi::path(
                            sprintf('upload/event/image/thumb/%s/%s', $event['path'], $event['image'])
                        );
                        if (file_exists($thumbOld)) {
                            Pi::service('file')->copy($thumbOld, $thumbNew);
                            Pi::service('file')->remove($thumbOld);
                        }

                        // remove item image
                        $itemOld = Pi::path(
                            sprintf('upload/guide/image/item/%s/%s', $event['path'], $event['image'])
                        );
                        if (file_exists($itemOld)) {
                            Pi::service('file')->remove($itemOld);
                        }
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
                                'controller' => [],
                            ],
                            'guide' => [
                                'name'       => 'guide',
                                'controller' => [],
                            ],
                        ],
                    ];

                    if (isset($event['topic']) && !empty($event['topic'])) {
                        $link['module']['event']['controller']['topic'] = [
                            'name'  => 'topic',
                            'topic' => $event['topic'],
                        ];
                    }

                    if (isset($event['guide_category']) && !empty($event['guide_category'])) {
                        $link['module']['guide']['controller']['category'] = [
                            'name'  => 'category',
                            'topic' => json_decode($event['guide_category'], true),
                        ];
                    }

                    if (isset($event['guide_location']) && !empty($event['guide_location'])) {
                        $link['module']['guide']['controller']['location'] = [
                            'name'  => 'location',
                            'topic' => json_decode($event['guide_location'], true),
                        ];
                    }

                    if (isset($event['guide_item']) && !empty($event['guide_item'])) {
                        $link['module']['guide']['controller']['item'] = [
                            'name'  => 'item',
                            'topic' => json_decode($event['guide_item'], true),
                        ];
                    }
                    if (isset($event['guide_owner']) && !empty($event['guide_owner'])) {
                        $link['module']['guide']['controller']['owner'] = [
                            'name'  => 'owner',
                            'topic' => [
                                $event['guide_owner'],
                            ],
                        ];
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
                                'slug'       => $event['slug'],
                            ]
                            )
                        );
                        // Update sitemap
                        Pi::api('sitemap', 'sitemap')->singleLink($loc, $story['status'], $module, 'event', $story['id']);
                    }
                    // Set message
                    $message = __('Guide events imported to event module');
                }
            } else {
                $message = __('Guide event table is empty');
            }
        } else {
            $message = __('Guide module not installed');
        }
        // Set jump
        $url = [
            'action' => 'index',
        ];
        $this->jump($url, $message);
        // Set view
        $this->view()->setTemplate(false);
    }

    public function sitemapAction()
    {
        $form    = new SitemapForm('sitemap');
        $message = __('Rebuild thie module links on sitemap module tabels');
        if ($this->request->isPost()) {
            // Set form date
            $values = $this->request->getPost()->toArray();
            switch ($values['type']) {
                case '1':
                    Pi::api('event', 'event')->sitemap();
                    break;
            }
            $message = __('Sitemap rebuild finished');
        }
        // Set view
        $this->view()->setTemplate('tools-sitemap');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Rebuild sitemap links'));
        $this->view()->assign('message', $message);
    }

    public function fillTimeEndAction()
    {
        $extraModel = Pi::model('extra', 'event');
        $select     = $extraModel->select();

        $select->where(
            [
                'time_end' => 0,
            ]
        );

        $extraCollection = $extraModel->selectWith($select);

        if ($extraCollection->count()) {
            foreach ($extraCollection as $extraEntity) {

                if ($extraEntity->time_start) {
                    $extraEntity->time_end = $extraEntity->time_start;
                    $extraEntity->save();
                }
            }

            $messenger = $this->plugin('flashMessenger');
            $messenger->addSuccessMessage(__('Time end fields are filled successfully'));
        } else {
            $messenger = $this->plugin('flashMessenger');
            $messenger->addMessage(__('Time end fields are filled yet'));
        }

        $this->redirect()->toRoute(null, ['action' => 'index']);
    }
}

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
use Pi\Mvc\Controller\ActionController;

class OrderController extends ActionController
{
    public function indexAction()
    {
        // Get info
        $list   = [];
        $order  = ['id DESC'];
        $select = $this->getModel('order')->select()->order($order);
        $rowset = $this->getModel('order')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id]         = Pi::api('order', 'event')->canonizeOrder($row);
            $list[$row->id]['user'] = Pi::user()->get(
                $row->uid,
                [
                    'id', 'identity', 'name', 'email',
                ]
            );
        }
        // Set view
        $this->view()->setTemplate('order-index');
        $this->view()->assign('list', $list);
    }

    public function acceptAction()
    {
        // Get id
        $id = $this->params('id');

        // Update sales
        $this->getModel('order')->update(
            [
                'status' => 1,
            ],
            [
                'id' => $id,
            ]
        );

        return [
            'status'     => 1,
            'ajaxstatus' => 1,
        ];
    }
}

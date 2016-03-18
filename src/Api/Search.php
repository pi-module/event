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
use Pi\Search\AbstractSearch;

class Search extends AbstractSearch
{
    /**
     * {@inheritDoc}
     */
    protected $table = 'story';

    /**
     * {@inheritDoc}
     */
    protected $searchIn = array(
        'title',
        'text_summary',
        'text_description',
    );

    /**
     * {@inheritDoc}
     */
    protected $meta = array(
        'id' => 'id',
        'title' => 'title',
        'text_summary' => 'content',
        'time_create' => 'time',
        'uid' => 'uid',
        'slug' => 'slug',
    );

    /**
     * {@inheritDoc}
     */
    protected $condition = array(
        'status' => 1,
        'type' => 'event',
    );

    /**
     * {@inheritDoc}
     */
    protected function getModel()
    {
        $model = Pi::model('story', 'news');

        return $model;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildUrl(array $item)
    {
        $link = Pi::url(Pi::service('url')->assemble('event', array(
            'module' => $this->getModule(),
            'controller' => 'detail',
            'slug' => $item['slug'],
        )));

        return $link;
    }
}

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
use Pi\Search\AbstractSearch;

class Search extends AbstractSearch
{
    /**
     * {@inheritDoc}
     */
    protected $table
        = [
            'story',
            'topic',
        ];

    /**
     * {@inheritDoc}
     */
    protected $searchIn
        = [
            'title',
            'text_summary',
            'text_description',
        ];

    /**
     * {@inheritDoc}
     */
    protected $meta
        = [
            'id'           => 'id',
            'title'        => 'title',
            'text_summary' => 'content',
            'time_create'  => 'time',
            'slug'         => 'slug',
            'main_image'   => 'main_image',
            'path'         => 'path',
        ];

    /**
     * {@inheritDoc}
     */
    protected $condition
        = [
            'status' => 1,
            'type'   => 'event',
        ];

    /**
     * {@inheritDoc}
     */
    protected $order
        = [
            'time_publish DESC',
            'id DESC',
        ];

    /**
     * {@inheritDoc}
     */
    protected function getModel($table = '')
    {
        $model = Pi::model($table, 'news');
        return $model;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildUrl(array $item, $table = '')
    {
        switch ($table) {
            case 'story':
                $link = Pi::url(
                    Pi::service('url')->assemble(
                        'event',
                        [
                        'module'     => $this->getModule(),
                        'controller' => 'detail',
                        'slug'       => $item['slug'],
                    ]
                    )
                );
                break;

            case 'topic':
                $link = Pi::url(
                    Pi::service('url')->assemble(
                        'event',
                        [
                        'module'     => $this->getModule(),
                        'controller' => 'category',
                        'slug'       => $item['slug'],
                    ]
                    )
                );
                break;
        }

        return $link;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildImage(array $item, $table = '')
    {
        return (string)Pi::api('doc', 'media')->getSingleLinkUrl($item['main_image'])->setConfigModule('news')->thumb('thumbnail');
    }
}

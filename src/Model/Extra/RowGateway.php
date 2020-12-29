<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt BSD 3-Clause License
 */

namespace Module\Event\Model\Extra;

use \Pi;

class RowGateway extends \Pi\Db\RowGateway\RowGateway
{
    public function save($rePopulate = true, $filter = true)
    {
        if ($this->time_start && (empty($this->time_end) || $this->time_end == 0)) {
            $this->time_end = $this->time_start;
        }

        $url = Pi::url(
            Pi::service('url')->assemble(
                'event',
                [
                'slug' => $this->slug,
            ]
            )
        );

        Pi::service('cache')->flushCacheByUrl($url, 'event');

        return parent::save($rePopulate, $filter);
    }
}

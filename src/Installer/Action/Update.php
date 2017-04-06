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
namespace Module\Event\Installer\Action;

use Pi;
use Pi\Application\Installer\Action\Update as BasicUpdate;
use Pi\Application\Installer\SqlSchema;
use Zend\EventManager\Event;

class Update extends BasicUpdate
{
    /**
     * {@inheritDoc}
     */
    protected function attachDefaultListeners()
    {
        $events = $this->events;
        $events->attach('update.pre', array($this, 'updateSchema'));
        parent::attachDefaultListeners();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function updateSchema(Event $e)
    {
        $moduleVersion = $e->getParam('version');

        // Set extra model
        $extraModel = Pi::model('extra', $this->module);
        $extraTable = $extraModel->getTable();
        $extraAdapter = $extraModel->getAdapter();

        // Set news story model
        $newsStoryModel = Pi::model('story', 'news');
        $newsStoryTable = $newsStoryModel->getTable();
        $newsStoryAdapter = $newsStoryModel->getAdapter();

        // Update to version 0.1.5
        if (version_compare($moduleVersion, '0.1.5', '<')) {
            // Update value
            $select = $extraModel->select();
            $rowSet = $extraModel->selectWith($select);
            foreach ($rowSet as $row) {
                // Set time
                $time = ($row->time_end) ? $row->time_end : $row->time_start;
                // Update time_publish
                $newsStoryModel->update(
                    array('time_publish' => $time),
                    array('id' => $row->id)
                );
            }
        }

        // Update to version 0.4.1
        if (version_compare($moduleVersion, '2.0.0', '<')) {
            // Alter table field `register_discount`
            $sql = sprintf("ALTER TABLE %s ADD `register_discount` TEXT", $extraTable);
            try {
                $extraAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult('db', array(
                    'status' => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ));
                return false;
            }
        }

        if (version_compare($moduleVersion, '2.0.1', '<')) {
            Pi::api('event', 'event')->migrateMedia();
        }
    }
}
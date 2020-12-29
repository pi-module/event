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

namespace Module\Event\Installer\Action;

use Pi;
use Pi\Application\Installer\Action\Update as BasicUpdate;
use Pi\Application\Installer\SqlSchema;
use Laminas\EventManager\Event;

class Update extends BasicUpdate
{
    /**
     * {@inheritDoc}
     */
    protected function attachDefaultListeners()
    {
        $events = $this->events;
        $events->attach('update.pre', [$this, 'updateSchema']);
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
        $extraModel   = Pi::model('extra', $this->module);
        $extraTable   = $extraModel->getTable();
        $extraAdapter = $extraModel->getAdapter();

        // Set news story model
        $newsStoryModel   = Pi::model('story', 'news');
        $newsStoryTable   = $newsStoryModel->getTable();
        $newsStoryAdapter = $newsStoryModel->getAdapter();

        // Set order model
        $orderModel   = Pi::model('order', $this->module);
        $orderTable   = $orderModel->getTable();
        $orderAdapter = $orderModel->getAdapter();

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
                    ['time_publish' => $time],
                    ['id' => $row->id]
                );
            }
        }

        // Update to version 0.4.1
        if (version_compare($moduleVersion, '0.4.1', '<')) {
            // Alter table field `register_discount`
            $sql = sprintf("ALTER TABLE %s ADD `register_discount` TEXT", $extraTable);
            try {
                $extraAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        // Update to version 2.0.1
        if (version_compare($moduleVersion, '2.0.1', '<')) {
            Pi::api('event', 'event')->migrateMedia();
        }

        // Update to version 2.0.4
        if (version_compare($moduleVersion, '2.0.4', '<')) {
            // Alter table field `map_latitude`
            $sql = sprintf("ALTER TABLE %s ADD `map_latitude` VARCHAR(16) NOT NULL DEFAULT ''", $extraTable);
            try {
                $extraAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table field `map_longitude`
            $sql = sprintf("ALTER TABLE %s ADD `map_longitude` VARCHAR(16) NOT NULL DEFAULT ''", $extraTable);
            try {
                $extraAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
            // Alter table field `map_zoom`
            $sql = sprintf("ALTER TABLE %s ADD `map_zoom` INT(5) UNSIGNED NOT NULL DEFAULT '0'", $extraTable);
            try {
                $extraAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        // Update to version 2.0.7
        if (version_compare($moduleVersion, '2.0.7', '<')) {
            // Alter table field `register_click`
            $sql = sprintf("ALTER TABLE %s ADD `register_click` INT(10) UNSIGNED NOT NULL DEFAULT '0'", $extraTable);
            try {
                $extraAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }
        if (version_compare($moduleVersion, '2.0.9', '<')) {
            // Alter table field `register_click`
            $sql = sprintf("ALTER TABLE %s CHANGE `register_stock` `register_stock` INT(10) UNSIGNED NULL DEFAULT NULL;", $extraTable);
            try {
                $extraAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }

            $sql = sprintf("UPDATE %s SET register_stock = null WHERE register_stock = 0 AND register_can = 0;", $extraTable);
            try {
                $extraAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }

        if (version_compare($moduleVersion, '2.0.10', '<')) {
            $sql = sprintf("ALTER TABLE %s DROP  `register_type` ", $extraTable);
            try {
                $extraAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                    'status'  => false,
                    'message' => 'Table alter query failed: '
                        . $exception->getMessage(),
                ]
                );
                return false;
            }
        }
        if (version_compare($moduleVersion, '2.0.12', '<')) {
            $sql = sprintf("ALTER TABLE %s DROP  `code_private`, DROP  `code_public`  ", $orderTable);
            try {
                $extraAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }
        }

        if (version_compare($moduleVersion, '2.2.1', '<')) {
            $sql = sprintf("ALTER TABLE %s ADD `main_image` INT(10) UNSIGNED NOT NULL DEFAULT '0'", $orderTable);
            try {
                $orderAdapter->query($sql, 'execute');
            } catch (\Exception $exception) {
                $this->setResult(
                    'db',
                    [
                        'status'  => false,
                        'message' => 'Table alter query failed: '
                            . $exception->getMessage(),
                    ]
                );
                return false;
            }
        }
    }
}

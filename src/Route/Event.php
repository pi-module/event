<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\Event\Route;

use Pi;
use Pi\Mvc\Router\Http\Standard;

class Event extends Standard
{
    /**
     * Default values.
     * @var array
     */
    protected $defaults = array(
        'module' => 'event',
        'controller' => 'index',
        'action' => 'index'
    );

    protected $controllerList = array(
        'index', 'category', 'detail', 'manage', 'register', 'json'
    );

    /**
     * {@inheritDoc}
     */
    protected $structureDelimiter = '/';

    /**
     * {@inheritDoc}
     */
    protected function parse($path)
    {
        $matches = array();
        $parts = array_filter(explode($this->structureDelimiter, $path));

        // Set controller
        $matches = array_merge($this->defaults, $matches);
        if (isset($parts[0]) && in_array($parts[0], $this->controllerList)) {
            $matches['controller'] = $this->decode($parts[0]);
            // Make Match
            if (isset($matches['controller'])) {
                switch ($matches['controller']) {
                    case 'category':
                        return false;
                        break;

                    case 'detail':
                        return false;
                        break;

                    case 'manage':
                        if (isset($parts[1]) && $parts[1] == 'update') {
                            $matches['action'] = 'update';
                            if (isset($parts[2]) && $parts[2] == 'id') {
                                $matches['id'] = intval($parts[3]);
                            } elseif (isset($parts[2]) && $parts[2] == 'item') {
                                $matches['item'] = intval($parts[3]);
                            }
                        } elseif (isset($parts[1]) && $parts[1] == 'remove'
                            && isset($parts[3]) && is_numeric($parts[3])
                        ) {
                            $matches['action'] = 'remove';
                            $matches['id'] = intval($parts[3]);
                        } elseif (isset($parts[1]) && $parts[1] == 'order'
                            && isset($parts[3]) && is_numeric($parts[3])
                        ) {
                            $matches['action'] = 'order';
                            $matches['id'] = intval($parts[3]);
                        }
                        break;

                    case 'register':
                        if (isset($parts[1]) && $parts[1] == 'add') {
                            $matches['action'] = 'add';
                            $matches['slug'] = $this->decode($parts[2]);
                        }
                        break;

                    case 'json':
                        $matches['action'] = $this->decode($parts[1]);

                        if ($parts[1] == 'filterCategory') {
                            $matches['slug'] = $this->decode($parts[2]);
                        } elseif ($parts[1] == 'filterTag') {
                            $matches['slug'] = $this->decode($parts[2]);
                        }

                        if (isset($parts[2]) && $parts[2] == 'id') {
                            $matches['id'] = intval($parts[3]);
                        }

                        if (isset($parts[2]) && $parts[2] == 'update') {
                            $matches['update'] = intval($parts[3]);
                        } elseif (isset($parts[4]) && $parts[4] == 'update') {
                            $matches['update'] = intval($parts[5]);
                        }

                        if (isset($parts[4]) && $parts[4] == 'password') {
                            $matches['password'] = $this->decode($parts[5]);
                        } elseif (isset($parts[6]) && $parts[6] == 'password') {
                            $matches['password'] = $this->decode($parts[7]);
                        }

                        if($matches['action'] == 'hit'){
                            $matches['slug'] = $this->decode($parts[2]);
                        }

                        break;
                }
            }
        } elseif (isset($parts[0])) {
            $parts[0] = urldecode($parts[0]);
            $categorySlug = Pi::registry('categoryRoute', 'event')->read();
            if (in_array($parts[0], $categorySlug)) {
                $matches['controller'] = 'category';
                $matches['action'] = 'index';
                $matches['slug'] = $this->decode($parts[0]);
            } else {
                $matches['controller'] = 'detail';
                $matches['action'] = 'index';
                $matches['slug'] = $this->decode($parts[0]);
            }
        }

        /* echo '<pre>';
        print_r($matches);
        print_r($parts);
        echo '</pre>'; */

        return $matches;
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return string
     */
    public function assemble(
        array $params = array(),
        array $options = array()
    )
    {
        $mergedParams = array_merge($this->defaults, $params);
        if (!$mergedParams) {
            return $this->prefix;
        }

        // Set module
        if (!empty($mergedParams['module'])) {
            $url['module'] = $mergedParams['module'];
        }

        // Set controller
        if (!empty($mergedParams['controller'])
            && $mergedParams['controller'] != 'index'
            && $mergedParams['controller'] != 'detail'
            && $mergedParams['controller'] != 'category'
            && in_array($mergedParams['controller'], $this->controllerList)
        ) {
            $url['controller'] = $mergedParams['controller'];
        }

        // Set action
        if (!empty($mergedParams['action'])
            && $mergedParams['action'] != 'index'
        ) {
            $url['action'] = $mergedParams['action'];
        }

        // Set slug
        if (!empty($mergedParams['slug'])) {
            $url['slug'] = $mergedParams['slug'];
        }

        // Set id
        if (!empty($mergedParams['id'])) {
            $url['id'] = 'id' . $this->paramDelimiter . $mergedParams['id'];
        }

        // Set item
        if (!empty($mergedParams['item'])) {
            $url['item'] = 'item' . $this->paramDelimiter . $mergedParams['item'];
        }

        // Set password
        if (!empty($mergedParams['password'])) {
            $url['password'] = 'password' . $this->paramDelimiter . $mergedParams['password'];
        }
        
         if (!empty($mergedParams['status'])) {
            $url['status'] = 'status' . $this->paramDelimiter . $mergedParams['status'];
        }

        // Make url
        $url = implode($this->paramDelimiter, $url);

        if (empty($url)) {
            return $this->prefix;
        }

        $finalUrl = rtrim($this->paramDelimiter . $url, '/');

        return $finalUrl;
    }
}

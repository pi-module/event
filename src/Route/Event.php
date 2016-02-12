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
namespace Module\Event\Route;

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
        'index', 'category', 'detail'
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
        } elseif (isset($parts[0]) && !in_array($parts[0], $this->controllerList)) {
            $matches['controller'] = 'detail';
        }

        // Make Match
        if (isset($matches['controller']) && isset($parts[0]) && !empty($parts[0])) {
            switch ($matches['controller']) {
                case 'category':
                    if (isset($parts[1]) && !empty($parts[1])) {
                        $matches['action'] = 'index';
                        $matches['slug'] = $this->decode($parts[1]);
                    } else {
                        $matches['action'] = 'list';
                    }
                    break;

                case 'detail':
                    $matches['slug'] = $this->decode($parts[0]);
                    break;
            }
        }

        echo '<pre>';
        print_r($matches);
        print_r($parts);
        echo '</pre>';

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

        // Set password
        if (!empty($mergedParams['password'])) {
            $url['password'] = 'password' . $this->paramDelimiter . $mergedParams['password'];
        }

        // Make url
        $url = implode($this->paramDelimiter, $url);

        if (empty($url)) {
            return $this->prefix;
        }
        return $this->paramDelimiter . $url;
    }
}
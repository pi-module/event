<?php

namespace Module\Event\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;

class ApiController extends ActionController
{
    public function favouriteAction()
    {
        /* @var Pi\Mvc\Controller\Plugin\View $view */
        $view = $this->view();

        Pi::service('log')->mute();

        // Get info from url
        $module = $this->params('module');
        $slug = $this->params('slug');
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Find event
        $event = Pi::api('event', 'event')->getEventSingle($slug, 'slug', 'full');

        // favourite
        if ($config['favourite_bar'] && Pi::service('module')->isActive('favourite')) {
            $favourite['is'] = Pi::api('favourite', 'favourite')->loadFavourite('event', 'extra', $event['id']);
            $favourite['item'] = $event['id'];
            $favourite['table'] = 'extra';
            $favourite['module'] = 'event';
            $view->assign('favourite', $favourite);



            $configFavourite = Pi::service('registry')->config->read('favourite');
            if ($configFavourite['favourite_list']) {
                $favouriteList = Pi::api('favourite', 'favourite')->listItemFavourite('event', 'extra', $event['id']);
                $view->assign('favouriteList', $favouriteList);
            }
        }

        $view->setLayout('layout-content');
        $view->setTemplate('partial/favourite');

        header("X-Robots-Tag: noindex, nofollow", true);

        echo Pi::service('view')->render($view->getViewModel());
        die();
    }
}

<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Frédéric TISSOT <contact@espritdev.fr>
 */
namespace Module\Event\Form\Element;

use Pi;
use Zend\Form\Element\Image as ZendImage;

class ImageCrop extends ZendImage
{
    /**
     * @return array
     */
    public function getAttributes()
    {
        $this->Attributes = array(
            'id' => 'imageview_0',
            'class' => 'imageview img-thumbnail item-img',
            'src' => $this->attributes['src'],
            'data-rel' => 'cropping',
        );
        return $this->Attributes;
    }
}
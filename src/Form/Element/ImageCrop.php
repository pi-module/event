<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Frédéric TISSOT <contact@espritdev.fr>
 */

namespace Module\Event\Form\Element;

use Pi;
use Laminas\Form\Element\Image as LaminasImage;

class ImageCrop extends LaminasImage
{
    /**
     * @return array
     */
    public function getAttributes()
    {
        $this->Attributes = [
            'id'       => 'imageview_0',
            'class'    => 'imageview img-thumbnail item-img',
            'src'      => $this->attributes['src'],
            'data-rel' => 'cropping',
        ];
        return $this->Attributes;
    }
}
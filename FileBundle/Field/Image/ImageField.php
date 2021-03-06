<?php

namespace Gravity\FileBundle\Field\Image;

use Gravity\FileBundle\Field\Image\Configuration\ImageFieldConfiguration;
use Gravity\FileBundle\Field\Image\Display\Image\ImageDisplay;
use Gravity\FileBundle\Field\Image\Widget\ImageBrowser\ImageBrowserWidget;
use GravityCMS\Component\Field\AbstractField;
use GravityCMS\Component\Field\Configuration\FieldSettingsConfiguration;
use GravityCMS\Component\Field\Display\DisplayInterface;
use GravityCMS\Component\Field\Widget\WidgetInterface;

class ImageField extends AbstractField
{
    /**
     * Get the identifier name of the field. This must be a unique name and contain only alphanumeric, underscores (_)
     * and period (.) characters in the format field.<plugin>.<type>
     *
     * @return string
     */
    public function getName()
    {
        return 'image';
    }

    /**
     * A friendly text label for the field widget
     *
     * @return string
     */
    public function getLabel()
    {
        return 'Image';
    }

    /**
     * Get the description of the field
     *
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * @return DisplayInterface
     */
    protected function getDefaultDisplay()
    {
        return new ImageDisplay();
    }

    /**
     * @return WidgetInterface
     */
    protected function getDefaultWidget()
    {
        return new ImageBrowserWidget();
    }

    /**
     * Get the entity class name for this field
     *
     * @return string
     */
    public function getEntityClass()
    {
        return 'Gravity\FileBundle\Entity\FieldImage';
    }

    /**
     * @return FieldSettingsConfiguration
     */
    public function getSettings()
    {
        return new ImageFieldConfiguration();
    }

}

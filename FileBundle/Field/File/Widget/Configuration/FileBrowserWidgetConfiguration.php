<?php

namespace Gravity\FileBundle\Field\File\Widget\Configuration;

use GravityCMS\Component\Configuration\AbstractConfiguration;
use GravityCMS\Component\Field\Widget\WidgetSettingsInterface;

/**
 * Class FileBrowserWidgetConfiguration
 *
 * @package Gravity\TagBundle\Field\File\Widget\Configuration
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class FileBrowserWidgetConfiguration extends AbstractConfiguration implements WidgetSettingsInterface
{
    /**
     * Get the type of the config
     *
     * @return string
     */
    public function getType()
    {
        return 'field.file.widget.file_browser';
    }

    /**
     * The service or object of the form
     *
     * @return string|object
     */
    public function getForm()
    {
        return new FileBrowserWidgetConfigurationForm();
    }
}

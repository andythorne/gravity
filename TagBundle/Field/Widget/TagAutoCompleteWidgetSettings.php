<?php

namespace Gravity\TagBundle\Field\Widget;

use Gravity\TagBundle\Field\Widget\Form\TagAutoCompleteWidgetSettingsForm;
use GravityCMS\Component\Configuration\AbstractConfiguration;
use GravityCMS\Component\Field\Widget\WidgetSettingsInterface;

/**
 * Class TagAutoCompleteWidgetSettings
 *
 * @package Gravity\TagBundle\Field\Widget
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class TagAutoCompleteWidgetSettings extends AbstractConfiguration implements WidgetSettingsInterface
{
    /**
     * Get the type of the config
     *
     * @return string
     */
    public function getType()
    {
        return 'field.type.text.widget.editor.settings';
    }

    /**
     * The service or object of the form
     *
     * @return string|object
     */
    public function getForm()
    {
        return new TagAutoCompleteWidgetSettingsForm();
    }
}

<?php

namespace Gravity\NodeBundle\Field\Text\Configuration;

use GravityCMS\Component\Field\Configuration\FieldSettingsConfiguration;

/**
 * Class TextFieldConfiguration
 *
 * @package Gravity\NodeBundle\Field\Text\Configuration
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class TextFieldConfiguration extends FieldSettingsConfiguration
{

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'field.text';
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return new TextFieldConfigurationForm();
    }
} 

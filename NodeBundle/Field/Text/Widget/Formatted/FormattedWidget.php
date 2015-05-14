<?php

namespace Gravity\NodeBundle\Field\Text\Widget\Formatted;

use Gravity\NodeBundle\Entity\FieldText;
use Gravity\NodeBundle\Field\Text\Widget\Formatted\Asset\FormattedWidgetLibrary;
use Gravity\NodeBundle\Field\Text\Widget\Formatted\Configuration\FormattedWidgetConfiguration;
use GravityCMS\Component\Field\FieldDefinitionInterface;
use GravityCMS\Component\Field\Widget\AbstractWidgetDefinition;

/**
 * Class FormattedWidget
 *
 * @package Gravity\NodeBundle\Field\Text\Widget
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class FormattedWidget extends AbstractWidgetDefinition
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'text.formatted';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Formatted Text Editor';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'WYSIWYG editor for formatted text';
    }

    public function getForm()
    {
        return new FormattedWidgetForm();
    }

    public function getEntityClass()
    {
        return 'Gravity\NodeBundle\Entity\FieldText';
    }

    public function getAssetLibraries()
    {
        return [
            new FormattedWidgetLibrary(),
        ];
    }

    /**
     * Checks if this widget supports the given field
     *
     * @param FieldDefinitionInterface $field
     *
     * @return string
     */
    public function supportsField(FieldDefinitionInterface $field)
    {
        return ($field->getName() === 'text');
    }
}

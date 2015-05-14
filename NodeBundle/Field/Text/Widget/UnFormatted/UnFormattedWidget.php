<?php

namespace Gravity\NodeBundle\Field\Text\Widget\UnFormatted;

use Gravity\NodeBundle\Entity\FieldText;
use Gravity\NodeBundle\Field\Text\Widget\UnFormatted\Asset\UnFormattedWidgetLibrary;
use GravityCMS\Component\Field\FieldDefinitionInterface;
use GravityCMS\Component\Field\Widget\AbstractWidgetDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UnFormattedWidget
 *
 * @package Gravity\NodeBundle\Field\Text\Widget
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class UnFormattedWidget extends AbstractWidgetDefinition
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'text.unformatted';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'UnFormatted Text Editor';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Unformatted text box';
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return new UnFormattedWidgetForm();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return 'Gravity\NodeBundle\Entity\FieldText';
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetLibraries()
    {
        return [
            new UnFormattedWidgetLibrary(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsField(FieldDefinitionInterface $field)
    {
        return ($field->getName() === 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefaults(
            [
                'multiline' => false
            ]
        );
        $optionsResolver->setAllowedTypes(
            [
                'multiline' => 'bool'
            ]
        );
    }

    /**
     * @param FieldText               $entity
     * @param WidgetSettingsInterface $configuration
     */
    public function setDefaults($entity, WidgetSettingsInterface $configuration)
    {
        $entity->setBody($configuration->getDefault());
    }
}

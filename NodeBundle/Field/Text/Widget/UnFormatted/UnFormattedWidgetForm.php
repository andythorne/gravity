<?php

namespace Gravity\NodeBundle\Field\Text\Widget\UnFormatted;

use Gravity\NodeBundle\Field\Text\Widget\UnFormatted\Configuration\UnFormattedWidgetConfiguration;
use GravityCMS\Component\Field\FieldReference;
use GravityCMS\CoreBundle\Entity\Field;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UnFormattedWidgetForm
 *
 * @package Gravity\NodeBundle\Field\Text\Widget\Form
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class UnFormattedWidgetForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FieldReference $field */
        $field = $options['field'];
        $limit = $field->getSettings()['limit'];
        /** @var UnFormattedWidgetConfiguration $widgetConfig */
        $widgetConfig = $field->getWidget()->getSettings();

        if($widgetConfig['multiline']){
            $builder
                ->add('body', 'textarea', [
                    'label' => $limit == 1 ? $field->getName() : false,
                    'attr'  => [
                        'class'      => 'form-control',
                        'data-limit' => $limit,
                    ],
                ]);
        } else {
            $builder
                ->add('body', 'text', [
                    'label' => $limit == 1 ? $field->getName() : false,
                    'attr'  => [
                        'class'      => 'form-control',
                        'data-limit' => $limit,
                    ],
                ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Gravity\NodeBundle\Entity\FieldText',
            ]
        );
    }

    public function getParent()
    {
        return 'field_widget';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gravity_field_text_widget';
    }
}

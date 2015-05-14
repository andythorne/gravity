<?php

namespace Gravity\NodeBundle\Field\Text\Widget\Formatted;

use GravityCMS\Component\Field\FieldReference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormattedWidgetForm
 *
 * @package Gravity\NodeBundle\Field\Text\Widget\Form
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class FormattedWidgetForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FieldReference $field */
        $field = $options['field'];
        $limit = $field->getSettings()['limit'];
        $builder
            ->add(
                'body',
                'textarea',
                [
                    'label' => $limit == 1 ? $field->getName() : false,
                    'attr'  => [
                        'class'      => 'form-control wysiwyg-editor',
                        'data-limit' => $limit,
                    ],
                ]
            );
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

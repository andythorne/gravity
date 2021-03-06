<?php


namespace Gravity\FileBundle\Field\Image\Widget\ImageBrowser\Configuration;

use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ImageBrowserWidgetConfigurationForm
 *
 * @package Gravity\FileBundle\Field\Image\Widget\ImageBrowser\Configuration
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class ImageBrowserWidgetConfigurationForm extends AbstractType
{
    /**
     * @var FilterConfiguration
     */
    protected $filterConfiguration;

    function __construct(FilterConfiguration $filterConfiguration)
    {
        $this->filterConfiguration = $filterConfiguration;

        foreach ($filterConfiguration->all() as $name => $filter) {
            $this->imageStyleOptions[$name] = ucfirst($name);
        }
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'imageStyle',
                'choice',
                [
                    'choices' => $this->imageStyleOptions
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
                'data_type' => 'Gravity\FileBundle\Entity\FieldImage'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'field_image_widget_image_browser_configuration';
    }

}

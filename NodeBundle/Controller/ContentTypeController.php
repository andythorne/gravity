<?php

namespace Gravity\NodeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Gravity\NodeBundle\Entity\ContentType;
use Gravity\NodeBundle\Entity\ContentTypeField;
use GravityCMS\CoreBundle\Entity\Field;
use Gravity\NodeBundle\Form\ContentTypeFieldForm;
use Gravity\NodeBundle\Form\ContentTypeForm;
use Gravity\NodeBundle\Form\ContentTypeFormViewForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ContentTypeController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        /** @var EntityManager $em */
        $em           = $this->getDoctrine()->getManager();
        $contentTypes = $em->getRepository('GravityNodeBundle:ContentType')->findAll();

        return $this->render(
            'GravityNodeBundle:ContentType:index.html.twig',
            [
                'contentTypes' => $contentTypes,
            ]
        );
    }

    /**
     * Create a form for a new content type
     *
     * @return Response
     */
    public function newAction()
    {
        $newContentType = new ContentType();

        $form = $this->createForm(
            new ContentTypeForm(),
            $newContentType,
            [
                'attr' => [
                    'class' => 'api-save'
                ],
                'method' => 'POST',
                'action' => $this->generateUrl('gravity_api_post_type'),
            ]
        );

        return $this->render(
            'GravityNodeBundle:ContentType:new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param ContentType $contentType
     *
     * @return Response
     *
     * @ParamConverter("contentType", options={"mapping": {"type" = "name"}})
     */
    public function editAction(ContentType $contentType)
    {
        $form = $this->createForm(
            new ContentTypeForm(),
            $contentType,
            [
                'attr' => [
                    'class' => 'api-save'
                ],
                'method' => 'PUT',
                'action' => $this->generateUrl('gravity_api_put_type',
                    ['id' => $contentType->getId()]),
            ]
        );

        return $this->render(
            'GravityNodeBundle:ContentType:edit-tab-content-type.html.twig',
            [
                'contentType' => $contentType,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param ContentType $contentType
     *
     * @return Response
     *
     * @ParamConverter("contentType", options={"mapping": {"type" = "name"}})
     */
    public function editFieldsAction(ContentType $contentType)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $field = new Field();
        $field->setDelta(count($contentType->getFields()));
        $form = $this->createForm('gravity_field', $field, [
            'attr' => [
                'class' => 'api-save'
            ],
            'method' => 'POST',
            'action' => $this->generateUrl('gravity_api_post_type_field', [
                'contentType' => $contentType->getId(),
            ]),
        ]);

        $form->remove('name');

        $fieldTypes = $this->get('gravity_cms.field_manager')->getFields();

        return $this->render(
            'GravityNodeBundle:ContentType:edit-tab-fields.html.twig',
            [
                'contentType' => $contentType,
                'fieldTypes' => $fieldTypes,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param ContentType $contentType
     * @param Field $field
     *
     * @return Response
     *
     *
     * @ParamConverter("contentType", options={"mapping": {"type" = "name"}})
     * @ParamConverter("field", options={"mapping": {"field" = "name"}})
     */
    public function editFieldAction(ContentType $contentType, Field $field)
    {
        $form = $this->createForm(
            'gravity_field',
            $field,
            [
                'attr' => [
                    'class' => 'api-save'
                ],
                'method' => 'PUT',
                'action' => $this->generateUrl('gravity_api_put_type_field', [
                    'contentType' => $contentType->getId(),
                    'contentTypeField' => $field->getId(),
                ]),
            ]
        );

        $form->remove('field');

        return $this->render(
            'GravityNodeBundle:ContentType:edit-tab-field-edit.html.twig',
            [
                'contentType' => $contentType,
                'field' => $field,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param ContentType      $contentType
     * @param Field $field
     *
     * @return Response
     *
     * @ParamConverter("contentType", options={"mapping": {"type" = "name"}})
     * @ParamConverter("field", options={"mapping": {"field" = "name"}})
     */
    public function editFieldSettingsAction(ContentType $contentType, Field $field)
    {
        $config = $field->getConfig();

        $form = $this->createForm($config->getForm(), $config, [
            'attr' => [
                'class' => 'api-save'
            ],
            'method' => 'PUT',
            'action' => $this->generateUrl('gravity_api_put_type_field_settings', [
                'contentType' => $contentType->getId(),
                'field' => $field->getId(),
            ]),
        ]);

        return $this->render(
            'GravityNodeBundle:ContentType:edit-tab-field-settings.html.twig',
            [
                'contentType' => $contentType,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param ContentType      $contentType
     *
     * @return Response
     *
     * @ParamConverter("contentType", options={"mapping": {"type" = "name"}})
     */
    public function editFormViewAction(ContentType $contentType)
    {
        $form = $this->createForm(new ContentTypeFormViewForm(), $contentType, [
            'attr' => [
                'class' => 'api-save'
            ],
            'method' => 'PUT',
            'action' => $this->generateUrl('gravity_api_put_type_form_view', [
                'id' => $contentType->getId(),
            ]),
        ]);

        return $this->render(
            'GravityNodeBundle:ContentType:edit-tab-form-view.html.twig',
            [
                'contentType' => $contentType,
                'form' => $form->createView(),
            ]
        );
    }

} 

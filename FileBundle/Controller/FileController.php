<?php

namespace Gravity\FileBundle\Controller;

use FOS\RestBundle\View\View;
use Gravity\FileBundle\Configuration\FileConfiguration;
use Gravity\FileBundle\Entity\File;
use Gravity\FileBundle\Document\File as FileDocument;
use Gravity\FileBundle\File\Exception\FileFileAlreadyExistsException;
use GravityCMS\Component\Configuration\Exception\ConfigurationNotFoundException;
use GravityCMS\CoreBundle\FosRest\View\View\JsonApiView;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class FileController
 *
 * @package Gravity\FileBundle\Controller
 */
class FileController extends Controller
{

    public function settingsAction()
    {
        $configManager = $this->get('gravity_cms.config_manager');

        try {
            $config = $configManager->get('file:settings');
        }
        catch(ConfigurationNotFoundException $e)
        {
            $config = new FileConfiguration();
            $configManager->create($config);
        }

        $form = $configManager->getForm($config, [
            'method' => 'PUT',
            'action' => $this->generateUrl('gravity_api_put_file_settings'),
            'attr'   => [
                'class' => 'api-save'
            ],
        ]);

        return $this->render('GravityFileBundle:Admin:settings.html.twig', [
            'form'   => $form->createView(),
            'config' => $config,
        ]);
    }

    /**
     * List all file items
     *
     * @return Response
     * @throws AccessDeniedException
     */
    public function listAction()
    {
        $form = $this->createForm('gravity_file', new File(), [
            'method' => 'POST',
            'action' => $this->generateUrl('gravity_api_post_file'),
        ]);

        return $this->render('GravityFileBundle:Admin:list.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * Edit a file entity
     *
     * @param $id
     *
     * @return Response
     * @throws AccessDeniedException
     */
    public function editAction($id)
    {
        $user = $this->getUser();
        if (!($user instanceof UserInterface) || !($user->hasGroup('admin'))) {
            throw new AccessDeniedException();
        }

        $fileManager = $this->get('linestorm.cms.file_manager');
        $provider    = $fileManager->getDefaultProviderInstance();

        $file = $fileManager->find($id);

        $form = $this->createForm($provider->getForm(), $file, [
            'action' => $this->generateUrl('gravity_api_put_file', ['id' => $file->getId()]),
            'method' => 'PUT',
        ]);

        return $this->render('LineStormFileBundle:Admin:edit.html.twig', [
            'image' => $file,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * Create a file entity
     *
     * @return Response
     * @throws AccessDeniedException
     */
    public function newAction()
    {
        $user = $this->getUser();
        if (!($user instanceof UserInterface) || !($user->hasGroup('admin'))) {
            throw new AccessDeniedException();
        }

        $fileManager = $this->get('linestorm.cms.file_manager');
        $provider    = $fileManager->getDefaultProviderInstance();
        $class       = $provider->getEntityClass();

        $form = $this->createForm('linestorm_cms_form_file_multiple', null, [
            'action' => $this->generateUrl('gravity_api_post_file_batch'),
            'method' => 'POST',
        ]);

        return $this->render('LineStormFileBundle:Admin:new.html.twig', [
            'image' => null,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * Upload a file entity
     *
     * @return Response
     * @throws AccessDeniedException
     */
    public function uploadAction(Request $request, Field $field)
    {
        // TODO: permissions!
//        $user = $this->getUser();
//        if (!($user instanceof UserInterface) || !($user->hasGroup('admin'))) {
//            throw new AccessDeniedException();
//        }

        $fileManager = $this->get('gravity.file_manager');
        $code = 201;
        try {
            $files   = $request->files->all();
            $file = array_shift($files);

            $file = $fileManager->upload($file);
        }
        catch (FileFileAlreadyExistsException $e) {
            $file = $e->getEntity();
            $code = 200;
        }
        catch (\Exception $e) {
            $view = View::create([
                'error' => $e->getMessage(),
            ], 400);
            $view->setFormat('json');

            return $this->get('fos_rest.view_handler')->handle($view);
        };

        $view = JsonApiView::create($file, $code, [
            'GRAVITY-API-GET' => $this->generateUrl('gravity_api_post_file'),
        ]);
        $view->setFormat('json');

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}

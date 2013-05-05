<?php
namespace ASK\TinyMceTinyImagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BrowserController extends Controller
{
    /**
     * @Extra\Route("/tiny-mce-tiny-images/view/window", name="ask_tinymcetinyimages_browser_window")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function windowAction()
    {
        return $this->render('ASKTinyMceTinyImagesBundle:View:window.html.twig', array(
            'form' => $this->createUploadForm()->createView(),
        ));
    }

    /**
     * @Extra\Route("/tiny-mce-tiny-images/js/editor_plugin.js")
     * @Extra\Route("/tiny-mce-tiny-images/js/editor_plugin_src.js")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editorPluginAction()
    {
        $response = $this->render('ASKTinyMceTinyImagesBundle:View:editor_plugin.js.twig');
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;
    }

    /**
     * @Extra\Route("/tiny-mce-tiny-images/list", name="ask_tinymcetinyimages_browser_list")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listAction()
    {
        $images = $this->getRepositoryImages()->findAll();

        $result = array();
        foreach ($images as $image) {
            $result[] = $this->normalizeImage($image);
        }

        return new JsonResponse(array(
            'success' => true,
            'images'  => $result,
        ));
    }

    /**
     * @Extra\Route("/tiny-mce-tiny-images/remove/{id}", name="ask_tinymcetinyimages_browser_remove")
     *
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeAction($id)
    {
        $this->throwNotFoundUnless($image = $this->getRepositoryImages()->find($id));

        $this->getDoctrine()->getManager()->remove($image);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(array(
            'success' => true,
        ));
    }

    /**
     * @Extra\Route("/tiny-mce-tiny-images/upload", name="ask_tinymcetinyimages_browser_upload")
     *
     * @param Request $request
     */
    public function uploadAction(Request $request)
    {
        $form = $this->createUploadForm();
        $form->bind($request);

        if ($form->isValid()) {
            $image = $form->getData();

            $this->getDoctrine()->getManager()->persist($image);
            $this->getDoctrine()->getManager()->flush();

            $content = array(
                'success' => true,
                'image'   => $this->normalizeImage($image),
            );

            // http://www.malsup.com/jquery/form/#file-upload
            return new Response('<textarea>' . json_encode($content) . '</textarea>', 200, array(
                'Content-Type' => 'text/html',
            ));
        }

        return new Response('<textarea>' . json_encode(array('success' => false)) . '</textarea>', 200, array(
            'Content-Type' => 'text/html',
        ));
    }

    /**
     * @param $image
     *
     * @return array
     */
    protected function normalizeImage($image)
    {
        $uploaderHelper = $this->getUploaderHelper();
        $cachePathResolver = $this->getCachePathResolver();

        $image_field = $this->container->getParameter('ask_tiny_mce_tiny_images.image_field');
        $thumbnail_filter = $this->container->getParameter('ask_tiny_mce_tiny_images.thumbnail_filter');

        $sourceUrl = $uploaderHelper->asset($image, $image_field);

        return array(
            'id'        => $image->getId(),
            'source'    => $this->getRequest()->getUriForPath($sourceUrl),
            'thumbnail' => $cachePathResolver->getBrowserPath($sourceUrl, $thumbnail_filter, true),
        );
    }

    /**
     * @param bool $condition
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function throwNotFoundUnless($condition)
    {
        if (false == $condition) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function createUploadForm()
    {
        $builder = $this->get('form.factory')->createNamedBuilder('', 'form', null, array(
            'data_class' => $this->container->getParameter('ask_tiny_mce_tiny_images.image_class')
        ));
        $builder->add($this->container->getParameter('ask_tiny_mce_tiny_images.image_field'), 'file');

        return $builder->getForm();
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepositoryImages()
    {
        return $this->getDoctrine()->getRepository(
            $this->container->getParameter('ask_tiny_mce_tiny_images.image_class'));
    }

    /**
     * @return \Vich\UploaderBundle\Templating\Helper\UploaderHelper
     */
    protected function getUploaderHelper()
    {
        return $this->container->get('vich_uploader.templating.helper.uploader_helper');
    }

    /**
     * @return \Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver
     */
    protected function getCachePathResolver()
    {
        return $this->container->get('imagine.cache.path.resolver');
    }
}
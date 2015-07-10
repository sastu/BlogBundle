<?php

namespace Core\Bundle\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Core\Bundle\BlogBundle\Entity\Post;
use Core\Bundle\BlogBundle\Form\PostType;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Core\Bundle\CommonBundle\Exception\ExceptionBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

/**
 * Post controller.
 *
 * @Route("/admin/post")
 */
class PostController extends Controller
{

    
    /**
     * Lists all Post entities.
     *
     * @Route("/")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $entities = $manager->getRepository('BlogBundle:Post')->findBy(array(), array('createdAt' => 'DESC'));

        return array('entities' => $entities);
    }
    
    
    
    /**
     * Returns a list of Classes entities in JSON format.
     *
     * @return JsonResponse
     *
     * @Route("/postlist.{_format}", requirements={ "_format" = "json" }, defaults={ "_format" = "json" })
     * @Method("GET")
     */
    public function listJsonAction()
    {
        $em = $this->getDoctrine()->getManager();

        $jsonList = $this->get('json_list');
        $jsonList->setRepository($em->getRepository('BlogBundle:Post'));

        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $projectIds = $this->get('admin_manager')->getProjectIds(); 
            $jsonList->setProjectIds($projectIds);
        }
        
        $response = $jsonList->get();

        return new JsonResponse($response);
    }
    
    /**
     * Creates a new Post entity.
     *
     * @Route("/")
     * @Method("POST")
     * @Template("BlogBundle:Post:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = new Post();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            
            $user = $this->container->get('security.context')->getToken()->getUser();
            $entity->setActor($user);
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'post.created');
            
            //add notification
            if(!is_null($entity->getProject()))
            $this->get('notification_manager')->addNotification($entity->getActor(), $entity->getProject(), 'post');
            
            return $this->redirect($this->generateUrl('core_blog_post_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Post entity.
     *
     * @param Post $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Post $entity)
    {
        $formConfig = array();
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $formConfig['projectIds'] = $this->get('admin_manager')->getProjectIds(); 
        }
        
        $type = new PostType();
        $form = $this->createForm(new PostType($formConfig), $entity, array(
            'action' => $this->generateUrl('core_blog_post_create'),
            'method' => 'POST',
            'attr' => array('id' => $type->getName())
        ));

//        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Post entity.
     *
     * @Route("/new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Post();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Post entity.
     *
     * @Route("/{id}")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BlogBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'edit' => true,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Post entity.
    *
    * @param Post $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Post $entity)
    {
        $formConfig = array();
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $formConfig['projectIds'] = $this->get('admin_manager')->getProjectIds(); 
        }
        
        if($entity->getPublished()->format('dmY') == '3011-0001'){
            $entity->setPublished(null);
        }
        $type = new PostType();
        $form = $this->createForm(new PostType($formConfig), $entity, array(
            'action' => $this->generateUrl('core_blog_post_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('id' => $type->getName())
        ));

//        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array('class' => 'pull-right btn btn-success')));

        return $form;
    }
    /**
     * Edits an existing Post entity.
     *
     * @Route("/{id}")
     * @Method("PUT")
     * @Template("BlogBundle:Post:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
         
        $entity = $em->getRepository('BlogBundle:Post')->find($id);

        if (!$entity) {            
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
           

            return $this->redirect($this->generateUrl('core_blog_post_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Post entity.
     *
     * @Route("/{id}")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BlogBundle:Post')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Post entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('core_blog_post_index'));
    }

    /**
     * Creates a form to delete a Post entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        $translator = $this->get('translator');
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('core_blog_post_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => $translator->trans('delete'), 'attr' => array('class' => 'btn red')))
            ->getForm()
        ;
    }
    
    
    /**
     * @Route("/post-remove" , name="admin_post_remove")
     * @Method("POST")
     */
    public function removePostAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $data = $this->get('request')->request->all();

            $manager = $this->getDoctrine()->getManager();

            if (isset($data['id'])) {
                 $entity = $manager->getRepository('BlogBundle:Post')->findOneBy(array(
                        'id' => $data['id']
                        ));
                $manager->remove($entity);
                $manager->flush();
            } else {
                throw new ExceptionBase('Index "id" not defined.');
            }

            $returnResponse = new stdClass();
            $returnResponse->status = 'success';

            return new JsonResponse($returnResponse);
        }
    }
   
    
    /**
     * @Route("/post-remove-tag" , name="admin_post_remove_tag")
     * @Method("POST")
     */
    public function removeTagPostAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $data = $this->get('request')->request->all();

            $manager = $this->getDoctrine()->getManager();

            if (isset($data['post_id']) && isset($data['tag_id'])) {

                $post = $manager->getRepository('CommonBundle:Node')->findOneBy(array(
                        'id' => $data['post_id']
                        ));
                $tag = $manager->getRepository('MediaBundle:Tag')->findOneBy(array(
                        'id' => $data['tag_id']
                        ));
                $post->removeTag($tag);
                $manager->remove($tag);
                $manager->flush();
                
            } else {
                throw new ExceptionBase('Index "post_id" or "tag_id" not defined.');
            }

            $returnResponse = new stdClass();
            $returnResponse->status = 'success';

            return new JsonResponse($returnResponse);
        }
    }

    /**
     * Manages a product image
     *
     * @return array
     *
     * @Route("/{type}/{id}-{slug}/{route}/{entity}/{image_entity}/image")
     */
    public function manageImage()
    {
        $this->get('upload_handler');

        return new Response();
    }
    
}

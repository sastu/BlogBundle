<?php

namespace Core\Bundle\BlogBundle\Controller;

use Doctrine\ORM\Query;
use Core\Bundle\BlogBundle\Entity\Comment;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Core\Bundle\BlogBundle\Form\CommentType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Comment controller.
 *
 * @Route("/admin/post/comment")
 */
class CommentController extends Controller
{
    /**
     * Lists all Comment entities.
     *
     * @return array
     *
     * @Route("/")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * Returns a list of Comment entities in JSON format.
     *
     * @return JsonResponse
     *
     * @Route("/list.{_format}", requirements={ "_format" = "json" }, defaults={ "_format" = "json" })
     * @Method("GET")
     */
    public function listJsonAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Kitchenit\AdminBundle\Services\DataTables\JsonList $jsonList */
        $jsonList = $this->get('json_list');
        $jsonList->setRepository($em->getRepository('BlogBundle:Comment'));

        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $projectIds = $this->get('admin_manager')->getProjectIds(); 
            $jsonList->setProjectIds($projectIds);
        }
        
        $response = $jsonList->get();

        return new JsonResponse($response);
    }

    /**
     * Creates a new Comment entity.
     *
     * @param Request $request The request
     *
     * @return array|RedirectResponse
     *
     * @Route("/")
     * @Method("POST")
     * @Template("AdminBundle:Comment:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Comment();
        $form = $this->createCreateForm($entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'comment.created');

            //add notification
            if($entity->isActive()){
                $user = $this->container->get('security.context')->getToken()->getUser();
                $this->get('notification_manager')->addNotification($user, $entity->getPost()->getProject(), 'comment');
            }
           
            return $this->redirect($this->generateUrl('core_blog_comment_show', array('id' => $entity->getId())));
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
    private function createCreateForm(Comment $entity)
    {
        $formConfig = array();
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user->isGranted('ROLE_MANAGER')) {
            $formConfig['projectIds'] = $this->get('admin_manager')->getProjectIds(); 
        }
        
        $type = new CommentType($formConfig);
        $form = $this->createForm($type, $entity);

        return $form;
    }
    
    /**
     * Displays a form to create a new Comment entity.
     *
     * @return array
     *
     * @Route("/new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Comment();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Comment entity.
     *
     * @param int $id The entity id
     *
     * @throws NotFoundHttpException
     * @return array
     *
     * @Route("/{id}", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Comment $entity */
        $entity = $em->getRepository('BlogBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Comment entity.
     *
     * @param int $id The entity id
     *
     * @throws NotFoundHttpException
     * @return array
     *
     * @Route("/{id}/edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Comment $entity */
        $entity = $em->getRepository('BlogBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $editForm = $this->createForm(new CommentType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Comment entity.
     *
     * @param Request $request The request
     * @param int     $id      The entity id
     *
     * @throws NotFoundHttpException
     * @return array|RedirectResponse
     *
     * @Route("/{id}", requirements={"id" = "\d+"})
     * @Method("PUT")
     * @Template("AdminBundle:Comment:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Comment $entity */
        $entity = $em->getRepository('BlogBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CommentType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'family.edited');

            return $this->redirect($this->generateUrl('core_blog_comment_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Comment entity.
     *
     * @param Request $request The request
     * @param int     $id      The entity id
     *
     * @throws NotFoundHttpException
     * @return RedirectResponse
     *
     * @Route("/{id}", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var Comment $entity */
            $entity = $em->getRepository('BlogBundle:Comment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Comment entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('info', 'family.deleted');
        }

        return $this->redirect($this->generateUrl('core_blog_comment_index'));
    }


    /**
     * Creates a form to delete a Comment entity by id.
     *
     * @param int $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }
}

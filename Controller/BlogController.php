<?php

namespace Core\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Core\Bundle\BlogBundle\Model\CommentFront;
use Core\Bundle\BlogBundle\Form\CommentFrontType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Core\Bundle\BlogBundle\Entity\Comment;
use Core\Bundle\CoreBundle\Entity\Actor;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
* @Route("/blog")
* @Template()
*/
class BlogController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $entities = $em->getRepository('BlogBundle:Post')->findBy(array('project' => null));
        $categories = $em->getRepository('BlogBundle:Category')->findBy(array());
        
        return array(
            'posts' => $entities,
            'categories' => $categories
        );
        
    }
    
    /**
     * @Route("/search")
     * @Route("/search/{search}")
     * @Template()
     */
    public function searchAction(Request $request, $search=null)
    {
        if($request->getMethod() == 'POST'){
            $search = $request->request->get('search');
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $posts = $this->get('blogManager')->searchPosts($search);
        $categories = $em->getRepository('BlogBundle:Category')->findBy(array());

        return array(
                'search' => $search,
                'posts' => $posts,
                'tags' => array(),
                'categories' => $categories
                );
    }
    
    /**
     * @Route("/{slug}")
     * @Method("GET")
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('BlogBundle:Post')->findOneBy(array('slug' => $slug));
        $categories = $em->getRepository('BlogBundle:Category')->findBy(array());
        
        
        $form = $this->createCommentForm(new CommentFront(), $entity);
        
        
        return array(
            'post' => $entity,
            'categories' => $categories,
            'form' => $form->createView()        
        );
        
    }
    
    /**
     * @Route("/tag/{tag}")
     * @Template()
     */
    public function tagAction($tag)
    {
        $em = $this->getDoctrine()->getManager();
        
        $categories = $em->getRepository('BlogBundle:Category')->findBy(array());
        
        $query = ' SELECT p'
                . ' FROM BlogBundle:Post p'
                . ' JOIN p.categories c '
                . " WHERE c.name LIKE '%".$tag."%' "
                . " OR c.slug LIKE '%".$tag."%' "
                ;
        $q = $em->createQuery($query);
        $posts = $q->getResult();
        
        return array(
            'tag' => $tag,
            'categories' => $categories,
            'posts' => $posts,
        );
    }
    
    /**
     * @Route("/comment/")
     * @Method("POST")
     * @Template()
     */
    public function commentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = new Comment();

        try {
            $comment = $this->resolve($comment, $request);
        } catch (\Exception $exception) {
            // Write flash message
            print_r($exception->getMessage());die();
        }

        //save
        $em->persist($comment);
        $em->flush();
       
        return $this->redirect($this->getRefererPath($request));
    }
    /**
     * Creates a form to create a Post entity.
     *
     * @param Post $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCommentForm(CommentFront $model, $entity)
    {
        $type = new CommentFrontType();
        $form = $this->createForm($type, $model, array(
            'action' => $this->generateUrl('core_blog_blog_comment', array('post' => $entity->getId())),
            'method' => 'POST',
            'attr' => array('id' => $type->getName(),'class' => 'comment-form')
        ));

        $form->add('submit', 'submit', array('label' => 'Enviar'));

        return $form;
    }
    
    
    
    /**
     * @param Comment $comment
     * @param Request $request
     *
     * @throws ItemResolvingException
     * @return CartItemInterface|void
     */
    public function resolve(Comment $comment, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $postId = $request->query->get('post');
        $itemForm = $request->request->get('core_blogbundle_commentfronttype');
        
      
        
        if( $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') ){
            $comment->setActor($this->container->get('security.context')->getToekn()->getUser());
        }else{
            //register user
            $actor = $this->createActor($itemForm);
            $comment->setActor($actor);
        }
        
        $postRepository = $em->getRepository('BlogBundle:Post');
        if (!$postId || !$post = $postRepository->find($postId)) {
            // no product id given, or product not found
            throw new \Exception('Requested post was not found');
        }

        // assign the product and quantity to the item
        $comment->setPost($post);
        $comment->setComment($itemForm['comment']);
 
        return $comment;
    }
    
    public function createActor($itemForm) 
    {
        $em = $this->getDoctrine()->getManager();
        $uniqid = uniqid();
        $actor = new Actor();
        $actor->setUsername($uniqid);
        $actor->setRawPassword($uniqid);
        $actor->setName($itemForm['name']);
        $actor->setEmail($itemForm['email']);
        
        //Encode pass
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($actor);
        $password = $encoder->encodePassword($actor->getPassword(), $actor->getSalt());
        $actor->setPassword($password);

        //Add ROLE
        $role = $em->getRepository('CoreBundle:Role')->findOneBy(array('role' => 'ROLE_USER'));
        $actor->addRole($role);

        $em->persist($actor);
        $em->flush();
        
        return $actor;
    }
    
    public function getRefererPath(Request $request=null)
    {
        $referer = $request->headers->get('referer');

        $baseUrl = $request->getSchemeAndHttpHost();

        $lastPath = substr($referer, strpos($referer, $baseUrl) + strlen($baseUrl));

        return $lastPath;
    }
 

}

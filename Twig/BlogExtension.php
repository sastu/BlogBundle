<?php

namespace Core\Bundle\BlogBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BlogExtension
 */
class BlogExtension extends \Twig_Extension
{

    private $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'blogHistory' => new \Twig_Function_Method($this, 'blogHistory', array(
                            'is_safe' => array('html')
                        )),
            'countArrayValues' => new \Twig_Function_Method($this, 'countArrayValues'),
            'shareUrl' => new \Twig_Function_Method($this, 'shareUrl', array(
                            'is_safe' => array('html')
                        )),
            'countPost' => new \Twig_Function_Method($this, 'countPost'),
        );
    }
    
  
    public function blogHistory()
    {
        $mediaManager = $this->container->get('blogManager');
        $history = $mediaManager->blogHistory();

        $twig = $this->container->get('twig');

        $content = $twig->render('BlogBundle:Blog:blog.history.html.twig', array(
            'history' => $history
            ));

        return $content;
    }
    
     public function countArrayValues($array)
    {
        $count = 0;
        
        foreach($array as $item) {  // go thought the first level
            foreach($item as $value) {  // go through the second level
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
    * Returns the part of a feedID
    *
    * @param string $feedID  ID of the feed to load
    */
    public function shareUrl($url, $big=false)
    {
        if (!is_numeric(strpos($url, 'http'))) {
            $core = $this->container->getParameter('core');
            $url = $core['server_base_url'] . $url;
        }
        $text = $this->container->get('translator')->trans('share.message');
        $tweetUrl =  'https://twitter.com/share?url='.$url.'&counturl='.$url.'&text='.$text;
        $faceUrl = 'http://www.facebook.com/sharer/sharer.php?u='.$url.'&text='.$text;
        $googleUrl = 'https://plus.google.com/share?url='.$url;
        $linkedUrl = 'https://www.linkedin.com/shareArticle?summary=&ro=false&title='.$text.'&mini=true&url='.$url.'&source=';

        $twig = $this->container->get('twig');
        $content = $twig->render('BlogBundle:Blog:share.html.twig', array(
            "tweetUrl" => $tweetUrl,
            "faceUrl" => $faceUrl,
            "googleUrl" => $googleUrl,
            "linkedUrl" => $linkedUrl,
            "id" => uniqid(),
            "big" => $big
        ));

        return $content;
    }
    
    /**
    * Returns integer counter
    *
    * @param object $actor  
    */
    public function countPost($actor)
    {
        $em = $this->container->get('doctrine')->getManager();
        $entities = $em->getRepository('BlogBundle:Post')->findBy(array('actor' => $actor));
        
        return count($entities);
    }
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'blog_extension';
    }
}
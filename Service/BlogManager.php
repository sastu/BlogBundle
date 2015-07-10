<?php
namespace Core\Bundle\BlogBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Core\Bundle\BlogBundle\Lib\ShareCounter;
use stdClass;

class BlogManager 
{
    protected $container = null;

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
     protected function getManager()
    {
        return $this->container->get('doctrine')->getManager();
    }
    
    public function blogHistory()
    {
        $manager = $this->getManager();
        if($this->container->getParameter('database_driver') == 'pdo_mysql'){
            $sql= " SELECT YEAR(post.created_at) as year, MONTH(post.created_at) as month, post.title, post.slug "
                    . " FROM post as post  "
                    . " group by post.created_at, post.title,  post.slug "
                    . " order by year DESC, month ASC, title ASC "
                    ;
        }elseif($this->container->getParameter('database_driver') == 'pdo_pgsql'){
            $sql= " SELECT date_part('year', post.created_at)as year, date_part('month', post.created_at) as month, post.title, post.slug "
                . " FROM post as post  "
                . " group by post.created_at, post.title,  post.slug "
                . " order by year DESC, month ASC, title ASC "
                ;
        }
    
        $stmt = $manager->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $returnValues = array();
        foreach ($result as $key => $value) {
            $year = $value['year'];
            if (isset($returnValues[$year][$this->getStringMonth($value['month'])])) {
                $returnValues[$year][$this->getStringMonth($value['month'])][] = $value;
            } else {
                $returnValues[$year][$this->getStringMonth($value['month'])] = array($value);
            }
        }

        return $returnValues;

    }
    
    public function getStringMonth($month)
    {
        return $this->container->get('translator')->trans(date("F", mktime(0, 0, 0, $month)));
    }
    
    public function getPostUrl($entity)
    {
        return $this->container->getParameter('server_base_url'). DIRECTORY_SEPARATOR . 'post' . DIRECTORY_SEPARATOR . $entity->getSlug();
    }
   
    public function searchPosts($search)
    {
        $em = $this->getManager();
        
        $query = ' SELECT p'
                . ' FROM BlogBundle:Post p'
                . ' JOIN p.categories c '
                . " WHERE p.title LIKE '%".$search."%'  "
                . " OR p.description LIKE '%".$search."%' "
                . " OR c.name LIKE '%".$search."%' "
                ;
        $q = $em->createQuery($query);
        $entities = $q->getResult();
        
        return $entities;
    }
    
    public function shareCounter($url=null)
    {

        if(is_null($url))  throw new \Exception('Url should be defined.');
        $shareClass = new ShareCounter($url);

        $returnValue= new stdClass();
        $returnValue->tweet = $shareClass->get_tweets();
        $returnValue->facebook = $shareClass->get_fb();
        $returnValue->linkedin = $shareClass->get_linkedin();
        $returnValue->google = $shareClass->get_plusones();
//        $returnValue->delicious = $shareClass->get_delicious();
//        $returnValue->stumble = $shareClass->get_stumble();
//        $returnValue->pinterest = $shareClass->get_pinterest();
        return $returnValue;
    }
}

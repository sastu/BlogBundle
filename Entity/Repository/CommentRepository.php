<?php

namespace Core\Bundle\BlogBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use Core\Bundle\BlogBundle\Entity\Category;


/**
 * Class CommentRepository
 */
class CommentRepository extends EntityRepository
{
    /**
     * Count the total of rows
     *
     * @return int
     */
    public function countTotal()
    {
        $qb = $this->getQueryBuilder()
            ->select('COUNT(c)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Find all rows filtered for DataTables
     *
     * @param string $search        The search string
     * @param int    $sortColumn    The column to sort by
     * @param string $sortDirection The direction to sort the column
     *
     * @return \Doctrine\ORM\Query
     */
    public function findAllForDataTables($search, $sortColumn, $sortDirection)
    {
        // select
        $qb = $this->getQueryBuilder()
            ->select('c.id, c.comment, c.isActive, a.name, a.surnames')
            ->leftJoin('c.actor', 'a')
            ;

        // search
        if (!empty($search)) {
            $qb->where('c.comment LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('c.id', $sortDirection);
                break;
            case 1:
                $qb->orderBy('c.comment', $sortDirection);
                break;
            case 2:
                $qb->orderBy('c.active', $sortDirection);
                break;
        }

        return $qb->getQuery();
    }

    /**
     * Find all rows filtered for DataTables
     *
     * @param string $search        The search string
     * @param int    $sortColumn    The column to sort by
     * @param string $sortDirection The direction to sort the column
     *
     * @return \Doctrine\ORM\Query
     */
    public function findAllForDataTablesByProjects($search, $sortColumn, $sortDirection, $projectIds)
    {
        // select
        $qb = $this->getQueryBuilder()
            ->select('c.id, c.comment, c.isActive, a.name as actorName, a.surnames as actorSurnames, pro.name projectName')
            ;

        // join
        $qb->leftJoin('c.actor', 'a')
            ->leftJoin('c.post', 'p')
            ->leftJoin('p.project', 'pro');

        // search
        if (!empty($search)) {
            $qb->where('c.comment LIKE :search OR actorName LIKE :search')
                ->andWhere('pro.id IN(:projectIds)')
                ->setParameter('search', '%'.$search.'%');
        }else{
            $qb->where('pro.id IN(:projectIds)');
        }
        
        $qb->setParameter('projectIds', array_values($projectIds));
        
        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('c.id', $sortDirection);
                break;
            case 1:
                $qb->orderBy('c.comment', $sortDirection);
                break;
            case 2:
                $qb->orderBy('c.active', $sortDirection);
                break;
        }
        
        if($sortColumn==''){
            $qb->orderBy('p.createdAt', 'DESC');
        }

        return $qb->getQuery();
    }
    
    /**
     * Find all rows with their related categories
     *
     * @return array
     */
    public function findAllWithCategories()
    {
        // select
        $qb = $this->getQueryBuilder()
            ->select('c');

        // sort by family and comment id
        $qb->orderBy('c.id', 'asc');

        return $qb->getQuery()
            ->getResult();
    }

    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $qb = $em->getRepository('BlogBundle:Comment')
            ->createQueryBuilder('c');

        return $qb;
    }
}
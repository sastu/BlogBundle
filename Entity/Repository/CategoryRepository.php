<?php

namespace Core\Bundle\BlogBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use Core\Bundle\BlogBundle\Entity\Category;


/**
 * Class CategoryRepository
 */
class CategoryRepository extends EntityRepository
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
            ->select('c.id, c.name');

        // search
        if (!empty($search)) {
            $qb->where('c.name LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('c.id', $sortDirection);
                break;
            case 1:
                $qb->orderBy('c.name', $sortDirection);
                break;
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

        // sort by family and category id
        $qb->orderBy('c.id', 'asc');

        return $qb->getQuery()
            ->getResult();
    }

    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $qb = $em->getRepository('BlogBundle:Category')
            ->createQueryBuilder('c');

        return $qb;
    }
}
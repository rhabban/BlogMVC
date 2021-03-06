<?php

namespace Blog\Repository;

use Doctrine\ORM\EntityRepository;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Zend\Paginator\Paginator;
use DateTime;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
    /**
     * @param array $criteria
     * @return Paginator
     */
    public function findActivePost(array $criteria = array())
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('p', 'u', 'c')
            ->from($this->getClassName(), 'p')
            ->innerJoin('p.user', 'u')
            ->innerJoin('p.category', 'c')
            ->where('p.created < :now')
            ->setParameter('now', new DateTime('now'))
            ->orderBy('p.created', 'DESC');

        if (isset($criteria['category'])) {
            $qb->andWhere('c.slug = :category')
                ->setParameter('category', $criteria['category']);
        }

        if (isset($criteria['author'])) {
            $qb->andWhere('u.id = :author')
                ->setParameter('author', $criteria['author']);
        }

        $doctrineAdapter = new PaginatorAdapter(new DoctrinePaginator($qb->getQuery(), false));

        return new Paginator($doctrineAdapter);
    }

    public function findLastPost($limit = 2)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('p')
            ->from($this->getClassName(), 'p')
            ->where('p.created < :now')
            ->setParameter('now', new DateTime('now'))
            ->orderBy('p.created', 'DESC');

        return $qb->getQuery()
                ->setMaxResults($limit)
                ->getResult();
    }
}

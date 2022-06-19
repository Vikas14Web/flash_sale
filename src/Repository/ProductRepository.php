<?php

namespace App\Repository;

use App\Entity\Product;
use App\Helper\Filter;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 */
class ProductRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $this->setRepositoryClass(Product::class);
        parent::__construct($registry);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Filter the Listing Reason.
     *
     * @throws QueryException
     */
    public function filterQueryBuilder(array $filters = null, array $orderBy = null, array $options = []): QueryBuilder
    {
        $filterQuery = $this->createQueryBuilder('a')
            ->select(['a']);

        if (null != $filters) {
            $criteria = Filter::apply($filters);
            $filterQuery->addCriteria($criteria);
        }

        if (null == $orderBy) {
            $filterQuery = $filterQuery->orderBy('a.createdAt', 'desc');
        } else {
            $filterQuery = $filterQuery->orderBy($orderBy[0], $orderBy[1]);
        }

        return $filterQuery;
    }
}

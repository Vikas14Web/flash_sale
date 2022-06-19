<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class EntityRepository extends ServiceEntityRepository
{
    private string $repositoryClass;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->getRepositoryClass());
    }

    public function setRepositoryClass(string $repositoryClass): void
    {
        $this->repositoryClass = $repositoryClass;
    }

    public function getRepositoryClass(): string
    {
        return $this->repositoryClass;
    }

    /**
     *  Paginator object with QueryBuilder.
     */
    public function findPaginatedResult($queryBuilder): Paginator
    {
        return new Paginator($queryBuilder);
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        $queryBuilder = $this->createQueryBuilder('repo');
        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere('repo'.".{$field} = :{$field}")->setParameter($field, $value);
        }
        if (is_array($orderBy)) {
            foreach ($orderBy as $field => $dir) {
                $queryBuilder->addOrderBy($field, $dir);
            }
        }
        $query = $queryBuilder->getQuery();
        $query->enableResultCache(3000,'repo_obj');

        return $query->getResult();
    }
}

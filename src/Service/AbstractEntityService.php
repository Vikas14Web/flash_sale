<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Psr\Cache\InvalidArgumentException;

/**
 * @property EntityRepository $repository
 */
abstract class AbstractEntityService
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    protected function getManager(): ObjectManager
    {
        return $this->managerRegistry->getManager();
    }

    public function find(int $id): object|null
    {
        return $this->repository->find($id);
    }

    public function getAll(): array
    {
        return $this->repository->findBy([], ['createdAt' => 'desc']);
    }

    public function save(object $entity): void
    {
        $entityManager = $this->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    /**
     * @throws \Exception
     */
    public function getPaginatedResults($request, array $defaultFilter = [], array $options = []): array
    {
        $filters = $request->query->get('filter') ? json_decode($request->query->get('filter'), true) : $defaultFilter;
        $currentPage = $request->query->getInt('page') ?: 1;
        $limit = $request->query->getInt('limit') ?: 10;
        $sort = $request->query->get('sort') ?: null;
        $queryBuilder = $this->repository->filterQueryBuilder($filters, $sort, $options);
        $paginator = $this->repository->findPaginatedResult($queryBuilder);
        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($currentPage - 1))
            ->setMaxResults($limit);

        return ['meta' => ['total' => $paginator->count(), 'page' => $currentPage, 'limit' => $limit],
            'items' => $paginator->getIterator()->getArrayCopy(), ];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function deleteResultCache(string $cacheId): void
    {
        /** @var EntityManagerInterface $manager */
        $manager = $this->managerRegistry->getManager();
        if ($manager->getConfiguration()->getResultCache()->hasItem($cacheId)) {
            $manager->getConfiguration()->getResultCache()->deleteItem($cacheId);
        }
    }
}

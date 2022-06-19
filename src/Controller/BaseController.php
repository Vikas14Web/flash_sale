<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class BaseController extends AbstractController
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getManager(): ObjectManager
    {
        return $this->managerRegistry->getManager();
    }

    public function success(object|array $data, array $serializeGroups = [], $status = Response::HTTP_OK, string $message = null, array $otherData = null): JsonResponse
    {
        $actualData = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];
        if (null != $otherData) {
            $actualData = array_merge($actualData, $otherData);
        }

        return $this->json($actualData, $status, [], [AbstractNormalizer::GROUPS => $serializeGroups]);
    }

    public function created(object|array $data, array $serializeGroups = [], string $message = null): JsonResponse
    {
        return $this->success(
            $data,
            $serializeGroups,
            Response::HTTP_CREATED,
            $message
        );
    }

    public function error(object|array $data, int $status = Response::HTTP_INTERNAL_SERVER_ERROR, string $message = 'Something went wrong'): JsonResponse
    {
        return $this->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $data,
        ], $status);
    }

    public function validationError(array $errors): JsonResponse
    {
        return $this->error(
            $errors,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Validation Failed'
        );
    }

    public function paginated(array $data, int $total, int $limit = 10, int $page = 1, array $serializeGroups = [], string $message = null): JsonResponse
    {
        return $this->json([
            'message' => $message,
            'status' => 'success',
            'data' => $data,
            'meta' => [
                'total' => $total,
                'limit' => $limit,
                'page' => $page,
            ],
        ], Response::HTTP_OK, [], [AbstractNormalizer::GROUPS => $serializeGroups]);
    }
}

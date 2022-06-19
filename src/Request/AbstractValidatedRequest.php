<?php

declare(strict_types=1);

namespace App\Request;

use ReflectionClass;
use Symfony\Component\Serializer\Annotation\Ignore;

abstract class AbstractValidatedRequest implements ValidatedRequestInterface
{
    #[Ignore]
    private ReflectionClass $reflectionClass;

    public function __construct()
    {
        $this->reflectionClass = new ReflectionClass($this);
    }

    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflectionClass;
    }

    public static function getDefaultIndexName(): string
    {
        return md5(static::class);
    }
}

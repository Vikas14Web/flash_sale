<?php

declare(strict_types=1);

namespace App\Request;

use App\Exception\RequestValidationException;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ReflectionClass;

class RequestValidator
{
    private ?Request $request;
    /**
     * @var array<string, mixed>|null
     */
    private ?array $requestBody = null;

    public function __construct(
        private ValidatorInterface $validator,
        private RequestStack $requestStack,
        private ServiceLocator $locator,
        private SerializerInterface $serializer
    ) {
    }

    /**
     * @throws \App\Exception\RequestValidationException
     */
    public function validate(AbstractValidatedRequest $requestToValidate): void
    {
        $this->init();

        $violations = $this->validator->validate(
            $this->getRequestBody($requestToValidate::class),
            $this->getConstraints($requestToValidate),
            ['Default', $this->request->getMethod()]
        );

        if ($violations->count() > 0) {
            throw new RequestValidationException($violations, 'Validation error.');
        }

        $this->_loadValidatedData($requestToValidate);
    }

    private function getConstraints(AbstractValidatedRequest $validatedRequest): Assert\Collection
    {
        $fields = [];
        $groups = $this->getGroups($validatedRequest);
        foreach ($validatedRequest->getReflectionClass()->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $constraints = [];
            if (\in_array($property->getName(), $groups, true)) {
                if (is_subclass_of($property->getType()->getName(), AbstractValidatedRequest::class)) {
                    if (($childRequest = $this->locator->get($property->getType()->getName())) instanceof AbstractValidatedRequest) {
                        $property->setValue($validatedRequest, $childRequest);

                        $fields[$property->getName()] = $this->getConstraints($childRequest);
                    }
                } else {
                    if (Type::BUILTIN_TYPE_ARRAY != $property->getType()) {
                        foreach ($property->getAttributes() as $attribute) {
                            if (!($instance = $attribute->newInstance()) instanceof Constraint) {
                                continue;
                            }
                            $constraints[] = $instance;
                        }

                        $fields[$property->getName()][] = $constraints;
                    } else {
                        $doc = $this->parseAnnotations($property->getDocComment());
                        $arrayPropertyType = new ReflectionClass($doc['param']);
                        if (($arrayPropertyInstance = $arrayPropertyType->newInstance()) instanceof AbstractValidatedRequest) {
                            /** @var \App\Request\AbstractValidatedRequest $arrayPropertyInstance */
                            $arrayConstraints = $this->getConstraints($arrayPropertyInstance);
                            $fields[$property->getName()] = new Assert\All(['constraints' => $arrayConstraints]);
                        }
                    }
                }
            }
        }

        return new Assert\Collection($fields);
    }

    private function getRequestBody(string $class): array
    {
        if (null !== $this->requestBody) {
            return $this->requestBody;
        }

        return $this->requestBody = $this->serializer->deserialize($this->request->getContent(), $class, 'json');
    }

    /**
     * @internal
     */
    public function _loadValidatedData(AbstractValidatedRequest $validatedRequest, ?string $children = null): void
    {
        $requestBody = $this->getRequestBody($validatedRequest::class);
        $requestBody = $children ? $requestBody[$children] : $requestBody;
        $groups = $this->getGroups($validatedRequest);
        foreach ($validatedRequest->getReflectionClass()->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (\in_array($property->getName(), $groups)) {
                if (!is_subclass_of($property->getType()->getName(), AbstractValidatedRequest::class)) {
                    $property->setValue($validatedRequest, $requestBody[$property->getName()]);
                } else {
                    /** @var \App\Request\AbstractValidatedRequest $childInstance */
                    $childInstance = $property->getValue($validatedRequest);
                    $this->_loadValidatedData($childInstance, $property->getName());
                }
            }
        }
    }

    /**
     *  Attributes are grouped based on Method.
     */
    private function getGroups(AbstractValidatedRequest $validatedRequest): array
    {
        $groups = [];
        foreach ($validatedRequest->getReflectionClass()->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            foreach ($property->getAttributes() as $attribute) {
                /** @var Groups $instance */
                if (!($instance = $attribute->newInstance()) instanceof Groups) {
                    continue;
                }
                if (\in_array($this->request->getMethod(), $instance->getGroups(), true)) {
                    $groups[] = $property->getName();
                }
            }
        }

        return $groups;
    }

    private function parseAnnotations($doc): array
    {
        preg_match_all('/@([a-z]+?)\s+(.*?)\n/i', $doc, $annotations);

        if (!isset($annotations[1]) || 0 == \count($annotations[1])) {
            return [];
        }

        return array_combine(array_map('trim', $annotations[1]), array_map('trim', $annotations[2]));
    }

    private function init(): void
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }
}

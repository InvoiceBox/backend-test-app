<?php

declare(strict_types=1);

namespace BackendTestApp\Presentation\Serializer;

use JMS\Serializer\SerializerInterface;
use JMS\Serializer\ArrayTransformerInterface;
use Doctrine\DBAL\Types\Types;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;

class JsonSerializer
{
    public const DEFAULT_GROUP = 'Default';

    public function __construct(private SerializerInterface $serializer, private ArrayTransformerInterface $transformer)
    {
    }

    public function deserialize(
        string $content,
        string $class,
        ?array $groups = null,
        ?object $existsObject = null
    ): mixed {
        return $this->serializer->deserialize(
            $content,
            $class,
            Types::JSON,
            DeserializationContext::create()
                ->setAttribute('target', $existsObject)
                ->setGroups($groups)
        );
    }

    public function serialize(object|array $object, ?array $groups = null ): string
    {
        return $this->serializer->serialize(
            $object,
            Types::JSON,
            SerializationContext::create()->setGroups($groups)
        );
    }

    public function createJsonResponse(object|array $object, ?array $groups = null): JsonResponse
    {
        return new JsonResponse(
            $this->transformer->toArray(
                $object,
                SerializationContext::create()->setGroups($groups),
            )
        );
    }
    public function createEmptyJsonResponse(): JsonResponse
    {
        return new JsonResponse(null);
    }
}

<?php

declare(strict_types=1);

namespace BackendTestApp\Presentation;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class JsonSerializer
{
    public const DEFAULT_GROUP = 'Default';

    public function __construct(private SerializerInterface $serializer, private NormalizerInterface $normalizer)
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
            JsonEncoder::FORMAT,
            [
                AbstractNormalizer::GROUPS => $groups,
                AbstractNormalizer::OBJECT_TO_POPULATE => $existsObject
            ]
        );
    }

    public function serialize(object|array $object, ?array $groups = null ): string
    {
        return $this->serializer->serialize(
            $object,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::GROUPS => $groups],
        );
    }

    public function createJsonResponse(object|array $object, ?array $groups = null): JsonResponse
    {
        return new JsonResponse(
            $this->normalizer->normalize(
                $object,
                JsonEncoder::FORMAT,
                [AbstractNormalizer::GROUPS => $groups],
            )
        );
    }
    public function createEmptyJsonResponse(): JsonResponse
    {
        return new JsonResponse(null);
    }
}

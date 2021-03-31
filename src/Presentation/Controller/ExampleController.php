<?php

declare(strict_types=1);

namespace BackendTestApp\Presentation\Controller;

use BackendTestApp\Application\DTO\ExampleFilter;
use BackendTestApp\Application\ExampleService;
use BackendTestApp\Domain\Entity\Example;
use BackendTestApp\Presentation\AuthenticationManager;
use BackendTestApp\Presentation\JsonResponse;
use BackendTestApp\Presentation\JsonSerializer;
use BackendTestApp\Presentation\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExampleController
{
    public function __construct(
        private ExampleService $exampleService,
        private JsonSerializer $serializer,
        private Validator $validator,
        private AuthenticationManager $privateAuthenticationManager
    ) {
    }

    #[Route('/example', name: 'find_example', methods: ['GET'])]
    public function find(
        Request $request,
        array $readSerializationGroups
    ): JsonResponse {

        $filter = new ExampleFilter($request->query->all());

        $example = $this->exampleService->findByFilter($filter, $this->privateAuthenticationManager->getCurrentUserId());

        return $this->serializer->createJsonResponse($example, $readSerializationGroups);
    }

    #[Route('/example/{id}', name: 'get_example', methods: ['GET'])]
    public function get(
        int $id,
        array $readSerializationGroups
    ): JsonResponse {
        $example = $this->exampleService->getById($id);

        $this->privateAuthenticationManager->checkCurrentUserId($example->getUserId());

        return $this->serializer->createJsonResponse($example, $readSerializationGroups);
    }

    #[Route('/example/{id}', name: 'delete_example', methods: ['DELETE'])]
    public function delete(
        int $id
    ): JsonResponse {
        $example = $this->exampleService->getById($id);
        $this->privateAuthenticationManager->checkCurrentUserId($example->getUserId());

        $this->exampleService->delete($example);

        return $this->serializer->createEmptyJsonResponse();
    }

    #[Route('/example', name: 'create_example', methods: ['POST'])]
    public function create(
        Request $request,
        array $createDeserializationGroups,
        array $readSerializationGroups,
    ): JsonResponse {
        $example = $this->serializer->deserialize(
            $request->getContent(),
            Example::class,
            $createDeserializationGroups
        );

        $this->validator->validate($example, $createDeserializationGroups);
        $this->exampleService->create($example, $this->privateAuthenticationManager->getCurrentUserId());

        return $this->serializer->createJsonResponse($example, $readSerializationGroups);
    }


    #[Route('/example/{id}', name: 'update_example', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        array $updateDeserializationGroups,
        array $readSerializationGroups,
    ): JsonResponse {
        $example = $this->serializer->deserialize(
            $request->getContent(),
            Example::class,
            $updateDeserializationGroups
        );
        $this->validator->validate($example, $updateDeserializationGroups);

        $example = $this->exampleService->getById($id);
        $this->privateAuthenticationManager->checkCurrentUserId($example->getUserId());
        $example = $this->serializer->deserialize(
            $request->getContent(),
            Example::class,
            $updateDeserializationGroups,
            $example
        );

        $this->exampleService->update($example);

        return $this->serializer->createJsonResponse($example, $readSerializationGroups);
    }
}

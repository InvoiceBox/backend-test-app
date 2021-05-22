<?php

declare(strict_types=1);

namespace BackendTestApp\Application;

use BackendTestApp\Application\DTO\ExampleFilter;
use BackendTestApp\Domain\Entity\Example;
use BackendTestApp\Infrastructure\Repository\ExampleRepository;
use Psr\Log\LoggerInterface;

class ExampleService
{
    public function __construct(
        private ExampleRepository $exampleRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function findByFilter(ExampleFilter $filter, ?int $userId = null): array
    {
        return $this->exampleRepository->findByFilter($filter, $userId);
    }

    public function getById(int $id): Example
    {
        return $this->exampleRepository->getById($id);
    }

    public function create(Example $example, ?int $userId = null): void
    {
        if ($userId) {
            $example->setUserId($userId);
        }

        /**
         * Здесь может быть любая бизнес логика:
         * -----
         * $foo = $this->fooService->getById($example->getFooId());
         * $example->setFoo($foo);
         * -----
         * $this->eventDispatcher->dispatch(new OnExampleCreatedEvent($example));
         */

        $this->exampleRepository->save($example);

        $this->logger->info('example entity has been created', ['id' => $example->getId()]);
    }

    public function update(Example $example): void
    {
        $this->exampleRepository->save($example);
    }

    public function delete(Example $example): void
    {
        $this->exampleRepository->delete($example);
    }
}

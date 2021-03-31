<?php

declare(strict_types=1);

namespace BackendTestApp\Presentation;

use BackendTestApp\Domain\Exception\FieldErrorCollection;
use BackendTestApp\Domain\Exception\Validation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(mixed $object, array $groups): void
    {
        $groups[] = JsonSerializer::DEFAULT_GROUP;

        /**
         * @var ConstraintViolationInterface[] $violations
         */
        $violations = $this->validator->validate($object, groups: $groups);
        if ($violations->count() > 0) {
            $fieldError = FieldErrorCollection::create();
            foreach ($violations as $violation) {
                $fieldError->add($violation->getPropertyPath(), $violation->getMessage());
            }

            throw new Validation('Validation error', $fieldError);
        }
    }
}

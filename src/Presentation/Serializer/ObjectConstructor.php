<?php

declare(strict_types=1);

namespace BackendTestApp\Presentation\Serializer;

use Doctrine\Instantiator\Instantiator;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

class ObjectConstructor implements ObjectConstructorInterface
{
    /** @var Instantiator */
    private $instantiator;

    /**
     * {@inheritdoc}
     */
    public function construct(
        DeserializationVisitorInterface $visitor,
        ClassMetadata $metadata,
        $data,
        array $type,
        DeserializationContext $context
    ): ?object
    {
        if ($context->hasAttribute('target')) {
            $object = $context->getAttribute('target');
            if ($object instanceof $metadata->name) {
                return $object;
            }
        }

        $object = $this->getInstantiator()->instantiate($metadata->name);

        if (method_exists($object,'__construct')) {
            $object->__construct();
        }

        return $object;
    }

    private function getInstantiator(): Instantiator
    {
        if (null === $this->instantiator) {
            $this->instantiator = new Instantiator();
        }

        return $this->instantiator;
    }
}

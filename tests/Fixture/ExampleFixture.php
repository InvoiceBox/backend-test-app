<?php

declare(strict_types=1);

namespace BackendTestApp\Tests\Fixture;

use BackendTestApp\Domain\Entity\Example;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ExampleFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $example = new Example();
        $example->setTitle('Integration module');
        $example->setDescription('Something like this');
        $example->setUserId(404);

        $manager->persist($example);
        $manager->flush();
    }
}

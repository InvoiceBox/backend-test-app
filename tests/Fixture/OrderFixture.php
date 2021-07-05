<?php


namespace BackendTestApp\Tests\Fixture;


use BackendTestApp\Domain\Entity\Orders;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrderFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $orders = new Orders();
        $orders->setAmount(10);
        $orders->setCreationDate("2021-07-04T16:16:34+00:00");
        $orders->setUserId(123);

        $manager->persist($orders);
        $manager->flush();
    }

}
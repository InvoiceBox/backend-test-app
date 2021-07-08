<?php


namespace BackendTestApp\Tests\Fixture;


use BackendTestApp\Domain\Entity\Example;
use BackendTestApp\Domain\Entity\OrderProduct;
use BackendTestApp\Domain\Entity\Order;
use BackendTestApp\Domain\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrderProductFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $orderProduct = new OrderProduct();
        $orderProduct->setProductId($manager->find(Product::class, 1));
        $orderProduct->setOrderId($manager->find(Order::class, 1));

        $manager->persist($orderProduct);
        $manager->flush();
    }
}
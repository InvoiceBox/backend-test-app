<?php


namespace BackendTestApp\Tests\Fixture;


use BackendTestApp\Domain\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $product = new Product();
        $product->setTitle('product 1');
        $product->setPrice(10);
        $product->setCount(10);
        $product->setPriceForAll(100);


        $manager->persist($product);
        $manager->flush();
    }

}
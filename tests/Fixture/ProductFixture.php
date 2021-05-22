<?php

declare(strict_types=1);

namespace BackendTestApp\Tests\Fixture;

use BackendTestApp\Domain\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++){
            $product = new Product();
            $product->setTitle('Product '.$i);
            $product->setSku(md5(uniqid((string)rand(), true)));
            $product->setPrice(mt_rand(10, 100));
            $product->setQty(1);
            $product->setUserId(404);

            $manager->persist($product);
        }

        $manager->flush();
    }
}

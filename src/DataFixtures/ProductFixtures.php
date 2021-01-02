<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Customer;
use App\Entity\Farm;
use App\Entity\Position;
use App\Entity\Price;
use App\Entity\Producer;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Uid\Uuid;

use function Sodium\add;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $farms = $manager->getRepository(Farm::class)->findAll();

        /** @var Farm $farm */
        foreach ($farms as $farm) {
            $position = new Position();
            $position->setLatitude(41.5);
            $position->setLongitude(7.5);
            $address = new Address();
            $address->setAddress("14 impasse du chant des oiseaux");
            $address->setPostCode("44210");
            $address->setCity("Pornic");
            $address->setPosition($position);
            $farm->setAddress($address);
            for ($i = 1; $i <= 10; $i++) {
                $product = new Product();
                $product->setId(Uuid::v4());
                $product->setFarm($farm);
                $product->setName("Product " . $i);
                $product->setDescription("Description . $i");
                $price = new Price();
                $price->setUnitPrice(rand(100, 1000));
                $price->setVat(2.1);
                $product->setPrice($price);
                $manager->persist($product);
            }
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}

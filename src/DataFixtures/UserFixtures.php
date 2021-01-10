<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Customer;
use App\Entity\Position;
use App\Entity\Producer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private UserPasswordEncoderInterface $userPasswordEncoder;

    /**
     * UserFixtures constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $producer = new Producer();
        $producer->setPassword($this->userPasswordEncoder->encodePassword($producer, "password"));
        $producer->setFirstName("Jane");
        $producer->setLastName("Doe");
        $producer->setEmail("producer@email.com");
        $producer->getFarm()->setName("Exploitation");
        $address = new Address();
        $address->setAddress("14 impasse du chant des oiseaux");
        $address->setPostCode("44210");
        $address->setCity("Pornic");
        $position = new Position();
        $position->setLatitude(47.110660);
        $position->setLongitude(-2.060817);
        $address->setPosition($position);
        $producer->getFarm()->setAddress($address);
        $manager->persist($producer);
        $manager->flush();

        for ($i = 1; $i <= 14; $i++) {
            $producer = new Producer();
            $producer->setPassword($this->userPasswordEncoder->encodePassword($producer, "password"));
            $producer->setFirstName("Jane");
            $producer->setLastName("Doe");
            $producer->setEmail("producer+" . $i . "@email.com");
            $producer->getFarm()->setName("Ferme");
            $address = new Address();
            $address->setAddress("14 impasse du chant des oiseaux");
            $address->setPostCode("44210");
            $address->setCity("Pornic");
            $position = new Position();
            $position->setLatitude(47.110660);
            $position->setLongitude(-2.060817);
            $address->setPosition($position);
            $producer->getFarm()->setAddress($address);
            $manager->persist($producer);
            $manager->flush();
        }

        $customer = new Customer();
        $customer->setPassword($this->userPasswordEncoder->encodePassword($customer, "password"));
        $customer->setFirstName("John");
        $customer->setLastName("Doe");
        $customer->setEmail("customer@email.com");
        $manager->persist($customer);

        for ($i = 1; $i <= 14; $i++) {
            $customer = new Customer();
            $customer->setPassword($this->userPasswordEncoder->encodePassword($customer, "password"));
            $customer->setFirstName("John");
            $customer->setLastName("Doe");
            $customer->setEmail("customer" . $i . "@email.com");
            $manager->persist($customer);
        }

        $manager->flush();
    }
}

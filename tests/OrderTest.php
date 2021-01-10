<?php

namespace App\Tests;

use App\Entity\Customer;
use App\Entity\Farm;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class OrderTest
 * @package App\Tests
 */
class OrderTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfulManageOrders(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $client->request(Request::METHOD_GET, $router->generate("order_manage"));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSuccessfulCreateOrderAndCancelIt(): void
    {
        $client = static::createAuthenticatedClient("customer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $product = $entityManager->getRepository(Product::class)->findOneBy([]);

        $client->request(Request::METHOD_GET, $router->generate("cart_add", [
            "id" => $product->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->request(Request::METHOD_GET, $router->generate("order_create"));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $customer = $entityManager->getRepository(Customer::class)->findOneByEmail("customer@email.com");

        $order = $entityManager->getRepository(Order::class)->findOneBy([
            "state" => "created",
            "customer" => $customer
        ]);

        $client->request(Request::METHOD_GET, $router->generate("order_cancel", [
            "id" => $order->getId()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $entityManager->clear();

        $order = $entityManager->getRepository(Order::class)->find($order->getId());

        $this->assertEquals("canceled", $order->getState());
    }
}

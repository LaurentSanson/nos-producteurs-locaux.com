<?php

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class RegistrationTest
 * @package App\Tests
 */
class RegistrationTest extends WebTestCase
{
    /**
     * @param string $role
     * @dataProvider provideRoles
     */
    public function testSuccessfulRegistration(string $role): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $router->generate("app_register", [
            "role" => $role
        ]));

        $form = $crawler->filter("form[name=registration_form]")->form([
            "registration_form[email]" => "email@email.com",
            "registration_form[firstname]" => "John",
            "registration_form[lastname]" => "Doe",
            "registration_form[plainPassword]" => "password",
            "registration_form[agreeTerms]" => true
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @return Generator
     */
    public function provideRoles(): Generator
    {
        yield ['producer'];
        yield ['customer'];
    }
}

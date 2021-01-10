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
     * @param array $formData
     * @dataProvider provideRoles
     */
    public function testSuccessfulRegistration(string $role, array $formData): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("app_register", [
            "role" => $role
        ]));

        $form = $crawler->filter("form[name=registration_form]")->form($formData);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @return Generator
     */
    public function provideRoles(): Generator
    {
        yield ['producer', [
            "registration_form[email]" => "email1@email.com",
            "registration_form[plainPassword]" => "password",
            "registration_form[firstname]" => "John",
            "registration_form[lastname]" => "Doe",
            "registration_form[farm][name]" => "Exploitation"
        ]];
        yield ['customer', [
            "registration_form[email]" => "email2@email.com",
            "registration_form[plainPassword]" => "password",
            "registration_form[firstname]" => "John",
            "registration_form[lastname]" => "Doe"
        ]];
    }

    /**
     * @param string $role
     * @param array $formData
     * @param string $errorMessage
     * @dataProvider provideBadRequests
     */
    public function testBadRequest(string $role, array $formData, string $errorMessage): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("app_register", [
            "role" => $role
        ]));

        $form = $crawler->filter("form[name=registration_form]")->form($formData);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorTextContains("span.form-error-message", $errorMessage);
    }

    public function provideBadRequests(): Generator
    {
        yield from $this->provideCustomerBadRequests();
        yield from $this->provideProducerBadRequests();
    }

    public function provideCustomerBadRequests(): Generator
    {
        yield [
            "customer",
            [
                "registration_form[email]" => "",
                "registration_form[plainPassword]" => "password",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "Doe"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "customer",
            [
                "registration_form[email]" => "email@email.com",
                "registration_form[plainPassword]" => "",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "Doe"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "customer",
            [
                "registration_form[email]" => "email@email.com",
                "registration_form[plainPassword]" => "password",
                "registration_form[firstname]" => "",
                "registration_form[lastname]" => "Doe"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "customer",
            [
                "registration_form[email]" => "email@email.com",
                "registration_form[plainPassword]" => "password",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => ""
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "customer",
            [
                "registration_form[email]" => "fail",
                "registration_form[plainPassword]" => "password",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "Doe"
            ],
            "Cette valeur n'est pas une adresse email valide."
        ];

        yield [
            "customer",
            [
                "registration_form[email]" => "email@email.com",
                "registration_form[plainPassword]" => "fail",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "Doe"
            ],
            "Cette chaîne est trop courte. Elle doit avoir au minimum 8 caractères."
        ];

        yield [
            "customer",
            [
                "registration_form[email]" => "customer@email.com",
                "registration_form[plainPassword]" => "password",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "Doe"
            ],
            "Cet e-mail est déjà associé à un compte"
        ];
    }

    public function provideProducerBadRequests(): Generator
    {
        yield [
            "producer",
            [
                "registration_form[email]" => "email@email.com",
                "registration_form[plainPassword]" => "password",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "Doe",
                "registration_form[farm][name]" => ""
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "producer",
            [
                "registration_form[email]" => "",
                "registration_form[plainPassword]" => "password",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "Doe",
                "registration_form[farm][name]" => "Exploitation"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "producer",
            [
                "registration_form[email]" => "email@email.com",
                "registration_form[plainPassword]" => "",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "Doe",
                "registration_form[farm][name]" => "Exploitation"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "producer",
            [
                "registration_form[email]" => "email@email.com",
                "registration_form[plainPassword]" => "password",
                "registration_form[firstname]" => "",
                "registration_form[lastname]" => "Doe",
                "registration_form[farm][name]" => "Exploitation"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "producer",
            [
                "registration_form[email]" => "email@email.com",
                "registration_form[plainPassword]" => "password",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "",
                "registration_form[farm][name]" => "Exploitation"
            ],
            "Cette valeur ne doit pas être vide."
        ];

        yield [
            "producer",
            [
                "registration_form[email]" => "fail",
                "registration_form[plainPassword]" => "password",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "Doe",
                "registration_form[farm][name]" => "Exploitation"
            ],
            "Cette valeur n'est pas une adresse email valide."
        ];

        yield [
            "producer",
            [
                "registration_form[email]" => "email@email.com",
                "registration_form[plainPassword]" => "fail",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "Doe",
                "registration_form[farm][name]" => "Exploitation"
            ],
            "Cette chaîne est trop courte. Elle doit avoir au minimum 8 caractères."
        ];

        yield [
            "producer",
            [
                "registration_form[email]" => "producer@email.com",
                "registration_form[plainPassword]" => "password",
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "Doe",
                "registration_form[farm][name]" => "Exploitation"
            ],
            "Cet e-mail est déjà associé à un compte"
        ];
    }
}

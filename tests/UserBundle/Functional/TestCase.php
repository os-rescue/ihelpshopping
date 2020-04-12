<?php

namespace IHelpShopping\Tests\UserBundle\Functional;

use IHelpShopping\Tests\SecureTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;

class TestCase extends SecureTestCase
{
    protected function createAuthenticatedClient(string $username = 'user', string $password = 'password'): Client
    {
        $data = $this->authenticate($username, $password);

        if (empty($data['token'])) {
            throw new \LogicException('Unable to get a JWT Token through the "/api/login_check" route.');
        }

        $client = $this->getSecureClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    protected function authenticate(string $username = 'user', string $password = 'password'): array
    {
        $client = $this->getSecureClient();
        $client->request(
            Request::METHOD_POST,
            '/api/login_check',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            \GuzzleHttp\json_encode(['email' => $username, 'password' => $password])
        );

        return json_decode($client->getResponse()->getContent(), true);
    }
}

<?php

namespace IHelpShopping\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class SecureTestCase extends WebTestCase
{
    protected function getSecureClient(): Client
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/api/');

        if (null !== $xsrf = $this->getXsrfToken($client)) {
            $client->setServerParameter('HTTP_X_XSRF_TOKEN', $xsrf);
        }

        return $client;
    }

    private function getXsrfToken(Client $client): ?string
    {
        $setCookie = $client->getResponse()->headers->get('set-cookie');
        $parts = null !== $setCookie ? explode(';', $setCookie) : null;

        return !empty($parts[0]) ? substr($parts[0], 11) : null;
    }
}

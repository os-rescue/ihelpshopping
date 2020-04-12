<?php

namespace IHelpShopping\Tests\Functional\Security;

use IHelpShopping\Tests\UserBundle\Functional\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserProfileTest extends TestCase
{
    private const TEST_ADMIN_EMAIL = 'admin1@test.com';
    private const TEST_USER_1_EMAIL = 'user1@test.com';
    private const TEST_USER_2_EMAIL = 'user2@test.com';
    private const TEST_USER_PASSWORD = 'AAAbbb111#';

    public function AsUserIWantAccessToAdminProfile()
    {
        $data = $this->authenticate(self::TEST_ADMIN_EMAIL, self::TEST_USER_PASSWORD);
        $uuid = $data['data']['user_id'];

        $client = $this->createAuthenticatedClient(
            self::TEST_USER_1_EMAIL,
            self::TEST_USER_PASSWORD
        );
        $client->request(Request::METHOD_GET, '/api/users/'.$uuid);

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testAsUserIWantAccessToAnotherUserProfile()
    {
        $data = $this->authenticate(self::TEST_USER_1_EMAIL, self::TEST_USER_PASSWORD);
        $uuid = $data['data']['user_id'];

        $client = $this->createAuthenticatedClient(self::TEST_USER_2_EMAIL, self::TEST_USER_PASSWORD);
        $client->request(Request::METHOD_GET, '/api/users/'.$uuid);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}

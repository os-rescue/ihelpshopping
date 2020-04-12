<?php

namespace IHelpShopping\Tests\EventListener\Security;

use IHelpShopping\Tests\UserBundle\Functional\TestCase;

class AuthenticationSuccessListenerTest extends TestCase
{
    protected const TEST_USER_EMAIL = 'user1@test.com';
    protected const TEST_USER_SECRET_CODE = 'AAAbbb111#';

    public function testOnAuthenticationSuccessResponse(): void
    {
        $data = $this->authenticate(self::TEST_USER_EMAIL, self::TEST_USER_SECRET_CODE);

        $this->assertTrue(\is_array($data));
        $this->assertCount(3, $data);

        $this->assertArrayHasKey('token', $data);
        $this->assertNotNull($data['token']);

        $this->assertArrayHasKey('data', $data);
        $this->assertNotNull($data['data']);
        $this->assertTrue(\is_array($data['data']));
        $this->assertCount(1, $data['data']);
        $this->assertArrayHasKey('user_id', $data['data']);
        $this->assertNotNull($data['data']['user_id']);

        $this->assertArrayHasKey('refresh_token', $data);
        $this->assertNotNull($data['refresh_token']);
    }
}

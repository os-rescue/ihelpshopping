<?php

namespace IHelpShopping\Tests\Validator;

use IHelpShopping\Entity\User;
use IHelpShopping\Validator\Constraints\CurrentPassword;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CurrentPasswordValidatorTest extends KernelTestCase
{
    private const TEST_EMAIL = 'user3@test.com';

    private $validator;
    private $manager;

    protected function setUp()
    {
        self::bootKernel();

        $this->validator = self::$container->get('validator');
        $this->manager = self::$container->get(RegistryInterface::class)->getManager();
    }

    /**
     * @dataProvider currentPasswordValidationDataProvider
     */
    public function testCurrentPasswordValidation(?string $newPassword, bool $isValid): void
    {
        $user = $this->getTestUser()->setPlainPassword($newPassword);

        $violations = $this->validator->validate($user, new CurrentPassword());

        $this->assertSame($isValid, 0 === count($violations));

        if (!$isValid) {
            $currentPasswordValidation = $violations->count() - 1;
            $this->assertNotNull($violations[$currentPasswordValidation]);
            $this->assertSame('already_used', $violations[$currentPasswordValidation]->getMessage());
        }
    }

    public function currentPasswordValidationDataProvider(): iterable
    {
        yield ['AAAbbb111', true];
        yield ['AAAbbb111#', false]; //This is old password
        yield [null, true];
    }

    private function getTestUser(): User
    {
        return $this->manager->getRepository(User::class)->findOneBy(
            [
                'email' => self::TEST_EMAIL,
            ]
        );
    }
}

<?php

namespace IHelpShopping\Tests\Validator;

use IHelpShopping\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WhitespaceValidatorTest extends KernelTestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    protected function setUp()
    {
        self::bootKernel();
        $this->validator = self::$container->get('validator');
    }

    /**
     * @dataProvider whitespaceValidationDataProvider
     */
    public function testWhitespaceValidation(string $firstName, int $expectedErrorsCount): void
    {
        $user = (new User())
            ->setFirstName($firstName)
            ->setLastName('Foo')
            ->setEmail('foobar@example.com')
            ->setUsername('foobar@example.com')
        ;

        $violations = $this->validate($user);
        $this->assertCount($expectedErrorsCount, $violations);

        if ($expectedErrorsCount > 0) {
            $this->assertSame('invalid_whitespaces', $violations[0]->getMessage());
            $this->assertSame('firstName', $violations[0]->getPropertyPath());
        }
    }

    public function whitespaceValidationDataProvider(): iterable
    {
        yield ['   ', 1];
        yield [' foo ', 1];
        yield ['   õöœūûęėæåāä', 1];
        yield ['õö œūûę ėæåāä   ', 1];
        yield ['foo bar', 0];
    }

    private function validate(User $user): ConstraintViolationListInterface
    {
        return $this->validator->validate($user);
    }
}

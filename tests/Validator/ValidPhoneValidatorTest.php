<?php

namespace IHelpShopping\Tests\Validator;

use IHelpShopping\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidPhoneValidatorTest extends KernelTestCase
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
     * @dataProvider phoneValidationDataProvider
     */
    public function testPhoneValidation(string $phone, string $mobilePhone, int $expectedErrorCount, $errors): void
    {
        $user = $this->createUser($phone, $mobilePhone);

        $violations = $this->validate($user);
        $this->assertCount($expectedErrorCount, $violations);
        if (count($errors) > 0) {
            foreach ($errors as $i => $error) {
                $this->assertSame($error['message'], $violations[$i]->getMessage());
                $this->assertSame($error['propertyPath'], $violations[$i]->getPropertyPath());
            }
        }
    }

    public function phoneValidationDataProvider(): iterable
    {
        yield [
            'foo', 'abcde', 2, [
                [
                    'message' => 'phone.invalid',
                    'propertyPath' => 'phoneNumber',
                ],
                [
                    'message' => 'phone.invalid',
                    'propertyPath' => 'mobileNumber',
                ]
            ]
        ];
        yield [
            '+12344567789', 'abcde', 1, [
                [
                    'message' => 'phone.invalid',
                    'propertyPath' => 'mobileNumber',
                ]
            ]
        ];
        yield [
            '+1(2)344567789', '+1(2)344567789', 0, []
        ];
        yield [
            '+1234cc4567789', '+1234ccc4567789', 2, [
                [
                    'message' => 'phone.invalid',
                    'propertyPath' => 'phoneNumber',
                ],
                [
                    'message' => 'phone.invalid',
                    'propertyPath' => 'mobileNumber',
                ]
            ]
        ];
        yield [
            '+1(2)344-567-789', '+1(2)344-567-789', 0, []
        ];
        yield [
            '+1(2)344*567*789', 'abcde', 2, [
                [
                    'message' => 'phone.invalid',
                    'propertyPath' => 'phoneNumber',
                ],
                [
                    'message' => 'phone.invalid',
                    'propertyPath' => 'mobileNumber',
                ]
            ]
        ];
        yield [
            '+1(2) 344 567 789', '+1(2) 344 567 789', 0, []
        ];
        yield [
            '00 12 344 567 789', '12 49 344 567 789', 0, []
        ];
        yield [
            '+1(2) 344-567-789', '+1(2) 344-567-789', 0, []
        ];
        yield [
            '+1(2) 344_567_789', '+1(2) 344_567_789', 2, [
                [
                    'message' => 'phone.invalid',
                    'propertyPath' => 'phoneNumber',
                ],
                [
                    'message' => 'phone.invalid',
                    'propertyPath' => 'mobileNumber',
                ]
            ]
        ];
    }

    private function createUser(?string $phoneNumber = null, ?string $mobileNumber = null): User
    {
        return (new User())
            ->setFirstName('foo')
            ->setLastname('bar')
            ->setPhoneNumber($phoneNumber)
            ->setMobileNumber($mobileNumber)
            ->setEmail('foobar@example.com')
            ->setUsername('foobar@example.com')
            ;
    }

    private function validate(User $user): ConstraintViolationListInterface
    {
        return $this->validator->validate($user);
    }
}

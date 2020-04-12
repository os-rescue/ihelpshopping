<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Element\DocumentElement;
use Behatch\Context\RestContext;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use IHelpShopping\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class UserContext implements Context
{
    private const USER_IRI = '/api/users';

    private static $currentTestUser;

    /**
     * @var RestContext
     */
    protected $restContext;

    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    /**
     * @BeforeScenario
     */
    public function before(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->restContext = $environment->getContext(RestContext::class);
    }

    /**
     * Updates the current user
     *
     * @Given I update the current user with body:
     */
    public function iUpdateTheCurrentUserWithBody(PyStringNode $body)
    {
        $response = $this->restContext->iSendARequestTo(
            Request::METHOD_PUT,
            self::$currentTestUser['@id'],
            $body
        );
        $this->refreshCurrentTestUser($response);

        return $response;
    }

    /**
     * Gets the current user
     *
     * @Given I get the current user
     */
    public function iGetTheCurrentUser()
    {
        return $this->restContext->iSendARequestTo(
            Request::METHOD_GET,
            self::$currentTestUser['@id']
        );
    }

    private function getTestUserIri(User $user): ?string
    {
        return sprintf('%s/%s', self::USER_IRI, $user->getId());
    }

    private function getTestUser(string $email): User
    {
        return $this->manager->getRepository(User::class)->findOneBy(
            [
                'email' => $email,
            ]
        );
    }

    private function refreshCurrentTestUser(DocumentElement $response): void
    {
        $data = json_decode((string) $response->getContent(), true);

        if (array_key_exists('@id', $data)) {
            self::$currentTestUser = $data;
        }
        if (array_key_exists('hydra:member', $data)) {
            self::$currentTestUser = $data['hydra:member'][0];
        }
    }

    private function setCurrentUserUuid(string $body): string
    {
        return str_replace("%user_uuids%", $this->getCurrentUserUuid(), $body);
    }

    private function getCurrentUserUuid(): string
    {
        return str_replace(sprintf('%s/', self::USER_IRI), '', self::$currentTestUser['@id']);
    }
}

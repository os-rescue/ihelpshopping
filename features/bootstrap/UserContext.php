<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behatch\Context\RestContext;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use IHelpShopping\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class UserContext implements Context
{
    private const RESOURCE_USER_IRI = '/api/users';
    private const RESOURCE_USER_ME_IRI = '/me';

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
     * Sends a HTTP request to add a set of requesters to the list of the helper
     *
     * @Given I send a http request to add a set of requesters to my list with body pattern:
     */
    public function iAddListOfRequestersWithBody(PyStringNode $body)
    {
        $body = $this->replaceUserByIRI($body);

        $response = $this->restContext->iSendARequestTo(
            Request::METHOD_PUT,
            sprintf('%s%s', self::RESOURCE_USER_IRI, self::RESOURCE_USER_ME_IRI),
            new PyStringNode((array) $body, 0)
        );

        return $response;
    }

    private function replaceUserByIRI(string $body): string
    {
        $users = $this->manager->getRepository(User::class)->findAll();
        $count = count($users);

        foreach ($users as $index => $user) {
            if (!strstr($body, $user->getEmail())) {
                continue;
            }

            $i = 1;
            while ($i < $count && !strstr($body, sprintf("%%user%d@test.com%%", $i))) {
                $i++;
            }

            $body = str_replace(
                sprintf("%%user%d@test.com%%", $i),
                sprintf('%s/%s', self::RESOURCE_USER_IRI, $user->getId()),
                $body
            );
        }

        return $body;
    }
}

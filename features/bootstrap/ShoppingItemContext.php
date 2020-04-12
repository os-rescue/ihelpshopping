<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Element\DocumentElement;
use Behatch\Context\RestContext;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class ShoppingItemContext implements Context
{
    private const RESOURCE_IRI = '/api/shopping_items';

    private static $currentTestShoppingItem;

    /**
     * @var RestContext
     */
    protected $restContext;

    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->manager = $registry->getManager();
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
     * Sends a HTTP request to create a new shopping item as current resource used for the next features
     *
     * @Given I create a current shopping item with body:
     */
    public function iCreateACurrentPolicyWithBody(PyStringNode $body)
    {
        $response = $this->restContext->iSendARequestTo(
            Request::METHOD_POST,
            self::RESOURCE_IRI,
            $body
        );
        $this->refreshCurrentTestShoppingItem($response);

        return $response;
    }

    /**
     * Updates the current shopping item
     *
     * @Given I update the current shopping item with body:
     */
    public function iUpdateTheCurrentShoppingItemWithBody(PyStringNode $body)
    {
        $response = $this->restContext->iSendARequestTo(
            Request::METHOD_PUT,
            self::$currentTestShoppingItem['@id'],
            $body
        );
        $this->refreshCurrentTestShoppingItem($response);

        return $response;
    }

    /**
     * Gets the current shopping item
     *
     * @Given I get the current shopping item
     */
    public function iGetTheCurrentShoppingItem()
    {
        return $this->restContext->iSendARequestTo(
            Request::METHOD_GET,
            self::$currentTestShoppingItem['@id']
        );
    }

    private function refreshCurrentTestShoppingItem(DocumentElement $response): void
    {
        $data = json_decode((string) $response->getContent(), true);

        if (array_key_exists('@id', $data)) {
            self::$currentTestShoppingItem = $data;
        }
        if (array_key_exists('hydra:member', $data)) {
            self::$currentTestShoppingItem = $data['hydra:member'][0];
        }
    }
}

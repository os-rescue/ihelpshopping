<?php

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behatch\Context\JsonContext;
use Behatch\Context\RestContext;
use Doctrine\Common\Persistence\ManagerRegistry;
use IHelpShopping\Kernel;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext extends MinkContext
{
    public const TEST_ADMIN_1_EMAIL = 'admin1@test.com';
    public const TEST_USER_1_EMAIL = 'user1@test.com';
    public const TEST_USER_2_EMAIL = 'user2@test.com';
    public const TEST_USER_3_EMAIL = 'user3@test.com';
    private const TEST_USER_PASSWORD = 'AAAbbb111#';

    private static $token;

    private $kernel;
    private $jwtManager;
    private $manager;

    protected $restContext;
    protected $jsonContext;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(
        KernelInterface $kernel,
        ManagerRegistry $doctrine,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->kernel = $kernel;
        $this->jwtManager = $jwtManager;
        $this->manager = $doctrine->getManager();
    }

    /**
     * @BeforeSuite
     */
    public static function prepare()
    {
        $kernel = new Kernel('test', true);
        $kernel->boot();
        $application = new Application($kernel);
        $container = $application->getKernel()->getContainer();

        $commands = [
            'test.doctrine.database_drop_command' => ['--force' => true],
            'test.doctrine.database_create_command' => [],
            'test.doctrine.schema_create_command' => [],
            'test.doctrine.fixtures_load_command' => ['--append' => true]
        ];

        foreach ($commands as $id => $options) {
            self::runCommand($application, $container->get($id), $options);
        }

        $kernel->shutdown();
    }

    private static function runCommand(Application $application, Command $command, array $options = [])
    {
        $application->add($command);
        $output = new ConsoleOutput();
        $options['command'] = $command->getName();

        if (!$application->getKernel()->isDebug()) {
            $options['--no-debug'] = true;
        }
        $input = new ArrayInput($options);
        $input->setInteractive(false);
        $command->run($input, $output);
    }

    /**
     * @BeforeScenario
     */
    public function before(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->restContext = $environment->getContext(RestContext::class);
        $this->jsonContext = $environment->getContext(JsonContext::class);
    }

    /**
     * @BeforeScenario @loginAsUser1
     */
    public function loginAsUser1()
    {
        $this->loginCheck(self::TEST_USER_1_EMAIL, self::TEST_USER_PASSWORD);
    }

    /**
     * @BeforeScenario @loginAsUser2
     */
    public function loginAsUser2()
    {
        $this->loginCheck(self::TEST_USER_2_EMAIL, self::TEST_USER_PASSWORD);
    }

    /**
     * @BeforeScenario @loginAsUser3
     */
    public function loginAsUser3()
    {
        $this->loginCheck(self::TEST_USER_3_EMAIL, self::TEST_USER_PASSWORD);
    }

    /**
     * @BeforeScenario @secureClient
     */
    public function secureClient(): void
    {
        $this->restContext->iSendARequestTo(Request::METHOD_GET, '/api/');
        $setCookie = $this->restContext->getSession()->getResponseHeader('set-cookie');
        $parts = null !== $setCookie ? explode(';', $setCookie) : null;
        $xsrfToken = !empty($parts[0]) ? substr($parts[0], 11) : null;

        if (null !== $xsrfToken) {
            $this->restContext->iAddHeaderEqualTo('X-XSRF-TOKEN', $xsrfToken);
        }
    }

    private function loginCheck(string $email, string $password)
    {
        $this->secureClient();
        $this->restContext->iAddHeaderEqualTo('Content-type', 'application/ld+json');

        $body = sprintf('{"email" : "%s", "password": "%s"}', $email, $password);
        $response = $this->restContext->iSendARequestTo(
            Request::METHOD_POST,
            '/api/login_check',
            new PyStringNode((array) $body, 0)
        );

        $data = json_decode((string) $response->getContent(), true);
        if (empty($data['token'])) {
            throw new \InvalidArgumentException(sprintf(
                "Error token. Response: %s",
                (string) $response->getContent()
            ));
        }

        self::$token = $data['token'];

        $this->restContext->setMinkParameter('user_id', $data['data']['user_id']);
    }

    /**
     * @BeforeScenario @setToken
     */
    public function setToken(BeforeScenarioScope $scope)
    {
        if (!empty(self::$token)) {
            $this->restContext->iAddHeaderEqualTo('Authorization', "Bearer ".self::$token);
        }
    }

    /**
     * @AfterScenario @logout
     */
    public function logout()
    {
        $this->restContext->iAddHeaderEqualTo('Authorization', '');
    }

    /**
     * @Then I wait :seconds second(s)
     */
    public function iWaitSeconds(int $seconds)
    {
        sleep($seconds);
    }

    /**
     * @AfterStep
     */
    public function printTheLastJsonAfterFailure(AfterStepScope $event): void
    {
        if (!$event->getTestResult()->isPassed()) {
            $this->printLastJsonResponseWithoutStackTrace();
        }
    }

    /**
     * @Then print last JSON response without stack trace
     */
    public function printLastJsonResponseWithoutStackTrace(): void
    {
        ob_start();
        $this->jsonContext->printLastJsonResponse();
        $output = ob_get_clean();
        $result = json_decode($output, true);
        unset($result['trace']);
        echo json_encode($result);
    }
}

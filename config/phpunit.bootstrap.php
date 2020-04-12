<?php

require_once __DIR__.'/bootstrap.php';

use IHelpShopping\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\ArrayInput;

$kernel = new Kernel('test', true);
$kernel->boot();
$application = new Application($kernel);
$container = $application->getKernel()->getContainer();

$runCommand = function (\Symfony\Component\Console\Command\Command $command, $options = []) use ($application): void {
    $application->add($command);
    $output = new ConsoleOutput();
    $options['command'] = $command->getName();

    if (!$application->getKernel()->isDebug()) {
        $options['--no-debug'] = true;
    }
    $input = new ArrayInput($options);
    $input->setInteractive(false);
    $command->run($input, $output);
};

$commands = [
    'test.doctrine.database_drop_command' => ['--force' => true],
    'test.doctrine.database_create_command' => [],
    'test.doctrine.schema_create_command' => [],
    'test.doctrine.fixtures_load_command' => ['--append' => true]
];

foreach ($commands as $id => $options) {
    $runCommand($container->get($id), $options);
}

$connection = $container->get('doctrine')->getConnection();
if ($connection->isConnected()) {
    $connection->close();
}

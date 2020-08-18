<?php

namespace LmConsole\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\{ArrayInput, InputDefinition, InputArgument};
use Symfony\Component\Console\Output\BufferedOutput;

class DebugEventsCommandTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testExecute()
    {
        $config = require __DIR__ . '/../../config/application.config.php';
        
        $application = \Laminas\Mvc\Application::init($config)->run();
        $eventManager = $application->getEventManager();
        $eventsList = $eventManager->getEventsList();

        /*$command = new DebugRoutesCommand();
        $input   = new ArrayInput([
            'command'    => 'debug:routes',
            'route_name' => 'test1',
        ], new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED),
            new InputArgument('route_name', InputArgument::REQUIRED),
        ]));
        $output  = new BufferedOutput();
        $command->execute($input, $output);
        echo "\n" . $output->fetch();

        $this->expectOutputRegex("/testing-url-1/");*/
    }
}

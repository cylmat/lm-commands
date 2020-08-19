<?php

namespace LmConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Interop\Container\ContainerInterface;
use LmConsole\Command\DebugEventsModel\EventDebuggerManager;


class DebugEventsCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'debug:events';

    /**
     * @var string
     */
    protected static $defaultArguments = '[route_name]';

    
    /**
     * Execute action
     * 
     * @return int Error code|Command::FAILURE|Command::SUCCESS if ok
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $output->writeln(["<comment> - Events of application</comment>","========================"]);

        $eventsList = $this->getEventsFromRoute('/');

        $this->displayTemplate($eventsList);

        return Command::SUCCESS;
    }

    /* protected */

    /**
     * Configuration of input arguments
     */
    protected function configure()
    {
        $this
            ->addArgument('route_name', InputArgument::OPTIONAL, 'The module route name.');

        $this
            // The short description shown while running "php bin/console list"
            ->setDescription('todo*** Debug events')
            ->setHelp('This command allows you to show a list of all events');
    }

    /**
     * Get Application configuration
     * included an EventManager which can debug all Events 
     */
    protected function getApplicationConfig(): array
    {
        $serviceConfig = [  
            'service_manager' =>  [
                'factories' => [
                    'EventManager' => function (ContainerInterface $container, $name, array $options = null) {
                        $shared = $container->has('SharedEventManager') ? $container->get('SharedEventManager') : null;
                        return new EventDebuggerManager($shared);
                    }
                ],
            ]
        ];
        return $serviceConfig;
    }

    /**
     * Simulate an MVC application
     * and get all Events on the dispatched route
     */
    protected function getEventsFromRoute(string $route): array
    {
        $config = require __DIR__ . '/../../config/application.config.php';
        $serviceConfig = $this->getApplicationConfig();

        $config = array_merge($config, $serviceConfig);
        
        $application = \Laminas\Mvc\Application::init($config)->run();
        $eventManager = $application->getEventManager();
        $eventsList = $eventManager->getEventsList();

        return $eventsList;
    }

    protected function displayTemplate(array $eventsList): void
    {
        $output = '';
        foreach ($eventsList as $eventName => $eventProperties) {
            $output .= $this->getEventTemplate($eventName, $eventProperties);
        }

        // Final render
        $this->output->writeln($output);
    }

    /**
     * Get template for one event
     */
    protected function getEventTemplate(string $eventName, array $eventProperties): string
    {
        $leftSize = 10;
        $centerSize = 100;
        $pipe = '|';

        // Display event name
        $head  = '[' . strtoupper($eventName) . ']' . PHP_EOL;

        // Display head bar
        $head .= $this->getPatternLine($leftSize, $centerSize);
        $head .= $this->getTextLine(" Priority ", $leftSize, " Callable ", $centerSize);
        $head .= $this->getPatternLine($leftSize, $centerSize);

        // Display events properties
        $main = '';
        foreach ($eventProperties as $priority => $callable) {
            $main .= $this->getTextLine(" $priority ", $leftSize, " $callable ", $centerSize);
        }
        $main .= $this->getPatternLine($leftSize, $centerSize) . PHP_EOL;
        return $head . $main;
    }

    /**
     * Get rendered pattern line
     */
    protected function getPatternLine(int $leftSize, int $centerSize)
    {
        $cross = '+';
        $dash = '-';

        return $cross . $this->getPattern($dash, $leftSize) . 
                $cross . $this->getPattern($dash, $centerSize) . 
                $cross . PHP_EOL;
    }

    /**
     * Get rendered text line
     */
    protected function getTextLine(string $leftText, int $leftSize, string $centerText, int $centerSize)
    {
        $pipe = '|';

        return $pipe . str_pad($leftText, $leftSize, ' ') . $pipe . 
                str_pad($centerText, $centerSize, ' ') . $pipe . PHP_EOL;
    }

    /**
     * Get a repeated $pattern of $size
     */
    protected function getPattern(string $pattern, int $size)
    {
        return str_pad('', $size, $pattern);
    }
}
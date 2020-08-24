<?php

/**
 * Debug events used in Laminas MVC Module
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Command;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Application;
use LmConsole\Command\DebugEventsModel\EventDebuggerManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use const PHP_EOL;

class DebugEventsCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'debug:events';

    /** @var string */
    protected static $defaultArguments = '[route_name] [event_name]';

    protected static $default_route = '/';
    
    /**
     * Execute action
     *
     * @return int Error code|Command::FAILURE|Command::SUCCESS if ok
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        $output->writeln(["<comment> - Events of application</comment>", "========================"]);

        $inputRoute = $input->getArgument('route_name') ?? self::$default_route;
        $inputEvent = $input->getArgument('event_name');
        $eventsList = $this->getEventsFromRoute($inputRoute, $inputEvent);
        $this->displayTemplate($eventsList);

        return Command::SUCCESS;
    }

    /* protected */

    /**
     * Configuration of input arguments
     */
    protected function configure(): void
    {
        $this
            ->addArgument('route_name', InputArgument::OPTIONAL, "The module route name, will be '/' otherwise.")
            ->addArgument('event_name', InputArgument::OPTIONAL, "The event name, or show all events.");

        $this
            // The short description shown while running "php bin/console list"
            ->setDescription('Debug events of application')
            ->setHelp(
                "This command allows you to show a list of all events of the application"
            );
    }

    /**
     * Get Application configuration
     * included an EventManager which can debug all Events
     */
    protected function getApplicationConfig(): array
    {
        return [
            'service_manager' => [
                'factories' => [
                    'EventManager' => function (ContainerInterface $container, $name, ?array $options = null) {
                        $shared = $container->has('SharedEventManager') ? $container->get('SharedEventManager') : null;
                        return new EventDebuggerManager($shared);
                    },
                ],
            ],
        ];
    }

    /**
     * Simulate an MVC application
     * and get all Events on the dispatched route
     */
    protected function getEventsFromRoute(string $inputRoute, ?string $inputEventName): array
    {
        $config        = require __DIR__ . '/../../config/application.config.php';
        $serviceConfig = $this->getApplicationConfig();

        $config = array_merge($config, $serviceConfig);
        
        $application  = Application::init($config)->run();
        $eventManager = $application->getEventManager(); //EventDebuggerManager
        $eventsList   = $eventManager->getEventsList($inputEventName);

        // Check input if no results
        if ($inputEventName && 0 === count($eventsList)) {
            $this->checkInputEventSpell($inputEventName, $eventManager);
        }

        return $eventsList;
    }

    /**
     * Check levenstein event spell if no events are returned
     */
    protected function checkInputEventSpell(string $inputEventName, EventDebuggerManager $eventManager): void
    {
        $eventsList = $eventManager->getEventsList();

        $result = [];
        foreach ($eventsList as $eventName => $properties) {
            if (levenshtein($eventName, $inputEventName) < 5) {
                $result[] = $eventName;
            }
        }

        $msg = "We couldn't find event '$inputEventName'. Did you mean one of these?" . PHP_EOL;
        foreach ($result as $existsName) {
            $msg .= ' - ' . $existsName . PHP_EOL;
        }
        $this->sendError($msg);
    }

    /**
     * Display all events
     */
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
     * Get template for one single event
     */
    protected function getEventTemplate(string $eventName, array $eventProperties): string
    {
        /*
         * info colors:  black, red, green, yellow, blue, magenta, cyan and white.
         * info options: bold, underscore, blink, reverse
         */
        $leftSize   = 10;
        $centerSize = 40; //Default value
        $pipe       = '|';

        // Get max propertie text size
        foreach ($eventProperties as $priority => $callable) {
            if (strlen($callable) > $centerSize) {
                $centerSize = strlen($callable) + 2; // count with '()' size
            }
        }

        // Display event name
        $head = ' [' . $eventName . ']' . PHP_EOL;

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
}

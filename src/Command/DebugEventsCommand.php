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
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugEventsCommand extends AbstractCommand
{
    protected const ROUTE_URL = 'route_url';
    protected const EVENT_NAME = 'event_name';
    
    /** @var string */
    protected static $defaultName = 'debug:events';

    /** @var string */
    protected static $defaultArguments = [
        self::ROUTE_URL => '/'
    ];
    
    /**
     * Execute action
     *
     * @return int Error code|Command::FAILURE|Command::SUCCESS if ok
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);
        
        // Display head
        $this->displayHead("Events of application");

        $inputUrl = $input->getArgument(self::ROUTE_URL); //take '/' by default
        $inputEvent = $input->getArgument(self::EVENT_NAME);

        $eventsList = $this->getEventsFromUrl($inputUrl, $inputEvent);
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
            ->addArgument(self::ROUTE_URL, InputArgument::OPTIONAL, "The route url (e.g.: 'my-url/') to test, or will check the '/' otherwise.")
            ->addArgument(self::EVENT_NAME, InputArgument::OPTIONAL, "The event name, or show all events for the specified url.");

        $this
            // The short description shown while running "php bin/console list"
            ->setDescription("Debug all the events of the application.")
            ->setHelp(
                "This command allows you to show a list of all events of the application\n" . 
                "The default value of route is the '/' one.\n" . 
                "You can select a specific event.\n" . 
                "\te.g: bin/laminas my-url myevent" 
            );
    }

    /**
     * Get Application configuration
     * included an EventManager which can debug all Events
     */
    protected function getServiceConfig(): array
    {
        return [
            'service_manager' => [
                'factories' => [
                    'EventManager' => function (ContainerInterface $container, $name, ?array $options = null) {
                        $shared = $container->has('SharedEventManager') ? $container->get('SharedEventManager') : null;
                        return new EventDebuggerManager($shared);
                    }
                ],
            ],
        ];
    }

    /**
     * Simulate an MVC application and get all Events on the dispatched url
     */
    protected function getEventsFromUrl(?string $inputUrl, ?string $inputEventName): array
    {
        $config        = require __DIR__ . '/../../config/application.config.php';
        $serviceConfig = $this->getServiceConfig();

        $config = array_merge($config, $serviceConfig);

        // Launch application
        $_SERVER['REQUEST_URI'] = $inputUrl ?? self::$defaultArguments[self::ROUTE_URL];
        $application = Application::init($config);
        $application->run();

        // Get events
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

        $eventsNames = array_keys($eventsList);
        $results = $this->checkArgSpell($inputEventName, $eventsNames);

        if (!$results) {
            $msg = "We couldn't find event '$inputEventName'." . PHP_EOL;
            $this->sendError($msg);
            return;
        }

        $msg = "We couldn't find event '$inputEventName'. Did you mean one of these?" . PHP_EOL;
        foreach ($results as $existsName) {
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
                $centerSize = strlen($callable); 
            }
        }

        // Align with head
        $centerSize += 2; // count with '()' size

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

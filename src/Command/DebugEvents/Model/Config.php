<?php

/**
 * Retrieve configuration and events
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Command\DebugEvents\Model;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Application;
use LmConsole\Config\GlobalConfigRetriever;
use LmConsole\Traits\ToolsTrait;
use LmConsole\Command\DebugEventsCommand;
use RuntimeException;

class Config
{
    use ToolsTrait;

    /**
     * Get all Events on the dispatched url
     */
    public function getEventsFromUrl(?string $inputUrl, ?string $inputEventName): array
    {
        $application = $this->runApplicationInstance();

        // Get events
        $eventManager = $application->getEventManager(); //EventDebuggerManager
        $eventsList   = $eventManager->getEventsList($inputEventName);

        // Check input if no results
        if ($inputEventName && 0 === count($eventsList)) {
            $this->checkInputEventSpell($inputEventName, $eventManager);
        }

        return $eventsList;
    }
    
    /* protected */

    /**
     * Simulate an MVC application
     */
    protected function runApplicationInstance(): object
    {
        $serviceConfig = $this->getServiceConfig();

        // Launch application
        $_SERVER['REQUEST_URI'] = $inputUrl ?? DebugEventsCommand::$defaultArguments[DebugEventsCommand::ROUTE_URL];

        $appConfig = GlobalConfigRetriever::getApplicationConfig();
        $appConfig = array_merge($appConfig, $serviceConfig);

        // Avoid to print something
        ob_start();
        $application = Application::init($appConfig)->run();
        ob_end_clean();

        return $application;
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
     * Check levenstein event spell if no events are returned
     * 
     * @throws RuntimeException;
     */
    protected function checkInputEventSpell(string $inputEventName, EventDebuggerManager $eventManager): void
    {
        $eventsList = $eventManager->getEventsList();

        $eventsNames = array_keys($eventsList);
        $results = $this->checkArgSpell($inputEventName, $eventsNames);

        if (!$results) {
            $msg = "We couldn't find event '$inputEventName'." . PHP_EOL;
            throw new RuntimeException($msg);
            return;
        }

        $msg = "We couldn't find event '$inputEventName'. Did you mean one of these?" . PHP_EOL;
        foreach ($results as $existsName) {
            $msg .= ' - ' . $existsName . PHP_EOL;
        }
        throw new RuntimeException($msg);
    }
}

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
        $output->writeln(["<comment> - Events of application</comment>","========================"]);

        $eventsList = $this->getEventsFromRoute('/');
        var_dump($eventsList);
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
}
<?php

namespace LmConsole\Command;

use Laminas\Cli\ContainerResolver;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function is_a;
use function is_array;
use function sprintf;

class DebugRoutesCommand extends AbstractCommand
{
    /** @var string Name of command */
    protected static $defaultName = 'debug:routes';

    protected static $defaultArguments = '[route_name]';

    /**
     * Execute action
     *
     * @return int Error code|Command::FAILURE|Command::SUCCESS if ok
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $routes = $definedRoutes = $this->getRoutes($input);

        if ($input->hasArgument('route_name') && isset($routes[$input->getArgument('route_name')])) {
            $routes = $routes[$input->getArgument('route_name')];
        }
        $output->writeln(["<comment>\t - Routes of application</comment>", "============"]);
        
        foreach ($definedRoutes as $i => $route) {
            $output->writeln([
                "<comment>{$route['name']}</comment>",
                "\t<info>Route: {$route['route']}</info>",
                $route['default_controller'] ? "\tdefault: " . $route['default_controller'] : "\t-no default params",
            ]);
        }
        return Command::SUCCESS;
    }

                                                /* protected */

    /**
     * Configuration of arguments
     */
    protected function configure()
    {
        $this
            ->addArgument('route_name', InputArgument::OPTIONAL, 'The module route name.');
        $this
            // The short description shown while running "php bin/console list"
            ->setDescription('Debug routes from [route_name] or all routes')
            ->setHelp('This command allows you to show a list of all routes ans their associated controllers');
    }

    /**
     * Get container configuration
     *
     * @throws RuntimeException
     */
    protected function getRoutes(InputInterface $input): array
    {
        $config    = $this->getConfig();
        $router    = $this->getFoundChild('router', $config);
        $allRoutes = $this->getFoundChild('routes', $router);
        
        if (! $allRoutes) {
            throw new RuntimeException("Routes are not defined in configuration file.");
        }

        $routeStack = [];
        $routeName  = $input->getArgument('route_name');

        // For a single defined route passed in Input
        if ($routeName && isset($allRoutes[$routeName])) {
            $routeStack[] = $this->getData($routeName, $allRoutes[$routeName]);
        } else {
            // Return all routes
            foreach ($allRoutes as $routeName => $routeData) {
                $routeStack[] = $this->getData($routeName, $routeData);
            }
        }

        return $routeStack;
    }

    /**
     * Get container configuration
     *
     * @throws RuntimeException
     */
    protected function getConfig(): array
    {
        // Services
        if (! $container = ContainerResolver::resolve()) {
            throw new RuntimeException("Configuration file is not provided");
        }
        if (! $container->get('config')) {
            throw new RuntimeException("Configuration data is not provided");
        }
        return $container->get('config');
    }

    /**
     * Return route data
     *
     * @throws RuntimeException
     */
    protected function getData(string $routeName, array $routeData): array
    {
        if (!$routeData) {
            throw new RuntimeException(sprintf('Missing route configuration in %s', $routeName));
        }

        $opt   = $this->getFoundChild('options', $routeData);
        if (!$opt) {
            throw new RuntimeException(sprintf("Missing options configuration in %s", $routeName));
        }

        $route = $this->getFoundChild('route', $opt);

        // Default options
        $defaults = $this->getFoundChild('defaults', $opt);
        $ctrl = null;
        if ($defaults) {
            $ctrl     = $this->getFoundChild('controller', $defaults);
            $action   = $this->getFoundChild('action', $defaults);
        } 

        // Return values
        return [
            'name'               => $routeName,
            'route'              => $route,
            'default_controller' => $ctrl ? $ctrl . '::' . $action : null,
        ];
    }

    protected function toArrayIterator(array $array): RecursiveIteratorIterator
    {
        $iter = new RecursiveArrayIterator($array);
        return new RecursiveIteratorIterator($iter, RecursiveIteratorIterator::SELF_FIRST);
    }

    /**
     * Return child value if $key found
     *
     * @return null|mixed
     */
    protected function getFoundChild(?string $key, array $parentArray)
    {
        if (! $key) {
            return null;
        }
        if (! is_array($parentArray) || is_a($parentArray, 'Iterator')) {
            return null;
        }
        foreach ($parentArray as $k => $child) {
            if ($k === $key) {
                return $child;
            }
        }
        return null;
    }
}

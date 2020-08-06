<?php

namespace LmConsole\Command;

use \Laminas\Cli\ContainerResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugRoutesCommand extends Command
{
    /**
     * @var string $defaultName Name of command
     */
    protected static $defaultName = 'debug:routes';

    /**
     * 
     */
    protected static $defaultArgument = '[route_name]';

    /**
     * Execute action
     * 
     * @return int Error code|Command::FAILURE|Command::SUCCESS if ok
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $routes = $defined_routes = $this->getRoutes($input);

        if ($input->hasArgument('route_name') && isset($routes[$input->getArgument('route_name')])) {
            $routes = $routes[$input->getArgument('route_name')];
        }
        $output->writeln(["<comment>\t - Routes of application</comment>","============"]);
        
        foreach ($defined_routes as $i => $route) {
            $output->writeln([
                "<comment>{$route['name']}</comment>",
                "\t<info>Route: {$route['route']}</info>",
                $route['default_controller'] ? "\tdefault: " . $route['default_controller'] : "\t-no default params"
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
     * @param InputInterface $input
     * @throws \RuntimeException
     */
    protected function getRoutes(InputInterface $input): array
    {
        $config = $this->getConfig();
        $router = $this->getFoundChild('router', $config);
        $all_routes = $this->getFoundChild('routes', $router);
        
        if (!$all_routes) {
            throw new \RuntimeException("Routes are not defined in configuration file.");
        }

        $routeStack = [];
        $route_name = $input->getArgument('route_name');

        // For a single defined route passed in Input
        if ($route_name && isset($all_routes[$route_name])) {
            $routeStack[] = $this->getData($route_name, $all_routes[$route_name]);
        } else {
            // Return all routes
            foreach ($all_routes as $route_name => $routeData) {
                $routeStack[] = $this->getData($route_name, $routeData);
            }
        }

        return $routeStack;
    }

    /**
     * Get container configuration
     * 
     * @throws \RuntimeException
     */
    protected function getConfig(): array
    {
        // Services
        if (!$container = ContainerResolver::resolve()) {
            throw new \RuntimeException("Configuration file is not provided");
        }
        if (!$container->get('config')) {
            throw new \RuntimeException("Configuration data is not provided");
        }
        return $container->get('config');
    }

    /**
     * Return route data 
     * 
     * @throws \RuntimeException
     */
    protected function getData(string $routeName, array $routeData): array
    {
        $routeStack = [];

        if (!$routeData) {
            throw new \RuntimeException(sprintf('Missing route configuration in %s', $routeName));
        }

        $opt = $this->getFoundChild('options', $routeData);
        $route = $this->getFoundChild('route', $opt);

        // Default options
        $defaults = $this->getFoundChild('defaults', $opt);
        $ctrl = $this->getFoundChild('controller', $defaults);
        $action = $this->getFoundChild('action', $defaults);

        // Return values
        $routeStack = [
            'name' => $routeName,
            'route' => $route,
            'default_controller' => $ctrl ? $ctrl . '::' . $action : null
        ];
        return $routeStack;
    }

    /**
     * 
     */
    protected function toArrayIterator(array $array): \RecursiveIteratorIterator
    {
        $iter = new \RecursiveArrayIterator($array);
        return new \RecursiveIteratorIterator($iter, \RecursiveIteratorIterator::SELF_FIRST);
    }

    /**
     * Return child value if $key found
     * 
     * @return mixed
     */
    protected function getFoundChild(?string $key, $parent)
    {
        if (!$key) return null;
        if (!is_array($parent) || is_a($parent, 'Iterator')) return null;
        foreach ($parent as $k => $child) {
            if($k === $key) {
                return $child;
            }
        }
        return null;
    }
}
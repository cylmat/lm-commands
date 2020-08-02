<?php

namespace MyConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugRoutes extends Command
{
    /**
     * @var string $defaultName Name of command
     */
    protected static $defaultName = 'debug:routes';

    /**
     * Execute action
     * 
     * @return int Error code|Command::FAILURE|Command::SUCCESS if ok
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->hasArgument('route_name') && isset($routes[$input->getArgument('route_name')])) {
            $routes = $routes[$input->getArgument('route_name')];
        }
        $defined_routes = $this->getRoutes($input);
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
     */
    protected function getRoutes(InputInterface $input): array
    {
        $config = $this->getConfig();
        $router = $this->getFoundChild('router', $config);
        $all_routes = $this->getFoundChild('routes', $router);
        
        $routeStack = [];
        $route_name = $input->getArgument('route_name');
        if ($route_name && isset($all_routes[$route_name])) {
            $routeStack = $this->getData($route_name, $all_routes);
        } else {
            foreach ($all_routes as $route_name => $routeData) {
                $routeStack[] = $this->getData($route_name, $routeData);
            }
        }
        
        return $routeStack;
    }

    /**
     * Get container configuration
     */
    protected function getConfig(): ?array
    {
        // Services
        if (!$container = \Laminas\Cli\ContainerResolver::resolve()) {
            return null;
        }
        return $container->get('config');
    }

    /**
     * @throw \RuntimeException
     */
    protected function getData(string $routeName, array $routeData): array
    {
        $routeStack = new \SplStack;
        $routeStack = [];
        $opt = $this->getFoundChild('options', $routeData);
        $route = $this->getFoundChild('route', $opt);
        
        if (!$route) {
            throw new \RuntimeException(sprintf('Missing route configuration in %s', $routeName));
        }
        $defaults = $this->getFoundChild('defaults', $opt);
        
        $ctrl = $this->getFoundChild('controller', $defaults);
        $action = $this->getFoundChild('action', $defaults);
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
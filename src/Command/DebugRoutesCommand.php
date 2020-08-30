<?php

/**
 * Debug routes of Laminas MVC Module
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Command;

use Laminas\Cli\ContainerResolver;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugRoutesCommand extends AbstractCommand
{
    protected const ROUTE_NAME = 'route_name';

    /** @var string Name of command */
    protected static $defaultName = 'debug:routes';

    /**
     * Execute action
     *
     * @return int Error code|Command::FAILURE|Command::SUCCESS if ok
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        // All routes
        $routes = $definedRoutes = $this->getRoutes($input);

        // One single route in Input
        if ($input->hasArgument(self::ROUTE_NAME) && isset($routes[$input->getArgument(self::ROUTE_NAME)])) {
            $routes = $routes[$input->getArgument(self::ROUTE_NAME)];
        }

        // Display head
        $this->displayHead("Routes of application");
        
        // Display routes
        $this->displayRoutes($routes);

        return Command::SUCCESS;
    }

    /* protected */

    /**
     * Configuration of arguments
     */
    protected function configure()
    {
        $this
            ->addArgument(self::ROUTE_NAME, InputArgument::OPTIONAL, "The module route name.");
        $this
            // The short description shown while running "php bin/console list"
            ->setDescription("Debug routes from [".self::ROUTE_NAME."] or all routes.")
            ->setHelp(
                "This command allows you to show a list of all routes ans their associated controllers"
            );
    }

    /**
     * Display routes
     */
    protected function displayRoutes(array $definedRoutes): void
    {
        // Get size of columns
        $leftSize = 40; //default value
        $centerSize = 50;
        $rightSize = 50;

        foreach ($definedRoutes as $i => $route) {
            // Left col with route name
            $size = strlen($route['name']);
            if ($size > $leftSize) {
                $leftSize = $size;
            }

            // Center col with others data
            $size = strlen($route['route']);
            if ($size > $centerSize) {
                $centerSize = $size;
            }

            $size = strlen($route['default_controller']);
            if ($size > $rightSize) {
                $rightSize = $size;
            }
        }

        // Align with head
        $leftSize += 2;
        $centerSize += 2;
        $rightSize += 2;

        $head = $main = '';
        
        // Display head bar
        $head = "\n";
        $head .= $this->getPatternLine($leftSize, $centerSize, $rightSize);
        $head .= $this->getTextLine(" Route ", $leftSize, " Url ", $centerSize, " Default ", $rightSize);
        $head .= $this->getPatternLine($leftSize, $centerSize, $rightSize);

        // Display routes properties
        foreach ($definedRoutes as $i => $route) {
            $default = $route['default_controller'] ? $route['default_controller'] : "no default params";
            $main .= $this->getTextLine(
                " {$route['name']} ", $leftSize, 
                " {$route['route']} ", $centerSize,
                " $default ", $rightSize
            );
        }
        $main .= $this->getPatternLine($leftSize, $centerSize, $rightSize) . PHP_EOL;
        
        $this->output->writeln($head . $main);
    }

    /**
     * Get all routes from container configuration
     *
     * @throws RuntimeException
     */
    protected function getRoutes(InputInterface $input): array
    {
        $config    = $this->getApplicationConfig();
        $allRoutes = $this->getRoutesFromConfig($config);

        $routeStack = [];
        $routeName  = $input->getArgument(self::ROUTE_NAME);

        // Wrong argument
        if ($routeName && !isset($allRoutes[$routeName])) {
            throw new RuntimeException("Route $routeName doesn't exists.");
        } elseif ($routeName && isset($allRoutes[$routeName])) {
            // For a single defined route passed in Input
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
     * Return route data
     *
     * @throws RuntimeException
     */
    protected function getData(string $routeName, array $routeData): array
    {
        if (! $routeData) {
            throw new RuntimeException(sprintf('Missing route configuration in %s', $routeName));
        }

        $opt = $this->getFoundChild('options', $routeData);
        if (! $opt) {
            throw new RuntimeException(sprintf("Missing options configuration in %s", $routeName));
        }

        $route = $this->getFoundChild('route', $opt);

        // Default options
        $defaults = $this->getFoundChild('defaults', $opt);
        $ctrl     = null;
        if ($defaults) {
            $ctrl   = $this->getFoundChild('controller', $defaults);
            $action = $this->getFoundChild('action', $defaults);
        }

        // Return values
        return [
            'name'               => $routeName,
            'route'              => $route,
            'default_controller' => $ctrl ? $ctrl . '::' . $action : null,
        ];
    }
}

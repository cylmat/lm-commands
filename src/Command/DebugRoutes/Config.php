<?php

/**
 * Retrieve configuration and events
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Command\DebugRoutes;

use LmConsole\Config\GlobalConfigRetriever;
use LmConsole\Traits\{ConfigTrait, ToolsTrait};
use LmConsole\Command\DebugRoutesCommand;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;

class Config
{
    use ConfigTrait, ToolsTrait;

    protected $input;
    
    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * Get all routes from container configuration
     *
     * @throws RuntimeException
     */
    public function getRoutes(InputInterface $input): array
    {
        $config    = $this->getContainerConfig();
        $allRoutes = $this->getRoutesFromConfig($config);

        $routeStack = [];
        $routeName  = $input->getArgument(DebugRoutesCommand::ROUTE_NAME);

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

    /* protected */

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

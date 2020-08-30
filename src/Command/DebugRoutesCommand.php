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

use RuntimeException;
use LmConsole\Command\DebugRoutes\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugRoutesCommand extends AbstractCommand
{
    const ROUTE_NAME = 'route_name';

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
        $config = Factory::getConfig($input);
        $routes = $definedRoutes = $config->getRoutes($input);

        // One single route in Input
        if ($input->hasArgument(self::ROUTE_NAME) && isset($routes[$input->getArgument(self::ROUTE_NAME)])) {
            $routes = $routes[$input->getArgument(self::ROUTE_NAME)];
        }

        $template = Factory::getTemplate($output);

        // Display head
        $template->displayTitle("Routes of application");
        
        // Display routes
        $template->displayRoutes($routes);

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
}

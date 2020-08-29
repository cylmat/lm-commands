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

use LmConsole\Command\DebugEvents\Factory;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugEventsCommand extends AbstractCommand
{
    public const ROUTE_URL = 'route_url';
    public const EVENT_NAME = 'event_name';
    
    /** @var string */
    public static $defaultName = 'debug:events';

    /** @var string */
    public static $defaultArguments = [
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

        $config = Factory::getConfig();
        $template = Factory::getTemplate($output);

        $eventsList = $config->getEventsFromUrl($inputUrl, $inputEvent);
        $template->displayTemplate($eventsList);

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
}

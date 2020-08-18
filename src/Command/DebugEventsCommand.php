<?php

namespace LmConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class DebugEventsCommand extends AbstractCommand
{
    /**
     * @var string $defaultName Name of command
     */
    protected static $defaultName = 'debug:events';

    protected static $defaultArguments = '[route_name]';

    
    /**
     * Execute action
     * 
     * @return int Error code|Command::FAILURE|Command::SUCCESS if ok
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(["<comment> - Events of application</comment>","========================"]);

      //  $composer = include __DIR__.'/../../../vendor/autoload.php';
        //var_dump(get_class_methods($composer));

        //var_dump($composer->getPrefixesPsr4());
        //var_dump(get_class_methods($this));
        
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
            ->setDescription('todo*** Debug events')
            ->setHelp('This command allows you to show a list of all events');
    }
}
<?php

namespace LmConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends Command
{
    protected $input, $output;

    /**
     * @var string
     */
    protected static $defaultName = null;

    /**
     * @var string
     */
    protected static $defaultArguments = null;

    /**
     * @inheritDoc
     */
    public static function getDefaultName()
    {
        if (!static::$defaultName) {
            throw new \DomainException("Please provide a default command name for ".static::class." class");
            return Command::FAILURE;
        }
        return parent::getDefaultName();
    }

    public static function getDefaultArguments()
    {
        if (!static::$defaultArguments) {
            throw new \DomainException("Please provide some default arguments for ".static::class." class");
        }
        return static::$defaultArguments;
    }

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        return Command::FAILURE; // Default value
    }

    protected function sendError(string $message)
    {
        $message = "ERROR: $message";
        $this->output->writeln($message);
    }   
}

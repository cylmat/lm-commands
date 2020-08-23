<?php

namespace LmConsole\Command;

use DomainException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends Command
{
    protected $input;
    
    protected $output;

    /** @var string */
    protected static $defaultName;

    /** @var string */
    protected static $defaultArguments;

    /**
     * {@inheritDoc}
     */
    public static function getDefaultName(): string
    {
        if (! static::$defaultName) {
            throw new DomainException("Please provide a default command name for " . static::class . " class");
        }
        return parent::getDefaultName();
    }

    public static function getDefaultArguments(): string
    {
        if (! static::$defaultArguments) {
            throw new DomainException("Please provide some default arguments for " . static::class
                . " class. Did you omit the argument 's'?");
        }
        return static::$defaultArguments;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input  = $input;
        $this->output = $output;

        return Command::FAILURE; // Default value
    }

    protected function sendError(string $message)
    {
        $message = "ERROR: $message";
        $this->output->writeln($message);
    }
}

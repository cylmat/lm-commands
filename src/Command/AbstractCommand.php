<?php

namespace LmConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends Command
{
    protected $input, $output;

    protected static $defaultName = null;

    protected static $defaultArguments = null;

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

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        return Command::SUCCESS;
    }

    protected function sendError(string $message)
    {
        $message = "ERROR: $message";
        $this->output->writeln($message);
    }   
}

<?php

/**
 * Abstract command used in LmConsole
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Command;

use DomainException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends Command
{
    /** @var InputInterface */
    protected $input;
    
    /** @var OutputInterface */
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

    /**
     * Get a string displaying the defaults arguments used in this command
     */
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

    /**
     * Display an error message to output
     */
    protected function sendError(string $message): void
    {
        $message = "ERROR: $message";
        $this->output->writeln($message);
    }
}

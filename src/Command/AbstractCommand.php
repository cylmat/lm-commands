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
use LmConsole\Traits\{ConfigTrait, DisplayTrait, ToolsTrait};
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends Command
{
    // Traits
    use DisplayTrait, ToolsTrait, ConfigTrait;

    /** @var InputInterface */
    protected $input;
    
    /** @var OutputInterface */
    protected $output;

    /** @var string */
    protected static $defaultName;

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
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input  = $input;
        $this->output = $output;

        return Command::FAILURE; // Default value
    }

    /**
     * Display head
     */
    public function displayHead(string $title): void
    {
        $subtitle = '=';

        $this->output->writeln("<comment>$title</comment>");
        $this->output->writeln($this->repeatPattern($subtitle, strlen($title)));
    }
}

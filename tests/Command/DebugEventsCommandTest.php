<?php

/**
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\{ArrayInput, InputDefinition, InputArgument};
use Symfony\Component\Console\Output\BufferedOutput;

class DebugEventsCommandTest extends TestCase
{
    protected $command;
    protected $output;
    protected $definition;

    public function setUp(): void
    {
        $this->command = new DebugEventsCommand;
        $this->output  = new BufferedOutput;

        $this->definition = new InputDefinition(array_merge(
            [new InputArgument('command', InputArgument::REQUIRED)],
            $this->command->getDefinition()->getArguments()
        ));
    }

    public function testDefault()
    {
        $input = new ArrayInput([
            'command'    => 'debug:events'
        ], $this->definition);
        
        $this->command->execute($input, $this->output);
        echo "\n" . $this->output->fetch();

        $this->expectOutputRegex("/Priority | Callable/");
    }

    public function testWithRoute()
    {
        $input = new ArrayInput([
            'command'    => 'debug:events',
            'route_url' => '/test1'
        ], $this->definition);
        
        $this->command->execute($input, $this->output);
        echo "\n" . $this->output->fetch();

        $this->expectOutputRegex("/Priority | Callable/");
    }

    public function testWithRouteAndEvent()
    {
        $input = new ArrayInput([
            'command'    => 'debug:events',
            'route_url' => '/test1',
            'event_name' => 'bootstrap'
        ], $this->definition);
        
        $this->command->execute($input, $this->output);
        echo "\n" . $this->output->fetch();

        $this->expectOutputRegex("/\[bootstrap\]/");
    }

    public function testWithErrorEvent()
    {
        $input = new ArrayInput([
            'command'    => 'debug:events',
            'route_url' => '/test1',
            'event_name' => 'a_bootstrap'
        ], $this->definition);

        $this->expectException(\RuntimeException::class);
        
        $this->command->execute($input, $this->output);
        echo "\n" . $this->output->fetch();
    }
}

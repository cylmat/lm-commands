<?php

/**
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Command;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Input\{ArrayInput, InputDefinition, InputArgument};
use Symfony\Component\Console\Output\BufferedOutput;

class DebugRoutesCommandTest extends TestCase
{
    protected $command;
    protected $output;
    protected $definition;

    public function setUp(): void
    {
        $this->command = new DebugRoutesCommand;
        $this->output  = new BufferedOutput;

        $this->definition = new InputDefinition(array_merge(
            [new InputArgument('command', InputArgument::REQUIRED)],
            $this->command->getDefinition()->getArguments(),
            $this->command->getDefinition()->getOptions()
        ));
    }

    public function testDefault()
    {
        $input = new ArrayInput([
            'command'    => 'debug:routes'
        ], $this->definition);

        $this->command->execute($input, $this->output);
        echo "\n" . $this->output->fetch();

        $this->expectOutputRegex("/testing-url-2/");
    }

    public function testWithRoute()
    {
        $input = new ArrayInput([
            'command'    => 'debug:routes',
            'route_name' => 'test1',
        ], $this->definition);
        
        $this->command->execute($input, $this->output);
        echo "\n" . $this->output->fetch();

        $this->expectOutputRegex("/testing-url-1/");
    }

    public function testWithFalseRoute()
    {
        $input = new ArrayInput([
            'command'    => 'debug:routes',
            'route_name' => 'no_exists_route',
        ], $this->definition);
        
        $this->expectException(RuntimeException::class);
        
        $this->command->execute($input, $this->output);
        echo "\n" . $this->output->fetch();
    }
}

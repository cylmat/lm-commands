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

class DebugRoutesCommandTest extends TestCase
{
    protected $command;
    protected $output;
    protected $definition;

    public function setUp(): void
    {
        $this->command = new DebugRoutesCommand;
        $this->output  = new BufferedOutput;

        $this->definition = new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED),
            new InputArgument('route_name', InputArgument::REQUIRED),
        ]);
    }

    public function testExecute()
    {
        $this->expectException(\Symfony\Component\Console\Exception\RuntimeException::class);

        $input = new ArrayInput([
            'command'    => 'debug:routes'
        ], $this->definition);
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
}

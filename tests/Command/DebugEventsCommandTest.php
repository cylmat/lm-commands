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
    public function setUp(): void
    {
    }

    public function testExecute()
    {
        $command = new DebugEventsCommand();
        $output  = new BufferedOutput();

        $definition = new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED),
            new InputArgument('route_name', InputArgument::OPTIONAL),
            new InputArgument('event_name', InputArgument::OPTIONAL),
        ]);

        $input = new ArrayInput([
            'command'    => 'debug:events',
            'route_name' => '/',
            'event_name' => '',
        ], $definition);
        
        $command->execute($input, $output);
        echo "\n" . $output->fetch();

        $this->expectOutputRegex("/Priority | Callable/");
    }
}

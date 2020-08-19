<?php

namespace LmConsole\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\{ArrayInput, InputDefinition, InputArgument};
use Symfony\Component\Console\Output\BufferedOutput;

class DebugRoutesCommandTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testExecute()
    {
        $command = new DebugRoutesCommand();
        $output  = new BufferedOutput();

        $definition = new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED),
            new InputArgument('route_name', InputArgument::REQUIRED)
        ]);

        $input   = new ArrayInput([
            'command'    => 'debug:routes',
            'route_name' => 'test1',
        ], $definition);
        
        $command->execute($input, $output);
        echo "\n" . $output->fetch();

        $this->expectOutputRegex("/testing-url-1/");
    }
}

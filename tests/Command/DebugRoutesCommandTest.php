<?php

namespace LmConsole\Command;

use \Symfony\Component\Console\Input\{ArrayInput, InputDefinition, InputArgument, InputOption};

class DebugRoutesCommandTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {

    }

    public function testExecute()
    {
        $command = new DebugRoutesCommand;
        $input = new ArrayInput([
            'command' => 'debug:routes', 
            'route_name' => 'test1'
        ], new InputDefinition([
                new InputArgument('command', InputArgument::REQUIRED),
                new InputArgument('route_name', InputArgument::REQUIRED)
            ])
        );
        $output = new \Symfony\Component\Console\Output\BufferedOutput;
        $command->execute($input, $output);
        echo "\n".$output->fetch();

        $this->expectOutputRegex("/testing-url-1/");
    }
}
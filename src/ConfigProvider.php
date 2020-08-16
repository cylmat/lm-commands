<?php

namespace LmConsole;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'laminas-cli' => $this->getCliConfig(),
        ];
    }

    public function getCliConfig(): array
    {
        return [
            'commands' => [
                'debug:routes [module]' => Command\DebugRoutesCommand::class,
            ],
        ];
    }
}

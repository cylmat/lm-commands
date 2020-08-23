<?php

namespace LmConsole;

class Module
{
    public function getConfig(): array
    {
        return (new ConfigProvider)();
    }
}

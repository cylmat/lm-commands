<?php

/**
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsoleTest;

class Module
{
    public function getConfig(): array
    {
        return (new ConfigProvider)();
    }
}

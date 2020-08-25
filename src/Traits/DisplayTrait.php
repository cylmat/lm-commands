<?php

/**
 * Display lines
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Traits;

trait DisplayTrait
{
    /**
     * Get rendered pattern line
     */
    protected function getPatternLine(int $leftSize, int $centerSize): string
    {
        $cross = '+';
        $dash  = '-';

        return $cross . $this->repeatPattern($dash, $leftSize)
                . $cross . $this->repeatPattern($dash, $centerSize)
                . $cross . PHP_EOL;
    }

    /**
     * Get rendered text line
     */
    protected function getTextLine(string $leftText, int $leftSize, string $centerText, int $centerSize): string
    {
        $pipe = '|';

        return $pipe . str_pad($leftText, $leftSize, ' ') . $pipe
                . str_pad($centerText, $centerSize, ' ') . $pipe . PHP_EOL;
    }

    /**
     * Get a repeated $pattern of $size
     */
    protected function repeatPattern(string $pattern, int $size): string
    {
        return str_pad('', $size, $pattern);
    }

    /**
     * Display an error message to output
     */
    protected function sendError(string $message): void
    {
        $message = "ERROR: $message";
        $this->output->writeln($message);
    }
}
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
     * Display head
     */
    public function displayHead(string $title): void
    {
        $subtitle = '=';

        $this->output->writeln("\n<comment>$title</comment>");
        $this->output->writeln($this->repeatPattern($subtitle, strlen($title)));
    }

    /**
     * Get rendered pattern line
     */
    protected function getPatternLine(int $leftSize, int $centerSize=null, ?int $rightSize=null): string
    {
        $cross = '+';
        $dash  = '-';

        return $cross . $this->repeatPattern($dash, $leftSize)
                . ($centerSize ? $cross . $this->repeatPattern($dash, $centerSize) : '') // Center
                . ($rightSize ? $cross . $this->repeatPattern($dash, $rightSize) : '') // Right column
                . $cross . PHP_EOL;
    }

    /**
     * Get rendered text line
     */
    protected function getTextLine(
        string $leftText, int $leftSize, 
        string $centerText=null, int $centerSize=null,
        ?string $rightText=null, ?int $rightSize=null): string
    {
        $pipe = '|';

        return $pipe
                . str_pad($leftText, $leftSize, ' ') . $pipe
                . ($centerSize ? str_pad($centerText, $centerSize, ' ') . $pipe : '') // Center
                . ($rightSize ? str_pad($rightText, $rightSize, ' ') . $pipe : '') // Right column
                . PHP_EOL;
    }

    /**
     * Get a repeated $pattern of $size
     */
    protected function repeatPattern(string $pattern, int $size): string
    {
        return str_pad('', $size, $pattern);
    }

    /**
     * Get max propertie text size
     * 
     * @param int $type=0|1 Get max size from (0: keys or 1: values), default is 1 for values
     */
    protected function getMaxLength(array $array, int $type=1): int
    {
        $max = 0;
        foreach ($array as $key => $value) {
            if (0 === $type) {
                if (strlen($key) > $max) {
                    $max = strlen($key); 
                }
            } elseif (1 === $type) {
                if (strlen($value) > $max) {
                    $max = strlen($value); 
                }
            }
        }
        return $max;
    }

    /**
     * Display an error message to output
     */
    protected function sendError(string $message): void
    {
        $message = "ERROR: $message \n";
        $this->output->writeln($message);
    }
}

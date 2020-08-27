<?php

/**
 * Tools
 *
 * @license https://opensource.org/licenses/MIT License
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LmConsole\Traits;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

trait ToolsTrait
{
    /**
     * Change array to iterator
     */
    protected function toArrayIterator(array $array): RecursiveIteratorIterator
    {
        $iter = new RecursiveArrayIterator($array);
        return new RecursiveIteratorIterator($iter, RecursiveIteratorIterator::SELF_FIRST);
    }

    /**
     * Return child value if $key found
     *
     * @return null|mixed
     */
    protected function getFoundChild(?string $key, array $parentArray)
    {
        if (! $key) {
            return null;
        }
        if (! is_array($parentArray) || is_a($parentArray, 'Iterator')) {
            return null;
        }
        foreach ($parentArray as $k => $child) {
            if ($k === $key) {
                return $child;
            }
        }
        return null;
    }

    /**
     * Check levenstein for one item against list
     */
    protected function checkArgSpell(string $argName, array $itemsList, int $count=5): array
    {
        // Results founds
        $results = [];
        foreach ($itemsList as $item) {
            if (levenshtein($argName, $item) < $count) {
                $results[] = $item;
            }
        }
        return $results;
    }
}

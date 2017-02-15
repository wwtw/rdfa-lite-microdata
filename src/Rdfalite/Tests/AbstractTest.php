<?php

/**
 * rdfa-lite
 *
 * @category    Jkphl
 * @package     Jkpl\Rdfalite
 * @subpackage  Jkpl\Rdfalite\Tests
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2017 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2017 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

namespace Jkphl\Rdfalite\Tests;

/**
 * Abstract test base class
 *
 * @package Jkpl\Rdfalite
 * @subpackage Jkpl\Rdfalite\Tests
 */
abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests if two arrays equal in their keys and values
     *
     * @param array $expected Expected result
     * @param array $actual Actual result
     * @param string $message Message
     */
    public function assertArrayEquals(array $expected, array $actual, $message = '')
    {
        $this->assertEquals(
            $this->sortArrayForComparison($expected),
            $this->sortArrayForComparison($actual),
            $message
        );
    }

    /**
     * Recursively sort an array for comparison with another array
     *
     * @param array $array Array
     * @return array                Sorted array
     */
    protected function sortArrayForComparison(array $array)
    {
        // Tests if all array keys are numeric
        $allNumeric = true;
        foreach (array_keys($array) as $key) {
            if (!is_numeric($key)) {
                $allNumeric = false;
                break;
            }
        }

        // If not all keys are numeric: Sort the array by key
        if (!$allNumeric) {
            ksort($array, SORT_STRING);
            return $this->sortArrayRecursive($array);
        }

        // Sort them by data type and value
        $array = $this->sortArrayRecursive($array);
        usort(
            $array,
            function (
                $first,
                $second
            ) {
                $aType = gettype($first);
                $bType = gettype($second);
                if ($aType === $bType) {
                    switch ($aType) {
                        case 'array':
                            return strcmp(implode('', array_keys($first)), implode('', array_keys($second)));
                        case 'object':
                            return strcmp(spl_object_hash($first), spl_object_hash($second));
                        default:
                            return strcmp(strval($first), strval($second));
                    }
                }

                return strcmp($aType, $bType);
            }
        );

        return $array;
    }

    /**
     * Recursively sort an array for comparison
     *
     * @param array $array Original array
     * @return array Sorted array
     */
    protected function sortArrayRecursive(array $array)
    {

        // Run through all elements and sort them recursively if they are an array
        reset($array);
        while (list($key, $value) = each($array)) {
            if (is_array($value)) {
                $array[$key] = $this->sortArrayForComparison($value);
            }
        }

        return $array;
    }

    /**
     * Cast a value as a (multidimensional) array
     *
     * @param mixed $value Value
     * @return array Cast value
     */
    protected function castArray($value)
    {
        return json_decode(json_encode((array)$value), true);
    }
}

<?php

/**
 * rdfa-lite
 *
 * @category Jkphl
 * @package Jkphl\Rdfalite
 * @subpackage Jkphl\Rdfalite\Tests
 * @author Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright Copyright © 2017 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
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

namespace Jkphl\Rdfalite\Tests\Ports;

use Jkphl\Rdfalite\Ports\Parser\RdfaLite\Html;

/**
 * Parser tests
 *
 * @package Jkphl\Rdfalite
 * @subpackage Jkphl\Rdfalite\Tests
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test parsing an RDFa Lite HTML file
     */
    public function testRdfaLiteHtmlFile()
    {
        $things = Html::parseFile(
            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'article-rdfa-lite.html'
        );
//        print_r($things);
    }

    /**
     * Test an invalid file
     *
     * @expectedException \Jkphl\Rdfalite\Ports\Exceptions\RuntimeException
     * @expectedExceptionCode 1487190674
     */
    public function testInvalidFile()
    {
        Html::parseFile('invalid');
    }

    /**
     * Test a runtime exception
     *
     * @expectedException \Jkphl\Rdfalite\Ports\Exceptions\RuntimeException
     */
//    public function testRuntimeException()
//    {
//        Html::parseFile(
//            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'empty-default-vocab-rdfa-lite.html'
//        );
//    }

    /**
     * Test a out of bounds exception
     *
     * @expectedException \Jkphl\Rdfalite\Ports\Exceptions\OutOfBoundsException
     */
//    public function testOutOfBoundsException()
//    {
//        Html::parseFile(
//            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'unknown-vocab-prefix-rdfa-lite.html'
//        );
//    }
}

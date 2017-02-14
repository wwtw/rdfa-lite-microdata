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

namespace Jkphl\Rdfalite\Tests\Domain;

use Jkphl\Rdfalite\Domain\Property\Property;
use Jkphl\Rdfalite\Domain\Vocabulary\Vocabulary;

/**
 * Property tests
 *
 * @package Jkphl\Rdfalite
 * @subpackage Jkphl\Rdfalite\Tests
 */
class PropertyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the property instantiation
     */
    public function testProperty()
    {
        $vocabulary = new Vocabulary(VocabularyTest::SCHEMA_ORG);
        $property = new Property('test', $vocabulary, 'value');
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('test', $property->getName());
        $this->assertEquals('value', $property->getValue());
        $this->assertEquals($vocabulary, $property->getVocabulary());
    }

    /**
     * Test invalid property name
     *
     * @expectedException \Jkphl\Rdfalite\Domain\Exceptions\RuntimeException
     * @expectedExceptionCode 1486848618
     */
    public function testInvalidPropertyName()
    {
        $vocabulary = new Vocabulary(VocabularyTest::SCHEMA_ORG);
        new Property('', $vocabulary, 'value');
    }
}

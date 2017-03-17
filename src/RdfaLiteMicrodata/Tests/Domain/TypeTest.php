<?php

/**
 * rdfa-lite-microdata
 *
 * @category Jkphl
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Tests
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

namespace Jkphl\RdfaLiteMicrodata\Tests\Domain;

use Jkphl\RdfaLiteMicrodata\Domain\Iri\Iri;
use Jkphl\RdfaLiteMicrodata\Domain\Type\Type;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\Vocabulary;

/**
 * Type tests
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Tests
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the type instantiation
     */
    public function testProperty()
    {
        $vocabulary = new Vocabulary(VocabularyTest::SCHEMA_ORG_URI);
        $type = new Type('test', $vocabulary);
        $this->assertInstanceOf(Type::class, $type);
        $this->assertEquals('test', $type->getType());
        $this->assertEquals($vocabulary, $type->getVocabulary());

        $typeIri = $type->toIri();
        $this->assertInstanceOf(Iri::class, $typeIri);
        $this->assertEquals(VocabularyTest::SCHEMA_ORG_URI, $typeIri->getProfile());
        $this->assertEquals('test', $typeIri->getName());
        $this->assertEquals(VocabularyTest::SCHEMA_ORG_URI.'test', strval($typeIri));
    }

    /**
     * Test invalid type
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Domain\Exceptions\RuntimeException
     * @expectedExceptionCode 1487435276
     */
    public function testInvalidPropertyName()
    {
        $vocabulary = new Vocabulary(VocabularyTest::SCHEMA_ORG_URI);
        new Type('', $vocabulary);
    }
}

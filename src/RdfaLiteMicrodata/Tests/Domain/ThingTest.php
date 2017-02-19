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

use Jkphl\RdfaLiteMicrodata\Application\Parser\NullVocabulary;
use Jkphl\RdfaLiteMicrodata\Domain\Property\Property;
use Jkphl\RdfaLiteMicrodata\Domain\Property\PropertyInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\Thing;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\ThingInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Type\Type;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\Vocabulary;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;

/**
 * Thing tests
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Tests
 */
class ThingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * schema.org vocabulary
     *
     * @var Vocabulary
     */
    protected static $schemaOrgVocabulary;

    /**
     * Setup all tests
     */
    public static function setUpBeforeClass()
    {
        self::$schemaOrgVocabulary = new Vocabulary(VocabularyTest::SCHEMA_ORG_URI);
    }

    /**
     * Test the instantiation of a minimum thing
     */
    public function testMinimumThing()
    {
        $type = new Type('Person', self::$schemaOrgVocabulary);
        $thing = new Thing($type);
        $this->assertInstanceOf(Thing::class, $thing);
        $this->assertEquals([$type], $thing->getTypes());
        $this->assertNull($thing->getResourceId());
        $this->assertTrue(is_array($thing->getProperties()));
        $this->assertEquals(0, count($thing->getProperties()));
    }

    /**
     * Test the resource ID
     */
    public function testResourceId()
    {
        $type = new Type('Person', self::$schemaOrgVocabulary);
        $thing = new Thing($type, 'bob');
        $this->assertInstanceOf(Thing::class, $thing);
        $this->assertEquals('bob', $thing->getResourceId());
    }

    /**
     * Test the thing instantiation with an invalid type
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Domain\Exceptions\RuntimeException
     * @expectedExceptionCode 1487435964
     */
    public function testInvalidTypeThing()
    {
        new Thing([null]);
    }

    /**
     * Test the instantiation of an anonymous thing
     */
    public function testAnonymousThing()
    {
        $thing = new Thing([]);
        $this->assertInstanceOf(Thing::class, $thing);
        $this->assertEquals([], $thing->getTypes());
    }

    /**
     * Test adding a property
     */
    public function testAddProperty()
    {
        $type = new Type('Person', self::$schemaOrgVocabulary);
        $thing = new Thing($type);
        $this->assertInstanceOf(Thing::class, $thing);

        $vocabulary = new Vocabulary(VocabularyTest::SCHEMA_ORG_URI);
        $property1 = new Property('test1', $vocabulary, 'value1');
        $property2 = new Property('test1', $vocabulary, 'value2');
        $property3 = new Property('test2', $vocabulary, 'value1');
        $thing->addProperty($property1);
        $thing->addProperty($property2);
        $thing->addProperty($property3);

        $this->validateProperties($thing, $vocabulary);
        $this->validateProperty($thing, $vocabulary, $property1, $property2);
    }

    /**
     * Validate all properties
     *
     * @param ThingInterface $thing Thing
     * @param VocabularyInterface $vocabulary Vocabulary
     */
    protected function validateProperties(ThingInterface $thing, VocabularyInterface $vocabulary)
    {
        $properties = $thing->getProperties();
        $this->assertTrue(is_array($properties));
        $this->assertTrue(array_key_exists($vocabulary->expand('test1'), $properties));
        $this->assertTrue(array_key_exists($vocabulary->expand('test2'), $properties));
        $this->assertEquals(2, count($properties));
    }

    /**
     * Validate a single property
     *
     * @param ThingInterface $thing Thing
     * @param VocabularyInterface $vocabulary Vocabulary
     * @param PropertyInterface $property1 First property
     * @param PropertyInterface $property2 Second property
     */
    protected function validateProperty(
        ThingInterface $thing,
        VocabularyInterface $vocabulary,
        PropertyInterface $property1,
        PropertyInterface $property2
    ) {
        $test1Property = $thing->getProperty('test1', $vocabulary);
        $this->assertTrue(is_array($test1Property));
        $this->assertEquals(2, count($test1Property));
        $this->assertEquals([$property1, $property2], $test1Property);
    }

    /**
     * Test getting an invalid property name
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Domain\Exceptions\OutOfBoundsException
     * @expectedExceptionCode 1486849016
     */
    public function testGetInvalidPropertyName()
    {
        $type = new Type('Person', self::$schemaOrgVocabulary);
        $thing = new Thing($type);
        $this->assertInstanceOf(Thing::class, $thing);
        $thing->getProperty('invalid', new NullVocabulary());
    }
}

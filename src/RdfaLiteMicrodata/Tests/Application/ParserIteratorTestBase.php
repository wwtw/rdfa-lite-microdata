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

namespace Jkphl\RdfaLiteMicrodata\Tests\Application;

use Jkphl\RdfaLiteMicrodata\Domain\Property\Property;
use Jkphl\RdfaLiteMicrodata\Domain\Property\PropertyInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Property\PropertyListInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\ThingInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Type\Type;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\Vocabulary;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;
use Jkphl\RdfaLiteMicrodata\Tests\AbstractTest;
use Jkphl\RdfaLiteMicrodata\Tests\Domain\VocabularyTest;

/**
 * Base class for parser & iterator tests
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Tests
 */
abstract class ParserIteratorTestBase extends AbstractTest
{
    /**
     * Test HTML with person RDFa Lite 1.1
     *
     * @var string
     */
    protected static $personRdfa;
    /**
     * Fixture directory
     *
     * @var string
     */
    protected static $fixtures;

    /**
     * Setup all tests
     */
    public static function setUpBeforeClass()
    {
        self::$fixtures = dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR;
        self::$personRdfa = file_get_contents(self::$fixtures.'person-rdfa-lite.html');
    }

    /**
     * Validate parsing results
     *
     * @param ThingInterface[] $things Parsing Results
     */
    protected function validatePersonResult(array $things)
    {
        $schemaOrgVocabulary = new Vocabulary(VocabularyTest::SCHEMA_ORG_URI);
        $foafVocabulary = new Vocabulary('http://xmlns.com/foaf/0.1/');

        $this->assertTrue(is_array($things));
        $this->assertEquals(1, count($things));
        $thing = $things[0];
        $type = new Type('Person', $schemaOrgVocabulary);
        $foafType = new Type('Person', $foafVocabulary);

        $this->assertInstanceOf(ThingInterface::class, $thing);
        $this->assertEquals([$type, $foafType], $thing->getTypes());
        $this->assertEquals('#joschi', $thing->getResourceId());

        $this->validateProperties($thing, $schemaOrgVocabulary);
    }

    /**
     * Validate properties
     *
     * @param ThingInterface $thing Thing
     * @param VocabularyInterface $schemaOrgVocabulary schema.org vocabulary
     */
    protected function validateProperties(ThingInterface $thing, VocabularyInterface $schemaOrgVocabulary)
    {
        $properties = $thing->getProperties();
        $this->assertInstanceOf(PropertyListInterface::class, $properties);
        $this->assertEquals(4, count($properties));

        $this->validateProperty($thing, 'name', $schemaOrgVocabulary, 'Joschi Kuphal');
        $this->validateProperty($thing, 'telephone', $schemaOrgVocabulary, '+49 911 9593945');
        $this->validateProperty($thing, 'image', $schemaOrgVocabulary, 'https://jkphl.is/avatar.jpg');
        $this->validateProperty($thing, 'preferredAnimal', new Vocabulary('http://open.vocab.org/terms/'), 'Unicorn');
    }

    /**
     * Validate a single property
     *
     * @param ThingInterface $thing Thing
     * @param string $name Property name
     * @param VocabularyInterface $vocabulary Property vocabulary
     * @param string $value Property value
     */
    protected function validateProperty(ThingInterface $thing, $name, VocabularyInterface $vocabulary, $value)
    {
        $property = $thing->getProperty($name, $vocabulary);
        $this->assertTrue(is_array($property));
        $this->assertEquals(1, count($property));
        $this->assertInstanceOf(PropertyInterface::class, $property[0]);
        $this->assertEquals(new Property($name, $vocabulary, $value), $property[0]);
    }
}

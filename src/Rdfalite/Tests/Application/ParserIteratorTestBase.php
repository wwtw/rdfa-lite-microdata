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

namespace Jkphl\Rdfalite\Tests\Application;

use Jkphl\Rdfalite\Domain\Property\Property;
use Jkphl\Rdfalite\Domain\Property\PropertyInterface;
use Jkphl\Rdfalite\Domain\Thing\ThingInterface;
use Jkphl\Rdfalite\Domain\Vocabulary\Vocabulary;
use Jkphl\Rdfalite\Tests\Domain\VocabularyTest;

/**
 * Base class for parser & iterator tests
 *
 * @package Jkphl\Rdfalite
 * @subpackage Jkphl\Rdfalite\Tests
 */
abstract class ParserIteratorTestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * Test HTML
     *
     * @var string
     */
    protected static $html;

    /**
     * Setup all tests
     */
    public static function setUpBeforeClass()
    {
        self::$html = file_get_contents(
            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'rdfa-lite-1.1.html'
        );
    }

    /**
     * Validate parsing results
     *
     * @param ThingInterface[] $things Parsing Results
     */
    protected function validateIteratorResult(array $things)
    {
        $schemaOrgVocabulary = new Vocabulary(VocabularyTest::SCHEMA_ORG);

        $this->assertTrue(is_array($things));
        $this->assertEquals(1, count($things));
        $thing = $things[0];

        $this->assertInstanceOf(ThingInterface::class, $thing);
        $this->assertEquals(VocabularyTest::SCHEMA_ORG.'Person', $thing->getType());
        $this->assertEquals($schemaOrgVocabulary, $thing->getVocabulary());
        $this->assertEquals('#joschi', $thing->getResourceId());

        $properties = $thing->getProperties();
        $this->assertTrue(is_array($properties));
        $this->assertEquals(4, count($properties));

        $name = $thing->getProperty('name');
        $this->assertTrue(is_array($name));
        $this->assertEquals(1, count($name));
        $this->assertInstanceOf(PropertyInterface::class, $name[0]);
        $this->assertEquals(new Property('name', $schemaOrgVocabulary, 'Joschi Kuphal'), $name[0]);

        $telephone = $thing->getProperty('telephone');
        $this->assertTrue(is_array($telephone));
        $this->assertEquals(1, count($telephone));
        $this->assertInstanceOf(PropertyInterface::class, $telephone[0]);
        $this->assertEquals(new Property('telephone', $schemaOrgVocabulary, '+49 911 9593945'), $telephone[0]);

        $image = $thing->getProperty('image');
        $this->assertTrue(is_array($image));
        $this->assertEquals(1, count($image));
        $this->assertInstanceOf(PropertyInterface::class, $image[0]);
        $this->assertEquals(new Property('image', $schemaOrgVocabulary, 'https://jkphl.is/avatar.jpg'), $image[0]);

        $preferredAnimal = $thing->getProperty('preferredAnimal');
        $this->assertTrue(is_array($preferredAnimal));
        $this->assertEquals(1, count($preferredAnimal));
        $this->assertInstanceOf(PropertyInterface::class, $preferredAnimal[0]);
        $this->assertEquals(new Property('preferredAnimal', new Vocabulary('http://open.vocab.org/terms/'), 'Unicorn'),
            $preferredAnimal[0]);
    }
}

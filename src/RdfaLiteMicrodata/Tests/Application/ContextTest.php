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

use Jkphl\RdfaLiteMicrodata\Application\Context\RdfaLiteContext;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\Thing;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\Vocabulary;
use Jkphl\RdfaLiteMicrodata\Tests\Domain\VocabularyTest;

/**
 * Parser context tests
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Tests
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test parser context instantiation
     */
    public function testContext()
    {
        $context = new RdfaLiteContext();
        $this->assertInstanceOf(RdfaLiteContext::class, $context);
        $this->assertTrue($context->hasVocabulary('schema'));

        // Test vocabulary retrieval
        $schemaOrgVocabulary = $context->getVocabulary('schema');
        $this->assertInstanceOf(Vocabulary::class, $schemaOrgVocabulary);
        $this->assertEquals($schemaOrgVocabulary, new Vocabulary(VocabularyTest::SCHEMA_ORG_URI));
    }

    /**
     * Test the registration of a vocabulary
     */
    public function testContextVocabRegistration()
    {
        $randomPrefix = 'random'.rand();
        $context = new RdfaLiteContext();
        $this->assertInstanceOf(RdfaLiteContext::class, $context);
        $this->assertFalse($context->hasVocabulary($randomPrefix));
        $contextHash = spl_object_hash($context);

        $sameContext = $context->registerVocabulary('schema', VocabularyTest::SCHEMA_ORG_URI);
        $this->assertEquals($contextHash, spl_object_hash($sameContext));

        $newContext = $sameContext->registerVocabulary($randomPrefix, 'http://example.com');
        $this->assertNotEquals($contextHash, spl_object_hash($newContext));
        $this->assertEquals(new Vocabulary('http://example.com'), $newContext->getVocabulary($randomPrefix));
    }

    /**
     * Test an invalid vocabulary prefix
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Application\Exceptions\RuntimeException
     * @expectedExceptionCode 1486927326
     */
    public function testInvalidVocabularyPrefix()
    {
        $context = new RdfaLiteContext();
        $this->assertInstanceOf(RdfaLiteContext::class, $context);
        $context->registerVocabulary('', null);
    }

    /**
     * Test an invalid vocabulary URI
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Domain\Exceptions\RuntimeException
     * @expectedExceptionCode 1486823170
     */
    public function testInvalidVocabularyUri()
    {
        $context = new RdfaLiteContext();
        $this->assertInstanceOf(RdfaLiteContext::class, $context);
        $context->registerVocabulary('test', null);
    }

    /**
     * Test an invalid vocabulary URI
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Application\Exceptions\OutOfBoundsException
     * @expectedExceptionCode 1486928423
     */
    public function testUnknownVocabularyPrefix()
    {
        $context = new RdfaLiteContext();
        $this->assertInstanceOf(RdfaLiteContext::class, $context);
        $context->getVocabulary('random'.rand());
    }

    /**
     * Test the default vocabulary
     */
    public function testDefaultVocabulary()
    {
        $context = new RdfaLiteContext();
        $this->assertInstanceOf(RdfaLiteContext::class, $context);
        $vocabulary = new Vocabulary(VocabularyTest::SCHEMA_ORG_URI);
        $newContext = $context->setDefaultVocabulary($vocabulary);
        $this->assertEquals($vocabulary, $newContext->getDefaultVocabulary());
        $this->assertNotEquals(spl_object_hash($context), spl_object_hash($newContext));
        $this->assertEquals(
            spl_object_hash($newContext),
            spl_object_hash($newContext->setDefaultVocabulary($vocabulary))
        );
    }

    /**
     * Test the parent thing
     */
    public function testParentThing()
    {
        $context = new RdfaLiteContext();
        $this->assertInstanceOf(RdfaLiteContext::class, $context);
        $vocabulary = new Vocabulary(VocabularyTest::SCHEMA_ORG_URI);
        $thing = new Thing('Person', $vocabulary);
        $newContext = $context->setParentThing($thing);
        $this->assertEquals($thing, $newContext->getParentThing());
        $this->assertNotEquals(spl_object_hash($context), spl_object_hash($newContext));
        $this->assertEquals(
            spl_object_hash($newContext),
            spl_object_hash($newContext->setParentThing($thing))
        );
    }

    /**
     * Test adding children
     */
    public function testAddingLevelChildren()
    {
        $context = new RdfaLiteContext();
        $this->assertInstanceOf(RdfaLiteContext::class, $context);
        $vocabulary = new Vocabulary(VocabularyTest::SCHEMA_ORG_URI);

        $thing = new Thing('Person', $vocabulary);
        $context->addChild($thing);
        $this->assertEquals([$thing], $context->getChildren());
        $context->addChild($thing);
        $this->assertEquals([$thing], $context->getChildren());

        $parent = new Thing('Person', $vocabulary);
        $newContext = $context->setParentThing($parent);
        $this->assertEquals([], $newContext->getChildren());
        $newContext->addChild($thing);
        $this->assertEquals([$thing], $newContext->getChildren());
        $newContext->addChild($thing);
        $this->assertEquals([$thing], $newContext->getChildren());
    }
}




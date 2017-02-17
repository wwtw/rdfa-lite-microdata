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
use Jkphl\RdfaLiteMicrodata\Application\Parser\Parser;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Factories\HtmlDocumentFactory;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Parser\RdfaLiteElementProcessor;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Service\ThingGateway;

/**
 * Parser tests
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Tests
 */
class ParserTest extends ParserIteratorTestBase
{
    /**
     * Test person parsing
     */
    public function testPerson()
    {
        $htmlDocumentFactory = new HtmlDocumentFactory();
        $rdfaElementProcessor = new RdfaLiteElementProcessor(true);
        $rdfaContext = new RdfaLiteContext();
        $parser = new Parser($htmlDocumentFactory, $rdfaElementProcessor, $rdfaContext);
        $this->assertInstanceOf(Parser::class, $parser);

        $things = $parser->parse(self::$personRdfa);
        $this->validatePersonResult($things);
    }

    /**
     * Test article parsing
     */
    public function testArticle()
    {
        $htmlDocumentFactory = new HtmlDocumentFactory();
        $rdfaElementProcessor = new RdfaLiteElementProcessor(true);
        $rdfaContext = new RdfaLiteContext();
        $parser = new Parser($htmlDocumentFactory, $rdfaElementProcessor, $rdfaContext);
        $this->assertInstanceOf(Parser::class, $parser);

        $things = $parser->parse(
            file_get_contents(
                dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'article-rdfa-lite.html'
            )
        );
        $this->assertArrayEquals(
            $this->castArray(
                json_decode(
                    file_get_contents(
                        dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'article-rdfa-lite.json'
                    )
                )
            ),
            $this->castArray((new ThingGateway())->export($things))
        );
    }

    /**
     * Test empty default vocabulary parsing
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Infrastructure\Exceptions\RuntimeException
     * @expectedExceptionCode 1487030264
     */
    public function testEmptyDefaultVocabulary()
    {
        $htmlDocumentFactory = new HtmlDocumentFactory();
        $rdfaElementProcessor = new RdfaLiteElementProcessor(true);
        $rdfaContext = new RdfaLiteContext();
        $parser = new Parser($htmlDocumentFactory, $rdfaElementProcessor, $rdfaContext);
        $this->assertInstanceOf(Parser::class, $parser);

        $parser->parse(
            file_get_contents(
                dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'empty-default-vocab-rdfa-lite.html'
            )
        );
    }

    /**
     * Test unknown vocabulary prefix parsing
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Infrastructure\Exceptions\OutOfBoundsException
     * @expectedExceptionCode 1486928423
     */
    public function testUnknownVocabularyPrefix()
    {
        $htmlDocumentFactory = new HtmlDocumentFactory();
        $rdfaElementProcessor = new RdfaLiteElementProcessor(true);
        $rdfaContext = new RdfaLiteContext();
        $parser = new Parser($htmlDocumentFactory, $rdfaElementProcessor, $rdfaContext);
        $this->assertInstanceOf(Parser::class, $parser);

        $parser->parse(
            file_get_contents(
                dirname(
                    __DIR__
                ).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'unknown-vocab-prefix-rdfa-lite.html'
            )
        );
    }
}

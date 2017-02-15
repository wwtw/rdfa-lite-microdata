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

use Jkphl\Rdfalite\Application\Parser\Parser;
use Jkphl\Rdfalite\Infrastructure\Factories\HtmlDocumentFactory;
use Jkphl\Rdfalite\Infrastructure\Parser\RdfaliteElementProcessor;
use Jkphl\Rdfalite\Infrastructure\Service\ThingGateway;

/**
 * Parser tests
 *
 * @package Jkphl\Rdfalite
 * @subpackage Jkphl\Rdfalite\Tests
 */
class ParserTest extends ParserIteratorTestBase
{
    /**
     * Test person parsing
     */
    public function testPerson()
    {
        $htmlDocumentFactory = new HtmlDocumentFactory();
        $rdfaElementProcessor = new RdfaliteElementProcessor();
        $parser = new Parser($htmlDocumentFactory, $rdfaElementProcessor);
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
        $rdfaElementProcessor = new RdfaliteElementProcessor();
        $parser = new Parser($htmlDocumentFactory, $rdfaElementProcessor);
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
     * @expectedException \Jkphl\Rdfalite\Infrastructure\Exceptions\RuntimeException
     * @expectedExceptionCode 1487030264
     */
    public function testEmptyDefaultVocabulary()
    {
        $htmlDocumentFactory = new HtmlDocumentFactory();
        $rdfaElementProcessor = new RdfaliteElementProcessor();
        $parser = new Parser($htmlDocumentFactory, $rdfaElementProcessor);
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
     * @expectedException \Jkphl\Rdfalite\Infrastructure\Exceptions\OutOfBoundsException
     * @expectedExceptionCode 1486928423
     */
    public function testUnknownVocabularyPrefix()
    {
        $htmlDocumentFactory = new HtmlDocumentFactory();
        $rdfaElementProcessor = new RdfaliteElementProcessor();
        $parser = new Parser($htmlDocumentFactory, $rdfaElementProcessor);
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

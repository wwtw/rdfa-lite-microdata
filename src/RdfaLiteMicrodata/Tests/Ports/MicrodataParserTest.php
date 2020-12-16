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

namespace Jkphl\RdfaLiteMicrodata\Tests\Ports;

use Jkphl\RdfaLiteMicrodata\Infrastructure\Factories\HtmlDocumentFactory;
use Jkphl\RdfaLiteMicrodata\Ports\Parser\Microdata;
use Jkphl\RdfaLiteMicrodata\Tests\AbstractTest;

/**
 * Microdata parser tests
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Tests
 */
class MicrodataParserTest extends AbstractTest
{
    /**
     * Test parsing a Microdata HTML file
     */
    public function testMicrodataHtmlFile()
    {
        $things = (new Microdata())->parseHtmlFile(
            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'movie-microdata.html'
        );
        $this->assertArrayEquals(
            $this->castArray(
                json_decode(
                    file_get_contents(
                        dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'movie-microdata.json'
                    )
                )
            ),
            $this->castArray($things)
        );
    }

    /**
     * Test parsing a Microdata HTML file with types / properties as IRIs
     */
    public function testMicrodataHtmlFileWithIris()
    {
        $things = (new Microdata(true))->parseHtmlFile(
            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'movie-microdata.html'
        );
        $this->assertArrayEquals(
            $this->castArray(
                json_decode(
                    file_get_contents(
                        dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'movie-microdata-iri.json'
                    )
                )
            ),
            $this->castArray($things)
        );
    }

    /**
     * Test parsing a Microdata HTML DOM
     */
    public function testMicrodataHtmlDom()
    {
        $htmlDomFactory = new HtmlDocumentFactory();
        $dom = $htmlDomFactory->createDocumentFromSource(
            file_get_contents(
                dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'movie-microdata.html'
            )
        );
        $things = (new Microdata())->parseDom($dom);
        $this->assertArrayEquals(
            $this->castArray(
                json_decode(
                    file_get_contents(
                        dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'movie-microdata.json'
                    )
                )
            ),
            $this->castArray($things)
        );
    }

    /**
     * Test parsing a Microdata HTML DOM containing the additionalType itemprop
     *
     * "An additional type for the item, typically used for adding more specific 
     *  types from external vocabularies in microdata syntax."
     * 
     * @see http://schema.org/additionalType
     */
    public function testMicrodataWithAdditionalType()
    {
        $htmlDomFactory = new HtmlDocumentFactory();

        $dom = $htmlDomFactory->createDocumentFromSource(
            file_get_contents(
                dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'additional-type-microdata.html'
            )
        );

        $things = (new Microdata())->parseDom($dom);

        $this->assertCount(2, $things->items);
        $this->assertCount(2, $things->items[0]->type);
        $this->assertContains('http://schema.org/Book', $things->items[0]->type);
        $this->assertContains('http://schema.org/Product', $things->items[0]->type);
    }

    /**
     * Test an anonymous item with property references
     */
    public function testAnonymousItemWithPropertyRefs()
    {
        $things = (new Microdata())->parseHtmlFile(
            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'anonymous-ref-microdata.html'
        );
        $this->assertArrayEquals(
            $this->castArray(
                json_decode(
                    file_get_contents(
                        dirname(
                            __DIR__
                        ).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'anonymous-ref-microdata.json'
                    )
                )
            ),
            $this->castArray($things)
        );
    }

    /**
     * Test an invalid file
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Ports\Exceptions\RuntimeException
     * @expectedExceptionCode 1487190674
     */
    public function testInvalidFile()
    {
        (new Microdata())->parseHtmlFile('invalid');
    }
}

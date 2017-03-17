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
use Jkphl\RdfaLiteMicrodata\Ports\Parser\RdfaLite;
use Jkphl\RdfaLiteMicrodata\Tests\AbstractTest;

/**
 * RDFa Lite parser tests
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Tests
 */
class RdfaLiteParserTest extends AbstractTest
{
    /**
     * Test parsing an RDFa Lite HTML file
     */
    public function testRdfaLiteHtmlFile()
    {
        $things = (new RdfaLite())->parseHtmlFile(
            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'article-rdfa-lite.html'
        );
        $this->assertArrayEquals(
            $this->castArray(
                json_decode(
                    file_get_contents(
                        dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'article-rdfa-lite.json'
                    )
                )
            ),
            $this->castArray($things)
        );
    }

    /**
     * Test parsing an RDFa Lite HTML file with types / properties as IRIs
     */
    public function testRdfaLiteHtmlFileWithIris()
    {
        $things = (new RdfaLite(true))->parseHtmlFile(
            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'article-rdfa-lite.html'
        );
        $this->assertArrayEquals(
            $this->castArray(
                json_decode(
                    file_get_contents(
                        dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR
                        .'article-rdfa-lite-iri.json'
                    )
                )
            ),
            $this->castArray($things)
        );
    }

    /**
     * Test parsing an RDFa Lite DOM
     */
    public function testRdfaLiteDom()
    {
        $htmlDomFactory = new HtmlDocumentFactory();
        $dom = $htmlDomFactory->createDocumentFromSource(
            file_get_contents(
                dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'article-rdfa-lite.html'
            )
        );
        $things = (new RdfaLite())->parseDom($dom);
        $this->assertArrayEquals(
            $this->castArray(
                json_decode(
                    file_get_contents(
                        dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'article-rdfa-lite.json'
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
        (new RdfaLite())->parseHtmlFile('invalid');
    }

    /**
     * Test an XML runtime exception
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Ports\Exceptions\RuntimeException
     */
    public function testHTMLRuntimeException()
    {
        (new RdfaLite())->parseHtmlFile(
            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'empty-default-vocab-rdfa-lite.html'
        );
    }

    /**
     * Test an HTML out of bounds exception
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Ports\Exceptions\OutOfBoundsException
     */
    public function testHTMLOutOfBoundsException()
    {
        (new RdfaLite())->parseHtmlFile(
            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'unknown-vocab-prefix-rdfa-lite.html'
        );
    }

    /**
     * Test parsing an RDFa Lite XML (XHTML) file
     */
    public function testRdfaLiteXmlFile()
    {
        $things = (new RdfaLite())->parseXmlFile(
            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'typed-property-rdfa-lite.xhtml'
        );
        $this->assertArrayEquals(
            $this->castArray(
                json_decode(
                    file_get_contents(
                        dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'
                        .DIRECTORY_SEPARATOR.'typed-property-rdfa-lite.json'
                    )
                )
            ),
            $this->castArray($things)
        );
    }


    /**
     * Test an XML runtime exception
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Ports\Exceptions\RuntimeException
     */
    public function testXMLRuntimeException()
    {
        (new RdfaLite())->parseXmlFile(
            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'empty-default-vocab-rdfa-lite.html'
        );
    }

    /**
     * Test an XML out of bounds exception
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Ports\Exceptions\OutOfBoundsException
     */
    public function testXMLOutOfBoundsException()
    {
        (new RdfaLite())->parseXmlFile(
            dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'unknown-vocab-prefix-rdfa-lite.html'
        );
    }
}

<?php

/**
 * rdfa-lite
 *
 * @category Jkphl
 * @package Jkphl\Micrometa
 * @subpackage Infrastructure
 * @author Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright Copyright © 2017 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

namespace Jkphl\RdfaLiteMicrodata\Tests\Infrastructure;

use Jkphl\RdfaLiteMicrodata\Infrastructure\Exceptions\HtmlParsingException;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Factories\HtmlDocumentFactory;

/**
 * DOM document factory tests
 *
 * @package Jkphl\Micrometa
 * @subpackage Jkphl\RdfaLiteMicrodata\Tests
 */
class HtmlDocumentFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the HTML document factory
     */
    public function testHtmlDocument()
    {
        $htmlDocumentFactory = new HtmlDocumentFactory();
        $htmlSource = '<html><head><title>Test</title></head><body><article>Test</article></body></html>';
        $this->assertInstanceOf(\DOMDocument::class, $htmlDocumentFactory->createDocumentFromSource($htmlSource));
    }

    /**
     * Test the HTML document factory with an invalid element
     *
     * @expectedException \Jkphl\RdfaLiteMicrodata\Infrastructure\Exceptions\HtmlParsingException
     */
    public function testHtmlDocumentInvalidElement()
    {
        $htmlDocumentFactory = new HtmlDocumentFactory();
        $htmlSource = '<html><head><title>Test</title></head><body><invalid>Test</invalid></body></html>';
        $htmlDocumentFactory->createDocumentFromSource($htmlSource);
    }

    /**
     * Test the HTML document factory parsing error
     */
    public function testHtmlDocumentParsingError()
    {
        try {
            $htmlDocumentFactory = new HtmlDocumentFactory();
            $htmlSource = '<html><head><title>Test</title></head><body><invalid>Test</invalid></body></html>';
            $htmlDocumentFactory->createDocumentFromSource($htmlSource);
        } catch (HtmlParsingException $e) {
            $parsingError = $e->getParsingError();
            $this->assertInstanceOf(\LibXMLError::class, $parsingError);
            $this->assertEquals(2, $parsingError->level);
            $this->assertEquals(801, $parsingError->code);
            $this->assertEquals('Tag invalid invalid', trim($parsingError->message));
        }
    }

    /**
     * Test a custom HTML parsing error handler
     */
    public function testHtmlDocumentCustomErrorHandler()
    {
        $customErrorHandler = function (\LibXMLError $error) {
            return ($error->level == 2) && ($error->code == 801) && (trim($error->message) == 'Tag invalid invalid');
        };
        $htmlDocumentFactory = new HtmlDocumentFactory($customErrorHandler);
        $htmlSource = '<html><head><title>Test</title></head><body><invalid>Test</invalid></body></html>';
        $htmlDocumentFactory->createDocumentFromSource($htmlSource);
    }
}

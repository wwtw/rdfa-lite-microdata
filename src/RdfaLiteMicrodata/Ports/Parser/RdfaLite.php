<?php

/**
 * rdfa-lite-microdata
 *
 * @category Jkphl
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Ports
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

namespace Jkphl\RdfaLiteMicrodata\Ports\Parser;

use Jkphl\RdfaLiteMicrodata\Application\Context\RdfaLiteContext;
use Jkphl\RdfaLiteMicrodata\Application\Parser\Parser;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Factories\HtmlDocumentFactory;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Factories\XmlDocumentFactory;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Parser\RdfaLiteElementProcessor;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Service\ThingGateway;
use Jkphl\RdfaLiteMicrodata\Ports\Exceptions\OutOfBoundsException;
use Jkphl\RdfaLiteMicrodata\Ports\Exceptions\RuntimeException;

/**
 * RDFa Lite 1.1 parser
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Ports
 * @see https://www.w3.org/TR/rdfa-lite/
 */
class RdfaLite extends AbstractParser
{
    /**
     * Parse an XML file
     *
     * @param string $file XML file path
     * @return array Extracted things
     */
    public static function parseXmlFile($file)
    {
        return self::parseXmlString(self::getFileContents($file));
    }

    /**
     * Parse an XML string
     *
     * @param string $string XML string
     * @return array Extracted things
     */
    public static function parseXmlString($string)
    {
        try {
            $xmlDocumentFactory = new XmlDocumentFactory();
            $rdfaElementProcessor = new RdfaLiteElementProcessor(false);
            $rdfaContext = new RdfaLiteContext();
            $parser = new Parser($xmlDocumentFactory, $rdfaElementProcessor, $rdfaContext);
            $things = $parser->parse($string);
            $gateway = new ThingGateway();
            return $gateway->export($things);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException($e->getMessage(), $e->getCode());
        } catch (\RuntimeException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Parse an HTML file
     *
     * @param string $file HTML file path
     * @return array Extracted things
     */
    public static function parseHtmlFile($file)
    {
        return self::parseHtmlString(self::getFileContents($file));
    }

    /**
     * Parse an HTML string
     *
     * @param string $string HTML string
     * @return array Extracted things
     */
    public static function parseHtmlString($string)
    {
        try {
            $htmlDocumentFactory = new HtmlDocumentFactory();
            $rdfaElementProcessor = new RdfaLiteElementProcessor(true);
            $rdfaContext = new RdfaLiteContext();
            $parser = new Parser($htmlDocumentFactory, $rdfaElementProcessor, $rdfaContext);
            $things = $parser->parse($string);
            $gateway = new ThingGateway();
            return $gateway->export($things);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException($e->getMessage(), $e->getCode());
        } catch (\RuntimeException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }
    }
}

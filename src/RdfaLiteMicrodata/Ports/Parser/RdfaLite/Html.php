<?php

/**
 * rdfa-lite-microdata
 *
 * @category    Jkphl
 * @package     Jkphl\RdfaLiteMicrodata
 * @subpackage  Jkphl\RdfaLiteMicrodata\Ports
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2017 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
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

namespace Jkphl\RdfaLiteMicrodata\Ports\Parser\RdfaLite;

use Jkphl\RdfaLiteMicrodata\Application\Parser\Parser;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Factories\HtmlDocumentFactory;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Parser\ParserInterface;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Parser\RdfaLiteElementProcessor;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Service\ThingGateway;
use Jkphl\RdfaLiteMicrodata\Ports\Exceptions\OutOfBoundsException;
use Jkphl\RdfaLiteMicrodata\Ports\Exceptions\RuntimeException;

/**
 * Html parser
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Ports
 */
class Html implements ParserInterface
{

    /**
     * Parse a file
     *
     * @param string $file File
     * @return array Extracted things
     */
    public static function parseFile($file)
    {
        // If the file does not exist
        if (!is_readable($file)) {
            throw new RuntimeException(
                sprintf(RuntimeException::INVALID_FILE_STR, $file),
                RuntimeException::INVALID_FILE
            );
        }

        return static::parseString(file_get_contents($file));
    }

    /**
     * Parse a string
     *
     * @param string $string String
     * @return array Extracted things
     */
    public static function parseString($string)
    {
        try {
            $htmlDocumentFactory = new HtmlDocumentFactory();
            $rdfaElementProcessor = new RdfaLiteElementProcessor();
            $parser = new Parser($htmlDocumentFactory, $rdfaElementProcessor);
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

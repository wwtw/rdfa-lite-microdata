<?php

/**
 * rdfa-lite
 *
 * @category Jkphl
 * @package Jkphl\Rdfalite
 * @subpackage Jkphl\Rdfalite\Domain
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

namespace Jkphl\Rdfalite\Domain\Vocabulary;

use Jkphl\Rdfalite\Domain\Exceptions\RuntimeException;

/**
 * Vocabulary
 *
 * @package Jkphl\Rdfalite
 * @subpackage Jkphl\Rdfalite\Domain
 */
class Vocabulary implements VocabularyInterface
{
    /**
     * URI where the vocabulary can be found
     *
     * @var string
     */
    public $uri;

    /**
     * Vocabulary constructor
     *
     * @param string $uri URI where the vocabulary can be found
     */
    public function __construct($uri)
    {
        $this->uri = self::validateVocabUri($uri);
    }

    /**
     * Validate a vocabulary URI
     *
     * @param string $uri URI
     * @return string Valid vocabulary URI
     * @throws RuntimeException If the vocabulary URI is invalid
     */
    public static function validateVocabUri($uri)
    {
        $uri = trim($uri);

        // If the vocabulary URI is invalid
        if (!strlen($uri) || !filter_var($uri, FILTER_VALIDATE_URL)) {
            throw new RuntimeException(
                sprintf(RuntimeException::INVALID_VOCABULARY_URI_STR, $uri),
                RuntimeException::INVALID_VOCABULARY_URI
            );
        }

        return $uri;
    }

    /**
     * Return the vocabulary URI
     *
     * @return string URI
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Expand a local type
     *
     * @param string $type Local type
     * @return string Expanded local type
     */
    public function expand($type)
    {
        return $this->uri.$type;
    }
}

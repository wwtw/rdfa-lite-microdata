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

namespace Jkphl\Rdfalite\Domain;

/**
 * Thing
 *
 * @package Jkphl\Rdfalite
 * @subpackage Jkphl\Rdfalite\Domain
 */
class Thing
{
    /**
     * Resource type
     *
     * @var string
     */
    protected $type;
    /**
     * Resource vocabulary
     *
     * @var Vocabulary
     */
    protected $vocabulary;
    /**
     * Resource ID
     *
     * @var string|null
     */
    protected $id = null;

    /**
     * Thing constructor
     *
     * @param string $type Resource type
     * @param Vocabulary $vocabulary Vocabulary in use
     * @param null|string $id Resource id
     */
    public function __construct($type, Vocabulary $vocabulary, $id = null)
    {
        $type = trim($type);
        if (!strlen($type)) {
            throw new Exception(
                sprintf(Exception::INVALID_RESOURCE_TYPE_STR, $type, $vocabulary->getUrl()),
                Exception::INVALID_RESOURCE_TYPE
            );
        }

        $this->type = $type;
        $this->vocabulary = $vocabulary;
        $this->id = $id;
    }

    /**
     * Return the resource type
     *
     * @return string Resource type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return the vocabulary in use
     *
     * @return Vocabulary Vocabulary
     */
    public function getVocabulary()
    {
        return $this->vocabulary;
    }

    /**
     * Return the resource ID
     *
     * @return null|string Resource ID
     */
    public function getId()
    {
        return $this->id;
    }
}

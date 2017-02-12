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

namespace Jkphl\Rdfalite\Domain\Thing;

use Jkphl\Rdfalite\Domain\Vocabulary\VocabularyInterface;

/**
 * Thing interface
 *
 * @package Jkphl\Rdfalite
 * @subpackage Jkphl\Rdfalite\Domain
 */
interface ThingInterface
{
    /**
     * Return the resource type
     *
     * @return string Resource type
     */
    public function getType();

    /**
     * Return the vocabulary in use
     *
     * @return VocabularyInterface Vocabulary
     */
    public function getVocabulary();

    /**
     * Return the resource ID
     *
     * @return null|string Resource ID
     */
    public function getResourceId();

    /**
     * Add a property value
     *
     * @param string $name Property name
     * @param mixed $value Property value
     * @return Thing Self reference
     */
    public function addProperty($name, $value);

    /**
     * Return all properties
     *
     * @return array Properties
     */
    public function getProperties();

    /**
     * Return the values of a single property
     *
     * @param string $name Property name
     * @return array Property values
     */
    public function getProperty($name);

    /**
     * Add a child
     *
     * @param ThingInterface $child Child
     * @return Thing Self reference
     */
    public function addChild(ThingInterface $child);

    /**
     * Return all children
     *
     * @return Thing[] Children
     */
    public function getChildren();
}

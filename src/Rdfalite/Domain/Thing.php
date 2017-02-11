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
class Thing implements ThingInterface
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
     * @var VocabularyInterface
     */
    protected $vocabulary;
    /**
     * Resource ID
     *
     * @var string|null
     */
    protected $id = null;
    /**
     * Child things
     *
     * @var Thing[]
     */
    protected $children = [];
    /**
     * Property
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Thing constructor
     *
     * @param string $type Resource type
     * @param VocabularyInterface $vocabulary Vocabulary in use
     * @param null|string $id Resource id
     */
    public function __construct($type, VocabularyInterface $vocabulary, $id = null)
    {
        $type = trim($type);
        if (!strlen($type)) {
            throw new RuntimeException(
                sprintf(RuntimeException::INVALID_RESOURCE_TYPE_STR, $type, $vocabulary->getUrl()),
                RuntimeException::INVALID_RESOURCE_TYPE
            );
        }

        $this->vocabulary = $vocabulary;
        $this->type = $this->vocabulary->expand($type);
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
     * @return VocabularyInterface Vocabulary
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

    /**
     * Add a property value
     *
     * @param string $name Property name
     * @param mixed $value Property value
     * @return Thing Self reference
     */
    public function addProperty($name, $value)
    {
        $name = $this->validatePropertyName($name);

        // Create the property values list if necessary
        if (!array_key_exists($name, $this->properties)) {
            $this->properties[$name] = [];
        }

        // Register the property value
        $this->properties[$name][] = $value;

        return $this;
    }

    /**
     * Validate a property name
     *
     * @param string $name Property name
     * @return string Sanitized property name
     * @throws RuntimeException If the property name is invalid
     */
    protected function validatePropertyName($name)
    {
        $name = trim($name);

        // If the property name is invalid
        if (!strlen($name) || !preg_match('/^[a-z][a-zA-Z0-9]*$/', $name)) {
            throw new RuntimeException(
                sprintf(RuntimeException::INVALID_PROPERTY_NAME_STR, $name), RuntimeException::INVALID_PROPERTY_NAME
            );
        }

        return $name;
    }

    /**
     * Return all properties
     *
     * @return array Properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Return the values of a single property
     *
     * @param string $name Property name
     * @return array Property values
     * @throws OutOfBoundsException If the property name is unknown
     */
    public function getProperty($name)
    {
        $name = $this->validatePropertyName($name);

        // If the property name is unknown
        if (!array_key_exists($name, $this->properties)) {
            throw new OutOfBoundsException(
                sprintf(OutOfBoundsException::UNKNOWN_PROPERTY_NAME_STR, $name),
                OutOfBoundsException::UNKNOWN_PROPERTY_NAME
            );
        }

        return $this->properties[$name];
    }

    /**
     * Add a child
     *
     * @param ThingInterface $child Child
     * @return Thing Self reference
     */
    public function addChild(ThingInterface $child)
    {
        $this->children[spl_object_hash($child)] = $child;
        return $this;
    }

    /**
     * Return all children
     *
     * @return Thing[] Children
     */
    public function getChildren()
    {
        return array_values($this->children);
    }
}

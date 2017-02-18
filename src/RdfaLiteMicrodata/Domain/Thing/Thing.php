<?php

/**
 * rdfa-lite-microdata
 *
 * @category Jkphl
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Domain
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

namespace Jkphl\RdfaLiteMicrodata\Domain\Thing;

use Jkphl\RdfaLiteMicrodata\Domain\Exceptions\OutOfBoundsException;
use Jkphl\RdfaLiteMicrodata\Domain\Exceptions\RuntimeException;
use Jkphl\RdfaLiteMicrodata\Domain\Property\PropertyInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Property\PropertyService;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;

/**
 * Thing
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Domain
 */
class Thing implements ThingInterface
{
    /**
     * Resource types
     *
     * @var string[]
     */
    protected $types;
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
    protected $resourceId = null;
    /**
     * Child things
     *
     * @var ThingInterface[]
     */
    protected $children = [];
    /**
     * Property
     *
     * @var array[]
     */
    protected $properties = [];

    /**
     * Thing constructor
     *
     * @param string|array $types Resource type(s)
     * @param VocabularyInterface $vocabulary Vocabulary in use
     * @param null|string $resourceId Resource id
     */
    public function __construct($types, VocabularyInterface $vocabulary, $resourceId = null)
    {
        $types = array_filter(array_map('trim', (array)$types), function ($t) {
            return strlen($t) > 0;
        });
        if (!count($types)) {
            throw new RuntimeException(
                sprintf(
                    RuntimeException::INVALID_RESOURCE_TYPES_STR,
                    "['".implode("', '", $types)."']",
                    $vocabulary->getUri()
                ),
                RuntimeException::INVALID_RESOURCE_TYPES
            );
        }

        $this->vocabulary = $vocabulary;
        $this->types = $types;
        $this->resourceId = $resourceId;
    }

    /**
     * Return the resource types
     *
     * @return string Resource types
     */
    public function getTypes()
    {
        return $this->types;
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
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Add a property value
     *
     * @param PropertyInterface $property Property
     * @return Thing Self reference
     */
    public function addProperty(PropertyInterface $property)
    {
        $name = $property->getVocabulary()->expand($property->getName());

        // Create the property values list if necessary
        if (!array_key_exists($name, $this->properties)) {
            $this->properties[$name] = [];
        }

        // Register the property value
        $this->properties[$name][] = $property;

        return $this;
    }

    /**
     * Return all properties
     *
     * @return array[] Properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Return the values of a single property
     *
     * @param string $name Property name
     * @param VocabularyInterface $vocabulary Vocabulary
     * @return array Property values
     */
    public function getProperty($name, VocabularyInterface $vocabulary)
    {
        $name = (new PropertyService())->validatePropertyName($name);
        $name = $vocabulary->expand($name);

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
     * @return ThingInterface[] Children
     */
    public function getChildren()
    {
        return array_values($this->children);
    }
}

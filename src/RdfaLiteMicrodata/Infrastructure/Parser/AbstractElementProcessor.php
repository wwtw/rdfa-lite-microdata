<?php

/**
 * rdfa-lite-microdata
 *
 * @category Jkphl
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Infrastructure
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

namespace Jkphl\RdfaLiteMicrodata\Infrastructure\Parser;

use Jkphl\RdfaLiteMicrodata\Application\Context\ContextInterface;
use Jkphl\RdfaLiteMicrodata\Application\Contract\ElementProcessorInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Property\Property;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\Thing;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\ThingInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Type\Type;
use Jkphl\RdfaLiteMicrodata\Domain\Type\TypeInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Exceptions\OutOfBoundsException;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Exceptions\RuntimeException;

/**
 * Abstract element processor
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Infrastructure
 */
abstract class AbstractElementProcessor implements ElementProcessorInterface
{
    /**
     * First property
     *
     * @var int
     */
    const PROPERTY_FIRST = 1;
    /**
     * Last property
     *
     * @var int
     */
    const PROPERTY_LAST = 2;
    /**
     * Tag name / attribute map
     *
     * @var array
     */
    protected static $tagNameAttributes = [
        'META' => 'content',
        'AUDIO' => 'src',
        'EMBED' => 'src',
        'IFRAME' => 'src',
        'IMG' => 'src',
        'SOURCE' => 'src',
        'TRACK' => 'src',
        'VIDEO' => 'src',
        'A' => 'href',
        'AREA' => 'href',
        'LINK' => 'href',
        'OBJECT' => 'data',
        'DATA' => 'value',
        'METER' => 'value',
        'TIME' => 'datetime'
    ];
    /**
     * Property cache
     *
     * @var array
     */
    protected static $propertyCache = [];
    /**
     * HTML mode
     *
     * @var boolean
     */
    protected $html;

    /**
     * Element processor constructor
     *
     * @param bool $html HTML mode
     */
    public function __construct($html = false)
    {
        $this->html = boolval($html);
    }

    /**
     * Process a DOM element's child
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Context
     * @return ContextInterface Context for children
     */
    public function processElementChildren(\DOMElement $element, ContextInterface $context)
    {
        // Process a child
        return $this->processChild($element, $context);
    }

    /**
     * Create a nested child
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Context
     * @return ContextInterface Context for children
     */
    abstract protected function processChild(\DOMElement $element, ContextInterface $context);

    /**
     * Create a property
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Inherited Context
     * @return ContextInterface Local context for this element
     */
    abstract protected function processProperty(\DOMElement $element, ContextInterface $context);

    /**
     * Create a property by prefix and name
     *
     * @param string $prefix Property prefix
     * @param string $name Property name
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Inherited Context
     * @param int $mode Property mode
     * @return ContextInterface Local context for this element
     */
    protected function processPropertyPrefixName(
        $prefix,
        $name,
        \DOMElement $element,
        ContextInterface $context,
        $mode
    ) {
        $vocabulary = $this->getVocabulary($prefix, $context);
        if ($vocabulary instanceof VocabularyInterface) {
            $context = $this->addProperty($element, $context, $name, $vocabulary, $mode);
        }

        return $context;
    }

    /**
     * Return a vocabulary by prefix with fallback to the default vocabulary
     *
     * @param string $prefix Vocabulary prefix
     * @param ContextInterface $context Context
     * @return VocabularyInterface Vocabulary
     */
    abstract protected function getVocabulary($prefix, ContextInterface $context);

    /**
     * Add a single property
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Inherited Context
     * @param string $name Property name
     * @param VocabularyInterface $vocabulary Property vocabulary
     * @param int $mode Property mode
     * @return ContextInterface Local context for this element
     */
    protected function addProperty(
        \DOMElement $element,
        ContextInterface $context,
        $name,
        VocabularyInterface $vocabulary,
        $mode
    ) {
        $resourceId = $this->getResourceId($element);

        // Get the property value
        $propertyValue = $this->getPropertyValue($element, $context);
        $property = new Property($name, $vocabulary, $propertyValue, $resourceId);

        // Add the property to the current parent thing
        $context->getParentThing()->addProperty($property);

        // If the property value is a thing and this is the element's last property
        if (($propertyValue instanceof ThingInterface) && ($mode & self::PROPERTY_LAST)) {
            // Set the thing as parent thing for nested iterations
            $context = $context->setParentThing($propertyValue);
        }

        return $context;
    }

    /**
     * Return the resource ID
     *
     * @param \DOMElement $element DOM element
     * @return string|null Resource ID
     */
    abstract protected function getResourceId(\DOMElement $element);

    /**
     * Return a property value (type and tag name dependent)
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Context
     * @return ThingInterface|string Property value
     */
    protected function getPropertyValue(\DOMElement $element, ContextInterface $context)
    {
        $value = $this->getPropertyCache($element);
        if ($value !== null) {
            return $value;
        }

        $propertyChild = $this->getPropertyChildValue($element, $context);
        if ($propertyChild instanceof ThingInterface) {
            return $this->setPropertyCache($element, $propertyChild);
        }

        // Return a string property value
        return $this->setPropertyCache($element, $this->getPropertyStringValue($element));
    }

    /**
     * Return a cached property value (if available)
     *
     * @param \DOMElement $element Element
     * @return mixed|null Property value
     */
    protected function getPropertyCache(\DOMElement $element)
    {
        $elementHash = spl_object_hash($element);
        return isset(self::$propertyCache[$elementHash]) ? self::$propertyCache[$elementHash] : null;
    }

    /**
     * Return a property child value
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Context
     * @return ThingInterface|null Property child value
     */
    abstract protected function getPropertyChildValue(\DOMElement $element, ContextInterface $context);

    /**
     * Cache a property value
     *
     * @param \DOMElement $element DOM element
     * @param mixed $value Value
     * @return mixed $value Value
     */
    protected function setPropertyCache(\DOMElement $element, $value)
    {
        return self::$propertyCache[spl_object_hash($element)] = $value;
    }

    /**
     * Return a property value (type and tag name dependent)
     *
     * @param \DOMElement $element DOM element
     * @return ThingInterface|string Property value
     */
    protected function getPropertyStringValue(\DOMElement $element)
    {
        // If HTML mode is active
        if ($this->html) {
            $tagName = strtoupper($element->tagName);

            // Map to an attribute (if applicable)
            if (array_key_exists($tagName, self::$tagNameAttributes)) {
                $value = strval($element->getAttribute(self::$tagNameAttributes[$tagName]));
                if (($tagName != 'TIME') || !empty($value)) {
                    return $value;
                }
            }
        }

        // Return the text content
        return $element->textContent;
    }

    /**
     * Return a thing by typeof value
     *
     * @param string $typeof Thing type
     * @param string $resourceId Resource ID
     * @param ContextInterface $context Context
     * @return Thing Thing
     * @throws RuntimeException If the default vocabulary is empty
     */
    protected function getThing($typeof, $resourceId, ContextInterface $context)
    {
        /** @var TypeInterface[] $types */
        $types = [];
        foreach (preg_split('/\s+/', $typeof) as $prefixedType) {
            $types[] = $this->getType($prefixedType, $context);
        }

        return new Thing($types, $resourceId);
    }

    /**
     * Instanciate a type
     *
     * @param string $prefixedType Prefixed type
     * @param ContextInterface $context Context
     * @return TypeInterface Type
     */
    protected function getType($prefixedType, ContextInterface $context)
    {
        list($prefix, $typeName) = $this->getPrefixName($prefixedType);
        $vocabulary = $this->getVocabulary($prefix, $context);
        if ($vocabulary instanceof VocabularyInterface) {
            return new Type($typeName, $vocabulary);
        }

        // If the default vocabulary is empty
        if (empty($prefix)) {
            throw new RuntimeException(
                RuntimeException::EMPTY_DEFAULT_VOCABULARY_STR,
                RuntimeException::EMPTY_DEFAULT_VOCABULARY
            );
        }

        throw new OutOfBoundsException(
            sprintf(OutOfBoundsException::UNKNOWN_VOCABULARY_PREFIX_STR, $prefix),
            OutOfBoundsException::UNKNOWN_VOCABULARY_PREFIX
        );
    }

    /**
     * Split a value into a vocabulary prefix and a name
     *
     * @param string $prefixName Prefixed name
     * @return array Prefix and name
     */
    abstract protected function getPrefixName($prefixName);
}

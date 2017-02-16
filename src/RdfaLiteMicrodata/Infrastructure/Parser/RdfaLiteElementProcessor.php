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

use Jkphl\RdfaLiteMicrodata\Application\Contract\ElementProcessorInterface;
use Jkphl\RdfaLiteMicrodata\Application\Parser\Context;
use Jkphl\RdfaLiteMicrodata\Domain\Property\Property;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\Thing;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\ThingInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\Vocabulary;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Exceptions\OutOfBoundsException;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Exceptions\RuntimeException;

/**
 * RDFa Lite 1.1 element processor
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Infrastructure
 */
class RdfaLiteElementProcessor implements ElementProcessorInterface
{
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
        'TIME' => 'datetime'
    ];

    /**
     * Process a DOM element
     *
     * @param \DOMElement $element DOM element
     * @param Context $context Inherited Context
     * @return Context Local context for this element
     */
    public function processElement(\DOMElement $element, Context $context)
    {
        // Process default vocabulary registrations
        $context = $this->processVocab($element, $context);

        // Register vocabulary prefixes
        $context = $this->processPrefix($element, $context);

        // Create properties
        $context = $this->processProperty($element, $context);
        return $context;
    }

    /**
     * Process changes of the default vocabulary
     *
     * @param \DOMElement $element DOM element
     * @param Context $context Inherited Context
     * @return Context Local context for this element
     */
    protected function processVocab(\DOMElement $element, Context $context)
    {
        if ($element->hasAttribute('vocab')) {
            $defaultVocabulary = new Vocabulary($element->getAttribute('vocab'));
            $context = $context->setDefaultVocabulary($defaultVocabulary);
        }

        return $context;
    }

    /**
     * Process vocabulary prefixes
     *
     * @param \DOMElement $element DOM element
     * @param Context $context Inherited Context
     * @return Context Local context for this element
     */
    protected function processPrefix(\DOMElement $element, Context $context)
    {
        if ($element->hasAttribute('prefix')) {
            $prefixes = preg_split('/\s+/', $element->getAttribute('prefix'));
            while (count($prefixes)) {
                $prefix = rtrim(array_shift($prefixes), ':');
                $uri = array_shift($prefixes);
                $context = $context->registerVocabulary($prefix, $uri);
            }
        }

        return $context;
    }

    /**
     * Create properties
     *
     * @param \DOMElement $element DOM element
     * @param Context $context Inherited Context
     * @return Context Local context for this element
     */
    protected function processProperty(\DOMElement $element, Context $context)
    {
        $processProperty = $element->hasAttribute('property') && ($context->getParentThing() instanceof ThingInterface);
        if ($processProperty) {
            list($prefix, $name) = $this->splitProperty($element->getAttribute('property'));
            $vocabulary = empty($prefix) ? $context->getDefaultVocabulary() : $context->getVocabulary($prefix);
            if ($vocabulary instanceof VocabularyInterface) {
                $context = $this->addProperty($element, $context, $name, $vocabulary);
            }
        }

        return $context;
    }

    /**
     * Split a property into prefix and name
     *
     * @param string $property Prefixed property
     * @return array Prefix and name
     */
    protected function splitProperty($property)
    {
        $property = explode(':', $property);
        $name = strval(array_pop($property));
        $prefix = strval(array_pop($property));
        return [$prefix, $name];
    }

    /**
     * Create properties
     *
     * @param \DOMElement $element DOM element
     * @param Context $context Inherited Context
     * @param string $name Property name
     * @param VocabularyInterface $vocabulary Property vocabulary
     * @return Context Local context for this element
     */
    protected function addProperty(\DOMElement $element, Context $context, $name, VocabularyInterface $vocabulary)
    {
        // Try to get a resource ID
        $resourceId = trim($element->getAttribute('resource')) ?: null;

        // Get the property value
        $propertyValue = $this->getPropertyValue($element, $context);
        $property = new Property($name, $vocabulary, $propertyValue, $resourceId);

        // Add the property to the current parent thing
        $context->getParentThing()->addProperty($property);

        // If the property value is a thing
        if ($propertyValue instanceof ThingInterface) {
            // Set the thing as parent thing for nested iterations
            $context = $context->setParentThing($propertyValue);
        }

        return $context;
    }

    /**
     * Return a property value (type and tag name dependent)
     *
     * @param \DOMElement $element DOM element
     * @param Context $context Context
     * @return ThingInterface|string Property value
     */
    protected function getPropertyValue(\DOMElement $element, Context $context)
    {
        // If the property creates a new type: Return the element itself
        if ($element->hasAttribute('typeof')) {
            return $this->getThing(
                $element->getAttribute('typeof'),
                trim($element->getAttribute('resource')) ?: null,
                $context
            );
        }

        // Return a string property value
        return $this->getPropertyStringValue($element);
    }

    /**
     * Return a thing by typeof value
     *
     * @param string $typeof Thing type
     * @param string $resourceId Resource ID
     * @param Context $context Context
     * @return ThingInterface Thing
     */
    protected function getThing($typeof, $resourceId, Context $context)
    {
        $typeof = explode(':', $typeof);
        $type = array_pop($typeof);
        $prefix = array_pop($typeof);

        return $this->getThingByPrefixType($prefix, $type, $resourceId, $context);
    }

    /**
     * Return a thing by prefix and type
     *
     * @param string $prefix Prefix
     * @param string $type Type
     * @param string $resourceId Resource ID
     * @param Context $context Context
     * @return ThingInterface Thing
     * @throws RuntimeException If the default vocabulary is empty
     * @throws OutOfBoundsException If the vocabulary prefix is unknown
     */
    protected function getThingByPrefixType($prefix, $type, $resourceId, Context $context)
    {
        // Determine the vocabulary to use
        try {
            $vocabulary = empty($prefix) ? $context->getDefaultVocabulary() : $context->getVocabulary($prefix);
            if ($vocabulary instanceof VocabularyInterface) {
                // Return a new thing
                return new Thing($type, $vocabulary, $resourceId);
            }

            // If the default vocabulary is empty
            if (empty($prefix)) {
                throw new RuntimeException(
                    RuntimeException::EMPTY_DEFAULT_VOCABULARY_STR,
                    RuntimeException::EMPTY_DEFAULT_VOCABULARY
                );
            }
        } catch (\Jkphl\RdfaLiteMicrodata\Application\Exceptions\OutOfBoundsException $e) {
            // Promote to client level exception
        }

        throw new OutOfBoundsException(
            sprintf(OutOfBoundsException::UNKNOWN_VOCABULARY_PREFIX_STR, $prefix),
            OutOfBoundsException::UNKNOWN_VOCABULARY_PREFIX
        );
    }

    /**
     * Return a property value (type and tag name dependent)
     *
     * @param \DOMElement $element DOM element
     * @return ThingInterface|string Property value
     */
    protected function getPropertyStringValue(\DOMElement $element)
    {
        $tagName = strtoupper($element->tagName);

        // Map to an attribute (if applicable)
        if (array_key_exists($tagName, self::$tagNameAttributes)) {
            $value = strval($element->getAttribute(self::$tagNameAttributes[$tagName]));
            if (($tagName != 'TIME') || !empty($value)) {
                return $value;
            }
        }

        // Return the text content
//        trigger_error(sprintf('RDFa Lite 1.1 element processor: Unhandled tag name "%s"', $element->tagName), E_USER_WARNING);
        return $element->textContent;
    }

    /**
     * Process a DOM element's children
     *
     * @param \DOMElement $element DOM element
     * @param Context $context Context
     * @return Context Context for children
     */
    public function processElementChildren(\DOMElement $element, Context $context)
    {
        // Process nested children
        return $this->processTypeof($element, $context);
    }

    /**
     * Create nested children
     *
     * @param \DOMElement $element DOM element
     * @param Context $context Context
     * @return Context Context for children
     */
    protected function processTypeof(\DOMElement $element, Context $context)
    {
        if ($element->hasAttribute('typeof') && empty($element->getAttribute('property'))) {
            $thing = $this->getThing(
                $element->getAttribute('typeof'),
                trim($element->getAttribute('resource')) ?: null,
                $context
            );

            // Add the new thing as a child to the current context
            // and set the thing as parent thing for nested iterations
            $context = $context->addChild($thing)->setParentThing($thing);
        }

        return $context;
    }
}

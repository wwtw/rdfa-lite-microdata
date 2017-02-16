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

use Jkphl\RdfaLiteMicrodata\Application\Context\Context;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\ThingInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\Vocabulary;

/**
 * RDFa Lite 1.1 element processor
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Infrastructure
 */
class RdfaLiteElementProcessor extends AbstractElementProcessor
{
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

        // Create a property
        return $this->processProperty($element, $context);
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
     * Create a property
     *
     * @param \DOMElement $element DOM element
     * @param Context $context Inherited Context
     * @return Context Local context for this element
     */
    protected function processProperty(\DOMElement $element, Context $context)
    {
        if ($element->hasAttribute('property') && ($context->getParentThing() instanceof ThingInterface)) {
            list($prefix, $name) = $this->splitProperty($element->getAttribute('property'));
            $context = $this->processPropertyPrefixName($prefix, $name, $element, $context);
        }

        return $context;
    }

    /**
     * Create a nested child
     *
     * @param \DOMElement $element DOM element
     * @param Context $context Context
     * @return Context Context for children
     */
    protected function processChild(\DOMElement $element, Context $context)
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
     * Return the resource ID
     *
     * @param \DOMElement $element DOM element
     * @return string|null Resource ID
     */
    protected function getResourceId(\DOMElement $element)
    {
        return trim($element->getAttribute('resource')) ?: null;
    }
}

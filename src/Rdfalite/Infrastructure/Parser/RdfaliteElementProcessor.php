<?php

/**
 * rdfa-lite
 *
 * @category Jkphl
 * @package Jkphl\Rdfalite
 * @subpackage Jkphl\Rdfalite\Infrastructure
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

namespace Jkphl\Rdfalite\Infrastructure\Parser;

use Jkphl\Rdfalite\Application\Contract\ElementProcessorInterface;
use Jkphl\Rdfalite\Application\Parser\Context;
use Jkphl\Rdfalite\Domain\Thing\Thing;
use Jkphl\Rdfalite\Domain\Thing\ThingInterface;
use Jkphl\Rdfalite\Domain\Vocabulary\Vocabulary;
use Jkphl\Rdfalite\Domain\Vocabulary\VocabularyInterface;
use Jkphl\Rdfalite\Infrastructure\Exceptions\OutOfBoundsException;

/**
 * RDFa Lite 1.1 element processor
 *
 * @package Jkphl\Rdfalite
 * @subpackage Jkphl\Rdfalite\Infrastructure
 */
class RdfaliteElementProcessor implements ElementProcessorInterface
{
    /**
     * Process a DOM element
     *
     * @param \DOMElement $element DOM element
     * @param Context $context Context
     * @return Context Context for children
     */
    public function processElement(\DOMElement $element, Context $context)
    {
        // Process default vocabulary registrations
        $context = $this->processVocab($element, $context);

        // Register vocabulary prefixes
        $context = $this->processPrefix($element, $context);

        // Create properties
        if ($element->hasAttribute('property')) {
            return $this->processProperty($element, $context);
        }

        // Process nested children
        return $this->processTypeof($element, $context);
    }

    /**
     * Process changes of the default vocabulary
     *
     * @param \DOMElement $element DOM element
     * @param Context $context Context
     * @return Context Context for children
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
     * @param Context $context Context
     * @return Context Context for children
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
     * @param Context $context Context
     * @return Context Context for children
     */
    protected function processProperty(\DOMElement $element, Context $context)
    {
        if ($element->hasAttribute('property')) {
            $property = explode(':', $element->getAttribute('property'));
            $name = array_pop($property);
            $prefix = array_pop($property);

            // Determine the vocabulary to use
            $vocabulary = empty($prefix) ? $context->getDefaultVocabulary() : $context->getVocabulary($prefix);
            if ($vocabulary instanceof VocabularyInterface) {


                // Add the new thing as a child to the current context
//                $context->getParentThing()->addProperty($name);
            }
        }

        return $context;
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
        if ($element->hasAttribute('typeof')) {
            $thing = $this->getThing($element->getAttribute('typeof'), $context);

            if ($thing instanceof ThingInterface) {
                // Add the new thing as a child to the current context
                $context->addChild($thing);

                // Set the thing as parent thing for nested iterations
                $context = $context->setParentThing($thing);
            }
        }

        return $context;
    }

    /**
     * Return a property value (type and tag name dependent)
     *
     * @param string $typeof Thing type
     * @param Context $context Context
     * @return ThingInterface|NULL|string Property value
     * @throws OutOfBoundsException If the default vocabulary is empty
     * @throws OutOfBoundsException If the vocabulary prefix is unknown
     */
    protected function getThing($typeof, Context $context)
    {
        $typeof = explode(':', $typeof);
        $type = array_pop($typeof);
        $prefix = array_pop($typeof);

        // Determine the vocabulary to use
        $vocabulary = empty($prefix) ? $context->getDefaultVocabulary() : $context->getVocabulary($prefix);
        if ($vocabulary instanceof VocabularyInterface) {

            // Return a new thing
            return new Thing($type, $vocabulary);
        }

        // If the default vocabulary is empty
        if (empty($prefix)) {
            throw new OutOfBoundsException(
                OutOfBoundsException::EMPTY_DEFAULT_VOCABULARY_STR,
                OutOfBoundsException::EMPTY_DEFAULT_VOCABULARY
            );
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
     * @param Context $context Context
     * @return ThingInterface|NULL|string Property value
     */
    protected function getPropertyValue(\DOMElement $element, Context $context)
    {
        // If the property creates a new type: Return the element itself
        if ($element->hasAttribute('typeof')) {
            return $this->getThing($element->getAttribute('typeof'), $context);
        }

        // Else: Depend on the tag name
        switch (strtoupper($element->tagName)) {
            case 'META':
                return $element->getAttribute('content');
                break;
            case 'AUDIO':
            case 'EMBED':
            case 'IFRAME':
            case 'IMG':
            case 'SOURCE':
            case 'TRACK':
            case 'VIDEO':
                return $element->getAttribute('src');
                break;
            case 'A':
            case 'AREA':
            case 'LINK':
                return $element->getAttribute('href');
                break;
            case 'OBJECT':
                return $element->getAttribute('data');
                break;
            case 'DATA':
                return $element->getAttribute('value');
                break;
            case 'TIME':
                $datetime = $element->getAttribute('datetime');
                if (!empty($datetime)) {
                    return $datetime;
                }
            default:
//              trigger_error(sprintf('RDFa Lite 1.1 element processor: Unhandled tag name "%s"', $element->tagName), E_USER_WARNING);
                return $element->textContent;
        }
    }
}

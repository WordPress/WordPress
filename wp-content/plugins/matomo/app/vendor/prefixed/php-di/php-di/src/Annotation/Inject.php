<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Annotation;

use Matomo\Dependencies\DI\Definition\Exception\InvalidAnnotation;
/**
 * "Inject" annotation.
 *
 * Marks a property or method as an injection point
 *
 * @api
 *
 * @Annotation
 * @Target({"METHOD","PROPERTY"})
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
final class Inject
{
    /**
     * Entry name.
     * @var string
     */
    private $name;
    /**
     * Parameters, indexed by the parameter number (index) or name.
     *
     * Used if the annotation is set on a method
     * @var array
     */
    private $parameters = [];
    /**
     * @throws InvalidAnnotation
     */
    public function __construct(array $values)
    {
        // Process the parameters as a list AND as a parameter array (we don't know on what the annotation is)
        // @Inject(name="foo")
        if (isset($values['name']) && is_string($values['name'])) {
            $this->name = $values['name'];
            return;
        }
        // @Inject
        if (!isset($values['value'])) {
            return;
        }
        $values = $values['value'];
        // @Inject("foo")
        if (is_string($values)) {
            $this->name = $values;
        }
        // @Inject({...}) on a method
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                if (!is_string($value)) {
                    throw new InvalidAnnotation(sprintf('@Inject({"param" = "value"}) expects "value" to be a string, %s given.', json_encode($value)));
                }
                $this->parameters[$key] = $value;
            }
        }
    }
    /**
     * @return string|null Name of the entry to inject
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @return array Parameters, indexed by the parameter number (index) or name
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }
}

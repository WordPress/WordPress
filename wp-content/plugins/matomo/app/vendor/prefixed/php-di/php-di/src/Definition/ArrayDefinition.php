<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition;

/**
 * Definition of an array containing values or references.
 *
 * @since 5.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ArrayDefinition implements Definition
{
    /**
     * Entry name.
     * @var string
     */
    private $name = '';
    /**
     * @var array
     */
    private $values;
    public function __construct(array $values)
    {
        $this->values = $values;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function setName(string $name)
    {
        $this->name = $name;
    }
    public function getValues() : array
    {
        return $this->values;
    }
    public function replaceNestedDefinitions(callable $replacer)
    {
        $this->values = array_map($replacer, $this->values);
    }
    public function __toString()
    {
        $str = '[' . \PHP_EOL;
        foreach ($this->values as $key => $value) {
            if (is_string($key)) {
                $key = "'" . $key . "'";
            }
            $str .= '    ' . $key . ' => ';
            if ($value instanceof Definition) {
                $str .= str_replace(\PHP_EOL, \PHP_EOL . '    ', (string) $value);
            } else {
                $str .= var_export($value, \true);
            }
            $str .= ',' . \PHP_EOL;
        }
        return $str . ']';
    }
}

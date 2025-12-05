<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition\Source;

/**
 * Reads DI definitions from a file returning a PHP array.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DefinitionFile extends DefinitionArray
{
    /**
     * @var bool
     */
    private $initialized = \false;
    /**
     * File containing definitions, or null if the definitions are given as a PHP array.
     * @var string|null
     */
    private $file;
    /**
     * @param string $file File in which the definitions are returned as an array.
     */
    public function __construct($file, Autowiring $autowiring = null)
    {
        // Lazy-loading to improve performances
        $this->file = $file;
        parent::__construct([], $autowiring);
    }
    public function getDefinition(string $name)
    {
        $this->initialize();
        return parent::getDefinition($name);
    }
    public function getDefinitions() : array
    {
        $this->initialize();
        return parent::getDefinitions();
    }
    /**
     * Lazy-loading of the definitions.
     */
    private function initialize()
    {
        if ($this->initialized === \true) {
            return;
        }
        $definitions = (require $this->file);
        if (!is_array($definitions)) {
            throw new \Exception("File {$this->file} should return an array of definitions");
        }
        $this->addDefinitions($definitions);
        $this->initialized = \true;
    }
}

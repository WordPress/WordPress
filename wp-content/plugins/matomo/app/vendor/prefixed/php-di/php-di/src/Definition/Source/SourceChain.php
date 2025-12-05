<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Definition\Source;

use Matomo\Dependencies\DI\Definition\Definition;
use Matomo\Dependencies\DI\Definition\ExtendsPreviousDefinition;
/**
 * Manages a chain of other definition sources.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class SourceChain implements DefinitionSource, MutableDefinitionSource
{
    /**
     * @var DefinitionSource[]
     */
    private $sources;
    /**
     * @var DefinitionSource
     */
    private $rootSource;
    /**
     * @var MutableDefinitionSource|null
     */
    private $mutableSource;
    /**
     * @param DefinitionSource[] $sources
     */
    public function __construct(array $sources)
    {
        // We want a numerically indexed array to ease the traversal later
        $this->sources = array_values($sources);
        $this->rootSource = $this;
    }
    /**
     * {@inheritdoc}
     *
     * @param int $startIndex Use this parameter to start looking from a specific
     *                        point in the source chain.
     */
    public function getDefinition(string $name, int $startIndex = 0)
    {
        $count = count($this->sources);
        for ($i = $startIndex; $i < $count; ++$i) {
            $source = $this->sources[$i];
            $definition = $source->getDefinition($name);
            if ($definition) {
                if ($definition instanceof ExtendsPreviousDefinition) {
                    $this->resolveExtendedDefinition($definition, $i);
                }
                return $definition;
            }
        }
        return null;
    }
    public function getDefinitions() : array
    {
        $names = [];
        foreach ($this->sources as $source) {
            $names = array_merge($names, $source->getDefinitions());
        }
        $names = array_keys($names);
        $definitions = array_combine($names, array_map(function (string $name) {
            return $this->getDefinition($name);
        }, $names));
        return $definitions;
    }
    public function addDefinition(Definition $definition)
    {
        if (!$this->mutableSource) {
            throw new \LogicException("The container's definition source has not been initialized correctly");
        }
        $this->mutableSource->addDefinition($definition);
    }
    private function resolveExtendedDefinition(ExtendsPreviousDefinition $definition, int $currentIndex)
    {
        // Look in the next sources only (else infinite recursion, and we can only extend
        // entries defined in the previous definition files - a previous == next here because
        // the array was reversed ;) )
        $subDefinition = $this->getDefinition($definition->getName(), $currentIndex + 1);
        if ($subDefinition) {
            $definition->setExtendedDefinition($subDefinition);
        }
    }
    public function setMutableDefinitionSource(MutableDefinitionSource $mutableSource)
    {
        $this->mutableSource = $mutableSource;
        array_unshift($this->sources, $mutableSource);
    }
}

<?php declare(strict_types=1);

namespace Automattic\WooCommerce\Vendor\League\Container\Definition;

use IteratorAggregate;
use Automattic\WooCommerce\Vendor\League\Container\ContainerAwareInterface;

interface DefinitionAggregateInterface extends ContainerAwareInterface, IteratorAggregate
{
    /**
     * Add a definition to the aggregate.
     *
     * @param string  $id
     * @param mixed   $definition
     * @param boolean $shared
     *
     * @return DefinitionInterface
     */
    public function add(string $id, $definition, bool $shared = false) : DefinitionInterface;

    /**
     * Checks whether alias exists as definition.
     *
     * @param string $id
     *
     * @return boolean
     */
    public function has(string $id) : bool;

    /**
     * Checks whether tag exists as definition.
     *
     * @param string $tag
     *
     * @return boolean
     */
    public function hasTag(string $tag) : bool;

    /**
     * Get the definition to be extended.
     *
     * @param string $id
     *
     * @return DefinitionInterface
     */
    public function getDefinition(string $id) : DefinitionInterface;

    /**
     * Resolve and build a concrete value from an id/alias.
     *
     * @param string  $id
     * @param boolean $new
     *
     * @return mixed
     */
    public function resolve(string $id, bool $new = false);

    /**
     * Resolve and build an array of concrete values from a tag.
     *
     * @param string  $tag
     * @param boolean $new
     *
     * @return mixed
     */
    public function resolveTagged(string $tag, bool $new = false);
}

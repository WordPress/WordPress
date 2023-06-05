<?php declare(strict_types=1);

namespace Automattic\WooCommerce\Vendor\League\Container\Definition;

use Generator;
use Automattic\WooCommerce\Vendor\League\Container\ContainerAwareTrait;
use Automattic\WooCommerce\Vendor\League\Container\Exception\NotFoundException;

class DefinitionAggregate implements DefinitionAggregateInterface
{
    use ContainerAwareTrait;

    /**
     * @var DefinitionInterface[]
     */
    protected $definitions = [];

    /**
     * Construct.
     *
     * @param DefinitionInterface[] $definitions
     */
    public function __construct(array $definitions = [])
    {
        $this->definitions = array_filter($definitions, function ($definition) {
            return ($definition instanceof DefinitionInterface);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function add(string $id, $definition, bool $shared = false) : DefinitionInterface
    {
        if (!$definition instanceof DefinitionInterface) {
            $definition = new Definition($id, $definition);
        }

        $this->definitions[] = $definition
            ->setAlias($id)
            ->setShared($shared)
        ;

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $id) : bool
    {
        foreach ($this->getIterator() as $definition) {
            if ($id === $definition->getAlias()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTag(string $tag) : bool
    {
        foreach ($this->getIterator() as $definition) {
            if ($definition->hasTag($tag)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(string $id) : DefinitionInterface
    {
        foreach ($this->getIterator() as $definition) {
            if ($id === $definition->getAlias()) {
                return $definition->setLeagueContainer($this->getLeagueContainer());
            }
        }

        throw new NotFoundException(sprintf('Alias (%s) is not being handled as a definition.', $id));
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $id, bool $new = false)
    {
        return $this->getDefinition($id)->resolve($new);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTagged(string $tag, bool $new = false) : array
    {
        $arrayOf = [];

        foreach ($this->getIterator() as $definition) {
            if ($definition->hasTag($tag)) {
                $arrayOf[] = $definition->setLeagueContainer($this->getLeagueContainer())->resolve($new);
            }
        }

        return $arrayOf;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator() : Generator
    {
        $count = count($this->definitions);

        for ($i = 0; $i < $count; $i++) {
            yield $this->definitions[$i];
        }
    }
}

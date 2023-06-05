<?php declare(strict_types=1);

namespace Automattic\WooCommerce\Vendor\League\Container\Inflector;

use Generator;
use Automattic\WooCommerce\Vendor\League\Container\ContainerAwareTrait;

class InflectorAggregate implements InflectorAggregateInterface
{
    use ContainerAwareTrait;

    /**
     * @var Inflector[]
     */
    protected $inflectors = [];

    /**
     * {@inheritdoc}
     */
    public function add(string $type, callable $callback = null) : Inflector
    {
        $inflector          = new Inflector($type, $callback);
        $this->inflectors[] = $inflector;

        return $inflector;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator() : Generator
    {
        $count = count($this->inflectors);

        for ($i = 0; $i < $count; $i++) {
            yield $this->inflectors[$i];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function inflect($object)
    {
        foreach ($this->getIterator() as $inflector) {
            $type = $inflector->getType();

            if (! $object instanceof $type) {
                continue;
            }

            $inflector->setLeagueContainer($this->getLeagueContainer());
            $inflector->inflect($object);
        }

        return $object;
    }
}

<?php

/*
 * This file is part of composer/semver.
 *
 * (c) Composer <https://github.com/composer>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Composer\Semver\Constraint;

/**
 * Defines the absence of a constraint.
 *
 * This constraint matches everything.
 */
class MatchAllConstraint implements ConstraintInterface
{
    /** @var string|null */
    protected $prettyString;

    /**
     * @param ConstraintInterface $provider
     *
     * @return bool
     */
    public function matches(ConstraintInterface $provider)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function compile($otherOperator)
    {
        return 'true';
    }

    /**
     * {@inheritDoc}
     */
    public function setPrettyString($prettyString)
    {
        $this->prettyString = $prettyString;
    }

    /**
     * {@inheritDoc}
     */
    public function getPrettyString()
    {
        if ($this->prettyString) {
            return $this->prettyString;
        }

        return (string) $this;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return '*';
    }

    /**
     * {@inheritDoc}
     */
    public function getUpperBound()
    {
        return Bound::positiveInfinity();
    }

    /**
     * {@inheritDoc}
     */
    public function getLowerBound()
    {
        return Bound::zero();
    }
}

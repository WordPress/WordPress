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
 * Defines a conjunctive or disjunctive set of constraints.
 */
class MultiConstraint implements ConstraintInterface
{
    /**
     * @var ConstraintInterface[]
     * @phpstan-var non-empty-array<ConstraintInterface>
     */
    protected $constraints;

    /** @var string|null */
    protected $prettyString;

    /** @var string|null */
    protected $string;

    /** @var bool */
    protected $conjunctive;

    /** @var Bound|null */
    protected $lowerBound;

    /** @var Bound|null */
    protected $upperBound;

    /**
     * @param ConstraintInterface[] $constraints A set of constraints
     * @param bool                  $conjunctive Whether the constraints should be treated as conjunctive or disjunctive
     *
     * @throws \InvalidArgumentException If less than 2 constraints are passed
     */
    public function __construct(array $constraints, $conjunctive = true)
    {
        if (\count($constraints) < 2) {
            throw new \InvalidArgumentException(
                'Must provide at least two constraints for a MultiConstraint. Use '.
                'the regular Constraint class for one constraint only or MatchAllConstraint for none. You may use '.
                'MultiConstraint::create() which optimizes and handles those cases automatically.'
            );
        }

        $this->constraints = $constraints;
        $this->conjunctive = $conjunctive;
    }

    /**
     * @return ConstraintInterface[]
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @return bool
     */
    public function isConjunctive()
    {
        return $this->conjunctive;
    }

    /**
     * @return bool
     */
    public function isDisjunctive()
    {
        return !$this->conjunctive;
    }

    /**
     * {@inheritDoc}
     */
    public function compile($otherOperator)
    {
        $parts = array();
        foreach ($this->constraints as $constraint) {
            $code = $constraint->compile($otherOperator);
            if ($code === 'true') {
                if (!$this->conjunctive) {
                    return 'true';
                }
            } elseif ($code === 'false') {
                if ($this->conjunctive) {
                    return 'false';
                }
            } else {
                $parts[] = '('.$code.')';
            }
        }

        if (!$parts) {
            return $this->conjunctive ? 'true' : 'false';
        }

        return $this->conjunctive ? implode('&&', $parts) : implode('||', $parts);
    }

    /**
     * @param ConstraintInterface $provider
     *
     * @return bool
     */
    public function matches(ConstraintInterface $provider)
    {
        if (false === $this->conjunctive) {
            foreach ($this->constraints as $constraint) {
                if ($provider->matches($constraint)) {
                    return true;
                }
            }

            return false;
        }

        // when matching a conjunctive and a disjunctive multi constraint we have to iterate over the disjunctive one
        // otherwise we'd return true if different parts of the disjunctive constraint match the conjunctive one
        // which would lead to incorrect results, e.g. [>1 and <2] would match [<1 or >2] although they do not intersect
        if ($provider instanceof MultiConstraint && $provider->isDisjunctive()) {
            return $provider->matches($this);
        }

        foreach ($this->constraints as $constraint) {
            if (!$provider->matches($constraint)) {
                return false;
            }
        }

        return true;
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
        if ($this->string !== null) {
            return $this->string;
        }

        $constraints = array();
        foreach ($this->constraints as $constraint) {
            $constraints[] = (string) $constraint;
        }

        return $this->string = '[' . implode($this->conjunctive ? ' ' : ' || ', $constraints) . ']';
    }

    /**
     * {@inheritDoc}
     */
    public function getLowerBound()
    {
        $this->extractBounds();

        if (null === $this->lowerBound) {
            throw new \LogicException('extractBounds should have populated the lowerBound property');
        }

        return $this->lowerBound;
    }

    /**
     * {@inheritDoc}
     */
    public function getUpperBound()
    {
        $this->extractBounds();

        if (null === $this->upperBound) {
            throw new \LogicException('extractBounds should have populated the upperBound property');
        }

        return $this->upperBound;
    }

    /**
     * Tries to optimize the constraints as much as possible, meaning
     * reducing/collapsing congruent constraints etc.
     * Does not necessarily return a MultiConstraint instance if
     * things can be reduced to a simple constraint
     *
     * @param ConstraintInterface[] $constraints A set of constraints
     * @param bool                  $conjunctive Whether the constraints should be treated as conjunctive or disjunctive
     *
     * @return ConstraintInterface
     */
    public static function create(array $constraints, $conjunctive = true)
    {
        if (0 === \count($constraints)) {
            return new MatchAllConstraint();
        }

        if (1 === \count($constraints)) {
            return $constraints[0];
        }

        $optimized = self::optimizeConstraints($constraints, $conjunctive);
        if ($optimized !== null) {
            list($constraints, $conjunctive) = $optimized;
            if (\count($constraints) === 1) {
                return $constraints[0];
            }
        }

        return new self($constraints, $conjunctive);
    }

    /**
     * @param  ConstraintInterface[] $constraints
     * @param  bool                  $conjunctive
     * @return ?array
     *
     * @phpstan-return array{0: list<ConstraintInterface>, 1: bool}|null
     */
    private static function optimizeConstraints(array $constraints, $conjunctive)
    {
        // parse the two OR groups and if they are contiguous we collapse
        // them into one constraint
        // [>= 1 < 2] || [>= 2 < 3] || [>= 3 < 4] => [>= 1 < 4]
        if (!$conjunctive) {
            $left = $constraints[0];
            $mergedConstraints = array();
            $optimized = false;
            for ($i = 1, $l = \count($constraints); $i < $l; $i++) {
                $right = $constraints[$i];
                if (
                    $left instanceof self
                    && $left->conjunctive
                    && $right instanceof self
                    && $right->conjunctive
                    && \count($left->constraints) === 2
                    && \count($right->constraints) === 2
                    && ($left0 = (string) $left->constraints[0])
                    && $left0[0] === '>' && $left0[1] === '='
                    && ($left1 = (string) $left->constraints[1])
                    && $left1[0] === '<'
                    && ($right0 = (string) $right->constraints[0])
                    && $right0[0] === '>' && $right0[1] === '='
                    && ($right1 = (string) $right->constraints[1])
                    && $right1[0] === '<'
                    && substr($left1, 2) === substr($right0, 3)
                ) {
                    $optimized = true;
                    $left = new MultiConstraint(
                        array(
                            $left->constraints[0],
                            $right->constraints[1],
                        ),
                        true);
                } else {
                    $mergedConstraints[] = $left;
                    $left = $right;
                }
            }
            if ($optimized) {
                $mergedConstraints[] = $left;
                return array($mergedConstraints, false);
            }
        }

        // TODO: Here's the place to put more optimizations

        return null;
    }

    /**
     * @return void
     */
    private function extractBounds()
    {
        if (null !== $this->lowerBound) {
            return;
        }

        foreach ($this->constraints as $constraint) {
            if (null === $this->lowerBound || null === $this->upperBound) {
                $this->lowerBound = $constraint->getLowerBound();
                $this->upperBound = $constraint->getUpperBound();
                continue;
            }

            if ($constraint->getLowerBound()->compareTo($this->lowerBound, $this->isConjunctive() ? '>' : '<')) {
                $this->lowerBound = $constraint->getLowerBound();
            }

            if ($constraint->getUpperBound()->compareTo($this->upperBound, $this->isConjunctive() ? '<' : '>')) {
                $this->upperBound = $constraint->getUpperBound();
            }
        }
    }
}

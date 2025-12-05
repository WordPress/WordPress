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
 * DO NOT IMPLEMENT this interface. It is only meant for usage as a type hint
 * in libraries relying on composer/semver but creating your own constraint class
 * that implements this interface is not a supported use case and will cause the
 * composer/semver components to return unexpected results.
 */
interface ConstraintInterface
{
    /**
     * Checks whether the given constraint intersects in any way with this constraint
     *
     * @param ConstraintInterface $provider
     *
     * @return bool
     */
    public function matches(ConstraintInterface $provider);

    /**
     * Provides a compiled version of the constraint for the given operator
     * The compiled version must be a PHP expression.
     * Executor of compile version must provide 2 variables:
     * - $v = the string version to compare with
     * - $b = whether or not the version is a non-comparable branch (starts with "dev-")
     *
     * @see Constraint::OP_* for the list of available operators.
     * @example return '!$b && version_compare($v, '1.0', '>')';
     *
     * @param int $otherOperator one Constraint::OP_*
     *
     * @return string
     *
     * @phpstan-param Constraint::OP_* $otherOperator
     */
    public function compile($otherOperator);

    /**
     * @return Bound
     */
    public function getUpperBound();

    /**
     * @return Bound
     */
    public function getLowerBound();

    /**
     * @return string
     */
    public function getPrettyString();

    /**
     * @param string|null $prettyString
     *
     * @return void
     */
    public function setPrettyString($prettyString);

    /**
     * @return string
     */
    public function __toString();
}

<?php

/*
 * This file is part of composer/semver.
 *
 * (c) Composer <https://github.com/composer>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Composer\Semver;

use Composer\Semver\Constraint\Constraint;
use Composer\Semver\Constraint\ConstraintInterface;

/**
 * Helper class to evaluate constraint by compiling and reusing the code to evaluate
 */
class CompilingMatcher
{
    /**
     * @var array
     * @phpstan-var array<string, callable>
     */
    private static $compiledCheckerCache = array();
    /**
     * @var array
     * @phpstan-var array<string, bool>
     */
    private static $resultCache = array();

    /** @var bool */
    private static $enabled;

    /**
     * @phpstan-var array<Constraint::OP_*, Constraint::STR_OP_*>
     */
    private static $transOpInt = array(
        Constraint::OP_EQ => Constraint::STR_OP_EQ,
        Constraint::OP_LT => Constraint::STR_OP_LT,
        Constraint::OP_LE => Constraint::STR_OP_LE,
        Constraint::OP_GT => Constraint::STR_OP_GT,
        Constraint::OP_GE => Constraint::STR_OP_GE,
        Constraint::OP_NE => Constraint::STR_OP_NE,
    );

    /**
     * Clears the memoization cache once you are done
     *
     * @return void
     */
    public static function clear()
    {
        self::$resultCache = array();
        self::$compiledCheckerCache = array();
    }

    /**
     * Evaluates the expression: $constraint match $operator $version
     *
     * @param ConstraintInterface $constraint
     * @param int                 $operator
     * @phpstan-param Constraint::OP_*  $operator
     * @param string              $version
     *
     * @return bool
     */
    public static function match(ConstraintInterface $constraint, $operator, $version)
    {
        $resultCacheKey = $operator.$constraint.';'.$version;

        if (isset(self::$resultCache[$resultCacheKey])) {
            return self::$resultCache[$resultCacheKey];
        }

        if (self::$enabled === null) {
            self::$enabled = !\in_array('eval', explode(',', (string) ini_get('disable_functions')), true);
        }
        if (!self::$enabled) {
            return self::$resultCache[$resultCacheKey] = $constraint->matches(new Constraint(self::$transOpInt[$operator], $version));
        }

        $cacheKey = $operator.$constraint;
        if (!isset(self::$compiledCheckerCache[$cacheKey])) {
            $code = $constraint->compile($operator);
            self::$compiledCheckerCache[$cacheKey] = $function = eval('return function($v, $b){return '.$code.';};');
        } else {
            $function = self::$compiledCheckerCache[$cacheKey];
        }

        return self::$resultCache[$resultCacheKey] = $function($version, strpos($version, 'dev-') === 0);
    }
}

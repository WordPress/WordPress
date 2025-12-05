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
 * Defines a constraint.
 */
class Constraint implements ConstraintInterface
{
    /* operator integer values */
    const OP_EQ = 0;
    const OP_LT = 1;
    const OP_LE = 2;
    const OP_GT = 3;
    const OP_GE = 4;
    const OP_NE = 5;

    /* operator string values */
    const STR_OP_EQ = '==';
    const STR_OP_EQ_ALT = '=';
    const STR_OP_LT = '<';
    const STR_OP_LE = '<=';
    const STR_OP_GT = '>';
    const STR_OP_GE = '>=';
    const STR_OP_NE = '!=';
    const STR_OP_NE_ALT = '<>';

    /**
     * Operator to integer translation table.
     *
     * @var array
     * @phpstan-var array<self::STR_OP_*, self::OP_*>
     */
    private static $transOpStr = array(
        '=' => self::OP_EQ,
        '==' => self::OP_EQ,
        '<' => self::OP_LT,
        '<=' => self::OP_LE,
        '>' => self::OP_GT,
        '>=' => self::OP_GE,
        '<>' => self::OP_NE,
        '!=' => self::OP_NE,
    );

    /**
     * Integer to operator translation table.
     *
     * @var array
     * @phpstan-var array<self::OP_*, self::STR_OP_*>
     */
    private static $transOpInt = array(
        self::OP_EQ => '==',
        self::OP_LT => '<',
        self::OP_LE => '<=',
        self::OP_GT => '>',
        self::OP_GE => '>=',
        self::OP_NE => '!=',
    );

    /**
     * @var int
     * @phpstan-var self::OP_*
     */
    protected $operator;

    /** @var string */
    protected $version;

    /** @var string|null */
    protected $prettyString;

    /** @var Bound */
    protected $lowerBound;

    /** @var Bound */
    protected $upperBound;

    /**
     * Sets operator and version to compare with.
     *
     * @param string $operator
     * @param string $version
     *
     * @throws \InvalidArgumentException if invalid operator is given.
     *
     * @phpstan-param self::STR_OP_* $operator
     */
    public function __construct($operator, $version)
    {
        if (!isset(self::$transOpStr[$operator])) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid operator "%s" given, expected one of: %s',
                $operator,
                implode(', ', self::getSupportedOperators())
            ));
        }

        $this->operator = self::$transOpStr[$operator];
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     *
     * @phpstan-return self::STR_OP_*
     */
    public function getOperator()
    {
        return self::$transOpInt[$this->operator];
    }

    /**
     * @param ConstraintInterface $provider
     *
     * @return bool
     */
    public function matches(ConstraintInterface $provider)
    {
        if ($provider instanceof self) {
            return $this->matchSpecific($provider);
        }

        // turn matching around to find a match
        return $provider->matches($this);
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

        return $this->__toString();
    }

    /**
     * Get all supported comparison operators.
     *
     * @return array
     *
     * @phpstan-return list<self::STR_OP_*>
     */
    public static function getSupportedOperators()
    {
        return array_keys(self::$transOpStr);
    }

    /**
     * @param  string $operator
     * @return int
     *
     * @phpstan-param  self::STR_OP_* $operator
     * @phpstan-return self::OP_*
     */
    public static function getOperatorConstant($operator)
    {
        return self::$transOpStr[$operator];
    }

    /**
     * @param string $a
     * @param string $b
     * @param string $operator
     * @param bool   $compareBranches
     *
     * @throws \InvalidArgumentException if invalid operator is given.
     *
     * @return bool
     *
     * @phpstan-param self::STR_OP_* $operator
     */
    public function versionCompare($a, $b, $operator, $compareBranches = false)
    {
        if (!isset(self::$transOpStr[$operator])) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid operator "%s" given, expected one of: %s',
                $operator,
                implode(', ', self::getSupportedOperators())
            ));
        }

        $aIsBranch = strpos($a, 'dev-') === 0;
        $bIsBranch = strpos($b, 'dev-') === 0;

        if ($operator === '!=' && ($aIsBranch || $bIsBranch)) {
            return $a !== $b;
        }

        if ($aIsBranch && $bIsBranch) {
            return $operator === '==' && $a === $b;
        }

        // when branches are not comparable, we make sure dev branches never match anything
        if (!$compareBranches && ($aIsBranch || $bIsBranch)) {
            return false;
        }

        return \version_compare($a, $b, $operator);
    }

    /**
     * {@inheritDoc}
     */
    public function compile($otherOperator)
    {
        if (strpos($this->version, 'dev-') === 0) {
            if (self::OP_EQ === $this->operator) {
                if (self::OP_EQ === $otherOperator) {
                    return sprintf('$b && $v === %s', \var_export($this->version, true));
                }
                if (self::OP_NE === $otherOperator) {
                    return sprintf('!$b || $v !== %s', \var_export($this->version, true));
                }
                return 'false';
            }

            if (self::OP_NE === $this->operator) {
                if (self::OP_EQ === $otherOperator) {
                    return sprintf('!$b || $v !== %s', \var_export($this->version, true));
                }
                if (self::OP_NE === $otherOperator) {
                    return 'true';
                }
                return '!$b';
            }

            return 'false';
        }

        if (self::OP_EQ === $this->operator) {
            if (self::OP_EQ === $otherOperator) {
                return sprintf('\version_compare($v, %s, \'==\')', \var_export($this->version, true));
            }
            if (self::OP_NE === $otherOperator) {
                return sprintf('$b || \version_compare($v, %s, \'!=\')', \var_export($this->version, true));
            }

            return sprintf('!$b && \version_compare(%s, $v, \'%s\')', \var_export($this->version, true), self::$transOpInt[$otherOperator]);
        }

        if (self::OP_NE === $this->operator) {
            if (self::OP_EQ === $otherOperator) {
                return sprintf('$b || (!$b && \version_compare($v, %s, \'!=\'))', \var_export($this->version, true));
            }

            if (self::OP_NE === $otherOperator) {
                return 'true';
            }
            return '!$b';
        }

        if (self::OP_LT === $this->operator || self::OP_LE === $this->operator) {
            if (self::OP_LT === $otherOperator || self::OP_LE === $otherOperator) {
                return '!$b';
            }
        } else { // $this->operator must be self::OP_GT || self::OP_GE here
            if (self::OP_GT === $otherOperator || self::OP_GE === $otherOperator) {
                return '!$b';
            }
        }

        if (self::OP_NE === $otherOperator) {
            return 'true';
        }

        $codeComparison = sprintf('\version_compare($v, %s, \'%s\')', \var_export($this->version, true), self::$transOpInt[$this->operator]);
        if ($this->operator === self::OP_LE) {
            if ($otherOperator === self::OP_GT) {
                return sprintf('!$b && \version_compare($v, %s, \'!=\') && ', \var_export($this->version, true)) . $codeComparison;
            }
        } elseif ($this->operator === self::OP_GE) {
            if ($otherOperator === self::OP_LT) {
                return sprintf('!$b && \version_compare($v, %s, \'!=\') && ', \var_export($this->version, true)) . $codeComparison;
            }
        }

        return sprintf('!$b && %s', $codeComparison);
    }

    /**
     * @param Constraint $provider
     * @param bool       $compareBranches
     *
     * @return bool
     */
    public function matchSpecific(Constraint $provider, $compareBranches = false)
    {
        $noEqualOp = str_replace('=', '', self::$transOpInt[$this->operator]);
        $providerNoEqualOp = str_replace('=', '', self::$transOpInt[$provider->operator]);

        $isEqualOp = self::OP_EQ === $this->operator;
        $isNonEqualOp = self::OP_NE === $this->operator;
        $isProviderEqualOp = self::OP_EQ === $provider->operator;
        $isProviderNonEqualOp = self::OP_NE === $provider->operator;

        // '!=' operator is match when other operator is not '==' operator or version is not match
        // these kinds of comparisons always have a solution
        if ($isNonEqualOp || $isProviderNonEqualOp) {
            if ($isNonEqualOp && !$isProviderNonEqualOp && !$isProviderEqualOp && strpos($provider->version, 'dev-') === 0) {
                return false;
            }

            if ($isProviderNonEqualOp && !$isNonEqualOp && !$isEqualOp && strpos($this->version, 'dev-') === 0) {
                return false;
            }

            if (!$isEqualOp && !$isProviderEqualOp) {
                return true;
            }
            return $this->versionCompare($provider->version, $this->version, '!=', $compareBranches);
        }

        // an example for the condition is <= 2.0 & < 1.0
        // these kinds of comparisons always have a solution
        if ($this->operator !== self::OP_EQ && $noEqualOp === $providerNoEqualOp) {
            return !(strpos($this->version, 'dev-') === 0 || strpos($provider->version, 'dev-') === 0);
        }

        $version1 = $isEqualOp ? $this->version : $provider->version;
        $version2 = $isEqualOp ? $provider->version : $this->version;
        $operator = $isEqualOp ? $provider->operator : $this->operator;

        if ($this->versionCompare($version1, $version2, self::$transOpInt[$operator], $compareBranches)) {
            // special case, e.g. require >= 1.0 and provide < 1.0
            // 1.0 >= 1.0 but 1.0 is outside of the provided interval

            return !(self::$transOpInt[$provider->operator] === $providerNoEqualOp
                && self::$transOpInt[$this->operator] !== $noEqualOp
                && \version_compare($provider->version, $this->version, '=='));
        }

        return false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return self::$transOpInt[$this->operator] . ' ' . $this->version;
    }

    /**
     * {@inheritDoc}
     */
    public function getLowerBound()
    {
        $this->extractBounds();

        return $this->lowerBound;
    }

    /**
     * {@inheritDoc}
     */
    public function getUpperBound()
    {
        $this->extractBounds();

        return $this->upperBound;
    }

    /**
     * @return void
     */
    private function extractBounds()
    {
        if (null !== $this->lowerBound) {
            return;
        }

        // Branches
        if (strpos($this->version, 'dev-') === 0) {
            $this->lowerBound = Bound::zero();
            $this->upperBound = Bound::positiveInfinity();

            return;
        }

        switch ($this->operator) {
            case self::OP_EQ:
                $this->lowerBound = new Bound($this->version, true);
                $this->upperBound = new Bound($this->version, true);
                break;
            case self::OP_LT:
                $this->lowerBound = Bound::zero();
                $this->upperBound = new Bound($this->version, false);
                break;
            case self::OP_LE:
                $this->lowerBound = Bound::zero();
                $this->upperBound = new Bound($this->version, true);
                break;
            case self::OP_GT:
                $this->lowerBound = new Bound($this->version, false);
                $this->upperBound = Bound::positiveInfinity();
                break;
            case self::OP_GE:
                $this->lowerBound = new Bound($this->version, true);
                $this->upperBound = Bound::positiveInfinity();
                break;
            case self::OP_NE:
                $this->lowerBound = Bound::zero();
                $this->upperBound = Bound::positiveInfinity();
                break;
        }
    }
}

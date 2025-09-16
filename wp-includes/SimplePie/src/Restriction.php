<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

/**
 * Handles `<media:restriction>` as defined in Media RSS
 *
 * Used by {@see \SimplePie\Enclosure::get_restriction()} and {@see \SimplePie\Enclosure::get_restrictions()}
 *
 * This class can be overloaded with {@see \SimplePie\SimplePie::set_restriction_class()}
 */
class Restriction
{
    public const RELATIONSHIP_ALLOW = 'allow';
    public const RELATIONSHIP_DENY = 'deny';

    /**
     * Relationship ('allow'/'deny')
     *
     * @var self::RELATIONSHIP_*|null
     * @see get_relationship()
     */
    public $relationship;

    /**
     * Type of restriction
     *
     * @var string|null
     * @see get_type()
     */
    public $type;

    /**
     * Restricted values
     *
     * @var string|null
     * @see get_value()
     */
    public $value;

    /**
     * Constructor, used to input the data
     *
     * For documentation on all the parameters, see the corresponding
     * properties and their accessors
     *
     * @param ?self::RELATIONSHIP_* $relationship
     */
    public function __construct(?string $relationship = null, ?string $type = null, ?string $value = null)
    {
        $this->relationship = $relationship;
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * String-ified version
     *
     * @return string
     */
    public function __toString()
    {
        // There is no $this->data here
        return md5(serialize($this));
    }

    /**
     * Get the relationship
     *
     * @return ?self::RELATIONSHIP_*
     */
    public function get_relationship()
    {
        if ($this->relationship !== null) {
            return $this->relationship;
        }

        return null;
    }

    /**
     * Get the type
     *
     * @return string|null
     */
    public function get_type()
    {
        if ($this->type !== null) {
            return $this->type;
        }

        return null;
    }

    /**
     * Get the list of restricted things
     *
     * @return string|null
     */
    public function get_value()
    {
        if ($this->value !== null) {
            return $this->value;
        }

        return null;
    }
}

class_alias('SimplePie\Restriction', 'SimplePie_Restriction');

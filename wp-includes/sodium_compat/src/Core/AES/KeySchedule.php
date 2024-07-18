<?php

if (class_exists('ParagonIE_Sodium_Core_AES_KeySchedule', false)) {
    return;
}

/**
 * @internal This should only be used by sodium_compat
 */
class ParagonIE_Sodium_Core_AES_KeySchedule
{
    /** @var array<int, int> $skey -- has size 120 */
    protected $skey;

    /** @var bool $expanded */
    protected $expanded = false;

    /** @var int $numRounds */
    private $numRounds;

    /**
     * @param array $skey
     * @param int $numRounds
     */
    public function __construct(array $skey, $numRounds = 10)
    {
        $this->skey = $skey;
        $this->numRounds = $numRounds;
    }

    /**
     * Get a value at an arbitrary index. Mostly used for unit testing.
     *
     * @param int $i
     * @return int
     */
    public function get($i)
    {
        return $this->skey[$i];
    }

    /**
     * @return int
     */
    public function getNumRounds()
    {
        return $this->numRounds;
    }

    /**
     * @param int $offset
     * @return ParagonIE_Sodium_Core_AES_Block
     */
    public function getRoundKey($offset)
    {
        return ParagonIE_Sodium_Core_AES_Block::fromArray(
            array_slice($this->skey, $offset, 8)
        );
    }

    /**
     * Return an expanded key schedule
     *
     * @return ParagonIE_Sodium_Core_AES_Expanded
     */
    public function expand()
    {
        $exp = new ParagonIE_Sodium_Core_AES_Expanded(
            array_fill(0, 120, 0),
            $this->numRounds
        );
        $n = ($exp->numRounds + 1) << 2;
        for ($u = 0, $v = 0; $u < $n; ++$u, $v += 2) {
            $x = $y = $this->skey[$u];
            $x &= 0x55555555;
            $exp->skey[$v] = ($x | ($x << 1)) & ParagonIE_Sodium_Core_Util::U32_MAX;
            $y &= 0xAAAAAAAA;
            $exp->skey[$v + 1] = ($y | ($y >> 1)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        }
        return $exp;
    }
}

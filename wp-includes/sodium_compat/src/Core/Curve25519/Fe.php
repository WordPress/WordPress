<?php

if (class_exists('ParagonIE_Sodium_Core_Curve25519_Fe', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core_Curve25519_Fe
 *
 * This represents a Field Element
 */
class ParagonIE_Sodium_Core_Curve25519_Fe implements ArrayAccess
{
    /**
     * @var int
     */
    public $e0 = 0;

    /**
     * @var int
     */
    public $e1 = 0;

    /**
     * @var int
     */
    public $e2 = 0;

    /**
     * @var int
     */
    public $e3 = 0;

    /**
     * @var int
     */
    public $e4 = 0;

    /**
     * @var int
     */
    public $e5 = 0;

    /**
     * @var int
     */
    public $e6 = 0;

    /**
     * @var int
     */
    public $e7 = 0;

    /**
     * @var int
     */
    public $e8 = 0;

    /**
     * @var int
     */
    public $e9 = 0;

    /**
     * @param int $e0
     * @param int $e1
     * @param int $e2
     * @param int $e3
     * @param int $e4
     * @param int $e5
     * @param int $e6
     * @param int $e7
     * @param int $e8
     * @param int $e9
     */
    public function __construct(
        $e0 = 0,
        $e1 = 0,
        $e2 = 0,
        $e3 = 0,
        $e4 = 0,
        $e5 = 0,
        $e6 = 0,
        $e7 = 0,
        $e8 = 0,
        $e9 = 0
    ) {
        $this->e0 = $e0;
        $this->e1 = $e1;
        $this->e2 = $e2;
        $this->e3 = $e3;
        $this->e4 = $e4;
        $this->e5 = $e5;
        $this->e6 = $e6;
        $this->e7 = $e7;
        $this->e8 = $e8;
        $this->e9 = $e9;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param array $array
     * @return self
     */
    public static function fromArray($array)
    {
        $obj = new ParagonIE_Sodium_Core_Curve25519_Fe();
        $obj->e0 = isset($array[0]) ? (int) $array[0] : 0;
        $obj->e1 = isset($array[1]) ? (int) $array[1] : 0;
        $obj->e2 = isset($array[2]) ? (int) $array[2] : 0;
        $obj->e3 = isset($array[3]) ? (int) $array[3] : 0;
        $obj->e4 = isset($array[4]) ? (int) $array[4] : 0;
        $obj->e5 = isset($array[5]) ? (int) $array[5] : 0;
        $obj->e6 = isset($array[6]) ? (int) $array[6] : 0;
        $obj->e7 = isset($array[7]) ? (int) $array[7] : 0;
        $obj->e8 = isset($array[8]) ? (int) $array[8] : 0;
        $obj->e9 = isset($array[9]) ? (int) $array[9] : 0;
        return $obj;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int|null $offset
     * @param int $value
     * @return void
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException('Expected an integer');
        }
        switch ($offset) {
            case 0:
                $this->e0 = $value;
                break;
            case 1:
                $this->e1 = $value;
                break;
            case 2:
                $this->e2 = $value;
                break;
            case 3:
                $this->e3 = $value;
                break;
            case 4:
                $this->e4 = $value;
                break;
            case 5:
                $this->e5 = $value;
                break;
            case 6:
                $this->e6 = $value;
                break;
            case 7:
                $this->e7 = $value;
                break;
            case 8:
                $this->e8 = $value;
                break;
            case 9:
                $this->e9 = $value;
                break;
            default:
                throw new OutOfBoundsException('Index out of bounds');
        }
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $offset
     * @return bool
     */
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $offset >= 0 && $offset < 10;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $offset
     * @return void
     */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        switch ($offset) {
            case 0:
                $this->e0 = 0;
                break;
            case 1:
                $this->e1 = 0;
                break;
            case 2:
                $this->e2 = 0;
                break;
            case 3:
                $this->e3 = 0;
                break;
            case 4:
                $this->e4 = 0;
                break;
            case 5:
                $this->e5 = 0;
                break;
            case 6:
                $this->e6 = 0;
                break;
            case 7:
                $this->e7 = 0;
                break;
            case 8:
                $this->e8 = 0;
                break;
            case 9:
                $this->e9 = 0;
                break;
            default:
                throw new OutOfBoundsException('Index out of bounds');
        }
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $offset
     * @return int
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        switch ($offset) {
            case 0:
                return (int) $this->e0;
            case 1:
                return (int) $this->e1;
            case 2:
                return (int) $this->e2;
            case 3:
                return (int) $this->e3;
            case 4:
                return (int) $this->e4;
            case 5:
                return (int) $this->e5;
            case 6:
                return (int) $this->e6;
            case 7:
                return (int) $this->e7;
            case 8:
                return (int) $this->e8;
            case 9:
                return (int) $this->e9;
            default:
                throw new OutOfBoundsException('Index out of bounds');
        }
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @return array
     */
    public function __debugInfo()
    {
        return array(
            implode(', ', array(
                $this->e0, $this->e1, $this->e2, $this->e3, $this->e4,
                $this->e5, $this->e6, $this->e7, $this->e8, $this->e9
            ))
        );
    }
}

<?php

if (class_exists('SplFixedArray')) {
    return;
}

/**
 * The SplFixedArray class provides the main functionalities of array. The
 * main differences between a SplFixedArray and a normal PHP array is that
 * the SplFixedArray is of fixed length and allows only integers within
 * the range as indexes. The advantage is that it allows a faster array
 * implementation.
 */
class SplFixedArray implements Iterator, ArrayAccess, Countable
{
    /** @var array<int, mixed> */
    private $internalArray = array();

    /** @var int $size */
    private $size = 0;

    /**
     * SplFixedArray constructor.
     * @param int $size
     */
    public function __construct($size = 0)
    {
        $this->size = $size;
        $this->internalArray = array();
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->internalArray);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        ksort($this->internalArray);
        return (array) $this->internalArray;
    }

    /**
     * @param array $array
     * @param bool $save_indexes
     * @return SplFixedArray
     * @psalm-suppress MixedAssignment
     */
    public static function fromArray(array $array, $save_indexes = true)
    {
        $self = new SplFixedArray(count($array));
        if($save_indexes) {
            foreach($array as $key => $value) {
                $self[(int) $key] = $value;
            }
        } else {
            $i = 0;
            foreach (array_values($array) as $value) {
                $self[$i] = $value;
                $i++;
            }
        }
        return $self;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return bool
     */
    public function setSize($size)
    {
        $this->size = $size;
        return true;
    }

    /**
     * @param string|int $index
     * @return bool
     */
    public function offsetExists($index)
    {
        return array_key_exists((int) $index, $this->internalArray);
    }

    /**
     * @param string|int $index
     * @return mixed
     */
    public function offsetGet($index)
    {
        return $this->internalArray[(int) $index];
    }

    /**
     * @param string|int $index
     * @param mixed $newval
     * @psalm-suppress MixedAssignment
     */
    public function offsetSet($index, $newval)
    {
        $this->internalArray[(int) $index] = $newval;
    }

    /**
     * @param string|int $index
     */
    public function offsetUnset($index)
    {
        unset($this->internalArray[(int) $index]);
    }

    /**
     * Rewind iterator back to the start
     * @link https://php.net/manual/en/splfixedarray.rewind.php
     * @return void
     * @since 5.3.0
     */
    public function rewind()
    {
        reset($this->internalArray);
    }

    /**
     * Return current array entry
     * @link https://php.net/manual/en/splfixedarray.current.php
     * @return mixed The current element value.
     * @since 5.3.0
     */
    public function current()
    {
        return current($this->internalArray);
    }

    /**
     * Return current array index
     * @return int The current array index.
     */
    public function key()
    {
        return key($this->internalArray);
    }

    /**
     * @return void
     */
    public function next()
    {
        next($this->internalArray);
    }

    /**
     * Check whether the array contains more elements
     * @link https://php.net/manual/en/splfixedarray.valid.php
     * @return bool true if the array contains any more elements, false otherwise.
     */
    public function valid()
    {
        if (empty($this->internalArray)) {
            return false;
        }
        $result = next($this->internalArray) !== false;
        prev($this->internalArray);
        return $result;
    }

    /**
     * Do nothing.
     */
    public function __wakeup()
    {
        // NOP
    }
}
<?php

declare (strict_types=1);
namespace Matomo\Dependencies\GeoIp2\Model;

/**
 * @ignore
 */
abstract class AbstractModel implements \JsonSerializable
{
    /**
     * @var array<string, mixed>
     */
    protected $raw;
    /**
     * @ignore
     */
    public function __construct(array $raw)
    {
        $this->raw = $raw;
    }
    /**
     * @ignore
     *
     * @return mixed
     */
    protected function get(string $field)
    {
        if (isset($this->raw[$field])) {
            return $this->raw[$field];
        }
        if (preg_match('/^is_/', $field)) {
            return \false;
        }
        return null;
    }
    /**
     * @ignore
     *
     * @return mixed
     */
    public function __get(string $attr)
    {
        if ($attr !== 'instance' && property_exists($this, $attr)) {
            return $this->{$attr};
        }
        throw new \RuntimeException("Unknown attribute: {$attr}");
    }
    /**
     * @ignore
     */
    public function __isset(string $attr) : bool
    {
        return $attr !== 'instance' && isset($this->{$attr});
    }
    public function jsonSerialize() : array
    {
        return $this->raw;
    }
}

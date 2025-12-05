<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig;

/**
 * Marks a content as safe.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Markup implements \Countable, \JsonSerializable
{
    private $content;
    private $charset;
    public function __construct($content, $charset)
    {
        $this->content = (string) $content;
        $this->charset = $charset;
    }
    public function __toString()
    {
        return $this->content;
    }
    /**
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return \mb_strlen($this->content, $this->charset);
    }
    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->content;
    }
}

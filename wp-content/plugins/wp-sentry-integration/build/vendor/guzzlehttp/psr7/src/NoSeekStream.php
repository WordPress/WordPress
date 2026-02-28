<?php

declare (strict_types=1);
namespace WPSentry\ScopedVendor\GuzzleHttp\Psr7;

use WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface;
/**
 * Stream decorator that prevents a stream from being seeked.
 */
final class NoSeekStream implements \WPSentry\ScopedVendor\Psr\Http\Message\StreamInterface
{
    use StreamDecoratorTrait;
    /** @var StreamInterface */
    private $stream;
    public function seek($offset, $whence = \SEEK_SET) : void
    {
        throw new \RuntimeException('Cannot seek a NoSeekStream');
    }
    public function isSeekable() : bool
    {
        return \false;
    }
}

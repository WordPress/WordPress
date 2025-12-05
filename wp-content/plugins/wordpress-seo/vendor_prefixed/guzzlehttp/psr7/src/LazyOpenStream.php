<?php

declare (strict_types=1);
namespace YoastSEO_Vendor\GuzzleHttp\Psr7;

use YoastSEO_Vendor\Psr\Http\Message\StreamInterface;
/**
 * Lazily reads or writes to a file that is opened only after an IO operation
 * take place on the stream.
 */
final class LazyOpenStream implements \YoastSEO_Vendor\Psr\Http\Message\StreamInterface
{
    use StreamDecoratorTrait;
    /** @var string */
    private $filename;
    /** @var string */
    private $mode;
    /**
     * @var StreamInterface
     */
    private $stream;
    /**
     * @param string $filename File to lazily open
     * @param string $mode     fopen mode to use when opening the stream
     */
    public function __construct(string $filename, string $mode)
    {
        $this->filename = $filename;
        $this->mode = $mode;
        // unsetting the property forces the first access to go through
        // __get().
        unset($this->stream);
    }
    /**
     * Creates the underlying stream lazily when required.
     */
    protected function createStream() : \YoastSEO_Vendor\Psr\Http\Message\StreamInterface
    {
        return \YoastSEO_Vendor\GuzzleHttp\Psr7\Utils::streamFor(\YoastSEO_Vendor\GuzzleHttp\Psr7\Utils::tryFopen($this->filename, $this->mode));
    }
}

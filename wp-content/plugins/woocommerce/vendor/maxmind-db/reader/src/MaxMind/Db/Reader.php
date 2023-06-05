<?php

declare(strict_types=1);

namespace MaxMind\Db;

use ArgumentCountError;
use BadMethodCallException;
use Exception;
use InvalidArgumentException;
use MaxMind\Db\Reader\Decoder;
use MaxMind\Db\Reader\InvalidDatabaseException;
use MaxMind\Db\Reader\Metadata;
use MaxMind\Db\Reader\Util;
use UnexpectedValueException;

/**
 * Instances of this class provide a reader for the MaxMind DB format. IP
 * addresses can be looked up using the get method.
 */
class Reader
{
    /**
     * @var int
     */
    private static $DATA_SECTION_SEPARATOR_SIZE = 16;
    /**
     * @var string
     */
    private static $METADATA_START_MARKER = "\xAB\xCD\xEFMaxMind.com";
    /**
     * @var int
     */
    private static $METADATA_START_MARKER_LENGTH = 14;
    /**
     * @var int
     */
    private static $METADATA_MAX_SIZE = 131072; // 128 * 1024 = 128KiB

    /**
     * @var Decoder
     */
    private $decoder;
    /**
     * @var resource
     */
    private $fileHandle;
    /**
     * @var int
     */
    private $fileSize;
    /**
     * @var int
     */
    private $ipV4Start;
    /**
     * @var Metadata
     */
    private $metadata;

    /**
     * Constructs a Reader for the MaxMind DB format. The file passed to it must
     * be a valid MaxMind DB file such as a GeoIp2 database file.
     *
     * @param string $database
     *                         the MaxMind DB file to use
     *
     * @throws InvalidArgumentException for invalid database path or unknown arguments
     * @throws InvalidDatabaseException
     *                                  if the database is invalid or there is an error reading
     *                                  from it
     */
    public function __construct(string $database)
    {
        if (\func_num_args() !== 1) {
            throw new ArgumentCountError(
                sprintf('%s() expects exactly 1 parameter, %d given', __METHOD__, \func_num_args())
            );
        }

        $fileHandle = @fopen($database, 'rb');
        if ($fileHandle === false) {
            throw new InvalidArgumentException(
                "The file \"$database\" does not exist or is not readable."
            );
        }
        $this->fileHandle = $fileHandle;

        $fileSize = @filesize($database);
        if ($fileSize === false) {
            throw new UnexpectedValueException(
                "Error determining the size of \"$database\"."
            );
        }
        $this->fileSize = $fileSize;

        $start = $this->findMetadataStart($database);
        $metadataDecoder = new Decoder($this->fileHandle, $start);
        [$metadataArray] = $metadataDecoder->decode($start);
        $this->metadata = new Metadata($metadataArray);
        $this->decoder = new Decoder(
            $this->fileHandle,
            $this->metadata->searchTreeSize + self::$DATA_SECTION_SEPARATOR_SIZE
        );
        $this->ipV4Start = $this->ipV4StartNode();
    }

    /**
     * Retrieves the record for the IP address.
     *
     * @param string $ipAddress
     *                          the IP address to look up
     *
     * @throws BadMethodCallException   if this method is called on a closed database
     * @throws InvalidArgumentException if something other than a single IP address is passed to the method
     * @throws InvalidDatabaseException
     *                                  if the database is invalid or there is an error reading
     *                                  from it
     *
     * @return mixed the record for the IP address
     */
    public function get(string $ipAddress)
    {
        if (\func_num_args() !== 1) {
            throw new ArgumentCountError(
                sprintf('%s() expects exactly 1 parameter, %d given', __METHOD__, \func_num_args())
            );
        }
        [$record] = $this->getWithPrefixLen($ipAddress);

        return $record;
    }

    /**
     * Retrieves the record for the IP address and its associated network prefix length.
     *
     * @param string $ipAddress
     *                          the IP address to look up
     *
     * @throws BadMethodCallException   if this method is called on a closed database
     * @throws InvalidArgumentException if something other than a single IP address is passed to the method
     * @throws InvalidDatabaseException
     *                                  if the database is invalid or there is an error reading
     *                                  from it
     *
     * @return array an array where the first element is the record and the
     *               second the network prefix length for the record
     */
    public function getWithPrefixLen(string $ipAddress): array
    {
        if (\func_num_args() !== 1) {
            throw new ArgumentCountError(
                sprintf('%s() expects exactly 1 parameter, %d given', __METHOD__, \func_num_args())
            );
        }

        if (!\is_resource($this->fileHandle)) {
            throw new BadMethodCallException(
                'Attempt to read from a closed MaxMind DB.'
            );
        }

        [$pointer, $prefixLen] = $this->findAddressInTree($ipAddress);
        if ($pointer === 0) {
            return [null, $prefixLen];
        }

        return [$this->resolveDataPointer($pointer), $prefixLen];
    }

    private function findAddressInTree(string $ipAddress): array
    {
        $packedAddr = @inet_pton($ipAddress);
        if ($packedAddr === false) {
            throw new InvalidArgumentException(
                "The value \"$ipAddress\" is not a valid IP address."
            );
        }

        $rawAddress = unpack('C*', $packedAddr);

        $bitCount = \count($rawAddress) * 8;

        // The first node of the tree is always node 0, at the beginning of the
        // value
        $node = 0;

        $metadata = $this->metadata;

        // Check if we are looking up an IPv4 address in an IPv6 tree. If this
        // is the case, we can skip over the first 96 nodes.
        if ($metadata->ipVersion === 6) {
            if ($bitCount === 32) {
                $node = $this->ipV4Start;
            }
        } elseif ($metadata->ipVersion === 4 && $bitCount === 128) {
            throw new InvalidArgumentException(
                "Error looking up $ipAddress. You attempted to look up an"
                . ' IPv6 address in an IPv4-only database.'
            );
        }

        $nodeCount = $metadata->nodeCount;

        for ($i = 0; $i < $bitCount && $node < $nodeCount; ++$i) {
            $tempBit = 0xFF & $rawAddress[($i >> 3) + 1];
            $bit = 1 & ($tempBit >> 7 - ($i % 8));

            $node = $this->readNode($node, $bit);
        }
        if ($node === $nodeCount) {
            // Record is empty
            return [0, $i];
        }
        if ($node > $nodeCount) {
            // Record is a data pointer
            return [$node, $i];
        }

        throw new InvalidDatabaseException(
            'Invalid or corrupt database. Maximum search depth reached without finding a leaf node'
        );
    }

    private function ipV4StartNode(): int
    {
        // If we have an IPv4 database, the start node is the first node
        if ($this->metadata->ipVersion === 4) {
            return 0;
        }

        $node = 0;

        for ($i = 0; $i < 96 && $node < $this->metadata->nodeCount; ++$i) {
            $node = $this->readNode($node, 0);
        }

        return $node;
    }

    private function readNode(int $nodeNumber, int $index): int
    {
        $baseOffset = $nodeNumber * $this->metadata->nodeByteSize;

        switch ($this->metadata->recordSize) {
            case 24:
                $bytes = Util::read($this->fileHandle, $baseOffset + $index * 3, 3);
                [, $node] = unpack('N', "\x00" . $bytes);

                return $node;

            case 28:
                $bytes = Util::read($this->fileHandle, $baseOffset + 3 * $index, 4);
                if ($index === 0) {
                    $middle = (0xF0 & \ord($bytes[3])) >> 4;
                } else {
                    $middle = 0x0F & \ord($bytes[0]);
                }
                [, $node] = unpack('N', \chr($middle) . substr($bytes, $index, 3));

                return $node;

            case 32:
                $bytes = Util::read($this->fileHandle, $baseOffset + $index * 4, 4);
                [, $node] = unpack('N', $bytes);

                return $node;

            default:
                throw new InvalidDatabaseException(
                    'Unknown record size: '
                    . $this->metadata->recordSize
                );
        }
    }

    /**
     * @return mixed
     */
    private function resolveDataPointer(int $pointer)
    {
        $resolved = $pointer - $this->metadata->nodeCount
            + $this->metadata->searchTreeSize;
        if ($resolved >= $this->fileSize) {
            throw new InvalidDatabaseException(
                "The MaxMind DB file's search tree is corrupt"
            );
        }

        [$data] = $this->decoder->decode($resolved);

        return $data;
    }

    /*
     * This is an extremely naive but reasonably readable implementation. There
     * are much faster algorithms (e.g., Boyer-Moore) for this if speed is ever
     * an issue, but I suspect it won't be.
     */
    private function findMetadataStart(string $filename): int
    {
        $handle = $this->fileHandle;
        $fstat = fstat($handle);
        $fileSize = $fstat['size'];
        $marker = self::$METADATA_START_MARKER;
        $markerLength = self::$METADATA_START_MARKER_LENGTH;

        $minStart = $fileSize - min(self::$METADATA_MAX_SIZE, $fileSize);

        for ($offset = $fileSize - $markerLength; $offset >= $minStart; --$offset) {
            if (fseek($handle, $offset) !== 0) {
                break;
            }

            $value = fread($handle, $markerLength);
            if ($value === $marker) {
                return $offset + $markerLength;
            }
        }

        throw new InvalidDatabaseException(
            "Error opening database file ($filename). " .
            'Is this a valid MaxMind DB file?'
        );
    }

    /**
     * @throws InvalidArgumentException if arguments are passed to the method
     * @throws BadMethodCallException   if the database has been closed
     *
     * @return Metadata object for the database
     */
    public function metadata(): Metadata
    {
        if (\func_num_args()) {
            throw new ArgumentCountError(
                sprintf('%s() expects exactly 0 parameters, %d given', __METHOD__, \func_num_args())
            );
        }

        // Not technically required, but this makes it consistent with
        // C extension and it allows us to change our implementation later.
        if (!\is_resource($this->fileHandle)) {
            throw new BadMethodCallException(
                'Attempt to read from a closed MaxMind DB.'
            );
        }

        return clone $this->metadata;
    }

    /**
     * Closes the MaxMind DB and returns resources to the system.
     *
     * @throws Exception
     *                   if an I/O error occurs
     */
    public function close(): void
    {
        if (\func_num_args()) {
            throw new ArgumentCountError(
                sprintf('%s() expects exactly 0 parameters, %d given', __METHOD__, \func_num_args())
            );
        }

        if (!\is_resource($this->fileHandle)) {
            throw new BadMethodCallException(
                'Attempt to close a closed MaxMind DB.'
            );
        }
        fclose($this->fileHandle);
    }
}

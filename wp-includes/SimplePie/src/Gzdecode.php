<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie;

/**
 * Decode 'gzip' encoded HTTP data
 *
 * @link http://www.gzip.org/format.txt
 * @link https://www.php.net/manual/en/function.gzdecode.php
 * @deprecated since SimplePie 1.9.0, use `gzdecode` function instead.
 */
class Gzdecode
{
    /**
     * Compressed data
     *
     * @access private
     * @var string
     * @see gzdecode::$data
     */
    public $compressed_data;

    /**
     * Size of compressed data
     *
     * @access private
     * @var int
     */
    public $compressed_size;

    /**
     * Minimum size of a valid gzip string
     *
     * @access private
     * @var int
     */
    public $min_compressed_size = 18;

    /**
     * Current position of pointer
     *
     * @access private
     * @var int
     */
    public $position = 0;

    /**
     * Flags (FLG)
     *
     * @access private
     * @var int
     */
    public $flags;

    /**
     * Uncompressed data
     *
     * @access public
     * @see gzdecode::$compressed_data
     * @var string
     */
    public $data;

    /**
     * Modified time
     *
     * @access public
     * @var int
     */
    public $MTIME;

    /**
     * Extra Flags
     *
     * @access public
     * @var int
     */
    public $XFL;

    /**
     * Operating System
     *
     * @access public
     * @var int
     */
    public $OS;

    /**
     * Subfield ID 1
     *
     * @access public
     * @see gzdecode::$extra_field
     * @see gzdecode::$SI2
     * @var string
     */
    public $SI1;

    /**
     * Subfield ID 2
     *
     * @access public
     * @see gzdecode::$extra_field
     * @see gzdecode::$SI1
     * @var string
     */
    public $SI2;

    /**
     * Extra field content
     *
     * @access public
     * @see gzdecode::$SI1
     * @see gzdecode::$SI2
     * @var string
     */
    public $extra_field;

    /**
     * Original filename
     *
     * @access public
     * @var string
     */
    public $filename;

    /**
     * Human readable comment
     *
     * @access public
     * @var string
     */
    public $comment;

    /**
     * Don't allow anything to be set
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value)
    {
        throw new Exception("Cannot write property $name");
    }

    /**
     * Set the compressed string and related properties
     *
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->compressed_data = $data;
        $this->compressed_size = strlen($data);
    }

    /**
     * Decode the GZIP stream
     *
     * @return bool Successfulness
     */
    public function parse()
    {
        if ($this->compressed_size >= $this->min_compressed_size) {
            $len = 0;

            // Check ID1, ID2, and CM
            if (substr($this->compressed_data, 0, 3) !== "\x1F\x8B\x08") {
                return false;
            }

            // Get the FLG (FLaGs)
            $this->flags = ord($this->compressed_data[3]);

            // FLG bits above (1 << 4) are reserved
            if ($this->flags > 0x1F) {
                return false;
            }

            // Advance the pointer after the above
            $this->position += 4;

            // MTIME
            $mtime = substr($this->compressed_data, $this->position, 4);
            // Reverse the string if we're on a big-endian arch because l is the only signed long and is machine endianness
            if (current((array) unpack('S', "\x00\x01")) === 1) {
                $mtime = strrev($mtime);
            }
            $this->MTIME = current((array) unpack('l', $mtime));
            $this->position += 4;

            // Get the XFL (eXtra FLags)
            $this->XFL = ord($this->compressed_data[$this->position++]);

            // Get the OS (Operating System)
            $this->OS = ord($this->compressed_data[$this->position++]);

            // Parse the FEXTRA
            if ($this->flags & 4) {
                // Read subfield IDs
                $this->SI1 = $this->compressed_data[$this->position++];
                $this->SI2 = $this->compressed_data[$this->position++];

                // SI2 set to zero is reserved for future use
                if ($this->SI2 === "\x00") {
                    return false;
                }

                // Get the length of the extra field
                $len = current((array) unpack('v', substr($this->compressed_data, $this->position, 2)));
                $this->position += 2;

                // Check the length of the string is still valid
                $this->min_compressed_size += $len + 4;
                if ($this->compressed_size >= $this->min_compressed_size) {
                    // Set the extra field to the given data
                    $this->extra_field = substr($this->compressed_data, $this->position, $len);
                    $this->position += $len;
                } else {
                    return false;
                }
            }

            // Parse the FNAME
            if ($this->flags & 8) {
                // Get the length of the filename
                $len = strcspn($this->compressed_data, "\x00", $this->position);

                // Check the length of the string is still valid
                $this->min_compressed_size += $len + 1;
                if ($this->compressed_size >= $this->min_compressed_size) {
                    // Set the original filename to the given string
                    $this->filename = substr($this->compressed_data, $this->position, $len);
                    $this->position += $len + 1;
                } else {
                    return false;
                }
            }

            // Parse the FCOMMENT
            if ($this->flags & 16) {
                // Get the length of the comment
                $len = strcspn($this->compressed_data, "\x00", $this->position);

                // Check the length of the string is still valid
                $this->min_compressed_size += $len + 1;
                if ($this->compressed_size >= $this->min_compressed_size) {
                    // Set the original comment to the given string
                    $this->comment = substr($this->compressed_data, $this->position, $len);
                    $this->position += $len + 1;
                } else {
                    return false;
                }
            }

            // Parse the FHCRC
            if ($this->flags & 2) {
                // Check the length of the string is still valid
                $this->min_compressed_size += $len + 2;
                if ($this->compressed_size >= $this->min_compressed_size) {
                    // Read the CRC
                    $crc = current((array) unpack('v', substr($this->compressed_data, $this->position, 2)));

                    // Check the CRC matches
                    if ((crc32(substr($this->compressed_data, 0, $this->position)) & 0xFFFF) === $crc) {
                        $this->position += 2;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }

            // Decompress the actual data
            if (($data = gzinflate(substr($this->compressed_data, $this->position, -8))) === false) {
                return false;
            }

            $this->data = $data;
            $this->position = $this->compressed_size - 8;

            // Check CRC of data
            $crc = current((array) unpack('V', substr($this->compressed_data, $this->position, 4)));
            $this->position += 4;
            /*if (extension_loaded('hash') && sprintf('%u', current(unpack('V', hash('crc32b', $this->data)))) !== sprintf('%u', $crc))
            {
                return false;
            }*/

            // Check ISIZE of data
            $isize = current((array) unpack('V', substr($this->compressed_data, $this->position, 4)));
            $this->position += 4;
            if (sprintf('%u', strlen($this->data) & 0xFFFFFFFF) !== sprintf('%u', $isize)) {
                return false;
            }

            // Wow, against all odds, we've actually got a valid gzip string
            return true;
        }

        return false;
    }
}

class_alias('SimplePie\Gzdecode', 'SimplePie_gzdecode');

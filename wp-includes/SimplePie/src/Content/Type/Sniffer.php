<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\Content\Type;

use InvalidArgumentException;
use SimplePie\File;
use SimplePie\HTTP\Response;

/**
 * Content-type sniffing
 *
 * Based on the rules in http://tools.ietf.org/html/draft-abarth-mime-sniff-06
 *
 * This is used since we can't always trust Content-Type headers, and is based
 * upon the HTML5 parsing rules.
 *
 *
 * This class can be overloaded with {@see \SimplePie\SimplePie::set_content_type_sniffer_class()}
 */
class Sniffer
{
    /**
     * File object
     *
     * @var File|Response
     */
    public $file;

    /**
     * Create an instance of the class with the input file
     *
     * @param File|Response $file Input file
     */
    public function __construct(/* File */ $file)
    {
        if (!is_object($file) || !$file instanceof Response) {
            // For BC we're asking for `File`, but internally we accept every `Response` implementation
            throw new InvalidArgumentException(sprintf(
                '%s(): Argument #1 ($file) must be of type %s',
                __METHOD__,
                File::class
            ), 1);
        }

        $this->file = $file;
    }

    /**
     * Get the Content-Type of the specified file
     *
     * @return string Actual Content-Type
     */
    public function get_type()
    {
        $content_type = $this->file->has_header('content-type') ? $this->file->get_header_line('content-type') : null;
        $content_encoding = $this->file->has_header('content-encoding') ? $this->file->get_header_line('content-encoding') : null;
        if ($content_type !== null) {
            if ($content_encoding === null
                && ($content_type === 'text/plain'
                    || $content_type === 'text/plain; charset=ISO-8859-1'
                    || $content_type === 'text/plain; charset=iso-8859-1'
                    || $content_type === 'text/plain; charset=UTF-8')) {
                return $this->text_or_binary();
            }

            if (($pos = strpos($content_type, ';')) !== false) {
                $official = substr($content_type, 0, $pos);
            } else {
                $official = $content_type;
            }
            $official = trim(strtolower($official));

            if ($official === 'unknown/unknown'
                || $official === 'application/unknown') {
                return $this->unknown();
            } elseif (substr($official, -4) === '+xml'
                || $official === 'text/xml'
                || $official === 'application/xml') {
                return $official;
            } elseif (substr($official, 0, 6) === 'image/') {
                if ($return = $this->image()) {
                    return $return;
                }

                return $official;
            } elseif ($official === 'text/html') {
                return $this->feed_or_html();
            }

            return $official;
        }

        return $this->unknown();
    }

    /**
     * Sniff text or binary
     *
     * @return string Actual Content-Type
     */
    public function text_or_binary()
    {
        $body = $this->file->get_body_content();

        if (substr($body, 0, 2) === "\xFE\xFF"
            || substr($body, 0, 2) === "\xFF\xFE"
            || substr($body, 0, 4) === "\x00\x00\xFE\xFF"
            || substr($body, 0, 3) === "\xEF\xBB\xBF") {
            return 'text/plain';
        } elseif (preg_match('/[\x00-\x08\x0E-\x1A\x1C-\x1F]/', $body)) {
            return 'application/octet-stream';
        }

        return 'text/plain';
    }

    /**
     * Sniff unknown
     *
     * @return string Actual Content-Type
     */
    public function unknown()
    {
        $body = $this->file->get_body_content();

        $ws = strspn($body, "\x09\x0A\x0B\x0C\x0D\x20");
        if (strtolower(substr($body, $ws, 14)) === '<!doctype html'
            || strtolower(substr($body, $ws, 5)) === '<html'
            || strtolower(substr($body, $ws, 7)) === '<script') {
            return 'text/html';
        } elseif (substr($body, 0, 5) === '%PDF-') {
            return 'application/pdf';
        } elseif (substr($body, 0, 11) === '%!PS-Adobe-') {
            return 'application/postscript';
        } elseif (substr($body, 0, 6) === 'GIF87a'
            || substr($body, 0, 6) === 'GIF89a') {
            return 'image/gif';
        } elseif (substr($body, 0, 8) === "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") {
            return 'image/png';
        } elseif (substr($body, 0, 3) === "\xFF\xD8\xFF") {
            return 'image/jpeg';
        } elseif (substr($body, 0, 2) === "\x42\x4D") {
            return 'image/bmp';
        } elseif (substr($body, 0, 4) === "\x00\x00\x01\x00") {
            return 'image/vnd.microsoft.icon';
        }

        return $this->text_or_binary();
    }

    /**
     * Sniff images
     *
     * @return string|false Actual Content-Type
     */
    public function image()
    {
        $body = $this->file->get_body_content();

        if (substr($body, 0, 6) === 'GIF87a'
            || substr($body, 0, 6) === 'GIF89a') {
            return 'image/gif';
        } elseif (substr($body, 0, 8) === "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") {
            return 'image/png';
        } elseif (substr($body, 0, 3) === "\xFF\xD8\xFF") {
            return 'image/jpeg';
        } elseif (substr($body, 0, 2) === "\x42\x4D") {
            return 'image/bmp';
        } elseif (substr($body, 0, 4) === "\x00\x00\x01\x00") {
            return 'image/vnd.microsoft.icon';
        }

        return false;
    }

    /**
     * Sniff HTML
     *
     * @return string Actual Content-Type
     */
    public function feed_or_html()
    {
        $body = $this->file->get_body_content();

        $len = strlen($body);
        $pos = strspn($body, "\x09\x0A\x0D\x20\xEF\xBB\xBF");

        while ($pos < $len) {
            switch ($body[$pos]) {
                case "\x09":
                case "\x0A":
                case "\x0D":
                case "\x20":
                    $pos += strspn($body, "\x09\x0A\x0D\x20", $pos);
                    continue 2;

                case '<':
                    $pos++;
                    break;

                default:
                    return 'text/html';
            }

            if (substr($body, $pos, 3) === '!--') {
                $pos += 3;
                if ($pos < $len && ($pos = strpos($body, '-->', $pos)) !== false) {
                    $pos += 3;
                } else {
                    return 'text/html';
                }
            } elseif (substr($body, $pos, 1) === '!') {
                if ($pos < $len && ($pos = strpos($body, '>', $pos)) !== false) {
                    $pos++;
                } else {
                    return 'text/html';
                }
            } elseif (substr($body, $pos, 1) === '?') {
                if ($pos < $len && ($pos = strpos($body, '?>', $pos)) !== false) {
                    $pos += 2;
                } else {
                    return 'text/html';
                }
            } elseif (substr($body, $pos, 3) === 'rss'
                || substr($body, $pos, 7) === 'rdf:RDF') {
                return 'application/rss+xml';
            } elseif (substr($body, $pos, 4) === 'feed') {
                return 'application/atom+xml';
            } else {
                return 'text/html';
            }
        }

        return 'text/html';
    }
}

class_alias('SimplePie\Content\Type\Sniffer', 'SimplePie_Content_Type_Sniffer');

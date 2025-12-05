<?php

declare (strict_types=1);
namespace YoastSEO_Vendor\GuzzleHttp\Psr7;

/**
 * @internal
 */
final class Rfc7230
{
    /**
     * Header related regular expressions (based on amphp/http package)
     *
     * Note: header delimiter (\r\n) is modified to \r?\n to accept line feed only delimiters for BC reasons.
     *
     * @see https://github.com/amphp/http/blob/v1.0.1/src/Rfc7230.php#L12-L15
     *
     * @license https://github.com/amphp/http/blob/v1.0.1/LICENSE
     */
    public const HEADER_REGEX = "(^([^()<>@,;:\\\"/[\\]?={}\x01- ]++):[ \t]*+((?:[ \t]*+[!-~\x80-\xff]++)*+)[ \t]*+\r?\n)m";
    public const HEADER_FOLD_REGEX = "(\r?\n[ \t]++)";
}

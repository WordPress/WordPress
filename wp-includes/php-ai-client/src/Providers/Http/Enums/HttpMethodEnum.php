<?php

declare (strict_types=1);
namespace WordPress\AiClient\Providers\Http\Enums;

use WordPress\AiClient\Common\AbstractEnum;
/**
 * Represents HTTP request methods.
 *
 * @since 0.1.0
 *
 * @method static self GET()
 * @method static self POST()
 * @method static self PUT()
 * @method static self PATCH()
 * @method static self DELETE()
 * @method static self HEAD()
 * @method static self OPTIONS()
 * @method static self CONNECT()
 * @method static self TRACE()
 *
 * @method bool isGet()
 * @method bool isPost()
 * @method bool isPut()
 * @method bool isPatch()
 * @method bool isDelete()
 * @method bool isHead()
 * @method bool isOptions()
 * @method bool isConnect()
 * @method bool isTrace()
 */
final class HttpMethodEnum extends AbstractEnum
{
    /**
     * GET method for retrieving resources.
     *
     * @var string
     */
    public const GET = 'GET';
    /**
     * POST method for creating resources.
     *
     * @var string
     */
    public const POST = 'POST';
    /**
     * PUT method for updating/replacing resources.
     *
     * @var string
     */
    public const PUT = 'PUT';
    /**
     * PATCH method for partially updating resources.
     *
     * @var string
     */
    public const PATCH = 'PATCH';
    /**
     * DELETE method for removing resources.
     *
     * @var string
     */
    public const DELETE = 'DELETE';
    /**
     * HEAD method for retrieving headers only.
     *
     * @var string
     */
    public const HEAD = 'HEAD';
    /**
     * OPTIONS method for retrieving allowed methods.
     *
     * @var string
     */
    public const OPTIONS = 'OPTIONS';
    /**
     * CONNECT method for establishing tunnel.
     *
     * @var string
     */
    public const CONNECT = 'CONNECT';
    /**
     * TRACE method for diagnostic purposes.
     *
     * @var string
     */
    public const TRACE = 'TRACE';
    /**
     * Checks if this method is idempotent.
     *
     * @since 0.1.0
     *
     * @return bool True if the method is idempotent, false otherwise.
     */
    public function isIdempotent(): bool
    {
        return in_array($this->value, [self::GET, self::HEAD, self::OPTIONS, self::TRACE, self::PUT, self::DELETE], \true);
    }
    /**
     * Checks if this method typically has a request body.
     *
     * @since 0.1.0
     *
     * @return bool True if the method typically has a body, false otherwise.
     */
    public function hasBody(): bool
    {
        return in_array($this->value, [self::POST, self::PUT, self::PATCH], \true);
    }
}

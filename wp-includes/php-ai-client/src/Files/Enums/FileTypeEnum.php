<?php

declare (strict_types=1);
namespace WordPress\AiClient\Files\Enums;

use WordPress\AiClient\Common\AbstractEnum;
/**
 * Represents the type of file storage.
 *
 * @method static self inline() Returns the inline file type.
 * @method static self remote() Returns the remote file type.
 * @method bool isInline() Checks if this is an inline file type.
 * @method bool isRemote() Checks if this is a remote file type.
 *
 * @since 0.1.0
 */
class FileTypeEnum extends AbstractEnum
{
    /**
     * Inline file with base64-encoded data.
     *
     * @var string
     */
    public const INLINE = 'inline';
    /**
     * Remote file referenced by URL.
     *
     * @var string
     */
    public const REMOTE = 'remote';
}

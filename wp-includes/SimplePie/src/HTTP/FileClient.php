<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\HTTP;

use InvalidArgumentException;
use SimplePie\File;
use SimplePie\Misc;
use SimplePie\Registry;
use Throwable;

/**
 * HTTP Client based on \SimplePie\File
 *
 * @internal
 */
final class FileClient implements Client
{
    /** @var Registry */
    private $registry;

    /** @var array{timeout?: int, redirects?: int, useragent?: string, force_fsockopen?: bool, curl_options?: array<mixed>} */
    private $options;

    /**
     * @param array{timeout?: int, redirects?: int, useragent?: string, force_fsockopen?: bool, curl_options?: array<mixed>} $options
     */
    public function __construct(Registry $registry, array $options = [])
    {
        $this->registry = $registry;
        $this->options = $options;
    }

    /**
     * send a request and return the response
     *
     * @param Client::METHOD_* $method
     * @param array<string, string> $headers
     *
     * @throws ClientException if anything goes wrong requesting the data
     */
    public function request(string $method, string $url, array $headers = []): Response
    {
        // @phpstan-ignore-next-line Enforce PHPDoc type.
        if ($method !== self::METHOD_GET) {
            throw new InvalidArgumentException(sprintf(
                '%s(): Argument #1 ($method) only supports method "%s".',
                __METHOD__,
                self::METHOD_GET
            ), 1);
        }

        try {
            $file = $this->registry->create(File::class, [
                $url,
                $this->options['timeout'] ?? 10,
                $this->options['redirects'] ?? 5,
                $headers,
                $this->options['useragent'] ?? Misc::get_default_useragent(),
                $this->options['force_fsockopen'] ?? false,
                $this->options['curl_options'] ?? []
            ]);
        } catch (Throwable $th) {
            throw new ClientException($th->getMessage(), $th->getCode(), $th);
        }

        if ($file->error !== null && $file->get_status_code() === 0) {
            throw new ClientException($file->error);
        }

        return $file;
    }
}

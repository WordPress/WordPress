<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\HTTP;

/**
 * HTTP Response for rax text
 *
 * This interface must be interoperable with Psr\Http\Message\ResponseInterface
 * @see https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface
 *
 * @internal
 */
final class RawTextResponse implements Response
{
    /**
     * @var string
     */
    private $raw_text;

    /**
     * @var string
     */
    private $permanent_url;

    /**
     * @var array<non-empty-array<string>>
     */
    private $headers = [];

    /**
     * @var string
     */
    private $requested_url;

    public function __construct(string $raw_text, string $filepath)
    {
        $this->raw_text = $raw_text;
        $this->permanent_url = $filepath;
        $this->requested_url = $filepath;
    }

    public function get_permanent_uri(): string
    {
        return $this->permanent_url;
    }

    public function get_final_requested_uri(): string
    {
        return $this->requested_url;
    }

    public function get_status_code(): int
    {
        return 200;
    }

    public function get_headers(): array
    {
        return $this->headers;
    }

    public function has_header(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function get_header(string $name): array
    {
        return isset($this->headers[strtolower($name)]) ? $this->headers[$name] : [];
    }

    public function with_header(string $name, $value)
    {
        $new = clone $this;

        $newHeader = [
            strtolower($name) => (array) $value,
        ];
        $new->headers = $newHeader + $this->headers;

        return $new;
    }

    public function get_header_line(string $name): string
    {
        return isset($this->headers[strtolower($name)]) ? implode(", ", $this->headers[$name]) : '';
    }

    public function get_body_content(): string
    {
        return $this->raw_text;
    }
}

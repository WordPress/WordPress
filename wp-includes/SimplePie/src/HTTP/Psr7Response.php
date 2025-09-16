<?php

// SPDX-FileCopyrightText: 2004-2023 Ryan Parman, Sam Sneddon, Ryan McCue
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace SimplePie\HTTP;

use Psr\Http\Message\ResponseInterface;

/**
 * HTTP Response based on a PSR-7 response
 *
 * This interface must be interoperable with Psr\Http\Message\ResponseInterface
 * @see https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface
 *
 * @internal
 */
final class Psr7Response implements Response
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var string
     */
    private $permanent_url;

    /**
     * @var string
     */
    private $requested_url;

    public function __construct(ResponseInterface $response, string $permanent_url, string $requested_url)
    {
        $this->response = $response;
        $this->permanent_url = $permanent_url;
        $this->requested_url = $requested_url;
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
        return $this->response->getStatusCode();
    }

    public function get_headers(): array
    {
        // The filtering is probably redundant but let’s make PHPStan happy.
        return array_filter($this->response->getHeaders(), function (array $header): bool {
            return count($header) >= 1;
        });
    }

    public function has_header(string $name): bool
    {
        return $this->response->hasHeader($name);
    }

    public function with_header(string $name, $value)
    {
        return new self($this->response->withHeader($name, $value), $this->permanent_url, $this->requested_url);
    }

    public function get_header(string $name): array
    {
        return $this->response->getHeader($name);
    }

    public function get_header_line(string $name): string
    {
        return $this->response->getHeaderLine($name);
    }

    public function get_body_content(): string
    {
        return $this->response->getBody()->__toString();
    }
}

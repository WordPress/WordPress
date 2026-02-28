<?php

declare(strict_types=1);

namespace Sentry;

/**
 * This class represents a Sentry DSN that can be obtained from the Settings
 * page of a project.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class Dsn implements \Stringable
{
    /**
     * @var string Regex to match the organization ID in the host.
     *             This only applies to Sentry SaaS DSNs that contain the organization ID.
     */
    private const SENTRY_ORG_ID_REGEX = '/^o(\d+)\./';

    /**
     * @var string The protocol to be used to access the resource
     */
    private $scheme;

    /**
     * @var string The host that holds the resource
     */
    private $host;

    /**
     * @var int The port on which the resource is exposed
     */
    private $port;

    /**
     * @var string The public key to authenticate the SDK
     */
    private $publicKey;

    /**
     * @var string The ID of the resource to access
     */
    private $projectId;

    /**
     * @var string The specific resource that the web client wants to access
     */
    private $path;

    /**
     * @var int|null
     */
    private $orgId;

    /**
     * Class constructor.
     *
     * @param string $scheme    The protocol to be used to access the resource
     * @param string $host      The host that holds the resource
     * @param int    $port      The port on which the resource is exposed
     * @param string $projectId The ID of the resource to access
     * @param string $path      The specific resource that the web client wants to access
     * @param string $publicKey The public key to authenticate the SDK
     * @param ?int   $orgId     The org ID
     */
    private function __construct(string $scheme, string $host, int $port, string $projectId, string $path, string $publicKey, ?int $orgId = null)
    {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->projectId = $projectId;
        $this->path = $path;
        $this->publicKey = $publicKey;
        $this->orgId = $orgId;
    }

    /**
     * Creates an instance of this class by parsing the given string.
     *
     * @param string $value The string to parse
     */
    public static function createFromString(string $value): self
    {
        $parsedDsn = parse_url($value);

        if ($parsedDsn === false) {
            throw new \InvalidArgumentException(\sprintf('The "%s" DSN is invalid.', $value));
        }

        foreach (['scheme', 'host', 'path', 'user'] as $component) {
            if (!isset($parsedDsn[$component]) || (isset($parsedDsn[$component]) && empty($parsedDsn[$component]))) {
                throw new \InvalidArgumentException(\sprintf('The "%s" DSN must contain a scheme, a host, a user and a path component.', $value));
            }
        }

        if (!\in_array($parsedDsn['scheme'], ['http', 'https'], true)) {
            throw new \InvalidArgumentException(\sprintf('The scheme of the "%s" DSN must be either "http" or "https".', $value));
        }

        $segmentPaths = explode('/', $parsedDsn['path']);
        $projectId = array_pop($segmentPaths);
        $lastSlashPosition = strrpos($parsedDsn['path'], '/');
        $path = $parsedDsn['path'];

        if ($lastSlashPosition !== false) {
            $path = substr($parsedDsn['path'], 0, $lastSlashPosition);
        }

        $orgId = null;
        if (preg_match(self::SENTRY_ORG_ID_REGEX, $parsedDsn['host'], $matches) == 1) {
            $orgId = (int) $matches[1];
        }

        return new self(
            $parsedDsn['scheme'],
            $parsedDsn['host'],
            $parsedDsn['port'] ?? ($parsedDsn['scheme'] === 'http' ? 80 : 443),
            $projectId,
            $path,
            $parsedDsn['user'],
            $orgId
        );
    }

    /**
     * Gets the protocol to be used to access the resource.
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Gets the host that holds the resource.
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Gets the port on which the resource is exposed.
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Gets the specific resource that the web client wants to access.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Gets the ID of the resource to access.
     */
    public function getProjectId(): string
    {
        return $this->projectId;
    }

    /**
     * Gets the public key to authenticate the SDK.
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getOrgId(): ?int
    {
        return $this->orgId;
    }

    /**
     * Returns the URL of the API for the envelope endpoint.
     */
    public function getEnvelopeApiEndpointUrl(): string
    {
        return $this->getBaseEndpointUrl() . '/envelope/';
    }

    /**
     * Returns the URL of the API for the CSP report endpoint.
     */
    public function getCspReportEndpointUrl(): string
    {
        return $this->getBaseEndpointUrl() . '/security/?sentry_key=' . $this->publicKey;
    }

    /**
     * @see https://www.php.net/manual/en/language.oop5.magic.php#object.tostring
     */
    public function __toString(): string
    {
        $url = $this->scheme . '://' . $this->publicKey;

        $url .= '@' . $this->host;

        if (($this->scheme === 'http' && $this->port !== 80) || ($this->scheme === 'https' && $this->port !== 443)) {
            $url .= ':' . $this->port;
        }

        if ($this->path !== null) {
            $url .= $this->path;
        }

        $url .= '/' . $this->projectId;

        return $url;
    }

    /**
     * Returns the base url to Sentry from the DSN.
     */
    private function getBaseEndpointUrl(): string
    {
        $url = $this->scheme . '://' . $this->host;

        if (($this->scheme === 'http' && $this->port !== 80) || ($this->scheme === 'https' && $this->port !== 443)) {
            $url .= ':' . $this->port;
        }

        if ($this->path !== null) {
            $url .= $this->path;
        }

        $url .= '/api/' . $this->projectId;

        return $url;
    }
}

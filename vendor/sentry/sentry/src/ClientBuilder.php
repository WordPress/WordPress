<?php

declare(strict_types=1);

namespace Sentry;

use Psr\Log\LoggerInterface;
use Sentry\HttpClient\HttpClient;
use Sentry\HttpClient\HttpClientInterface;
use Sentry\Serializer\PayloadSerializer;
use Sentry\Serializer\RepresentationSerializerInterface;
use Sentry\Transport\HttpTransport;
use Sentry\Transport\TransportInterface;

/**
 * A configurable builder for Client objects.
 */
final class ClientBuilder
{
    /**
     * @var Options The client options
     */
    private $options;

    /**
     * @var TransportInterface|null The transport
     */
    private $transport;

    /**
     * @var HttpClientInterface|null The HTTP client
     */
    private $httpClient;

    /**
     * @var RepresentationSerializerInterface|null The representation serializer to be injected in the client
     */
    private $representationSerializer;

    /**
     * @var LoggerInterface|null A PSR-3 logger to log internal errors and debug messages
     */
    private $logger;

    /**
     * @var string The SDK identifier, to be used in {@see Event} and {@see SentryAuth}
     */
    private $sdkIdentifier = Client::SDK_IDENTIFIER;

    /**
     * @var string The SDK version of the Client
     */
    private $sdkVersion = Client::SDK_VERSION;

    /**
     * Class constructor.
     *
     * @param Options|null $options The client options
     */
    public function __construct(?Options $options = null)
    {
        $this->options = $options ?? new Options();
    }

    /**
     * @param array<string, mixed> $options The client options, in naked array form
     */
    public static function create(array $options = []): self
    {
        return new self(new Options($options));
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function setRepresentationSerializer(RepresentationSerializerInterface $representationSerializer): self
    {
        $this->representationSerializer = $representationSerializer;

        return $this;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger ?? $this->options->getLogger();
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function setSdkIdentifier(string $sdkIdentifier): self
    {
        $this->sdkIdentifier = $sdkIdentifier;

        return $this;
    }

    public function setSdkVersion(string $sdkVersion): self
    {
        $this->sdkVersion = $sdkVersion;

        return $this;
    }

    public function getTransport(): TransportInterface
    {
        return $this->transport
            ?? $this->options->getTransport()
            ?? new HttpTransport(
                $this->options,
                $this->getHttpClient(),
                new PayloadSerializer($this->options),
                $this->getLogger()
            );
    }

    public function setTransport(TransportInterface $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient
            ?? $this->options->getHttpClient()
            ?? new HttpClient($this->sdkIdentifier, $this->sdkVersion);
    }

    public function setHttpClient(HttpClientInterface $httpClient): self
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    public function getClient(): ClientInterface
    {
        return new Client(
            $this->options,
            $this->getTransport(),
            $this->sdkIdentifier,
            $this->sdkVersion,
            $this->representationSerializer,
            $this->getLogger()
        );
    }
}

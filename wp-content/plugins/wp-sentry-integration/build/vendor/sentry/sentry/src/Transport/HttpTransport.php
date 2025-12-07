<?php

declare (strict_types=1);
namespace Sentry\Transport;

use WPSentry\ScopedVendor\Psr\Log\LoggerInterface;
use WPSentry\ScopedVendor\Psr\Log\NullLogger;
use Sentry\Event;
use Sentry\HttpClient\HttpClientInterface;
use Sentry\HttpClient\Request;
use Sentry\Options;
use Sentry\Serializer\PayloadSerializerInterface;
use Sentry\Spotlight\SpotlightClient;
/**
 * @internal
 */
class HttpTransport implements \Sentry\Transport\TransportInterface
{
    /**
     * @var Options
     */
    private $options;
    /**
     * @var HttpClientInterface The HTTP client
     */
    private $httpClient;
    /**
     * @var PayloadSerializerInterface The event serializer
     */
    private $payloadSerializer;
    /**
     * @var LoggerInterface A PSR-3 logger
     */
    private $logger;
    /**
     * @var RateLimiter The rate limiter
     */
    private $rateLimiter;
    /**
     * @param Options                    $options           The options
     * @param HttpClientInterface        $httpClient        The HTTP client
     * @param PayloadSerializerInterface $payloadSerializer The event serializer
     * @param LoggerInterface|null       $logger            An instance of a PSR-3 logger
     */
    public function __construct(\Sentry\Options $options, \Sentry\HttpClient\HttpClientInterface $httpClient, \Sentry\Serializer\PayloadSerializerInterface $payloadSerializer, ?\WPSentry\ScopedVendor\Psr\Log\LoggerInterface $logger = null)
    {
        $this->options = $options;
        $this->httpClient = $httpClient;
        $this->payloadSerializer = $payloadSerializer;
        $this->logger = $logger ?? new \WPSentry\ScopedVendor\Psr\Log\NullLogger();
        $this->rateLimiter = new \Sentry\Transport\RateLimiter($this->logger);
    }
    /**
     * {@inheritdoc}
     */
    public function send(\Sentry\Event $event) : \Sentry\Transport\Result
    {
        $this->sendRequestToSpotlight($event);
        $eventDescription = \sprintf('%s%s [%s]', $event->getLevel() !== null ? $event->getLevel() . ' ' : '', (string) $event->getType(), (string) $event->getId());
        if ($this->options->getDsn() === null) {
            $this->logger->info(\sprintf('Skipping %s, because no DSN is set.', $eventDescription), ['event' => $event]);
            return new \Sentry\Transport\Result(\Sentry\Transport\ResultStatus::skipped(), $event);
        }
        $targetDescription = \sprintf('%s [project:%s]', $this->options->getDsn()->getHost(), $this->options->getDsn()->getProjectId());
        $this->logger->info(\sprintf('Sending %s to %s.', $eventDescription, $targetDescription), ['event' => $event]);
        $eventType = $event->getType();
        if ($this->rateLimiter->isRateLimited((string) $eventType)) {
            $this->logger->warning(\sprintf('Rate limit exceeded for sending requests of type "%s".', (string) $eventType), ['event' => $event]);
            return new \Sentry\Transport\Result(\Sentry\Transport\ResultStatus::rateLimit());
        }
        $request = new \Sentry\HttpClient\Request();
        $request->setStringBody($this->payloadSerializer->serialize($event));
        try {
            $response = $this->httpClient->sendRequest($request, $this->options);
        } catch (\Throwable $exception) {
            $this->logger->error(\sprintf('Failed to send %s to %s. Reason: "%s".', $eventDescription, $targetDescription, $exception->getMessage()), ['exception' => $exception, 'event' => $event]);
            return new \Sentry\Transport\Result(\Sentry\Transport\ResultStatus::failed());
        }
        if ($response->hasError()) {
            $this->logger->error(\sprintf('Failed to send %s to %s. Reason: "%s".', $eventDescription, $targetDescription, $response->getError()), ['event' => $event]);
            return new \Sentry\Transport\Result(\Sentry\Transport\ResultStatus::unknown());
        }
        $this->rateLimiter->handleResponse($response);
        $resultStatus = \Sentry\Transport\ResultStatus::createFromHttpStatusCode($response->getStatusCode());
        $this->logger->info(\sprintf('Sent %s to %s. Result: "%s" (status: %s).', $eventDescription, $targetDescription, \strtolower((string) $resultStatus), $response->getStatusCode()), ['response' => $response, 'event' => $event]);
        return new \Sentry\Transport\Result($resultStatus, $event);
    }
    /**
     * {@inheritdoc}
     */
    public function close(?int $timeout = null) : \Sentry\Transport\Result
    {
        return new \Sentry\Transport\Result(\Sentry\Transport\ResultStatus::success());
    }
    /**
     * @internal
     */
    public function getHttpClient() : \Sentry\HttpClient\HttpClientInterface
    {
        return $this->httpClient;
    }
    private function sendRequestToSpotlight(\Sentry\Event $event) : void
    {
        if (!$this->options->isSpotlightEnabled()) {
            return;
        }
        $request = new \Sentry\HttpClient\Request();
        $request->setStringBody($this->payloadSerializer->serialize($event));
        try {
            $spotLightResponse = \Sentry\Spotlight\SpotlightClient::sendRequest($request, $this->options->getSpotlightUrl() . '/stream');
            if ($spotLightResponse->hasError()) {
                $this->logger->info(\sprintf('Failed to send the event to Spotlight. Reason: "%s".', $spotLightResponse->getError()), ['event' => $event]);
            }
        } catch (\Throwable $exception) {
            $this->logger->info(\sprintf('Failed to send the event to Spotlight. Reason: "%s".', $exception->getMessage()), ['exception' => $exception, 'event' => $event]);
        }
    }
}

<?php

declare(strict_types=1);

namespace Sentry\Transport;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Sentry\Event;
use Sentry\HttpClient\HttpClientInterface;
use Sentry\HttpClient\Request;
use Sentry\Options;
use Sentry\Serializer\PayloadSerializerInterface;
use Sentry\Spotlight\SpotlightClient;

/**
 * @internal
 */
class HttpTransport implements TransportInterface
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
    public function __construct(
        Options $options,
        HttpClientInterface $httpClient,
        PayloadSerializerInterface $payloadSerializer,
        ?LoggerInterface $logger = null
    ) {
        $this->options = $options;
        $this->httpClient = $httpClient;
        $this->payloadSerializer = $payloadSerializer;
        $this->logger = $logger ?? new NullLogger();
        $this->rateLimiter = new RateLimiter($this->logger);
    }

    /**
     * {@inheritdoc}
     */
    public function send(Event $event): Result
    {
        $this->sendRequestToSpotlight($event);

        $eventDescription = \sprintf(
            '%s%s [%s]',
            $event->getLevel() !== null ? $event->getLevel() . ' ' : '',
            (string) $event->getType(),
            (string) $event->getId()
        );

        if ($this->options->getDsn() === null) {
            $this->logger->info(\sprintf('Skipping %s, because no DSN is set.', $eventDescription), ['event' => $event]);

            return new Result(ResultStatus::skipped(), $event);
        }

        $targetDescription = \sprintf(
            '%s [project:%s]',
            $this->options->getDsn()->getHost(),
            $this->options->getDsn()->getProjectId()
        );

        $this->logger->info(\sprintf('Sending %s to %s.', $eventDescription, $targetDescription), ['event' => $event]);

        $eventType = $event->getType();
        if ($this->rateLimiter->isRateLimited((string) $eventType)) {
            $this->logger->warning(
                \sprintf('Rate limit exceeded for sending requests of type "%s".', (string) $eventType),
                ['event' => $event]
            );

            return new Result(ResultStatus::rateLimit());
        }

        // Since profiles are attached to transaction we have to check separately if they are rate limited.
        // We can do this after transactions have been checked because if transactions are rate limited,
        // so are profiles but not the other way around.
        if ($event->getSdkMetadata('profile') !== null) {
            if ($this->rateLimiter->isRateLimited(RateLimiter::DATA_CATEGORY_PROFILE)) {
                // Just remove profiling data so the normal transaction can be sent.
                $event->setSdkMetadata('profile', null);
                $this->logger->warning(
                    'Rate limit exceeded for sending requests of type "profile". The profile has been dropped.',
                    ['event' => $event]
                );
            }
        }

        $request = new Request();
        $request->setStringBody($this->payloadSerializer->serialize($event));

        try {
            $response = $this->httpClient->sendRequest($request, $this->options);
        } catch (\Throwable $exception) {
            $this->logger->error(
                \sprintf('Failed to send %s to %s. Reason: "%s".', $eventDescription, $targetDescription, $exception->getMessage()),
                ['exception' => $exception, 'event' => $event]
            );

            return new Result(ResultStatus::failed());
        }

        if ($response->hasError()) {
            $this->logger->error(
                \sprintf('Failed to send %s to %s. Reason: "%s".', $eventDescription, $targetDescription, $response->getError()),
                ['event' => $event]
            );

            return new Result(ResultStatus::unknown());
        }

        $this->rateLimiter->handleResponse($response);

        $resultStatus = ResultStatus::createFromHttpStatusCode($response->getStatusCode());

        $this->logger->info(
            \sprintf('Sent %s to %s. Result: "%s" (status: %s).', $eventDescription, $targetDescription, strtolower((string) $resultStatus), $response->getStatusCode()),
            ['response' => $response, 'event' => $event]
        );

        return new Result($resultStatus, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function close(?int $timeout = null): Result
    {
        return new Result(ResultStatus::success());
    }

    /**
     * @internal
     */
    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    private function sendRequestToSpotlight(Event $event): void
    {
        if (!$this->options->isSpotlightEnabled()) {
            return;
        }

        $eventDescription = \sprintf(
            '%s%s [%s]',
            $event->getLevel() !== null ? $event->getLevel() . ' ' : '',
            (string) $event->getType(),
            (string) $event->getId()
        );

        $this->logger->info(\sprintf('Sending %s to Spotlight.', $eventDescription), ['event' => $event]);

        $request = new Request();
        $request->setStringBody($this->payloadSerializer->serialize($event));

        try {
            $spotLightResponse = SpotlightClient::sendRequest(
                $request,
                $this->options->getSpotlightUrl() . '/stream'
            );

            if ($spotLightResponse->hasError()) {
                $this->logger->info(
                    \sprintf('Failed to send the event to Spotlight. Reason: "%s".', $spotLightResponse->getError()),
                    ['event' => $event]
                );
            }
        } catch (\Throwable $exception) {
            $this->logger->info(
                \sprintf('Failed to send the event to Spotlight. Reason: "%s".', $exception->getMessage()),
                ['exception' => $exception, 'event' => $event]
            );
        }
    }
}

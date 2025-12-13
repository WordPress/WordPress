<?php

declare(strict_types=1);

namespace Sentry\Integration;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Sentry\Event;
use Sentry\Exception\JsonException;
use Sentry\Options;
use Sentry\SentrySdk;
use Sentry\State\Scope;
use Sentry\UserDataBag;
use Sentry\Util\JSON;
use Symfony\Component\OptionsResolver\Options as SymfonyOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This integration collects information from the request and attaches them to
 * the event.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class RequestIntegration implements IntegrationInterface
{
    /**
     * This constant represents the size limit in bytes beyond which the body
     * of the request is not captured when the `max_request_body_size` option
     * is set to `small`.
     */
    private const REQUEST_BODY_SMALL_MAX_CONTENT_LENGTH = 10 ** 3;

    /**
     * This constant represents the size limit in bytes beyond which the body
     * of the request is not captured when the `max_request_body_size` option
     * is set to `medium`.
     */
    private const REQUEST_BODY_MEDIUM_MAX_CONTENT_LENGTH = 10 ** 4;

    /**
     * This constant is a map of maximum allowed sizes for each value of the
     * `max_request_body_size` option.
     */
    private const MAX_REQUEST_BODY_SIZE_OPTION_TO_MAX_LENGTH_MAP = [
        'never' => 0,
        'small' => self::REQUEST_BODY_SMALL_MAX_CONTENT_LENGTH,
        'medium' => self::REQUEST_BODY_MEDIUM_MAX_CONTENT_LENGTH,
        'always' => \PHP_INT_MAX,
    ];

    /**
     * This constant defines the default list of headers that may contain
     * sensitive data and that will be sanitized if sending PII is disabled.
     */
    private const DEFAULT_SENSITIVE_HEADERS = [
        'Authorization',
        'Cookie',
        'Set-Cookie',
        'X-Forwarded-For',
        'X-Real-IP',
    ];

    /**
     * @var RequestFetcherInterface PSR-7 request fetcher
     */
    private $requestFetcher;

    /**
     * @var array<string, mixed> The options
     *
     * @psalm-var array{
     *     pii_sanitize_headers: string[]
     * }
     */
    private $options;

    /**
     * Constructor.
     *
     * @param RequestFetcherInterface|null $requestFetcher PSR-7 request fetcher
     * @param array<string, mixed>         $options        The options
     *
     * @psalm-param array{
     *     pii_sanitize_headers?: string[]
     * } $options
     */
    public function __construct(?RequestFetcherInterface $requestFetcher = null, array $options = [])
    {
        $resolver = new OptionsResolver();

        $this->configureOptions($resolver);

        $this->requestFetcher = $requestFetcher ?? new RequestFetcher();
        $this->options = $resolver->resolve($options);
    }

    /**
     * {@inheritdoc}
     */
    public function setupOnce(): void
    {
        Scope::addGlobalEventProcessor(function (Event $event): Event {
            $currentHub = SentrySdk::getCurrentHub();
            $integration = $currentHub->getIntegration(self::class);
            $client = $currentHub->getClient();

            // The client bound to the current hub, if any, could not have this
            // integration enabled. If this is the case, bail out
            if ($integration === null || $client === null) {
                return $event;
            }

            $this->processEvent($event, $client->getOptions());

            return $event;
        });
    }

    private function processEvent(Event $event, Options $options): void
    {
        $request = $this->requestFetcher->fetchRequest();

        if ($request === null) {
            return;
        }

        $requestData = [
            'url' => (string) $request->getUri(),
            'method' => $request->getMethod(),
        ];

        if ($request->getUri()->getQuery()) {
            $requestData['query_string'] = $request->getUri()->getQuery();
        }

        if ($options->shouldSendDefaultPii()) {
            $serverParams = $request->getServerParams();

            if (!empty($serverParams['REMOTE_ADDR'])) {
                $user = $event->getUser();
                $requestData['env']['REMOTE_ADDR'] = $serverParams['REMOTE_ADDR'];

                if ($user === null) {
                    $user = UserDataBag::createFromUserIpAddress($serverParams['REMOTE_ADDR']);
                } elseif ($user->getIpAddress() === null) {
                    $user->setIpAddress($serverParams['REMOTE_ADDR']);
                }

                $event->setUser($user);
            }

            $requestData['cookies'] = $request->getCookieParams();
            $requestData['headers'] = $request->getHeaders();
        } else {
            $requestData['headers'] = $this->sanitizeHeaders($request->getHeaders());
        }

        $requestBody = $this->captureRequestBody($options, $request);

        if (!empty($requestBody)) {
            $requestData['data'] = $requestBody;
        }

        $event->setRequest($requestData);
    }

    /**
     * Removes headers containing potential PII.
     *
     * @param array<array-key, string[]> $headers Array containing request headers
     *
     * @return array<string, string[]>
     */
    private function sanitizeHeaders(array $headers): array
    {
        foreach ($headers as $name => $values) {
            // Cast the header name into a string, to avoid errors on numeric headers
            $name = (string) $name;

            if (!\in_array(strtolower($name), $this->options['pii_sanitize_headers'], true)) {
                continue;
            }

            foreach ($values as $headerLine => $headerValue) {
                $headers[$name][$headerLine] = '[Filtered]';
            }
        }

        return $headers;
    }

    /**
     * Gets the decoded body of the request, if available. If the Content-Type
     * header contains "application/json" then the content is decoded and if
     * the parsing fails then the raw data is returned. If there are submitted
     * fields or files, all of their information are parsed and returned.
     *
     * @param Options                $options The options of the client
     * @param ServerRequestInterface $request The server request
     *
     * @return mixed
     */
    private function captureRequestBody(Options $options, ServerRequestInterface $request)
    {
        $maxRequestBodySize = $options->getMaxRequestBodySize();
        $requestBodySize = (int) $request->getHeaderLine('Content-Length');

        if (!$this->isRequestBodySizeWithinReadBounds($requestBodySize, $maxRequestBodySize)) {
            return null;
        }

        $requestData = $request->getParsedBody();
        $requestData = array_replace(
            $this->parseUploadedFiles($request->getUploadedFiles()),
            \is_array($requestData) ? $requestData : []
        );

        if (!empty($requestData)) {
            return $requestData;
        }

        $requestBody = '';
        $maxLength = self::MAX_REQUEST_BODY_SIZE_OPTION_TO_MAX_LENGTH_MAP[$maxRequestBodySize];

        if ($maxLength > 0) {
            $stream = $request->getBody();
            while ($maxLength > 0 && !$stream->eof()) {
                if ('' === $buffer = $stream->read(min($maxLength, self::REQUEST_BODY_MEDIUM_MAX_CONTENT_LENGTH))) {
                    break;
                }
                $requestBody .= $buffer;
                $maxLength -= \strlen($buffer);
            }
        }

        if ($request->getHeaderLine('Content-Type') === 'application/json') {
            try {
                return JSON::decode($requestBody);
            } catch (JsonException $exception) {
                // Fallback to returning the raw data from the request body
            }
        }

        return $requestBody;
    }

    /**
     * Create an array with the same structure as $uploadedFiles, but replacing
     * each UploadedFileInterface with an array of info.
     *
     * @param array<string, mixed> $uploadedFiles The uploaded files info from a PSR-7 server request
     *
     * @return array<string, mixed>
     */
    private function parseUploadedFiles(array $uploadedFiles): array
    {
        $result = [];

        foreach ($uploadedFiles as $key => $item) {
            if ($item instanceof UploadedFileInterface) {
                $result[$key] = [
                    'client_filename' => $item->getClientFilename(),
                    'client_media_type' => $item->getClientMediaType(),
                    'size' => $item->getSize(),
                ];
            } elseif (\is_array($item)) {
                $result[$key] = $this->parseUploadedFiles($item);
            } else {
                throw new \UnexpectedValueException(\sprintf('Expected either an object implementing the "%s" interface or an array. Got: "%s".', UploadedFileInterface::class, \is_object($item) ? \get_class($item) : \gettype($item)));
            }
        }

        return $result;
    }

    private function isRequestBodySizeWithinReadBounds(int $requestBodySize, string $maxRequestBodySize): bool
    {
        if ($requestBodySize <= 0) {
            return false;
        }

        if ($maxRequestBodySize === 'none' || $maxRequestBodySize === 'never') {
            return false;
        }

        if ($maxRequestBodySize === 'small' && $requestBodySize > self::REQUEST_BODY_SMALL_MAX_CONTENT_LENGTH) {
            return false;
        }

        if ($maxRequestBodySize === 'medium' && $requestBodySize > self::REQUEST_BODY_MEDIUM_MAX_CONTENT_LENGTH) {
            return false;
        }

        return true;
    }

    /**
     * Configures the options of the client.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('pii_sanitize_headers', self::DEFAULT_SENSITIVE_HEADERS);
        $resolver->setAllowedTypes('pii_sanitize_headers', 'string[]');
        $resolver->setNormalizer('pii_sanitize_headers', static function (SymfonyOptions $options, array $value): array {
            return array_map('strtolower', $value);
        });
    }
}

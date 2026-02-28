# Upgrade 3.x to 4.0

- This version exclusively uses the [envelope endpoint](https://develop.sentry.dev/sdk/envelopes/) to send event data to Sentry.

  If you are using [sentry.io](https://sentry.io), no action is needed.
  If you are using an on-premise/self-hosted installation of Sentry, the minimum requirement is now version `>= v20.6.0`.

- Added `ext-curl` as a composer requirement.

- The `IgnoreErrorsIntegration` integration was removed. Use the `ignore_exceptions` option instead.

  ```php
  Sentry\init([
      'ignore_exceptions' => [BadThingsHappenedException::class],
  ]);
  ```

  This option performs an [`is_a`](https://www.php.net/manual/en/function.is-a.php) check, so you can also ignore more generic exceptions.

- Removed support for `symfony/options-resolver: ^3.4.43`.

- The `RequestFetcher` now relies on `guzzlehttp/psr7: ^1.8.4|^2.1.1`.

- Added new methods to `ClientInterface`

  ```php
  public function getCspReportUrl(): ?string;

  public function getStacktraceBuilder(): StacktraceBuilder;
  ```

- Added new methods to `HubInterface`

  ```php
  public function captureCheckIn(string $slug, CheckInStatus $status, $duration = null, ?MonitorConfig $monitorConfig = null, ?string $checkInId = null): ?string;
  ```

- The new default value for the `trace_propagation_targets` option is now `null`. To not attach any headers to outgoing requests, using the `GuzzleTracingMiddleware`, set this option to `[]`.
- The `ignore_exceptions` option now performs a `is_a` check on the provided class strings.
- The `send_attempts` option was removed. You may implement a custom transport if you rely on this behaviour.
- The `enable_compression` option was removed. Use `http_compression` instead.
- The `logger` option now accepts a `Psr\Log\LoggerInterface` instance instead of `string`.

- Removed `Options::getSendAttempts/setSendAttempts()`.
- Removed `Options::isCompressionEnabled/setEnableCompression()`. Use `Options::isHttpCompressionEnabled/setEnableHttpCompression()` instead.
- Removed `SpanContext::fromTraceparent()`. Use `Sentry\continueTrace()` instead.
- Removed `TransactionContext::fromSentryTrace()`. Use `Sentry\continueTrace()` instead.
- Removed `Sentry\Exception\InvalidArgumentException`. Use `\InvalidArgumentException` instead.
- Removed `Sentry\Exception/ExceptionInterface`.
- Removed `ClientBuilderInterface()`.
- Removed `ClientBuilder::setSerializer()`.
- Removed `ClientBuilder::setTransportFactory()`. You can set a custom transport via the `transport` option.
- Removed `Client::__construct()` parameter `SerializerInterface $serializer`.
- Removed `TransportFactoryInterface`.
- Removed `DefaultTransportFactory`.
- Removed `HttpClientFactoryInterface`.
- Removed `HttpClientFactory`.
- Removed `NullTransport`.
- Removed `Dsn::getSecretKey()`.
- Removed `Dsn::setSecretKey()`.
- Removed `Dsn::getStoreApiEndpointUrl()`.
- Removed `EventType::default()`.
- Removed adding the value of the `logger` option as a tag on the event. If you rely on this behaviour, add the tag manually.

- Added return type to `Dsn::getProjectId(): string`.
- Changed return type to `Options::getLogger(): ?LoggerInterface`.
- Changed parameter type of `Options::setLogger(LoggerInterface $logger)`.

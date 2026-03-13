# CHANGELOG

## 4.19.1

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.19.1.

### Bug Fixes

- Don't cast metrics value to `float` in constructor, drop invalid metrics instead. [(#1981)](https://github.com/getsentry/sentry-php/pull/1981)

## 4.19.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.19.0.

### Features

- Add support for metrics. [(#1968)](https://github.com/getsentry/sentry-php/pull/1968)
```php
// Counter metric
\Sentry\trace_metrics()->count('test-counter', 10, ['my-attribute' => 'foo']);

// Gauge metric
\Sentry\trace_metrics()->gauge('test-gauge', 50.0, ['my-attribute' => 'foo'], \Sentry\Unit::millisecond());

// Distribution metric
\Sentry\trace_metrics()->distribution('test-distribution', 20.0, ['my-attribute' => 'foo'], \Sentry\Unit::kilobyte());

// Flush metrics
\Sentry\trace_metrics()->flush();
```

### Bug Fixes

- Add rate limiting for profiles and cron check-ins. [(#1970)](https://github.com/getsentry/sentry-php/pull/1970)
- Fix Spotlight so it always registers the error integrations and emits transport logs even when no DSN is configured. [(#1964)](https://github.com/getsentry/sentry-php/pull/1964)

## 4.18.1

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.18.1.

### Misc

- Add `addFeatureFlag` helper function. [(#1960)](https://github.com/getsentry/sentry-php/pull/1960)
```php
\Sentry\addFeatureFlag("my.feature.enabled", true);
```

## 4.18.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.18.0.

### Features

- Add support for feature flags. [(#1951)](https://github.com/getsentry/sentry-php/pull/1951)
```php
\Sentry\SentrySdk::getCurrentHub()->configureScope(function (\Sentry\State\Scope $scope) {
    $scope->addFeatureFlag("my.feature.enabled", true);
});
```
- Add more representations for log attributes instead of dropping them. [(#1950)](https://github.com/getsentry/sentry-php/pull/1950)

### Misc

- Merge log attributes in a separate method. [(#1931)](https://github.com/getsentry/sentry-php/pull/1931)

## 4.17.1

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.17.1.

### Misc

- Call `curl_close` only on PHP version 7.4 and below to prevent deprecation warnings. [(#1947)](https://github.com/getsentry/sentry-php/pull/1947)

## 4.17.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.17.0.

### Bug Fixes

- Empty strings will no longer display `<encoding error>` when serialized. [(#1940)](https://github.com/getsentry/sentry-php/pull/1940)

### Misc

- Remove `symfony/phpunit-bridge` as a dev dependency. [(#1930)](https://github.com/getsentry/sentry-php/pull/1930)
- Update `sentry.origin` to be consistent with other SDKs. [(#1938)](https://github.com/getsentry/sentry-php/pull/1938)

## 4.16.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.16.0.

### Features

- Remove `max_breadcrumbs` limit. [(#1890)](https://github.com/getsentry/sentry-php/pull/1890)
- Implement `__destruct` in `LogsHandler` to make sure logs are always flushed. [(#1916)](https://github.com/getsentry/sentry-php/pull/1916)

### Bug Fixes

- Use PSR log level when logging messages using the PSR-3 logger within the SDK. [(#1907)](https://github.com/getsentry/sentry-php/pull/1907)
- Remove `@internal` annotation from `Sentry\Transport\Result`. [(#1904)](https://github.com/getsentry/sentry-php/pull/1904)

### Misc

- Add `sentry.origin` attribute to `LogsHandler`. [(#1917)](https://github.com/getsentry/sentry-php/pull/1917)

## 4.15.2

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.15.2.

### Bug Fixes

- Ensure the Monolog handler only processes records permitted by their log level. [(#1888)](https://github.com/getsentry/sentry-php/pull/1888)

## 4.15.1

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.15.1.

### Bug Fixes

- Do not send `template` attribute with logs when there are no template values [(#1885)](https://github.com/getsentry/sentry-php/pull/1885)

## 4.15.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.15.0.

### Features

- Add Monolog Sentry Logs handler [(#1867)](https://github.com/getsentry/sentry-php/pull/1867)

  This new handler allows you to capture Monolog logs as Sentry logs. To use it, configure your Monolog logger:

  ```php
  use Monolog\Logger;
  use Sentry\Monolog\LogsHandler;
  use Sentry\Logs\LogLevel;

  // Initialize Sentry SDK first (make sure 'enable_logs' is set to true)
  \Sentry\init([
      'dsn' => '__YOUR_DSN__',
      'enable_logs' => true,
  ]);

  // Create a Monolog logger
  $logger = new Logger('my-app');

  // Add the Sentry logs handler
  // Optional: specify minimum log level (defaults to LogLevel::debug())
  $handler = new LogsHandler(LogLevel::info());
  $logger->pushHandler($handler);

  // Now your logs will be sent to Sentry
  $logger->info('User logged in', ['user_id' => 123]);
  $logger->error('Payment failed', ['order_id' => 456]);
  ```

  Note: The handler will not collect logs for exceptions (they should be handled separately via `captureException`).

### Bug Fixes

- Fix non string indexed attributes passed as log attributes [(#1882)](https://github.com/getsentry/sentry-php/pull/1882)
- Use correct `sample_rate` key when deriving sampleRand [(#1874)](https://github.com/getsentry/sentry-php/pull/1874)
- Do not call `Reflection*::setAccessible()` in PHP >= 8.1 [(#1872)](https://github.com/getsentry/sentry-php/pull/1872)

## 4.14.2

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.14.2.

### Bug Fixes

- Add missing sample rates in the envelope header [(#1870)](https://github.com/getsentry/sentry-php/pull/1870)

## 4.14.1

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.14.1.

### Bug Fixes

- Fix missing user attributes on logs [(#1864)](https://github.com/getsentry/sentry-php/pull/1864)

## 4.14.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.14.0.

### Features

- Serialize enum variants with the variant name [(#1860)](https://github.com/getsentry/sentry-php/pull/1860)

### Bug Fixes

- Fix handling of backtrace frames [(#1862)](https://github.com/getsentry/sentry-php/pull/1862)
- Set allowed types for `http_ssl_native_ca` [(#1858)](https://github.com/getsentry/sentry-php/pull/1858)

## 4.13.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.13.0.

### Features

- Add regex support for `ignore_exceptions` and `ignore_transactions` [(#1850)](https://github.com/getsentry/sentry-php/pull/1850)

  You can now use regular expressions to ignore exceptions and transactions:

  ```php
  Sentry\init([
      'ignore_exceptions' => [
          '/.*ArgumentException$/',
      ],
      'ignore_transactions' => [
          '/^GET \/api\/users\/\d+$/',
      ],
  ]);
  ```

- Add support for variadic parameters and null values [(#1849)](https://github.com/getsentry/sentry-php/pull/1849)

### Bug Fixes

- Fix `Options::setEnableLogs` [(#1852)](https://github.com/getsentry/sentry-php/pull/1852)
- Fix `vsprintf` not handling errors [(#1855)](https://github.com/getsentry/sentry-php/pull/1855)

## 4.12.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.12.0.

### Features

- Add support for Sentry Structured Logs [(#1813)](https://github.com/getsentry/sentry-php/pull/1813)

  You can now send logs directly to Sentry using the new logging API:

  ```php
  Sentry\init([
      // Enable logs to be sent to Sentry
      'enable_logs' => true,
  ]);
  ```

  ```php
  use function Sentry\logger;

  // Log messages at different levels
  logger()->info('User logged in', ['user_id' => 123]);
  logger()->warn('Deprecated function used', ['function' => 'old_function']);
  logger()->error('Database connection failed', ['host' => 'db.example.com']);
  logger()->fatal('Critical system failure: %s', ['Out of memory'], ['component' => 'database']);

  // Flush logs to Sentry
  logger()->flush();

  // We recommend registering the flushing in a shutdown function
  register_shutdown_function(static fn () => logger()->flush());
  ```

  To learn more, head over to our [docs](https://docs.sentry.io/platforms/php/logs/).

### Bug Fixes

- Log correct source of sampling decision [(#1836)](https://github.com/getsentry/sentry-php/pull/1836)

## 4.11.1

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.11.1.

### Bug Fixes

- Fix stripping prefixes from closure frames for PHP 8.4 and up [(#1828)](https://github.com/getsentry/sentry-php/pull/1828)

## 4.11.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.11.0.

### Features

- Serialize `\DateTimeInterface` objects by default [(#1803)](https://github.com/getsentry/sentry-php/pull/1803)
- Add support for [Propagated Ramdom Value](https://develop.sentry.dev/sdk/telemetry/traces/#propagated-random-value) [(#1793)](https://github.com/getsentry/sentry-php/pull/1793)
- Use the `SENTRY_SPOTLIGHT` environment variable as the input for the `spotlight` configuration option [(#1789)](https://github.com/getsentry/sentry-php/pull/1789)

### Bug Fixes

- Fix cases where anonymous stacktrace frames did not get their prefixes stripped [(#1820)](https://github.com/getsentry/sentry-php/pull/1820)
- Fix Guzzle middleware not setting the span it created as the current span, which resulted in nesting issues in the trace view [(#1801)](https://github.com/getsentry/sentry-php/pull/1801)

## 4.10.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.10.0.

### Features

- The SDK was updated to support PHP 8.4 [(#1760)](https://github.com/getsentry/sentry-php/pull/1760)
- Expose a new `http_ssl_native_ca` option to tell the HTTP client to use the operating system's native CA store for certificate verification [(#1766)](https://github.com/getsentry/sentry-php/pull/1766)

### Bug Fixes

- Fix the `http_timeout` & `http_connect_timeout` options, which now also work with sub second values [(#1785)](https://github.com/getsentry/sentry-php/pull/1785)

### Misc

- HTTP breadcrumbs created by the `GuzzleTracingMiddleware` are now set to a warning status for `4xx` responses and an error status for `5xx` responses [(#1773)](https://github.com/getsentry/sentry-php/pull/1773)
- All public Metrics APIs are now no-op, intneral APIs were removed [(#1786)](https://github.com/getsentry/sentry-php/pull/1786)

## 4.9.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.9.0.

### Features

- Allow retrieving a single piece of data from the span by it's key [(#1767)](https://github.com/getsentry/sentry-php/pull/1767)

  ```php
  \Sentry\SentrySdk::getCurrentHub()->getSpan()?->setData([
      'failure' => $span->getData('failure', 0) + 1,
  ]);
  ```

- Add span trace origin [(#1769)](https://github.com/getsentry/sentry-php/pull/1769)

## 4.8.1

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.8.1.

### Bug Fixes

- Guard against empty `REMOTE_ADDR` [(#1751)](https://github.com/getsentry/sentry-php/pull/1751)

## 4.8.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.8.0.

### Features

- Add timing span when emiting a timing metric [(#1717)](https://github.com/getsentry/sentry-php/pull/1717)

  ```php
  use function Sentry\metrics;

  // This will now both emit a distribution metric and a span with the "expensive-operation" key
  metrics()->timing(
      key: 'expensive-operation',
      callback: fn() => doExpensiveOperation(),
  );
  ```

### Bug Fixes

- Fix missing data on HTTP spans [(#1735)](https://github.com/getsentry/sentry-php/pull/1735)
- Test span sampled status before creating child spans [(#1740)](https://github.com/getsentry/sentry-php/pull/1740)

### Misc

- Implement fast path for ignoring errors [(#1737)](https://github.com/getsentry/sentry-php/pull/1737)
- Add array shape for better autocomplete of `Sentry\init` function [(#1738)](https://github.com/getsentry/sentry-php/pull/1738)
- Represent callable strings as strings [(#1741)](https://github.com/getsentry/sentry-php/pull/1741)
- Use `AWS_LAMBDA_FUNCTION_VERSION` environment variable for release if available [(#1742)](https://github.com/getsentry/sentry-php/pull/1742)

## 4.7.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.7.0.

### Features

- Improve debugging experience by emitting more logs from the SDK [(#1705)](https://github.com/getsentry/sentry-php/pull/1705)
- Handle `metric_bucket` rate limits [(#1726)](https://github.com/getsentry/sentry-php/pull/1726) & [(#1728)](https://github.com/getsentry/sentry-php/pull/1728)

### Bug Fixes

- Fix deprecation notice when trying to serialize a callable [(#1732)](https://github.com/getsentry/sentry-php/pull/1732)

### Misc

- Deprecated `SpanStatus::resourceExchausted()`. Use `SpanStatus::resourceExhausted()` instead [(#1725)](https://github.com/getsentry/sentry-php/pull/1725)
- Update metric normalization [(#1729)](https://github.com/getsentry/sentry-php/pull/1729)

## 4.6.1

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.6.1.

### Bug Fixes

- Always add the sampled flag to the W3C `traceparent` header [(#1713)](https://github.com/getsentry/sentry-php/pull/1713)
- Add `JSON_ERROR_NON_BACKED_ENUM` to allowed `JSON::encode()` errors. [(#1707)](https://github.com/getsentry/sentry-php/pull/1707)

## 4.6.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.6.0.

### Features

- Add the PHP SAPI to the runtime context [(#1700)](https://github.com/getsentry/sentry-php/pull/1700)

### Bug Fixes

- Correctly apply properties/options in `ClientBuilder::class` [(#1699)](https://github.com/getsentry/sentry-php/pull/1699)
- Attach `_metrics_summary` to transactions [(#1702)](https://github.com/getsentry/sentry-php/pull/1702)

### Misc

- Remove `final` from `Metrics::class` [(#1697)](https://github.com/getsentry/sentry-php/pull/1697)
- Return early when using `ignore_exceptions` [(#1701)](https://github.com/getsentry/sentry-php/pull/1701)
- Attach exceptions to the log message from `FrameContextifierIntegration::class` [(#1678)](https://github.com/getsentry/sentry-php/pull/1678)

## 4.5.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.5.0.

### Features

- Add `before_send_check_in` and `before_send_metrics` [(#1690)](https://github.com/getsentry/sentry-php/pull/1690)

  ```php
  \Sentry\init([
      'before_send_check_in' => function (\Sentry\Event $event) {
          $checkIn = $event->getCheckIn(),
          // modify the check-in or return null to not send it
      },
  ]);
  ```

  ```php
  \Sentry\init([
      'before_send_metrics' => function (\Sentry\Event $event) {
          $metrics = $event->getMetrics(),
          // modify the metrics or return null to not send it
      },
  ]);
  ```

### Bug Fixes

- Fix `_metrics_summary` formatting [(#1682)](https://github.com/getsentry/sentry-php/pull/1682)

- Fix `DebugFileLogger` and `DebugStdOutLogger` to be usable with PHP 7.2 and up [(#1691)](https://github.com/getsentry/sentry-php/pull/1691)

- Allow whitespace in metric tag values [(#1692)](https://github.com/getsentry/sentry-php/pull/1692)

## 4.4.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.4.0.

### Features

- Add `metrics()->timing()` [(#1670)](https://github.com/getsentry/sentry-php/pull/1670)

  This allows you to emit a distribution metric based on the duration of the provided callback.

  ```php
  use function Sentry\metrics;

  metrics()->timing(
      key: 'my-metric',
      callback: fn() => doSomething(),
  );
  ```

- Add `withMonitor()` [(#1679)](https://github.com/getsentry/sentry-php/pull/1679)

  This wraps a callback into monitor check-ins.

  ```php
  use function Sentry\withMonitor;

  withMonitor(
      slug: 'my-monitor',
      callback: fn () => doSomething(),
      monitorConfig: new MonitorConfig(...),
  );
  ```

- Add new `failure_issue_threshold` and `recovery_threshold` configuration to `MonitorConfig` [(#1685)](https://github.com/getsentry/sentry-php/pull/1685)

- Add `TransactionContext::make()` and `SpanContext::make()` [(#1684)](https://github.com/getsentry/sentry-php/pull/1684)

  ```php
  use Sentry\Tracing\SpanContext;

  $spanCpntext = SpanContext::make()
      ->setOp('http.client')
      ->setDescription('GET https://example.com')
  ```
- Add support for fluent use of `Transaction::setName()` [(#1687)](https://github.com/getsentry/sentry-php/pull/1687)

- Add support for the W3C `traceparent` header [(#1680)](https://github.com/getsentry/sentry-php/pull/1680)

### Bug Fixes

- Do not send an empty event if no metrics are in the bucket [(#1676)](https://github.com/getsentry/sentry-php/pull/1676)

- Fix the `http_ssl_verify_peer` option to set the correct value to `CURLOPT_SSL_VERIFYPEER` [(#1686)](https://github.com/getsentry/sentry-php/pull/1686)

### Misc

- Depreacted `UserDataBag::getSegment()` and `UserDataBag::setSegment()`. You may use a custom tag or context instead [(#1681)](https://github.com/getsentry/sentry-php/pull/1681)

## 4.3.1

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.3.1.

### Bug Fixes

- Fix tags not being serialized correctly for metrics [(#1672)](https://github.com/getsentry/sentry-php/pull/1672)

### Misc

- Remove `@internal` annotation from `MetricsUnit` class [(#1671)](https://github.com/getsentry/sentry-php/pull/1671)

## 4.3.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.3.0.

### Features

- Add support for Sentry Developer Metrics [(#1619)](https://github.com/getsentry/sentry-php/pull/1619)

  ```php
  use function Sentry\metrics;

  // Add 4 to a counter named hits
  metrics()->increment(key: 'hits', value: 4);

  // Add 25 to a distribution named response_time with unit milliseconds
  metrics()->distribution(key: 'response_time', value: 25, unit: MetricsUnit::millisecond());

  // Add 2 to gauge named parallel_requests, tagged with type: "a"
  metrics()->gauge(key: 'parallel_requests, value: 2, tags: ['type': 'a']);

  // Add a user's email to a set named users.sessions, tagged with role: "admin"
  metrics()->set('users.sessions, 'jane.doe@example.com', null, ['role' => User::admin()]);

  // Add 2 to gauge named `parallel_requests`, tagged with `type: "a"`
  Sentry.metrics.gauge('parallel_requests', 2, { tags: { type: 'a' } });

  // Flush the metrics to Sentry
  metrics()->flush();

  // We recommend registering the flushing in a shutdown function
  register_shutdown_function(static fn () => metrics()->flush());
  ```

  To learn more about Sentry Developer Merics, join the discussion at https://github.com/getsentry/sentry-php/discussions/1666.

### Bug Fixes

- Disallow to seralize the `HubAdapter::class` [(#1663)](https://github.com/getsentry/sentry-php/pull/1663)
- Do not overwrite trace context on event [(#1668)](https://github.com/getsentry/sentry-php/pull/1668)
- Serialize breadcrumb data to display correct in the Sentry UI [(#1669)](https://github.com/getsentry/sentry-php/pull/1669)

### Misc

- Remove the `final` keyword from `Hub::class`, `Client::class` and `Scope::class` [(#1665)](https://github.com/getsentry/sentry-php/pull/1665)

## 4.2.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.2.0.

### Features

- Add a config option to allow overriding the Spotlight url [(#1659)](https://github.com/getsentry/sentry-php/pull/1659)

  ```php
  Sentry\init([
      'spotlight_url' => 'http://localhost:8969',
  ]);
  ```

### Bug Fixes

- Restore setting the `logger` value on the event payload [(#1657)](https://github.com/getsentry/sentry-php/pull/1657)

- Only apply the `sample_rate` on error/message events [(#1662)](https://github.com/getsentry/sentry-php/pull/1662)

  This fixes an issue where Cron Check-Ins were wrongly sampled out if a `sample_rate` lower than `1.0` is used.

### Misc

- Remove the `@internal` annotation from `ClientBuilder::class` [(#1661)](https://github.com/getsentry/sentry-php/pull/1661)

## 4.1.0

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.1.0.

### Features

- Add support for Spotlight [(#1647)](https://github.com/getsentry/sentry-php/pull/1647)

  Spotlight is Sentry for Development. Inspired by an old project, Django Debug Toolbar. Spotlight brings a rich debug overlay into development environments, and it does it by leveraging the existing power of Sentry's SDKs.

  To learn more about Spotlight, go to https://spotlightjs.com/.

### Misc

- Normalize `response` status [(#1644)](https://github.com/getsentry/sentry-php/pull/1644)

## 4.0.1

The Sentry SDK team is happy to announce the immediate availability of Sentry PHP SDK v4.0.1.

### Bug Fixes

- Fix capturing out-of-memory errors when memory-constrained [(#1636)](https://github.com/getsentry/sentry-php/pull/1636)
- Check if the cURL extension is installed [(#1632)](https://github.com/getsentry/sentry-php/pull/1632)

## 4.0.0

The Sentry SDK team is thrilled to announce the immediate availability of Sentry PHP SDK v4.0.0.

### Breaking Change

Please refer to the [UPGRADE-4.0.md](UPGRADE-4.0.md) guide for a complete list of breaking changes.

- This version exclusively uses the [envelope endpoint](https://develop.sentry.dev/sdk/envelopes/) to send event data to Sentry.

  If you are using [sentry.io](https://sentry.io), no action is needed.
  If you are using an on-premise/self-hosted installation of Sentry, the minimum requirement is now version `>= v20.6.0`.

- You need to have `ext-curl` installed to use the SDK.

- The `IgnoreErrorsIntegration` integration was removed. Use the `ignore_exceptions` option instead.

  ```php
  Sentry\init([
      'ignore_exceptions' => [BadThingsHappenedException::class],
  ]);
  ```

  This option performs an [`is_a`](https://www.php.net/manual/en/function.is-a.php) check now, so you can also ignore more generic exceptions.

### Features

- Add new fluent APIs [(#1601)](https://github.com/getsentry/sentry-php/pull/1601)

  ```php
  // Before
  $transactionContext = new TransactionContext();
  $transactionContext->setName('GET /example');
  $transactionContext->setOp('http.server');

  // After
  $transactionContext = (new TransactionContext())
      ->setName('GET /example');
      ->setOp('http.server');
  ```

- Simplify the breadcrumb API [(#1603)](https://github.com/getsentry/sentry-php/pull/1603)

  ```php
  // Before
  \Sentry\addBreadcrumb(
      new \Sentry\Breadcrumb(
          \Sentry\Breadcrumb::LEVEL_INFO,
          \Sentry\Breadcrumb::TYPE_DEFAULT,
          'auth',                // category
          'User authenticated',  // message (optional)
          ['user_id' => $userId] // data (optional)
      )
  );

  // After
  \Sentry\addBreadcrumb(
      category: 'auth',
      message: 'User authenticated', // optional
      metadata: ['user_id' => $userId], // optional
      level: Breadcrumb::LEVEL_INFO, // set by default
      type: Breadcrumb::TYPE_DEFAULT, // set by default
  );
  ```

- New `logger` option [(#1625)](https://github.com/getsentry/sentry-php/pull/1625)

  To make it easier to debug the internals of the SDK, the `logger` option now accepts a `Psr\Log\LoggerInterface` instance.
  We do provide two implementations, `Sentry\Logger\DebugFileLogger` and `Sentry\Logger\DebugStdOutLogger`.

  ```php
  // This logs messages to the provided file path
  Sentry\init([
      'logger' => new DebugFileLogger(filePath: ROOT . DS . 'sentry.log'),
  ]);

  // This logs messages to stdout
  Sentry\init([
      'logger' => new DebugStdOutLogger(),
  ]);
  ```

- New default cURL HTTP client [(#1589)](https://github.com/getsentry/sentry-php/pull/1589)

  The SDK now ships with its own HTTP client based on cURL. A few new options were added.

  ```php
  Sentry\init([
      'http_proxy_authentication' => 'username:password', // user name and password to use for proxy authentication
      'http_ssl_verify_peer' => false, // default true, verify the peer's SSL certificate
      'http_compression' => false, // default true, http request body compression
  ]);
  ```

  To use a different client, you may use the `http_client` option.

  ```php
  use Sentry\Client;
  use Sentry\HttpClient\HttpClientInterface;
  use Sentry\HttpClient\Request;
  use Sentry\HttpClient\Response;
  use Sentry\Options;

  $httpClient = new class() implements HttpClientInterface {
      public function sendRequest(Request $request, Options $options): Response
      {

          // your custom implementation

          return new Response($response->getStatusCode(), $response->getHeaders(), '');
      }
  };

  Sentry\init([
      'http_client' => $httpClient,
  ]);
  ```

  To use a different transport, you may use the `transport` option. A custom transport must implement the `TransportInterface`.
  If you use the `transport` option, the `http_client` option has no effect.

### Misc

- The abandoned package `php-http/message-factory` was removed.

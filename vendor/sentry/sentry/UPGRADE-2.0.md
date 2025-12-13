# Upgrade from 1.10 to 2.0

Version `2.x` is a complete rewrite of the existing code base. The public API has been trimmed down to a minimum.
The preferred way of using the SDK is through our "Static API" / global functions.

Here is a simple example to get started:

```php
\Sentry\init(['dsn' => '___PUBLIC_DSN___' ]);

\Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
     $scope->setTag('page_locale', 'de-at');
     $scope->setUser(['email' => 'john.doe@example.com']);
     $scope->setLevel(\Sentry\Severity::warning());
     $scope->setExtra('character_name', 'Mighty Fighter');
});

// The following capture call will contain the data from the previous configured Scope
try {
    thisFunctionThrows(); // -> throw new \Exception('foo bar');
} catch (\Exception $exception) {
    \Sentry\captureException($exception);
}

\Sentry\addBreadcrumb(new Breadcrumb(Breadcrumb::LEVEL_ERROR, Breadcrumb::TYPE_ERROR, 'error_reporting', 'Message'));
```

The call to `\Sentry\init()` sets up global exception/error handlers and any uncaught error will be sent to Sentry.
Version `>= 2.0` conforms to the [Unified SDK API](https://docs.sentry.io/development/sdk-dev/unified-api/).
It has a fundamentally different concept, it's no longer recommended to just use a `Client` unless you really know what you are doing.

Please visit [our docs](https://docs.sentry.io/error-reporting/quickstart/?platform=php) to get a full overview.

### Client options

- The `exclude` option has been removed. 

- The `excluded_app_path` option has been renamed to `in_app_exclude`

- The `send_callback` option has been renamed to `before_send`.

- The `name` option has been renamed to `server_name`.

- The `secret_key` option has been removed.

- The `public_key` option has been removed.

- The `message_limit` option has been removed.

- The `project` option has been removed.

- The `severity_map` option has been removed.

- The `ignore_server_port` option has been removed.

- The `trust_x_forwarded_proto` option has been removed.

- The `mb_detect_order` option has been removed.

- The `trace` option has been removed.

- The `tags` option has been removed in favour of setting them in the scope.

- The `site` option has been removed.

- The `extra_data` option has been removed in favour of setting additional data
  in the scope.

- The `curl_method` option has been removed.

- The `curl_path` option has been removed.

- The `curl_ipv4` option has been removed.

- The `curl_ssl_version` option has been removed.

- The `verify_ssl` option has been removed.

- The `ca_cert` option has been removed.

- The `proxy` option has been removed in favour of leaving to the user the burden
  of configuring the HTTP client options using a custom client.

- The `processors` option has been removed.

- The `processors_options` option has been removed.

- The `transport` option has been removed in favour of setting it using the
  client builder.

- The `install_default_breadcrumb_handlers` option has been removed.

- The `serialize_all_object` option has been removed.

- The `context_lines` option has been added to configure the number of lines of
  code context to capture.

- The `max_value_length` option has been added to set an upper bound to the length
  of serialized items.

- The `server` option has been renamed to `dsn`.

### Misc

- All the classes have been renamed and moved around to follow the PSR-4
  convention and `final` have been added where appropriate.

- The `Raven_Autoloader` class has been removed. To install and use the
  library you are required to use [Composer](https://getcomposer.org/).

- The `Raven_Util` class has been removed.

- The `Raven_Compat` class has been removed.

- The `Raven_Util` class has been removed.

- The `Raven_CurlHandler` class has been removed.

- The `Raven_TransactionStack` class has been removed.

- The `Raven_Exception` class has been removed.

### Client

- The constructor of the `Client` (before `Raven_Client`) class has changed its signature and
  now requires to be passed a configuration object, an instance of a transport
  and an event factory.

  Before:

  ```php
  public function __construct($options_or_dsn = null, $options = array())
  {
      // ...
  }
  ```

  After:

  ```php
  public function __construct(Options $options, TransportInterface $transport, EventFactoryInterface $eventFactory)
  {
      // ...
  }
  ```
  
 The suggested way to create your own instance of the client is to use the provided builder (`ClientBuilder`) that will take care of instantiating a few dependencies like the PSR-7 factories and the HTTP client.

- The method `Raven_Client::close_all_children_link` has been removed and there

- The methods `Raven_Client::getRelease` and `Raven_Client::setRelease` have
  been removed. You should use `Options::getRelease` and `Options::setRelease`
  instead.

  Before:

  ```php
  $client->getRelease();
  $client->setRelease(...);
  ```

  After:

  ```php
  use Sentry\State\Hub;

  $options = Hub::getCurrent()->getClient()->getOptions();

  $options->getRelease();
  $options->setRelease(...);
  ```

- The methods `Raven_Client::getEnvironment` and `Raven_Client::setEnvironment`
  have been removed. You should use `Options::getEnvironment` and `Options::setEnvironment`
  instead.

  Before:

  ```php
  $client->getEnvironment();
  $client->setEnvironment(...);
  ```

  After:

  ```php
  use Sentry\State\Hub;

  $options = Hub::getCurrent()->getClient()->getOptions();

  $options->getEnvironment();
  $options->setEnvironment(...);
  ```

- The method `Raven_Client::getInputStream` has been removed.

- The methods `Raven_Client::getDefaultPrefixes` and `Raven_Client::setPrefixes`
  have been removed. You should use `Options::getPrefixes` and
  `Options::setPrefixes` instead.

  Before:

  ```php
  $client->getPrefixes();
  $client->setPrefixes(...);
  ```

  After:

  ```php
  use Sentry\State\Hub;

  $options = Hub::getCurrent()->getClient()->getOptions();

  $options->getPrefixes();
  $options->setPrefixes(...);
  ```

- The methods `Raven_Client::getAppPath` and `Raven_Client::setAppPath` have been
  removed. You should use `Options::getProjectRoot` and `Options::setProjectRoot`
  instead.

  Before:

  ```php
  $client->getAppPath();
  $client->setAppPath(...);
  ```

  After:

  ```php
  use Sentry\State\Hub;

  $options = Hub::getCurrent()->getClient()->getOptions();

  $options->getProjectRoot();
  $options->setProjectRoot(...);
  ```

- The methods `Raven_Client::getExcludedAppPaths` and `Raven_Client::setExcludedAppPaths`
  have been removed. You should use `Options::getInAppExcludedPaths`
  and `Options::setInAppExcludedPaths` instead.

  Before:

  ```php
  $client->getExcludedAppPaths();
  $client->setExcludedAppPaths(...);
  ```

  After:

  ```php
  use Sentry\State\Hub;

  $options = Hub::getCurrent()->getClient()->getOptions();

  $options->getExcludedAppPaths();
  $options->setExcludedAppPaths(...);
  ```

- The methods `Raven_Client::getSendCallback` and `Raven_Client::setSendCallback`
  have been removed. You should use `Options::getBeforeSendCallback` and
  `Options::setBeforeSendCallback` instead.

  Before:

  ```php
  $client->getSendCallback();
  $client->setSendCallback(...);
  ```

  After:

  ```php
  use Sentry\State\Hub;

  $options = Hub::getCurrent()->getClient()->getOptions();

  $options->getBeforeSendCallback();
  $options->setBeforeSendCallback(...);

- The method `Raven_Client::getServerEndpoint` has been removed. You should use
  `Options::getDsn` instead.

  Before:

  ```php
  $client->getServerEndpoint();
  ```

  After:

  ```php
  use Sentry\State\Hub;

  $options = Hub::getCurrent()->getClient()->getOptions();

  $options->getDsn();
  ```

- The methods `Raven_Client::getTransport` and `Raven_Client::setTransport` have
  been removed. The transport is now a required dependency of the client and must
  be passed as required constructor argument.

- The method `Raven_Client::getUserAgent` has been removed.

- The method `Raven_Client::getErrorTypes` has been removed. You should use
  `Configuration::getErrorTypes` instead.

  Before:

  ```php
  $client->getErrorTypes();
  ```

  After:

  ```php
  $client->getConfig()->getErrorTypes();
  ```

- The `Raven_Client::getDefaultProcessors` method has been removed.

- The `Raven_Client::setProcessorsFromOptions` method has been removed.

- The `Raven_Client::getLastEventID` method has been removed. The ID of the
  last event that was captured is now returned by each of the `Client::capture*`
  methods. You can also use `Hub::getCurrent()->getLastEventId()`.

- The `Raven_Client::parseDSN` method has been removed.

- The `Raven_Client::getLastError` method has been removed.

- The `Raven_Client::getIdent` method has been removed.

- The `Raven_Client::registerShutdownFunction` method has been removed.

- The `Raven_Client::is_http_request` method has been removed.

- The `Raven_Client::get_http_data` method has been removed.

- The `Raven_Client::get_user_data` method has been removed.

- The `Raven_Client::get_extra_data` method has been removed.

- The `Raven_Client::get_default_data` method has been removed.

- The `Raven_Client::message` method has been removed.

- The `Raven_Client::exception` method has been removed.

- The `Raven_Client::captureQuery` method has been removed.

- The `Raven_Client::captureMessage` method has changed its signature.

  Before:

  ```php
  public function captureMessage($message, $params = array(), $data = array(), $stack = false, $vars = null)
  {
      // ...
  }
  ```

  After:

  ```php
  public function captureMessage(string $message, ?Severity $level = null, ?Scope $scope = null): ?string
  {
      // ...
  }
  ```

- The `Raven_Client::captureException` method has changed its signature.

  Before:

  ```php
  public function captureException($exception, $data = null, $logger = null, $vars = null)
  {
      // ...
  }
  ```

  After:

  ```php
  public function captureException(\Throwable $exception, ?Scope $scope = null): ?string
  {
      // ...
  }
  ```

- The `Raven_Client::captureLastError` method has changed its signature.

  Before:

  ```php
  public function captureLastError()
  {
      // ...
  }
  ```

  After:

  ```php
  public function captureLastError(?Scope $scope = null): ?string
  {
      // ...
  }
  ```

- The method `Raven_Client::capture` has been removed.

- The method `Raven_Client::sanitize` has been removed.

- The method `Raven_Client::process` has been removed.

- The method `Raven_Client::sendUnsentErrors` has been removed.

- The method `Raven_Client::encode` has been removed.

- The method `Raven_Client::send` has been removed.

- The method `Raven_Client::send_remote` has been removed.

- The method `Raven_Client::get_default_ca_cert` has been removed.

- The method `Raven_Client::get_curl_options` has been removed.

- The method `Raven_Client::send_http` has been removed.

- The method `Raven_Client::buildCurlCommand` has been removed.

- The method `Raven_Client::send_http_asynchronous_curl_exec` has been removed.

- The method `Raven_Client::send_http_synchronous` has been removed.

- The method `Raven_Client::get_auth_header` has been removed.

- The method `Raven_Client::getAuthHeader` has been removed.

- The method `Raven_Client::uuid4` has been removed.

- The method `Raven_Client::get_current_url` has been removed.

- The method `Raven_Client::isHttps` has been removed.

- The method `Raven_Client::translateSeverity` has been removed.

- The method `Raven_Client::registerSeverityMap` has been removed.

- The method `Raven_Client::set_user_data` has been removed.

- The method `Raven_Client::onShutdown` has been removed.

- The method `Raven_Client::createProcessors` has been removed.

- The method `Raven_Client::setProcessors` has been removed.

- The method `Raven_Client::getLastSentryError` has been removed.

- The method `Raven_Client::getShutdownFunctionHasBeenSet` has been removed.

- The method `Raven_Client::close_curl_resource` has been removed.

- The method `Raven_Client::setSerializer` has been removed. You can set it
  using the client builder.

  Before:

  ```php
  $client = new Raven_Client();
  $client->setSerializer(...);
  ```

  After:

  ```php
  use Sentry\ClientBuilder;

  $clientBuilder = ClientBuilder::create();
  $clientBuilder->setSerializer(...);
  ```

- The method `Raven_Client::setReprSerializer` has been removed. You can set it
  using the client builder.

  Before:

  ```php
  $client = new Raven_Client();
  $client->setSerializer(...);
  ```

  After:

  ```php
  use Sentry\ClientBuilder;

  $clientBuilder = ClientBuilder::create();
  $clientBuilder->setRepresentationSerializer(...);
  ```

- The method `Raven_Client::cleanup_php_version` has been removed.

- The method `Raven_Client::registerDefaultBreadcrumbHandlers` has been removed.

- The `Raven_Client::user_context` method has been removed. You can set this
  data in the current active scope.

  Before:

  ```php
  $client->user_context(array('foo', 'bar'));
  ```

  After:

  ```php
  use Sentry\State\Hub;
  use Sentry\State\Scope;

  Hub::getCurrent()->configureScope(function (Scope $scope): void {
      $scope->setUser(['email' => 'foo@example.com']);
  });
  ```

- The `Raven_Client::tags_context` method has been removed. You can set this
  data in the current active scope.

  Before:

  ```php
  $client->tags_context(array('foo', 'bar'));
  ```

  After:

  ```php
  use Sentry\State\Hub;
  use Sentry\State\Scope;

  Hub::getCurrent()->configureScope(function (Scope $scope): void {
      $scope->setTag('tag_name', 'tag_value');
  });
  ```

- The `Raven_Client::extra_context` method has been removed. You can set this
  data in the current active scope.

  Before:

  ```php
  $client->extra_context(array('foo' => 'bar'));
  ```

  After:

  ```php
  use Sentry\State\Hub;
  use Sentry\State\Scope;

  Hub::getCurrent()->configureScope(function (Scope $scope): void {
      $scope->setExtra('extra_key', 'extra_value');
  });
  ```

- The method `Raven_Client::install` has been removed. The error handler is
  registered automatically when using the `ExceptionListenerIntegration` 
  and `ErrorListenerIntegration` integrations (which are enabled by default).

### Processors

- The `Raven_Processor_RemoveCookiesProcessor` class has been removed.

- The `Raven_Processor_SanitizeStacktraceProcessor` class has been removed.

- The `Raven_Processor_SanitizeHttpHeadersProcessor` class has been removed.

- The `Raven_Processor_RemoveHttpBodyProcessor` class has been removed.

- The `Raven_Processor_SanitizeDataProcessor` class has been removed.

- The `Raven_Processor` class has been removed.

### Context

- The `Raven_Context` class has been renamed to `Context`.

- The `tags`, `extra` and `user` properties of the `Raven_Context` class have
  been removed. Each instance of the new class represents now a single context
  type at once.

## Error handlers

- The `Raven_Breadcrumbs_ErrorHandler` class has been removed.

- The `Raven_Breadcrumbs_MonologHandler` class has been removed.

- The `Raven_ErrorHandler` class has been renamed to `ErrorHandler` and has
  been made `final`.

- The method `Raven_ErrorHandler::handleError` has changed its signature by removing
  the `$context` argument and it has been marked as `internal` to make it clear that
  it should not be called publicly and its method visibility is subject to changes
  without any notice.

- The methods `Raven_ErrorHandler::registerErrorHandler`, `Raven_ErrorHandler::registerExceptionHandler`
  and `Raven_ErrorHandler::registerShutdownFunction` have been removed. You should
  use the `ErrorHandler::register` method instead, but note that it registers all
  error handlers (error, exception and fatal error) at once and there is no way
  anymore to only use one of them.

  Before:

  ```php
  $errorHandler = new Raven_ErrorHandler($client);
  $errorHandler->registerErrorHandler();
  $errorHandler->registerExceptionHandler();
  $errorHandler->registerShutdownFunction();
  ```

  After:

  ```php
  use Sentry\ErrorHandler;

  ErrorHandler::register(function (\Throwable $exception): void {
      // ...
  });
  ```

- The method `Raven_ErrorHandler::handleError` has changed its signature by
  removing the `$context` argument and it has been marked as `internal` to
  make it clear that it should not be called publicly and its method visibility
  is subject to changes without any notice.

- The method `Raven_ErrorHandler::handleFatalError` has changed its signature
  by adding an optional argument named `$error` and it has been marked as `internal`
  to make it clear that it should not be called publicly and its method visibility
  is subject to changes without any notice.

- The method `Raven_ErrorHandler::handleException` has changed its signature by
  removing the `$isError` and `$vars` arguments and it has been marked as `internal`
  to make it clear that it should not be called publicly and its method visibility
  is subject to changes without any notice.

- The method `Raven_ErrorHandler::bitwiseOr` has been removed and there is no
  replacement for it.

- The method `Raven_ErrorHandler::shouldCaptureFatalError` has been removed and
  there is no replacement for it.

### Serializers

- The `Raven_Serializer` class has been renamed to `Serializer` and its constructor
  changed signature.

  Before:

  ```php
  public function __construct($mb_detect_order = null, $message_limit = null)
  {
      // ...
  }
  ```

  After:

  ```php
  public function __construct(int $maxDepth = 3, ?string $mbDetectOrder = null, int $messageLimit = Client::MESSAGE_MAX_LENGTH_LIMIT)
  {
      // ...
  }
  ```

- The `Raven_ReprSerializer` class has been renamed to `RepresentationSerializer`
  and its constructor changed signature.

  Before:

  ```php
  public function __construct($mb_detect_order = null, $message_limit = null)
  {
      // ...
  }
  ```

  After:

  ```php
  public function __construct(int $maxDepth = 3, ?string $mbDetectOrder = null, int $messageLimit = Client::MESSAGE_MAX_LENGTH_LIMIT)
  {
      // ...
  }
  ```

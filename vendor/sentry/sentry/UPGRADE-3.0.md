# Upgrade 2.x to 3.0

- Removed the `HubInterface::getCurrentHub()` and `HubInterface::setCurrentHub()` methods. Use `SentrySdk::getCurrentHub()` and `SentrySdk::setCurrentHub()` instead
- Removed the `ErrorHandler::registerOnce()` method, use `ErrorHandler::register*Handler()` instead
- Removed the `ErrorHandler::addErrorListener` method, use `ErrorHandler::addErrorHandlerListener()` instead
- Removed the `ErrorHandler::addFatalErrorListener` method, use `ErrorHandler::addFatalErrorHandlerListener()` instead
- Removed the `ErrorHandler::addExceptionListener` method, use `ErrorHandler::addExceptionHandlerListener()` instead
- The signature of the `ErrorListenerIntegration::__construct()` method changed to not accept any parameter
- The signature of the `FatalErrorListenerIntegration::__construct()` method changed to not accept any parameter
- The `ErrorListenerIntegration` integration does not get called anymore when a fatal error occurs
- The default value of the `error_types` option changed to the value get from `error_reporting()`
- The signature of the `capture*()` global functions changed to return an instance of the `EventId` class instead of a `string`
- The signature of the `ClientInterface::capture*()` methods changed to return an instance of the `EventId` class instead of a `string`
- The signature of the `HubInterface::capture*e()` methods changed to return an instance of the `EventId` class instead of a `string`
- The signature of the `Event::getId()` method changed to return an instance of the `EventId` class instead of a `string`
- The signature of the `Options::getDsn()` method changed to always return an instance of the `Dsn` class instead of a `string`
- Removed the `Options::getProjectId`, `Options::getPublicKey` and `Options::getSecretKey` methods, use `Options::getDsn()` instead
- Removed the `Breadcrumb::LEVEL_CRITICAL` constant, use `Breadcrumb::LEVEL_FATAL` instead
- Removed the `Breadcrumb::levelFromErrorException()` method
- Removed the `PluggableHttpClientFactory` class
- Removed the following methods from the `ClientBuilderInterface` interface, use `ClientBuilderInterface::setTransportFactory()` instead:
  - `ClientBuilderInterface::setUriFactory()`
  - `ClientBuilderInterface::setMessageFactory()`
  - `ClientBuilderInterface::setTransport()`
  - `ClientBuilderInterface::setHttpClient()`
  - `ClientBuilderInterface::addHttpClientPlugin()`
  - `ClientBuilderInterface::removeHttpClientPlugin()`.
- Removed the following methods from the `Options` class, use the `IgnoreErrorsIntegration` integration instead:
  - `Options::getExcludedExceptions()`
  - `Options::setExcludedExceptions()`
  - `Options::isExcludedException()`
  - `Options::getProjectRoot()`
  - `Options::setProjectRoot()`
- Removed the `Context::CONTEXT_USER`, `Context::CONTEXT_RUNTIME`, `Context::CONTEXT_TAGS`, `Context::CONTEXT_EXTRA`, `Context::CONTEXT_SERVER_OS` constants
- The signature of the `Scope::setUser()` method changed to `setUser(array|Sentry\UserDataBag $user)`
- The signature of the `TransportInterface::send()` method changed to return a promise instead of the event ID
- The signature of the `HttpClientFactory::__construct()` method changed to accept instances of the PSR-17 factories in place of Httplug's ones
- The signature of the `DefaultTransportFactory::__construct()` method changed to accept instances of the PSR-17 factories in place of Httplug's ones
- The signature of the `GzipEncoderPlugin::__construct()` method changed to accept an instance of the `Psr\Http\Message\StreamFactoryInterface` interface only
- The Monolog handler does not set anymore the tags and extras on the event by extracting automatically the data from the record payload. You can decorate the
  class and set such data on the scope as shown below:

  ```php
  use Monolog\Handler\HandlerInterface;
  use Sentry\State\Scope;
  use function Sentry\withScope;

  final class MonologHandler implements HandlerInterface
  {
      private $decoratedHandler;

      public function __construct(HandlerInterface $decoratedHandler)
      {
          $this->decoratedHandler = $decoratedHandler;
      }

      public function isHandling(array $record): bool
      {
          return $this->decoratedHandler->isHandling($record);
      }

      public function handle(array $record): bool
      {
          $result = false;

          withScope(function (Scope $scope) use ($record, &$result): void {
              $scope->setTags(...);
              $scope->setExtras(...);

              $result = $this->decoratedHandler->handle($record);
          });

          return $result;
      }

      public function handleBatch(array $records): void
      {
          $this->decoratedHandler->handleBatch($records);
      }

      public function close(): void
      {
          $this->decoratedHandler->close();
      }
  }
  ```

- Removed the `TagsContext`, `ExtraContext` and `Context` classes, data is now stored in a plain array
- Renamed the `ServerOsContext` class to `OsContext`
- The `OsContext` and `RuntimeContext` classes do not implement anymore the `ArrayAccess`, `IteratorAggregate` and `JsonSerializable` interfaces and became `final`
- The following methods have been removed from the `OsContext` and `RuntimeContext` classes:
  - `*Context::merge()`
  - `*Context::setData()`
  - `*Context::replaceData()`
  - `*Context::clear()`
  - `*Context::isEmpty()`
  - `*Context::toArray()`
- Removed the `UserContext` class, use `UserDataBag` instead
- The signature of the constructor of the `RuntimeContext` class changed to `RuntimeContext::__construct(string $name, ?string $version = null)`
- The signature of the constructor of the `OsContext` class changed to `OsContext::__construct(string $name, ?string $version = null, ?string $build = null, ?string $kernelVersion = null)`
- Removed the `Event::getExtraContext()` method, use `Event::getExtra()` instead
- Removed the `Event::getTagsContext()` method, use `Event::getTags()` instead
- Removed the `Event::getUserContext()` method, use `Event::getUser()` instead
- Renamed the `Event::getServerOsContext()` method to `Event::getOsContext()`
- The signature of the `Scope::setUser()` method changed to accept a plain array
- Removed the `FlushableClientInterface` and `ClosableTransportInterface` interfaces. Their methods have been moved to the corresponding `ClientInterface` and `TransportInterface` interfaces
- Removed the `Event::toArray()` and `Event::jsonSerialize()` methods, use `PayloadSerializerInterface::serialize()` instead
- Removed the `Breadcrumb::toArray()` and `Breadcrumb::jsonSerialize()` methods
- Removed the `Frame::toArray()` and `Frame::jsonSerialize()` methods
- Removed the `Stacktrace::toArray()` and `Stacktrace::jsonSerialize()` methods
- Removed the `SpoolTransport` class and the `SpoolInterface` interface with related implementation
- Made the `Event::__construct()` method `private`, use the named constructors instead
- The signature of `ClientInterface::captureEvent()` changed to `ClientInterface::captureEvent(Event $event, ?EventHint $hint = null, ?Scope $scope = null)`
- The signature of `HubInterface::captureEvent()` changed to `HubInterface::captureEvent(Event $event, ?EventHint $hint = null)`
- The signature of `captureEvent()` changed to `captureEvent(Event $event, ?EventHint $hint = null)`
- The signature of `Scope::applyToEvent()` changed to `Scope::applyToEvent(Event $event, ?EventHint $hint = null)`
- Global and scope event processors will now receive a `EventHint` as the second parameter, callable should now have the signature `callable(Event $event, EventHint $hint)`

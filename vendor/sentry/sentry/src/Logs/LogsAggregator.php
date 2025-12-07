<?php

declare(strict_types=1);

namespace Sentry\Logs;

use Sentry\Attributes\Attribute;
use Sentry\Client;
use Sentry\Event;
use Sentry\EventId;
use Sentry\SentrySdk;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Sentry\Util\Arr;
use Sentry\Util\Str;

/**
 * @internal
 */
final class LogsAggregator
{
    /**
     * @var Log[]
     */
    private $logs = [];

    /**
     * @param string                       $message    see sprintf for a description of format
     * @param array<int, string|int|float> $values     see sprintf for a description of values
     * @param array<string, mixed>         $attributes additional attributes to add to the log
     */
    public function add(
        LogLevel $level,
        string $message,
        array $values = [],
        array $attributes = []
    ): void {
        $timestamp = microtime(true);

        $hub = SentrySdk::getCurrentHub();
        $client = $hub->getClient();

        // There is no need to continue if there is no client
        if ($client === null) {
            return;
        }

        $options = $client->getOptions();
        $sdkLogger = $options->getLogger();

        if (!$options->getEnableLogs()) {
            if ($sdkLogger !== null) {
                $sdkLogger->info(
                    'Log will be discarded because "enable_logs" is "false".'
                );
            }

            return;
        }

        $formattedMessage = Str::vsprintfOrNull($message, $values);

        if ($formattedMessage === null) {
            // If formatting fails we don't format the message and log the error
            if ($sdkLogger !== null) {
                $sdkLogger->warning('Failed to format log message with values.', [
                    'message' => $message,
                    'values' => $values,
                ]);
            }

            $formattedMessage = $message;
        }

        $log = (new Log($timestamp, $this->getTraceId($hub), $level, $formattedMessage))
            ->setAttribute('sentry.release', $options->getRelease())
            ->setAttribute('sentry.environment', $options->getEnvironment() ?? Event::DEFAULT_ENVIRONMENT)
            ->setAttribute('sentry.server.address', $options->getServerName())
            ->setAttribute('sentry.trace.parent_span_id', $hub->getSpan() ? $hub->getSpan()->getSpanId() : null);

        if ($client instanceof Client) {
            $log->setAttribute('sentry.sdk.name', $client->getSdkIdentifier());
            $log->setAttribute('sentry.sdk.version', $client->getSdkVersion());
        }

        $hub->configureScope(function (Scope $scope) use ($log) {
            $user = $scope->getUser();
            if ($user !== null) {
                if ($user->getId() !== null) {
                    $log->setAttribute('user.id', $user->getId());
                }
                if ($user->getEmail() !== null) {
                    $log->setAttribute('user.email', $user->getEmail());
                }
                if ($user->getUsername() !== null) {
                    $log->setAttribute('user.name', $user->getUsername());
                }
            }
        });

        if (\count($values)) {
            $log->setAttribute('sentry.message.template', $message);

            foreach ($values as $key => $value) {
                $log->setAttribute("sentry.message.parameter.{$key}", $value);
            }
        }

        $attributes = Arr::simpleDot($attributes);

        foreach ($attributes as $key => $value) {
            if (!\is_string($key)) {
                if ($sdkLogger !== null) {
                    $sdkLogger->info(
                        \sprintf("Dropping log attribute with non-string key '%s' and value of type '%s'.", $key, \gettype($value))
                    );
                }

                continue;
            }

            $attribute = Attribute::tryFromValue($value);

            if ($attribute === null) {
                if ($sdkLogger !== null) {
                    $sdkLogger->info(
                        \sprintf("Dropping log attribute {$key} with value of type '%s' because it is not serializable or an unsupported type.", \gettype($value))
                    );
                }

                continue;
            }

            $log->setAttribute($key, $attribute);
        }

        $log = ($options->getBeforeSendLogCallback())($log);

        if ($log === null) {
            if ($sdkLogger !== null) {
                $sdkLogger->info(
                    'Log will be discarded because the "before_send_log" callback returned "null".',
                    ['log' => $log]
                );
            }

            return;
        }

        if ($sdkLogger !== null) {
            $sdkLogger->log($log->getPsrLevel(), "Logs item: {$log->getBody()}", $log->attributes()->toSimpleArray());
        }

        $this->logs[] = $log;
    }

    public function flush(): ?EventId
    {
        if (empty($this->logs)) {
            return null;
        }

        $hub = SentrySdk::getCurrentHub();
        $event = Event::createLogs()->setLogs($this->logs);

        $this->logs = [];

        return $hub->captureEvent($event);
    }

    /**
     * @return Log[]
     */
    public function all(): array
    {
        return $this->logs;
    }

    private function getTraceId(HubInterface $hub): string
    {
        $span = $hub->getSpan();

        if ($span !== null) {
            return (string) $span->getTraceId();
        }

        $traceId = '';

        $hub->configureScope(function (Scope $scope) use (&$traceId) {
            $traceId = (string) $scope->getPropagationContext()->getTraceId();
        });

        return $traceId;
    }
}

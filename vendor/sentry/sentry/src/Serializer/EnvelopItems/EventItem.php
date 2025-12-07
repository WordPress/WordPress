<?php

declare(strict_types=1);

namespace Sentry\Serializer\EnvelopItems;

use Sentry\Event;
use Sentry\ExceptionDataBag;
use Sentry\Serializer\Traits\BreadcrumbSeralizerTrait;
use Sentry\Serializer\Traits\StacktraceFrameSeralizerTrait;
use Sentry\Util\JSON;
use Sentry\Util\Str;

/**
 * @internal
 */
class EventItem implements EnvelopeItemInterface
{
    use BreadcrumbSeralizerTrait;
    use StacktraceFrameSeralizerTrait;

    public static function toEnvelopeItem(Event $event): string
    {
        $header = [
            'type' => (string) $event->getType(),
            'content_type' => 'application/json',
        ];

        $payload = [
            'timestamp' => $event->getTimestamp(),
            'platform' => 'php',
            'sdk' => $event->getSdkPayload(),
        ];

        if ($event->getStartTimestamp() !== null) {
            $payload['start_timestamp'] = $event->getStartTimestamp();
        }

        if ($event->getLevel() !== null) {
            $payload['level'] = (string) $event->getLevel();
        }

        if ($event->getLogger() !== null) {
            $payload['logger'] = $event->getLogger();
        }

        if ($event->getTransaction() !== null) {
            $payload['transaction'] = $event->getTransaction();
        }

        if ($event->getServerName() !== null) {
            $payload['server_name'] = $event->getServerName();
        }

        if ($event->getRelease() !== null) {
            $payload['release'] = $event->getRelease();
        }

        if ($event->getEnvironment() !== null) {
            $payload['environment'] = $event->getEnvironment();
        }

        if (!empty($event->getFingerprint())) {
            $payload['fingerprint'] = $event->getFingerprint();
        }

        if (!empty($event->getModules())) {
            $payload['modules'] = $event->getModules();
        }

        if (!empty($event->getExtra())) {
            $payload['extra'] = $event->getExtra();
        }

        if (!empty($event->getTags())) {
            $payload['tags'] = $event->getTags();
        }

        $user = $event->getUser();
        if ($user !== null) {
            $payload['user'] = array_merge($user->getMetadata(), [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'ip_address' => $user->getIpAddress(),
                'segment' => $user->getSegment(),
            ]);
        }

        $osContext = $event->getOsContext();
        if ($osContext !== null) {
            $payload['contexts']['os'] = [
                'name' => $osContext->getName(),
                'version' => $osContext->getVersion(),
                'build' => $osContext->getBuild(),
                'kernel_version' => $osContext->getKernelVersion(),
            ];
        }

        $runtimeContext = $event->getRuntimeContext();
        if ($runtimeContext !== null) {
            $payload['contexts']['runtime'] = [
                'name' => $runtimeContext->getName(),
                'sapi' => $runtimeContext->getSAPI(),
                'version' => $runtimeContext->getVersion(),
            ];
        }

        if (!empty($event->getContexts())) {
            $payload['contexts'] = array_merge($payload['contexts'] ?? [], $event->getContexts());
        }

        if (!empty($event->getBreadcrumbs())) {
            $payload['breadcrumbs']['values'] = array_map([self::class, 'serializeBreadcrumb'], $event->getBreadcrumbs());
        }

        if (!empty($event->getRequest())) {
            $payload['request'] = $event->getRequest();
        }

        if ($event->getMessage() !== null) {
            if (empty($event->getMessageParams())) {
                $payload['message'] = $event->getMessage();
            } else {
                $payload['message'] = [
                    'message' => $event->getMessage(),
                    'params' => $event->getMessageParams(),
                    'formatted' => $event->getMessageFormatted() ?? Str::vsprintfOrNull($event->getMessage(), $event->getMessageParams()) ?? $event->getMessage(),
                ];
            }
        }

        $exceptions = $event->getExceptions();
        for ($i = \count($exceptions) - 1; $i >= 0; --$i) {
            $payload['exception']['values'][] = self::serializeException($exceptions[$i]);
        }

        $stacktrace = $event->getStacktrace();
        if ($stacktrace !== null) {
            $payload['stacktrace'] = [
                'frames' => array_map([self::class, 'serializeStacktraceFrame'], $stacktrace->getFrames()),
            ];
        }

        return \sprintf("%s\n%s", JSON::encode($header), JSON::encode($payload));
    }

    /**
     * @return array<string, mixed>
     *
     * @psalm-return array{
     *     type: string,
     *     value: string,
     *     stacktrace?: array{
     *         frames: array<array<string, mixed>>
     *     },
     *     mechanism?: array{
     *         type: string,
     *         handled: boolean,
     *         data?: array<string, mixed>
     *     }
     * }
     */
    protected static function serializeException(ExceptionDataBag $exception): array
    {
        $exceptionMechanism = $exception->getMechanism();
        $exceptionStacktrace = $exception->getStacktrace();
        $result = [
            'type' => $exception->getType(),
            'value' => $exception->getValue(),
        ];

        if ($exceptionStacktrace !== null) {
            $result['stacktrace'] = [
                'frames' => array_map([self::class, 'serializeStacktraceFrame'], $exceptionStacktrace->getFrames()),
            ];
        }

        if ($exceptionMechanism !== null) {
            $result['mechanism'] = [
                'type' => $exceptionMechanism->getType(),
                'handled' => $exceptionMechanism->isHandled(),
            ];

            if ($exceptionMechanism->getData() !== []) {
                $result['mechanism']['data'] = $exceptionMechanism->getData();
            }
        }

        return $result;
    }
}

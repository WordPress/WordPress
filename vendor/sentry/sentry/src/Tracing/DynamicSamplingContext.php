<?php

declare(strict_types=1);

namespace Sentry\Tracing;

use Sentry\Options;
use Sentry\State\HubInterface;
use Sentry\State\Scope;

/**
 * This class represents the Dynamic Sampling Context (dsc).
 *
 * @see https://develop.sentry.dev/sdk/performance/dynamic-sampling-context/
 */
final class DynamicSamplingContext
{
    private const SENTRY_ENTRY_PREFIX = 'sentry-';

    /**
     * @var array<string, string> The dsc entries
     */
    private $entries = [];

    /**
     * @var bool Indicates if the dsc is mutable or immutable
     */
    private $isFrozen = false;

    /**
     * Construct a new dsc object.
     */
    private function __construct()
    {
    }

    /**
     * Set a new key value pair on the dsc.
     *
     * @param string $key   the list member key
     * @param string $value the list member value
     */
    public function set(string $key, string $value, bool $forceOverwrite = false): self
    {
        if ($this->isFrozen && !$forceOverwrite) {
            return $this;
        }

        $this->entries[$key] = $value;

        return $this;
    }

    /**
     * Check if a key value pair is set on the dsc.
     *
     * @param string $key the list member key
     */
    public function has(string $key): bool
    {
        return isset($this->entries[$key]);
    }

    /**
     * Get a value from the dsc.
     *
     * @param string      $key     the list member key
     * @param string|null $default the default value to return if no value exists
     */
    public function get(string $key, ?string $default = null): ?string
    {
        return $this->entries[$key] ?? $default;
    }

    /**
     * Mark the dsc as frozen.
     */
    public function freeze(): self
    {
        $this->isFrozen = true;

        return $this;
    }

    /**
     * Indicates that the dsc is frozen and cannot be mutated.
     */
    public function isFrozen(): bool
    {
        return $this->isFrozen;
    }

    /**
     * Check if there are any entries set.
     */
    public function hasEntries(): bool
    {
        return !empty($this->entries);
    }

    /**
     * Gets the dsc entries.
     *
     * @return array<string, string>
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * Parse the baggage header.
     *
     * @param string $header the baggage header contents
     */
    public static function fromHeader(string $header): self
    {
        $samplingContext = new self();

        foreach (explode(',', $header) as $listMember) {
            if (empty(trim($listMember))) {
                continue;
            }

            $keyValueAndProperties = explode(';', $listMember, 2);
            $keyValue = trim($keyValueAndProperties[0]);

            if (!str_contains($keyValue, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $keyValue, 2);

            if (mb_substr($key, 0, mb_strlen(self::SENTRY_ENTRY_PREFIX)) === self::SENTRY_ENTRY_PREFIX) {
                $samplingContext->set(rawurldecode(mb_substr($key, mb_strlen(self::SENTRY_ENTRY_PREFIX))), rawurldecode($value));
            }
        }

        // Once we receive a baggage header with Sentry entries from an upstream SDK,
        // we freeze the contents so it cannot be mutated anymore by this SDK.
        // It should only be propagated to the next downstream SDK or the Sentry server itself.
        $samplingContext->isFrozen = $samplingContext->hasEntries();

        return $samplingContext;
    }

    /**
     * Create a dsc object.
     *
     * @see https://develop.sentry.dev/sdk/performance/dynamic-sampling-context/#baggage-header
     */
    public static function fromTransaction(Transaction $transaction, HubInterface $hub): self
    {
        $samplingContext = new self();
        $samplingContext->set('trace_id', (string) $transaction->getTraceId());

        $sampleRate = $transaction->getMetaData()->getSamplingRate();
        if ($sampleRate !== null) {
            $samplingContext->set('sample_rate', (string) $sampleRate);
        }

        // Only include the transaction name if it has good quality
        if ($transaction->getMetadata()->getSource() !== TransactionSource::url()) {
            $samplingContext->set('transaction', $transaction->getName());
        }

        $client = $hub->getClient();

        if ($client !== null) {
            $options = $client->getOptions();

            if ($options->getDsn() !== null && $options->getDsn()->getPublicKey() !== null) {
                $samplingContext->set('public_key', $options->getDsn()->getPublicKey());
            }
            if ($options->getDsn() !== null && $options->getDsn()->getOrgId() !== null) {
                $samplingContext->set('org_id', (string) $options->getDsn()->getOrgId());
            }

            if ($options->getRelease() !== null) {
                $samplingContext->set('release', $options->getRelease());
            }

            if ($options->getEnvironment() !== null) {
                $samplingContext->set('environment', $options->getEnvironment());
            }
        }

        if ($transaction->getSampled() !== null) {
            $samplingContext->set('sampled', $transaction->getSampled() ? 'true' : 'false');
        }

        if ($transaction->getMetadata()->getSampleRand() !== null) {
            $samplingContext->set('sample_rand', (string) $transaction->getMetadata()->getSampleRand());
        }

        $samplingContext->freeze();

        return $samplingContext;
    }

    public static function fromOptions(Options $options, Scope $scope): self
    {
        $samplingContext = new self();
        $samplingContext->set('trace_id', (string) $scope->getPropagationContext()->getTraceId());
        $samplingContext->set('sample_rand', (string) $scope->getPropagationContext()->getSampleRand());

        if ($options->getTracesSampleRate() !== null) {
            $samplingContext->set('sample_rate', (string) $options->getTracesSampleRate());
        }

        if ($options->getDsn() !== null && $options->getDsn()->getPublicKey() !== null) {
            $samplingContext->set('public_key', $options->getDsn()->getPublicKey());
        }

        if ($options->getDsn() !== null && $options->getDsn()->getOrgId() !== null) {
            $samplingContext->set('org_id', (string) $options->getDsn()->getOrgId());
        }

        if ($options->getRelease() !== null) {
            $samplingContext->set('release', $options->getRelease());
        }

        if ($options->getEnvironment() !== null) {
            $samplingContext->set('environment', $options->getEnvironment());
        }

        $samplingContext->freeze();

        return $samplingContext;
    }

    /**
     * Serialize the dsc as a string.
     */
    public function __toString(): string
    {
        $result = [];

        foreach ($this->entries as $key => $value) {
            $result[] = rawurlencode(self::SENTRY_ENTRY_PREFIX . $key) . '=' . rawurlencode($value);
        }

        return implode(',', $result);
    }
}

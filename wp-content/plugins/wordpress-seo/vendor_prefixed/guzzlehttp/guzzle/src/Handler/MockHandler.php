<?php

namespace YoastSEO_Vendor\GuzzleHttp\Handler;

use YoastSEO_Vendor\GuzzleHttp\Exception\RequestException;
use YoastSEO_Vendor\GuzzleHttp\HandlerStack;
use YoastSEO_Vendor\GuzzleHttp\Promise as P;
use YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface;
use YoastSEO_Vendor\GuzzleHttp\TransferStats;
use YoastSEO_Vendor\GuzzleHttp\Utils;
use YoastSEO_Vendor\Psr\Http\Message\RequestInterface;
use YoastSEO_Vendor\Psr\Http\Message\ResponseInterface;
use YoastSEO_Vendor\Psr\Http\Message\StreamInterface;
/**
 * Handler that returns responses or throw exceptions from a queue.
 *
 * @final
 */
class MockHandler implements \Countable
{
    /**
     * @var array
     */
    private $queue = [];
    /**
     * @var RequestInterface|null
     */
    private $lastRequest;
    /**
     * @var array
     */
    private $lastOptions = [];
    /**
     * @var callable|null
     */
    private $onFulfilled;
    /**
     * @var callable|null
     */
    private $onRejected;
    /**
     * Creates a new MockHandler that uses the default handler stack list of
     * middlewares.
     *
     * @param array|null    $queue       Array of responses, callables, or exceptions.
     * @param callable|null $onFulfilled Callback to invoke when the return value is fulfilled.
     * @param callable|null $onRejected  Callback to invoke when the return value is rejected.
     */
    public static function createWithMiddleware(array $queue = null, callable $onFulfilled = null, callable $onRejected = null) : \YoastSEO_Vendor\GuzzleHttp\HandlerStack
    {
        return \YoastSEO_Vendor\GuzzleHttp\HandlerStack::create(new self($queue, $onFulfilled, $onRejected));
    }
    /**
     * The passed in value must be an array of
     * {@see \Psr\Http\Message\ResponseInterface} objects, Exceptions,
     * callables, or Promises.
     *
     * @param array<int, mixed>|null $queue       The parameters to be passed to the append function, as an indexed array.
     * @param callable|null          $onFulfilled Callback to invoke when the return value is fulfilled.
     * @param callable|null          $onRejected  Callback to invoke when the return value is rejected.
     */
    public function __construct(array $queue = null, callable $onFulfilled = null, callable $onRejected = null)
    {
        $this->onFulfilled = $onFulfilled;
        $this->onRejected = $onRejected;
        if ($queue) {
            // array_values included for BC
            $this->append(...\array_values($queue));
        }
    }
    public function __invoke(\YoastSEO_Vendor\Psr\Http\Message\RequestInterface $request, array $options) : \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface
    {
        if (!$this->queue) {
            throw new \OutOfBoundsException('Mock queue is empty');
        }
        if (isset($options['delay']) && \is_numeric($options['delay'])) {
            \usleep((int) $options['delay'] * 1000);
        }
        $this->lastRequest = $request;
        $this->lastOptions = $options;
        $response = \array_shift($this->queue);
        if (isset($options['on_headers'])) {
            if (!\is_callable($options['on_headers'])) {
                throw new \InvalidArgumentException('on_headers must be callable');
            }
            try {
                $options['on_headers']($response);
            } catch (\Exception $e) {
                $msg = 'An error was encountered during the on_headers event';
                $response = new \YoastSEO_Vendor\GuzzleHttp\Exception\RequestException($msg, $request, $response, $e);
            }
        }
        if (\is_callable($response)) {
            $response = $response($request, $options);
        }
        $response = $response instanceof \Throwable ? \YoastSEO_Vendor\GuzzleHttp\Promise\Create::rejectionFor($response) : \YoastSEO_Vendor\GuzzleHttp\Promise\Create::promiseFor($response);
        return $response->then(function (?\YoastSEO_Vendor\Psr\Http\Message\ResponseInterface $value) use($request, $options) {
            $this->invokeStats($request, $options, $value);
            if ($this->onFulfilled) {
                ($this->onFulfilled)($value);
            }
            if ($value !== null && isset($options['sink'])) {
                $contents = (string) $value->getBody();
                $sink = $options['sink'];
                if (\is_resource($sink)) {
                    \fwrite($sink, $contents);
                } elseif (\is_string($sink)) {
                    \file_put_contents($sink, $contents);
                } elseif ($sink instanceof \YoastSEO_Vendor\Psr\Http\Message\StreamInterface) {
                    $sink->write($contents);
                }
            }
            return $value;
        }, function ($reason) use($request, $options) {
            $this->invokeStats($request, $options, null, $reason);
            if ($this->onRejected) {
                ($this->onRejected)($reason);
            }
            return \YoastSEO_Vendor\GuzzleHttp\Promise\Create::rejectionFor($reason);
        });
    }
    /**
     * Adds one or more variadic requests, exceptions, callables, or promises
     * to the queue.
     *
     * @param mixed ...$values
     */
    public function append(...$values) : void
    {
        foreach ($values as $value) {
            if ($value instanceof \YoastSEO_Vendor\Psr\Http\Message\ResponseInterface || $value instanceof \Throwable || $value instanceof \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface || \is_callable($value)) {
                $this->queue[] = $value;
            } else {
                throw new \TypeError('Expected a Response, Promise, Throwable or callable. Found ' . \YoastSEO_Vendor\GuzzleHttp\Utils::describeType($value));
            }
        }
    }
    /**
     * Get the last received request.
     */
    public function getLastRequest() : ?\YoastSEO_Vendor\Psr\Http\Message\RequestInterface
    {
        return $this->lastRequest;
    }
    /**
     * Get the last received request options.
     */
    public function getLastOptions() : array
    {
        return $this->lastOptions;
    }
    /**
     * Returns the number of remaining items in the queue.
     */
    public function count() : int
    {
        return \count($this->queue);
    }
    public function reset() : void
    {
        $this->queue = [];
    }
    /**
     * @param mixed $reason Promise or reason.
     */
    private function invokeStats(\YoastSEO_Vendor\Psr\Http\Message\RequestInterface $request, array $options, \YoastSEO_Vendor\Psr\Http\Message\ResponseInterface $response = null, $reason = null) : void
    {
        if (isset($options['on_stats'])) {
            $transferTime = $options['transfer_time'] ?? 0;
            $stats = new \YoastSEO_Vendor\GuzzleHttp\TransferStats($request, $response, $transferTime, $reason);
            $options['on_stats']($stats);
        }
    }
}

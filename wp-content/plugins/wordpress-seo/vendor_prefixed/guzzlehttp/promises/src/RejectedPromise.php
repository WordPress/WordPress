<?php

declare (strict_types=1);
namespace YoastSEO_Vendor\GuzzleHttp\Promise;

/**
 * A promise that has been rejected.
 *
 * Thenning off of this promise will invoke the onRejected callback
 * immediately and ignore other callbacks.
 *
 * @final
 */
class RejectedPromise implements \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface
{
    private $reason;
    /**
     * @param mixed $reason
     */
    public function __construct($reason)
    {
        if (\is_object($reason) && \method_exists($reason, 'then')) {
            throw new \InvalidArgumentException('You cannot create a RejectedPromise with a promise.');
        }
        $this->reason = $reason;
    }
    public function then(callable $onFulfilled = null, callable $onRejected = null) : \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface
    {
        // If there's no onRejected callback then just return self.
        if (!$onRejected) {
            return $this;
        }
        $queue = \YoastSEO_Vendor\GuzzleHttp\Promise\Utils::queue();
        $reason = $this->reason;
        $p = new \YoastSEO_Vendor\GuzzleHttp\Promise\Promise([$queue, 'run']);
        $queue->add(static function () use($p, $reason, $onRejected) : void {
            if (\YoastSEO_Vendor\GuzzleHttp\Promise\Is::pending($p)) {
                try {
                    // Return a resolved promise if onRejected does not throw.
                    $p->resolve($onRejected($reason));
                } catch (\Throwable $e) {
                    // onRejected threw, so return a rejected promise.
                    $p->reject($e);
                }
            }
        });
        return $p;
    }
    public function otherwise(callable $onRejected) : \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface
    {
        return $this->then(null, $onRejected);
    }
    public function wait(bool $unwrap = \true)
    {
        if ($unwrap) {
            throw \YoastSEO_Vendor\GuzzleHttp\Promise\Create::exceptionFor($this->reason);
        }
        return null;
    }
    public function getState() : string
    {
        return self::REJECTED;
    }
    public function resolve($value) : void
    {
        throw new \LogicException('Cannot resolve a rejected promise');
    }
    public function reject($reason) : void
    {
        if ($reason !== $this->reason) {
            throw new \LogicException('Cannot reject a rejected promise');
        }
    }
    public function cancel() : void
    {
        // pass
    }
}

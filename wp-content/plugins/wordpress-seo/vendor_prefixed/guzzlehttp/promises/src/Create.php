<?php

declare (strict_types=1);
namespace YoastSEO_Vendor\GuzzleHttp\Promise;

final class Create
{
    /**
     * Creates a promise for a value if the value is not a promise.
     *
     * @param mixed $value Promise or value.
     */
    public static function promiseFor($value) : \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface
    {
        if ($value instanceof \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface) {
            return $value;
        }
        // Return a Guzzle promise that shadows the given promise.
        if (\is_object($value) && \method_exists($value, 'then')) {
            $wfn = \method_exists($value, 'wait') ? [$value, 'wait'] : null;
            $cfn = \method_exists($value, 'cancel') ? [$value, 'cancel'] : null;
            $promise = new \YoastSEO_Vendor\GuzzleHttp\Promise\Promise($wfn, $cfn);
            $value->then([$promise, 'resolve'], [$promise, 'reject']);
            return $promise;
        }
        return new \YoastSEO_Vendor\GuzzleHttp\Promise\FulfilledPromise($value);
    }
    /**
     * Creates a rejected promise for a reason if the reason is not a promise.
     * If the provided reason is a promise, then it is returned as-is.
     *
     * @param mixed $reason Promise or reason.
     */
    public static function rejectionFor($reason) : \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface
    {
        if ($reason instanceof \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface) {
            return $reason;
        }
        return new \YoastSEO_Vendor\GuzzleHttp\Promise\RejectedPromise($reason);
    }
    /**
     * Create an exception for a rejected promise value.
     *
     * @param mixed $reason
     */
    public static function exceptionFor($reason) : \Throwable
    {
        if ($reason instanceof \Throwable) {
            return $reason;
        }
        return new \YoastSEO_Vendor\GuzzleHttp\Promise\RejectionException($reason);
    }
    /**
     * Returns an iterator for the given value.
     *
     * @param mixed $value
     */
    public static function iterFor($value) : \Iterator
    {
        if ($value instanceof \Iterator) {
            return $value;
        }
        if (\is_array($value)) {
            return new \ArrayIterator($value);
        }
        return new \ArrayIterator([$value]);
    }
}

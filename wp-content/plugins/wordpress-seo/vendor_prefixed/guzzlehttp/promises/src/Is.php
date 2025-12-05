<?php

declare (strict_types=1);
namespace YoastSEO_Vendor\GuzzleHttp\Promise;

final class Is
{
    /**
     * Returns true if a promise is pending.
     */
    public static function pending(\YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface $promise) : bool
    {
        return $promise->getState() === \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface::PENDING;
    }
    /**
     * Returns true if a promise is fulfilled or rejected.
     */
    public static function settled(\YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface $promise) : bool
    {
        return $promise->getState() !== \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface::PENDING;
    }
    /**
     * Returns true if a promise is fulfilled.
     */
    public static function fulfilled(\YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface $promise) : bool
    {
        return $promise->getState() === \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface::FULFILLED;
    }
    /**
     * Returns true if a promise is rejected.
     */
    public static function rejected(\YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface $promise) : bool
    {
        return $promise->getState() === \YoastSEO_Vendor\GuzzleHttp\Promise\PromiseInterface::REJECTED;
    }
}

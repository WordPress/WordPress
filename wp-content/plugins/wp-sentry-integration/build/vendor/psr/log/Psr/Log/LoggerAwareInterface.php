<?php

namespace WPSentry\ScopedVendor\Psr\Log;

/**
 * Describes a logger-aware instance.
 */
interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(\WPSentry\ScopedVendor\Psr\Log\LoggerInterface $logger);
}

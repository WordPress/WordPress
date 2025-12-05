<?php

namespace Yoast\WP\SEO\AI_Free_Sparks\Application;

/**
 * Interface Consent_Handler_Interface
 *
 * This interface defines the methods for handling user consent.
 */
interface Free_Sparks_Handler_Interface {

	/**
	 * Retrieves the timestamp.
	 *
	 * @param string $format The format in which to return the timestamp. Defaults to 'Y-m-d H:i:s'.
	 *
	 * @return ?string The timestamp when the user started using free sparks, or null if not set.
	 */
	public function get( string $format = 'Y-m-d H:i:s' ): ?string;

	/**
	 * Registers the starting of the free sparks.
	 *
	 * @param ?int $timestamp The timestamp when the user started using free sparks. If null, the current time will be
	 *                        used.
	 *
	 * @return bool True if the operation was successful, false otherwise.
	 */
	public function start( ?int $timestamp = null ): bool;
}

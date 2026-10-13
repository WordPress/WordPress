<?php
/**
 * Secrets API: WP_Secret_Version class
 *
 * @package WordPress
 * @subpackage Secrets
 * @since 7.2.0
 */

/**
 * Identifies which slot of a secret's two-slot history to operate on.
 *
 * String constants on a final class rather than a native enum: enums require PHP 8.1,
 * and this class ships in core, whose minimum supported PHP version is 7.4.
 *
 * @since 7.2.0
 */
final class WP_Secret_Version {

	/**
	 * The current value of a secret.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const CURRENT = 'current';

	/**
	 * The value a secret held immediately before its most recent overwrite.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	const PREVIOUS = 'previous';
}

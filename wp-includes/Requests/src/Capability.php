<?php
/**
 * Capability interface declaring the known capabilities.
 *
 * @package Requests\Utilities
 */

namespace WpOrg\Requests;

/**
 * Capability interface declaring the known capabilities.
 *
 * This is used as the authoritative source for which capabilities can be queried.
 *
 * @package Requests\Utilities
 */
interface Capability {

	/**
	 * Support for SSL.
	 *
	 * @var string
	 */
	const SSL = 'ssl';

	/**
	 * Collection of all capabilities supported in Requests.
	 *
	 * Note: this does not automatically mean that the capability will be supported for your chosen transport!
	 *
	 * @var array<string>
	 */
	const ALL = [
		self::SSL,
	];
}

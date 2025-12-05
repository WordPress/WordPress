<?php

namespace Yoast\WP\SEO\Services\Health_Check;

/**
 * Runs the Default_Tagline health check.
 */
class Default_Tagline_Runner implements Runner_Interface {

	/**
	 * The default WordPress tagline.
	 */
	public const DEFAULT_BLOG_DESCRIPTION = 'Just another WordPress site';

	/**
	 * Is set to true when the default tagline is set.
	 *
	 * @var bool
	 */
	private $has_default_tagline = true;

	/**
	 * Runs the health check. Checks if the tagline is set to WordPress' default tagline, or to its set translation.
	 *
	 * @return void
	 */
	public function run() {
		$blog_description = \get_option( 'blogdescription' );

		// We are using the WordPress internal translation.
		// @TODO: This doesn't work when checking in a cron for some reason, investigate.
		$translated_blog_description = \__( 'Just another WordPress site', 'default' );

		$this->has_default_tagline = $translated_blog_description === $blog_description || $blog_description === self::DEFAULT_BLOG_DESCRIPTION;
	}

	/**
	 * Returns true if the tagline is set to a non-default tagline.
	 *
	 * @return bool The boolean indicating if the health check was succesful.
	 */
	public function is_successful() {
		return ! $this->has_default_tagline;
	}
}

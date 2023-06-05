<?php
/* HEADER */ // phpcs:ignore

/**
 * This class handles the shutdown of the autoloader.
 */
class Shutdown_Handler {

	/**
	 * The Plugins_Handler instance.
	 *
	 * @var Plugins_Handler
	 */
	private $plugins_handler;

	/**
	 * The plugins cached by this autoloader.
	 *
	 * @var string[]
	 */
	private $cached_plugins;

	/**
	 * Indicates whether or not this autoloader was included by another.
	 *
	 * @var bool
	 */
	private $was_included_by_autoloader;

	/**
	 * Constructor.
	 *
	 * @param Plugins_Handler $plugins_handler The Plugins_Handler instance to use.
	 * @param string[]        $cached_plugins The plugins cached by the autoloaer.
	 * @param bool            $was_included_by_autoloader Indicates whether or not the autoloader was included by another.
	 */
	public function __construct( $plugins_handler, $cached_plugins, $was_included_by_autoloader ) {
		$this->plugins_handler            = $plugins_handler;
		$this->cached_plugins             = $cached_plugins;
		$this->was_included_by_autoloader = $was_included_by_autoloader;
	}

	/**
	 * Handles the shutdown of the autoloader.
	 */
	public function __invoke() {
		// Don't save a broken cache if an error happens during some plugin's initialization.
		if ( ! did_action( 'plugins_loaded' ) ) {
			// Ensure that the cache is emptied to prevent consecutive failures if the cache is to blame.
			if ( ! empty( $this->cached_plugins ) ) {
				$this->plugins_handler->cache_plugins( array() );
			}

			return;
		}

		// Load the active plugins fresh since the list we pulled earlier might not contain
		// plugins that were activated but did not reset the autoloader. This happens
		// when a plugin is in the cache but not "active" when the autoloader loads.
		// We also want to make sure that plugins which are deactivating are not
		// considered "active" so that they will be removed from the cache now.
		try {
			$active_plugins = $this->plugins_handler->get_active_plugins( false, ! $this->was_included_by_autoloader );
		} catch ( \Exception $ex ) {
			// When the package is deleted before shutdown it will throw an exception.
			// In the event this happens we should erase the cache.
			if ( ! empty( $this->cached_plugins ) ) {
				$this->plugins_handler->cache_plugins( array() );
			}
			return;
		}

		// The paths should be sorted for easy comparisons with those loaded from the cache.
		// Note we don't need to sort the cached entries because they're already sorted.
		sort( $active_plugins );

		// We don't want to waste time saving a cache that hasn't changed.
		if ( $this->cached_plugins === $active_plugins ) {
			return;
		}

		$this->plugins_handler->cache_plugins( $active_plugins );
	}
}

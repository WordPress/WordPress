<?php
/**
 * Font Library class.
 *
 * This file contains the Font Library class definition.
 *
 * @package    WordPress
 * @subpackage Fonts
 * @since      6.5.0
 */

/**
 * Font Library class.
 *
 * @since 6.5.0
 */
class WP_Font_Library {

	/**
	 * Font collections.
	 *
	 * @since 6.5.0
	 * @var array
	 */
	private $collections = array();

	/**
	 * Container for the main instance of the class.
	 *
	 * @since 6.5.0
	 * @var WP_Font_Library|null
	 */
	private static $instance = null;

	/**
	 * Register a new font collection.
	 *
	 * @since 6.5.0
	 *
	 * @param string $slug         Font collection slug.
	 * @param array  $data_or_file Font collection data array or a path/URL to a JSON file
	 *                             containing the font collection.
	 *                             See {@see wp_register_font_collection()} for the supported fields.
	 * @return WP_Font_Collection|WP_Error A font collection if it was registered successfully,
	 *                                     or WP_Error object on failure.
	 */
	public function register_font_collection( $slug, $data_or_file ) {
		$new_collection = new WP_Font_Collection( $slug, $data_or_file );

		if ( $this->is_collection_registered( $new_collection->slug ) ) {
			$error_message = sprintf(
				/* translators: %s: Font collection slug. */
				__( 'Font collection with slug: "%s" is already registered.' ),
				$new_collection->slug
			);
			_doing_it_wrong(
				__METHOD__,
				$error_message,
				'6.5.0'
			);
			return new WP_Error( 'font_collection_registration_error', $error_message );
		}
		$this->collections[ $new_collection->slug ] = $new_collection;
		return $new_collection;
	}

	/**
	 * Unregisters a previously registered font collection.
	 *
	 * @since 6.5.0
	 *
	 * @param string $slug Font collection slug.
	 * @return bool True if the font collection was unregistered successfully and false otherwise.
	 */
	public function unregister_font_collection( $slug ) {
		if ( ! $this->is_collection_registered( $slug ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: Font collection slug. */
				sprintf( __( 'Font collection "%s" not found.' ), $slug ),
				'6.5.0'
			);
			return false;
		}
		unset( $this->collections[ $slug ] );
		return true;
	}

	/**
	 * Checks if a font collection is registered.
	 *
	 * @since 6.5.0
	 *
	 * @param string $slug Font collection slug.
	 * @return bool True if the font collection is registered and false otherwise.
	 */
	private function is_collection_registered( $slug ) {
		return array_key_exists( $slug, $this->collections );
	}

	/**
	 * Gets all the font collections available.
	 *
	 * @since 6.5.0
	 *
	 * @return array List of font collections.
	 */
	public function get_font_collections() {
		return $this->collections;
	}

	/**
	 * Gets a font collection.
	 *
	 * @since 6.5.0
	 *
	 * @param string $slug Font collection slug.
	 * @return WP_Font_Collection|WP_Error Font collection object,
	 *                                     or WP_Error object if the font collection doesn't exist.
	 */
	public function get_font_collection( $slug ) {
		if ( $this->is_collection_registered( $slug ) ) {
			return $this->collections[ $slug ];
		}
		return new WP_Error( 'font_collection_not_found', __( 'Font collection not found.' ) );
	}

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 6.5.0
	 *
	 * @return WP_Font_Library The main instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

<?php
/**
 * @package WPSEO\Frontend
 */

/**
 * This code handles the Google+ specific output that's not covered by OpenGraph.
 */
class WPSEO_GooglePlus {

	/**
	 * @var    object    Instance of this class
	 */
	public static $instance;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'wpseo_googleplus', array( $this, 'google_plus_title' ), 10 );
		add_action( 'wpseo_googleplus', array( $this, 'description' ), 11 );
		add_action( 'wpseo_googleplus', array( $this, 'google_plus_image' ), 12 );

		add_action( 'wpseo_head', array( $this, 'output' ), 40 );
	}

	/**
	 * Get the singleton instance of this class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Output the Google+ specific content
	 */
	public function output() {
		/**
		 * Action: 'wpseo_googleplus' - Hook to add all Google+ specific output to.
		 */
		do_action( 'wpseo_googleplus' );
	}

	/**
	 * Output the Google+ specific description
	 */
	public function description() {
		if ( is_singular() ) {
			$desc = WPSEO_Meta::get_value( 'google-plus-description' );

			/**
			 * Filter: 'wpseo_googleplus_desc' - Allow developers to change the Google+ specific description output
			 *
			 * @api string $desc The description string
			 */
			$desc = trim( apply_filters( 'wpseo_googleplus_desc', $desc ) );

			if ( is_string( $desc ) && '' !== $desc ) {
				echo '<meta itemprop="description" content="', esc_attr( $desc ), '">', "\n";
			}
		}
	}

	/**
	 * Output the Google+ specific title
	 */
	public function google_plus_title() {
		if ( is_singular() ) {
			$title = WPSEO_Meta::get_value( 'google-plus-title' );

			/**
			 * Filter: 'wpseo_googleplus_title' - Allow developers to change the Google+ specific title
			 *
			 * @api string $title The title string
			 */
			$title = trim( apply_filters( 'wpseo_googleplus_title', $title ) );

			if ( is_string( $title ) && $title !== '' ) {
				$title = wpseo_replace_vars( $title, get_post() );

				echo '<meta itemprop="name" content="', esc_attr( $title ), '">', "\n";
			}
		}
	}

	/**
	 * Output the Google+ specific image
	 */
	public function google_plus_image() {
		if ( is_singular() ) {
			$image = WPSEO_Meta::get_value( 'google-plus-image' );

			/**
			 * Filter: 'wpseo_googleplus_image' - Allow changing the Google+ image
			 *
			 * @api string $img Image URL string
			 */
			$image = trim( apply_filters( 'wpseo_googleplus_image', $image ) );

			if ( is_string( $image ) && $image !== '' ) {
				echo '<meta itemprop="image" content="', esc_url( $image ), '">', "\n";
			}
		}
	}
}

<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Class for managing integration toggles.
 */
class Yoast_Integration_Toggles {

	/**
	 * Available integration toggles.
	 *
	 * @var array
	 */
	protected $toggles;

	/**
	 * Instance holder.
	 *
	 * @var self|null
	 */
	protected static $instance = null;

	/**
	 * Gets the main integration toggles manager instance used.
	 *
	 * This essentially works like a Singleton, but for its drawbacks does not restrict
	 * instantiation otherwise.
	 *
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Gets all available integration toggles.
	 *
	 * @return array List of sorted Yoast_Feature_Toggle instances.
	 */
	public function get_all() {
		if ( $this->toggles === null ) {
			$this->toggles = $this->load_toggles();
		}

		return $this->toggles;
	}

	/**
	 * Loads the available integration toggles.
	 *
	 * Also ensures that the toggles are all Yoast_Feature_Toggle instances and sorted by their order value.
	 *
	 * @return array List of sorted Yoast_Feature_Toggle instances.
	 */
	protected function load_toggles() {
		$integration_toggles = [
			(object) [
				/* translators: %s: 'Semrush' */
				'name'            => sprintf( __( '%s integration', 'wordpress-seo' ), 'Semrush' ),
				'setting'         => 'semrush_integration_active',
				'label'           => sprintf(
					/* translators: %s: 'Semrush' */
					__( 'The %s integration offers suggestions and insights for keywords related to the entered focus keyphrase.', 'wordpress-seo' ),
					'Semrush'
				),
				'order'           => 10,
			],
			(object) [
				/* translators: %s: Algolia. */
				'name'               => sprintf( esc_html__( '%s integration', 'wordpress-seo' ), 'Algolia' ),
				'premium'            => true,
				'setting'            => 'algolia_integration_active',
				'label'              => __( 'Improve the quality of your site search! Automatically helps your users find your cornerstone and most important content in your internal search results. It also removes noindexed posts & pages from your site’s search results.', 'wordpress-seo' ),
				/* translators: %s: Algolia. */
				'read_more_label'    => sprintf( __( 'Find out more about our %s integration.', 'wordpress-seo' ), 'Algolia' ),
				'read_more_url'      => 'https://yoa.st/4eu',
				'premium_url'        => 'https://yoa.st/4ex',
				'premium_upsell_url' => 'https://yoa.st/get-algolia-integration',
				'order'              => 25,
			],
		];

		/**
		 * Filter to add integration toggles from add-ons.
		 *
		 * @param array $integration_toggles Array with integration toggle objects where each object
		 *                                   should have a `name`, `setting` and `label` property.
		 */
		$integration_toggles = apply_filters( 'wpseo_integration_toggles', $integration_toggles );

		$integration_toggles = array_map( [ $this, 'ensure_toggle' ], $integration_toggles );
		usort( $integration_toggles, [ $this, 'sort_toggles_callback' ] );

		return $integration_toggles;
	}

	/**
	 * Ensures that the passed value is a Yoast_Feature_Toggle.
	 *
	 * @param Yoast_Feature_Toggle|object|array $toggle_data Feature toggle instance, or raw object or array
	 *                                                       containing integration toggle data.
	 * @return Yoast_Feature_Toggle Feature toggle instance based on $toggle_data.
	 */
	protected function ensure_toggle( $toggle_data ) {
		if ( $toggle_data instanceof Yoast_Feature_Toggle ) {
			return $toggle_data;
		}

		if ( is_object( $toggle_data ) ) {
			$toggle_data = get_object_vars( $toggle_data );
		}

		return new Yoast_Feature_Toggle( $toggle_data );
	}

	/**
	 * Callback for sorting integration toggles by their order.
	 *
	 * {@internal Once the minimum PHP version goes up to PHP 7.0, the logic in the function
	 * can be replaced with the spaceship operator `<=>`.}
	 *
	 * @param Yoast_Feature_Toggle $feature_a Feature A.
	 * @param Yoast_Feature_Toggle $feature_b Feature B.
	 *
	 * @return int An integer less than, equal to, or greater than zero indicating respectively
	 *             that feature A is considered to be less than, equal to, or greater than feature B.
	 */
	protected function sort_toggles_callback( Yoast_Feature_Toggle $feature_a, Yoast_Feature_Toggle $feature_b ) {
		return ( $feature_a->order - $feature_b->order );
	}
}

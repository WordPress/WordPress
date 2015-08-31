<?php
/**
 * @package WPSEO\Frontend
 */

/**
 * Class WPSEO_JSON_LD
 *
 * Outputs schema code specific for Google's JSON LD stuff
 *
 * @since 1.8
 */
class WPSEO_JSON_LD {

	/**
	 * @var array Holds the plugins options.
	 */
	public $options = array();

	/**
	 * @var array Holds the social profiles for the entity
	 */
	private $profiles = array();

	/**
	 * @var array Holds the data to put out
	 */
	private $data = array();

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->options = WPSEO_Options::get_all();

		add_action( 'wpseo_head', array( $this, 'json_ld' ), 90 );
		add_action( 'wpseo_json_ld', array( $this, 'website' ), 10 );
		add_action( 'wpseo_json_ld', array( $this, 'organization_or_person' ), 20 );
	}

	/**
	 * JSON LD output function that the functions for specific code can hook into
	 *
	 * @since 1.8
	 */
	public function json_ld() {
		do_action( 'wpseo_json_ld' );
	}

	/**
	 * Outputs code to allow Google to recognize social profiles for use in the Knowledge graph
	 *
	 * @since 1.8
	 */
	public function organization_or_person() {
		if ( '' === $this->options['company_or_person'] ) {
			return;
		}

		$this->prepare_organization_person_markup();

		switch ( $this->options['company_or_person'] ) {
			case 'company':
				$this->organization();
				break;
			case 'person':
				$this->person();
				break;
		}

		$this->output( $this->options['company_or_person'] );
	}

	/**
	 * Outputs code to allow recognition of the internal search engine
	 *
	 * @since 1.5.7
	 *
	 * @link  https://developers.google.com/structured-data/site-name
	 */
	public function website() {
		$this->data = array(
			'@context' => 'http://schema.org',
			'@type'    => 'WebSite',
			'url'      => $this->get_home_url(),
			'name'     => $this->get_website_name(),
		);

		$this->add_alternate_name();
		$this->internal_search_section();

		$this->output( 'website' );
	}

	/**
	 * Outputs the JSON LD code in a valid JSON+LD wrapper
	 *
	 * @since 1.8
	 *
	 * @param string $context The context of the output, useful for filtering.
	 */
	private function output( $context ) {
		/**
		 * Filter: 'wpseo_json_ld_output' - Allows filtering of the JSON+LD output
		 *
		 * @api array $output The output array, before its JSON encoded
		 *
		 * @param string $context The context of the output, useful to determine whether to filter or not.
		 */
		$this->data = apply_filters( 'wpseo_json_ld_output', $this->data, $context );

		if ( function_exists( 'wp_json_encode' ) ) {
			$json_data = wp_json_encode( $this->data );  // Function wp_json_encode() was introduced in WP 4.1.
		}
		else {
			$json_data = json_encode( $this->data );
		}

		if ( is_array( $this->data ) && ! empty( $this->data ) ) {
			echo "<script type='application/ld+json'>", $json_data, '</script>', "\n";
		}

		// Empty the $data array so we don't output it twice.
		$this->data = array();
	}

	/**
	 * Schema for Organization
	 */
	private function organization() {
		if ( '' !== $this->options['company_name'] ) {
			$this->data['@type'] = 'Organization';
			$this->data['name']  = $this->options['company_name'];
			$this->data['logo']  = $this->options['company_logo'];
			return;
		}
		$this->data = false;
	}

	/**
	 * Schema for Person
	 */
	private function person() {
		if ( '' !== $this->options['person_name'] ) {
			$this->data['@type'] = 'Person';
			$this->data['name']  = $this->options['person_name'];
			return;
		}
		$this->data = false;
	}

	/**
	 * Prepares the organization or person markup
	 */
	private function prepare_organization_person_markup() {
		$this->fetch_social_profiles();

		$this->data = array(
			'@context' => 'http://schema.org',
			'@type'    => '',
			'url'      => WPSEO_Frontend::get_instance()->canonical( false, true ),
			'sameAs'   => $this->profiles,
		);
	}

	/**
	 * Retrieve the social profiles to display in the organization output.
	 *
	 * @since 1.8
	 *
	 * @link  https://developers.google.com/webmasters/structured-data/customize/social-profiles
	 */
	private function fetch_social_profiles() {
		$social_profiles = array(
			'facebook_site',
			'instagram_url',
			'linkedin_url',
			'plus-publisher',
			'myspace_url',
			'youtube_url',
			'pinterest_url',
		);
		foreach ( $social_profiles as $profile ) {
			if ( $this->options[ $profile ] !== '' ) {
				$this->profiles[] = $this->options[ $profile ];
			}
		}

		if ( ! empty( $this->options['twitter_site'] ) ) {
			$this->profiles[] = 'https://twitter.com/' . $this->options['twitter_site'];
		}
	}

	/**
	 * Retrieves the home URL
	 *
	 * @return string
	 */
	private function get_home_url() {
		/**
		 * Filter: 'wpseo_json_home_url' - Allows filtering of the home URL for Yoast SEO's JSON+LD output
		 *
		 * @api unsigned string
		 */
		return apply_filters( 'wpseo_json_home_url', trailingslashit( home_url() ) );
	}

	/**
	 * Returns an alternate name if one was specified in the Yoast SEO settings
	 */
	private function add_alternate_name() {
		if ( '' !== $this->options['alternate_website_name'] ) {
			$this->data['alternateName'] = $this->options['alternate_website_name'];
		}
	}

	/**
	 * Adds the internal search JSON LD code if it's not disabled
	 *
	 * @link https://developers.google.com/structured-data/slsb-overview
	 */
	private function internal_search_section() {
		/**
		 * Filter: 'disable_wpseo_json_ld_search' - Allow disabling of the json+ld output
		 *
		 * @api bool $display_search Whether or not to display json+ld search on the frontend
		 */
		if ( ! apply_filters( 'disable_wpseo_json_ld_search', false ) ) {
			/**
			 * Filter: 'wpseo_json_ld_search_url' - Allows filtering of the search URL for Yoast SEO
			 *
			 * @api string $search_url The search URL for this site with a `{search_term_string}` variable.
			 */
			$search_url = apply_filters( 'wpseo_json_ld_search_url', $this->get_home_url() . '?s={search_term_string}' );

			$this->data['potentialAction'] = array(
				'@type'       => 'SearchAction',
				'target'      => $search_url,
				'query-input' => 'required name=search_term_string',
			);
		}
	}

	/**
	 * Returns the website name either from Yoast SEO's options or from the site settings
	 *
	 * @since 2.1
	 *
	 * @return string
	 */
	private function get_website_name() {
		if ( '' !== $this->options['website_name'] ) {
			return $this->options['website_name'];
		}

		return get_bloginfo( 'name' );
	}

	/**
	 * Renders internal search schema markup
	 *
	 * @deprecated 2.1
	 * @deprecated use WPSEO_JSON_LD::website()
	 */
	public function internal_search() {
		_deprecated_function( __METHOD__, 'WPSEO 2.1', 'WPSEO_JSON_LD::website()' );

		$this->website();
	}
}

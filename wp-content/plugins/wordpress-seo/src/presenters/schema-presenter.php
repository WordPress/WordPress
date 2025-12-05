<?php

namespace Yoast\WP\SEO\Presenters;

use WPSEO_Utils;

/**
 * Presenter class for the schema object.
 */
class Schema_Presenter extends Abstract_Indexable_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'schema';

	/**
	 * Returns the schema output.
	 *
	 * @return string The schema tag.
	 */
	public function present() {
		$deprecated_data = [
			'_deprecated' => 'Please use the "wpseo_schema_*" filters to extend the Yoast SEO schema data - see the WPSEO_Schema class.',
		];

		/**
		 * Filter: 'wpseo_json_ld_output' - Allows disabling Yoast's schema output entirely.
		 *
		 * @param mixed  $deprecated If false or an empty array is returned, disable our output.
		 * @param string $empty
		 */
		$return = \apply_filters( 'wpseo_json_ld_output', $deprecated_data, '' );
		if ( $return === [] || $return === false ) {
			return '';
		}

		/**
		 * Action: 'wpseo_json_ld' - Output Schema before the main schema from Yoast SEO is output.
		 */
		\do_action( 'wpseo_json_ld' );

		$schema = $this->get();
		if ( \is_array( $schema ) ) {
			$output = WPSEO_Utils::format_json_encode( $schema );
			$output = \str_replace( "\n", \PHP_EOL . "\t", $output );
			return '<script type="application/ld+json" class="yoast-schema-graph">' . $output . '</script>';
		}

		return '';
	}

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return array The raw value.
	 */
	public function get() {
		return $this->presentation->schema;
	}
}

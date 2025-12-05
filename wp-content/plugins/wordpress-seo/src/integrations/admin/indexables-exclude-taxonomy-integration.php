<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Indexables_Exclude_Taxonomy_Integration class
 */
class Indexables_Exclude_Taxonomy_Integration implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Indexables_Exclude_Taxonomy_Integration constructor.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_indexable_excluded_taxonomies', [ $this, 'exclude_taxonomies_for_indexation' ] );
	}

	/**
	 * Exclude the taxonomy from the indexable table.
	 *
	 * @param array $excluded_taxonomies The excluded taxonomies.
	 *
	 * @return array The excluded taxonomies, including specific taxonomies.
	 */
	public function exclude_taxonomies_for_indexation( $excluded_taxonomies ) {
		$taxonomies_to_exclude = \array_merge( $excluded_taxonomies, [ 'wp_pattern_category' ] );

		if ( $this->options_helper->get( 'disable-post_format', false ) ) {
			return \array_merge( $taxonomies_to_exclude, [ 'post_format' ] );
		}

		return $taxonomies_to_exclude;
	}
}

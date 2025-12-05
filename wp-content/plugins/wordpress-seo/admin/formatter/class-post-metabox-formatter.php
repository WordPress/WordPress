<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Formatter
 */

use Yoast\WP\SEO\Editors\Application\Seo\Post_Seo_Information_Repository;

/**
 * This class provides data for the post metabox by return its values for localization.
 */
class WPSEO_Post_Metabox_Formatter implements WPSEO_Metabox_Formatter_Interface {

	/**
	 * Holds the WordPress Post.
	 *
	 * @var WP_Post
	 */
	private $post;

	/**
	 * The permalink to follow.
	 *
	 * @var string
	 */
	private $permalink;

	/**
	 * Constructor.
	 *
	 * @param WP_Post|array $post      Post object.
	 * @param array         $options   Title options to use.
	 * @param string        $structure The permalink to follow.
	 */
	public function __construct( $post, array $options, $structure ) {
		$this->post      = $post;
		$this->permalink = $structure;
	}

	/**
	 * Determines whether the social templates should be used.
	 *
	 * @deprecated 23.1
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function use_social_templates() {
		_deprecated_function( __METHOD__, 'Yoast SEO 23.1' );
	}

	/**
	 * Returns the translated values.
	 *
	 * @return array
	 */
	public function get_values() {

		$values = [
			'metaDescriptionDate' => '',
		];

		if ( $this->post instanceof WP_Post ) {

			/** @var Post_Seo_Information_Repository $repo */
			$repo = YoastSEO()->classes->get( Post_Seo_Information_Repository::class );
			$repo->set_post( $this->post );

			$values_to_set = [
				'isInsightsEnabled' => $this->is_insights_enabled(),
			];

			$values = ( $values_to_set + $values );
			$values = ( $repo->get_seo_data() + $values );
		}

		/**
		 * Filter: 'wpseo_post_edit_values' - Allows changing the values Yoast SEO uses inside the post editor.
		 *
		 * @param array   $values The key-value map Yoast SEO uses inside the post editor.
		 * @param WP_Post $post   The post opened in the editor.
		 */
		return apply_filters( 'wpseo_post_edit_values', $values, $this->post );
	}

	/**
	 * Determines whether the insights feature is enabled for this post.
	 *
	 * @return bool
	 */
	protected function is_insights_enabled() {
		return WPSEO_Options::get( 'enable_metabox_insights', false );
	}
}

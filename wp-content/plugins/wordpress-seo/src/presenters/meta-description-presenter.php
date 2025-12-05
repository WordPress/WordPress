<?php

namespace Yoast\WP\SEO\Presenters;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;

/**
 * Presenter class for the meta description.
 */
class Meta_Description_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'description';

	/**
	 * Returns the meta description for a post.
	 *
	 * @return string The meta description tag.
	 */
	public function present() {
		$output = parent::present();

		if ( ! empty( $output ) ) {
			return $output;
		}

		if ( \current_user_can( 'wpseo_manage_options' ) ) {
			return '<!-- '
				. \sprintf(
					/* translators: %1$s resolves to Yoast SEO, %2$s resolves to the Settings submenu item. */
					\esc_html__( 'Admin only notice: this page does not show a meta description because it does not have one, either write it for this page specifically or go into the [%1$s - %2$s] menu and set up a template.', 'wordpress-seo' ),
					\esc_html__( 'Yoast SEO', 'wordpress-seo' ),
					\esc_html__( 'Settings', 'wordpress-seo' )
				)
				. ' -->';
		}

		return '';
	}

	/**
	 * Run the meta description content through replace vars, the `wpseo_metadesc` filter and sanitization.
	 *
	 * @return string The filtered meta description.
	 */
	public function get() {
		$meta_description = $this->replace_vars( $this->presentation->meta_description );

		/**
		 * Filter: 'wpseo_metadesc' - Allow changing the Yoast SEO meta description sentence.
		 *
		 * @param string                 $meta_description The description sentence.
		 * @param Indexable_Presentation $presentation     The presentation of an indexable.
		 */
		$meta_description = \apply_filters( 'wpseo_metadesc', $meta_description, $this->presentation );
		$meta_description = $this->helpers->string->strip_all_tags( \stripslashes( $meta_description ) );
		return \trim( $meta_description );
	}
}

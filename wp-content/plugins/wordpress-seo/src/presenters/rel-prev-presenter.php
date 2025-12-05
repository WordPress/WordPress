<?php

namespace Yoast\WP\SEO\Presenters;

/**
 * Presenter class for the rel prev meta tag.
 */
class Rel_Prev_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'prev';

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = self::LINK_REL_HREF;

	/**
	 * The method of escaping to use.
	 *
	 * @var string
	 */
	protected $escaping = 'url';

	/**
	 * Returns the rel prev meta tag.
	 *
	 * @param bool $output_tag Optional. Whether or not to output the HTML tag. Defaults to true.
	 *
	 * @return string The rel prev tag.
	 */
	public function present( $output_tag = true ) {
		$output = parent::present();

		if ( ! empty( $output ) ) {
			/**
			 * Filter: 'wpseo_prev_rel_link' - Allow changing link rel output by Yoast SEO.
			 *
			 * @param string $unsigned The full `<link` element.
			 */
			return \apply_filters( 'wpseo_prev_rel_link', $output );
		}

		return '';
	}

	/**
	 * Run the rel prev content through the `wpseo_adjacent_rel_url` filter.
	 *
	 * @return string The filtered adjacent link.
	 */
	public function get() {
		if ( \in_array( 'noindex', $this->presentation->robots, true ) ) {
			return '';
		}

		/**
		 * Filter: 'wpseo_adjacent_rel_url' - Allow filtering of the rel prev URL put out by Yoast SEO.
		 *
		 * @param string                 $canonical    The rel prev URL.
		 * @param string                 $rel          Link relationship, prev or next.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		return (string) \trim( \apply_filters( 'wpseo_adjacent_rel_url', $this->presentation->rel_prev, 'prev', $this->presentation ) );
	}
}

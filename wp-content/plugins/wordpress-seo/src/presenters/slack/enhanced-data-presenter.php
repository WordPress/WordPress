<?php

namespace Yoast\WP\SEO\Presenters\Slack;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Presenter;

/**
 * Presenter class for the Slack enhanced data.
 */
class Enhanced_Data_Presenter extends Abstract_Indexable_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'twitter:misc';

	/**
	 * Presents the enhanced data for Slack
	 *
	 * @return string The Twitter tags for Slack.
	 */
	public function present() {
		$enhanced_data = $this->get();
		$twitter_tags  = '';
		$i             = 1;
		$class         = \is_admin_bar_showing() ? ' class="yoast-seo-meta-tag"' : '';
		foreach ( $enhanced_data as $label => $value ) {
			$twitter_tags .= \sprintf( "\t" . '<meta name="twitter:label%1$d" content="%2$s"' . $class . ' />' . "\n", $i, \esc_attr( $label ) );
			$twitter_tags .= \sprintf( "\t" . '<meta name="twitter:data%1$d" content="%2$s"' . $class . ' />' . "\n", $i, \esc_attr( $value ) );
			++$i;
		}

		return \trim( $twitter_tags );
	}

	/**
	 * Gets the enhanced data array.
	 *
	 * @return array The enhanced data array
	 */
	public function get() {
		$data                   = [];
		$author_id              = $this->presentation->source->post_author;
		$estimated_reading_time = $this->presentation->estimated_reading_time_minutes;

		if ( $this->presentation->model->object_sub_type === 'post' && $author_id ) {
			$data[ \__( 'Written by', 'wordpress-seo' ) ] = \get_the_author_meta( 'display_name', $author_id );
		}

		if ( ! empty( $estimated_reading_time ) ) {
			/* translators: %s expands to the reading time number, in minutes */
			$data[ \__( 'Est. reading time', 'wordpress-seo' ) ] = \sprintf( \_n( '%s minute', '%s minutes', $estimated_reading_time, 'default' ), $estimated_reading_time );
		}

		/**
		 * Filter: 'wpseo_enhanced_slack_data' - Allows filtering of the enhanced data for sharing on Slack.
		 *
		 * @param array                  $data         The enhanced Slack sharing data.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		return \apply_filters( 'wpseo_enhanced_slack_data', $data, $this->presentation );
	}
}

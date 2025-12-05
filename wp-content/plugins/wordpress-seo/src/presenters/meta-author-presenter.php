<?php

namespace Yoast\WP\SEO\Presenters;

use WP_User;
use Yoast\WP\SEO\Presentations\Indexable_Presentation;

/**
 * Presenter class for the meta author tag.
 */
class Meta_Author_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'author';

	/**
	 * Returns the author for a post in a meta author tag.
	 *
	 * @return string The meta author tag.
	 */
	public function present() {
		$output = parent::present();

		if ( ! empty( $output ) ) {
			return $output;
		}

		return '';
	}

	/**
	 * Get the author's display name.
	 *
	 * @return string The author's display name.
	 */
	public function get() {
		if ( $this->presentation->model->object_sub_type !== 'post' ) {
			return '';
		}

		$user_data = \get_userdata( $this->presentation->context->post->post_author );

		if ( ! $user_data instanceof WP_User ) {
			return '';
		}

		/**
		 * Filter: 'wpseo_meta_author' - Allow developers to filter the article's author meta tag.
		 *
		 * @param string                 $author_name  The article author's display name. Return empty to disable the tag.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		return \trim( $this->helpers->schema->html->smart_strip_tags( \apply_filters( 'wpseo_meta_author', $user_data->display_name, $this->presentation ) ) );
	}
}

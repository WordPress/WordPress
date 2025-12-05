<?php

namespace Yoast\WP\SEO\Presenters\Open_Graph;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter;

/**
 * Presenter class for the Open Graph article author.
 */
class Article_Author_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'article:author';

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = self::META_PROPERTY_CONTENT;

	/**
	 * Run the article author's Facebook URL through the `wpseo_opengraph_author_facebook` filter.
	 *
	 * @return string The filtered article author's Facebook URL.
	 */
	public function get() {
		/**
		 * Filter: 'wpseo_opengraph_author_facebook' - Allow developers to filter the article author's Facebook URL.
		 *
		 * @param bool|string            $article_author The article author's Facebook URL, return false to disable.
		 * @param Indexable_Presentation $presentation   The presentation of an indexable.
		 */
		return \trim( \apply_filters( 'wpseo_opengraph_author_facebook', $this->presentation->open_graph_article_author, $this->presentation ) );
	}
}

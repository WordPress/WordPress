<?php

namespace Yoast\WP\SEO\Presenters;

/**
 * Presenter class for the URL list.
 */
class Url_List_Presenter extends Abstract_Presenter {

	/**
	 * If the url should be target blank.
	 *
	 * @var bool
	 */
	private $target_blank;

	/**
	 * A list of arrays containing titles and URLs.
	 *
	 * @var array
	 */
	private $links;

	/**
	 * Classname for the URL list.
	 *
	 * @var string
	 */
	private $class_name;

	/**
	 * Url_List_Presenter constructor.
	 *
	 * @param array  $links        A list of arrays containing titles and urls.
	 * @param string $class_name   Classname for the url list.
	 * @param bool   $target_blank If the url should be target blank.
	 */
	public function __construct( $links, $class_name = 'yoast-url-list', $target_blank = false ) {
		$this->links        = $links;
		$this->class_name   = $class_name;
		$this->target_blank = $target_blank;
	}

	/**
	 * Presents the URL list.
	 *
	 * @return string The URL list.
	 */
	public function present() {
		$output = '<ul class="' . $this->class_name . '">';
		foreach ( $this->links as $link ) {
			$output .= '<li><a';
			if ( $this->target_blank ) {
				$output .= ' target = "_blank"';
			}
			$output .= ' href="' . $link['permalink'] . '">' . $link['title'] . '</a></li>';
		}
		$output .= '</ul>';

		return $output;
	}
}

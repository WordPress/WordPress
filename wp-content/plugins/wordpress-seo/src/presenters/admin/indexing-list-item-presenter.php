<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Class Indexing_List_Item_Presenter.
 *
 * @package Yoast\WP\SEO\Presenters\Admin
 */
class Indexing_List_Item_Presenter extends Abstract_Presenter {

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	protected $short_link_helper;

	/**
	 * Indexing_List_Item_Presenter constructor.
	 *
	 * @param Short_Link_Helper $short_link_helper Represents the short link helper.
	 */
	public function __construct( Short_Link_Helper $short_link_helper ) {
		$this->short_link_helper = $short_link_helper;
	}

	/**
	 * Presents the list item for the tools menu.
	 *
	 * @return string The list item HTML.
	 */
	public function present() {
		$output  = \sprintf( '<li><strong>%s</strong><br/>', \esc_html__( 'Optimize SEO Data', 'wordpress-seo' ) );
		$output .= \sprintf(
			'%1$s <a href="%2$s" target="_blank">%3$s</a>',
			\esc_html__( 'You can speed up your site and get insight into your internal linking structure by letting us perform a few optimizations to the way SEO data is stored. If you have a lot of content it might take a while, but trust us, it\'s worth it.', 'wordpress-seo' ),
			\esc_url( $this->short_link_helper->get( 'https://yoa.st/3-z' ) ),
			\esc_html__( 'Learn more about the benefits of optimized SEO data.', 'wordpress-seo' )
		);

		$output .= '<div id="yoast-seo-indexing-action" style="margin: 16px 0;"></div>';
		$output .= '</li>';

		return $output;
	}
}

<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Represents the presenter class for Help link.
 */
class Help_Link_Presenter extends Abstract_Presenter {

	/**
	 * Help link.
	 *
	 * @var string
	 */
	private $link;

	/**
	 * Help link visually hidden text.
	 *
	 * @var string
	 */
	private $link_text;

	/**
	 * Whether the Help link opens in a new browser tab.
	 *
	 * @var bool
	 */
	private $opens_in_new_browser_tab;

	/**
	 * An instance of the WPSEO_Admin_Asset_Manager class.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $asset_manager;

	/**
	 * Help_Link_Presenter constructor.
	 *
	 * @param string $link                     Help link.
	 * @param string $link_text                Help link visually hidden text.
	 * @param bool   $opens_in_new_browser_tab Whether the link opens in a new browser tab. Default true.
	 */
	public function __construct( $link = '', $link_text = '', $opens_in_new_browser_tab = true ) {
		$this->link                     = $link;
		$this->link_text                = $link_text;
		$this->opens_in_new_browser_tab = $opens_in_new_browser_tab;

		if ( ! $this->asset_manager ) {
			$this->asset_manager = new WPSEO_Admin_Asset_Manager();
		}

		$this->asset_manager->enqueue_style( 'admin-global' );
	}

	/**
	 * Presents the Help link.
	 *
	 * @return string The styled Help link.
	 */
	public function present() {
		if ( $this->link === '' || $this->link_text === '' ) {
			return;
		}

		$target_blank_attribute = '';
		$new_tab_message        = '';

		if ( $this->opens_in_new_browser_tab ) {
			$target_blank_attribute = ' target="_blank"';
			/* translators: Hidden accessibility text. */
			$new_tab_message = ' ' . \__( '(Opens in a new browser tab)', 'wordpress-seo' );
		}

		return \sprintf(
			'<a href="%1$s"%2$s class="yoast_help yoast-help-link dashicons"><span class="yoast-help-icon" aria-hidden="true"></span><span class="screen-reader-text">%3$s</span></a>',
			\esc_url( $this->link ),
			$target_blank_attribute,
			\esc_html( $this->link_text . $new_tab_message )
		);
	}
}

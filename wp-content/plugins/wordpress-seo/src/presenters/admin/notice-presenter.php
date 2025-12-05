<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Represents the presenter class for Yoast-styled WordPress admin notices.
 */
class Notice_Presenter extends Abstract_Presenter {

	/**
	 * The title of the admin notice.
	 *
	 * @var string
	 */
	private $title;

	/**
	 * The content of the admin notice.
	 *
	 * @var string
	 */
	private $content;

	/**
	 * The filename of the image for the notice. Should be a file in the 'images' folder.
	 *
	 * @var string
	 */
	private $image_filename;

	/**
	 * HTML string to be displayed after the main content, usually a button.
	 *
	 * @var string
	 */
	private $button;

	/**
	 * Whether the notice should be dismissible.
	 *
	 * @var bool
	 */
	private $is_dismissible;

	/**
	 * The id for the div of the notice.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * An instance of the WPSEO_Admin_Asset_Manager class.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * Notice_Presenter constructor.
	 *
	 * @param string      $title          Title of the admin notice.
	 * @param string      $content        Content of the admin notice.
	 * @param string|null $image_filename Optional. The filename of the image of the admin notice,
	 *                                    should be inside the 'images' folder.
	 * @param string|null $button         Optional. An HTML string to be displayed after the main content,
	 *                                    usually a button.
	 * @param bool        $is_dismissible Optional. Whether the admin notice should be dismissible.
	 * @param string      $id             Optional. The id of the notice.
	 */
	public function __construct( $title, $content, $image_filename = null, $button = null, $is_dismissible = false, $id = '' ) {
		$this->title          = $title;
		$this->content        = $content;
		$this->image_filename = $image_filename;
		$this->button         = $button;
		$this->is_dismissible = $is_dismissible;
		$this->id             = $id;

		if ( ! $this->asset_manager ) {
			$this->asset_manager = new WPSEO_Admin_Asset_Manager();
		}

		$this->asset_manager->enqueue_style( 'notifications' );
	}

	/**
	 * Presents the Notice.
	 *
	 * @return string The styled Notice.
	 */
	public function present() {
		$dismissible = ( $this->is_dismissible ) ? ' is-dismissible' : '';
		$id          = ( $this->id ) ? ' id="' . $this->id . '"' : '';

		// WordPress admin notice.
		$out  = '<div' . $id . ' class="notice notice-yoast yoast' . $dismissible . '">';
		$out .= '<div class="notice-yoast__container">';

		// Header.
		$out .= '<div>';
		$out .= '<div class="notice-yoast__header">';
		$out .= '<span class="yoast-icon"></span>';
		$out .= \sprintf(
			'<h2 class="notice-yoast__header-heading yoast-notice-migrated-header">%s</h2>',
			\esc_html( $this->title )
		);
		$out .= '</div>';
		$out .= '<div class="notice-yoast-content">';
		$out .= '<p>' . $this->content . '</p>';
		if ( $this->button !== null ) {
			$out .= '<p>' . $this->button . '</p>';
		}
		$out .= '</div>';
		$out .= '</div>';

		if ( $this->image_filename !== null ) {
			$out .= '<img src="' . \esc_url( \plugin_dir_url( \WPSEO_FILE ) . 'images/' . $this->image_filename ) . '" alt="" height="60" width="75"/>';
		}

		$out .= '</div>';
		$out .= '</div>';

		return $out;
	}
}

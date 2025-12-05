<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Class WPSEO_Premium_popup.
 */
class WPSEO_Premium_Popup {

	/**
	 * An unique identifier for the popup
	 *
	 * @var string
	 */
	private $identifier = '';

	/**
	 * The heading level of the title of the popup.
	 *
	 * @var string
	 */
	private $heading_level = '';

	/**
	 * The title of the popup.
	 *
	 * @var string
	 */
	private $title = '';

	/**
	 * The content of the popup.
	 *
	 * @var string
	 */
	private $content = '';

	/**
	 * The URL for where the button should link to.
	 *
	 * @var string
	 */
	private $url = '';

	/**
	 * Wpseo_Premium_Popup constructor.
	 *
	 * @param string $identifier    An unique identifier for the popup.
	 * @param string $heading_level The heading level for the title of the popup.
	 * @param string $title         The title of the popup.
	 * @param string $content       The content of the popup.
	 * @param string $url           The URL for where the button should link to.
	 */
	public function __construct( $identifier, $heading_level, $title, $content, $url ) {
		$this->identifier    = $identifier;
		$this->heading_level = $heading_level;
		$this->title         = $title;
		$this->content       = $content;
		$this->url           = $url;
	}

	/**
	 * Returns the premium popup as an HTML string.
	 *
	 * @param bool $popup Show this message as a popup show it straight away.
	 *
	 * @return string
	 */
	public function get_premium_message( $popup = true ) {
		// Don't show in Premium.
		if ( defined( 'WPSEO_PREMIUM_FILE' ) ) {
			return '';
		}

		$assets_uri = trailingslashit( plugin_dir_url( WPSEO_FILE ) );

		/* translators: %s expands to Yoast SEO Premium */
		$cta_text = esc_html( sprintf( __( 'Get %s', 'wordpress-seo' ), 'Yoast SEO Premium' ) );
		/* translators: Hidden accessibility text. */
		$new_tab_message = '<span class="screen-reader-text">' . esc_html__( '(Opens in a new browser tab)', 'wordpress-seo' ) . '</span>';
		$caret_icon      = '<span aria-hidden="true" class="yoast-button-upsell__caret"></span>';
		$classes         = '';
		if ( $popup ) {
			$classes = ' hidden';
		}
		$micro_copy = __( '1 year free support and updates included!', 'wordpress-seo' );

		$popup = <<<EO_POPUP
<div id="wpseo-{$this->identifier}-popup" class="wpseo-premium-popup wp-clearfix$classes">
	<img class="alignright wpseo-premium-popup-icon" src="{$assets_uri}packages/js/images/Yoast_SEO_Icon.svg" width="150" height="150" alt="Yoast SEO" />
	<{$this->heading_level} id="wpseo-contact-support-popup-title" class="wpseo-premium-popup-title">{$this->title}</{$this->heading_level}>
	{$this->content}
	<a id="wpseo-{$this->identifier}-popup-button" class="yoast-button-upsell" href="{$this->url}" target="_blank">
		{$cta_text} {$new_tab_message} {$caret_icon}
	</a><br/>
	<small>{$micro_copy}</small>
</div>
EO_POPUP;

		return $popup;
	}
}

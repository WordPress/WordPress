<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

use Yoast\WP\SEO\Conditionals\WooCommerce_Conditional;
use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;

/**
 * Class WPSEO_Premium_Upsell_Admin_Block
 */
class WPSEO_Premium_Upsell_Admin_Block {

	/**
	 * Hook to display the block on.
	 *
	 * @var string
	 */
	protected $hook;

	/**
	 * Identifier to use in the dismissal functionality.
	 *
	 * @var string
	 */
	protected $identifier = 'premium_upsell';

	/**
	 * Registers which hook the block will be displayed on.
	 *
	 * @param string $hook Hook to display the block on.
	 */
	public function __construct( $hook ) {
		$this->hook = $hook;
	}

	/**
	 * Registers WordPress hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( $this->hook, [ $this, 'render' ] );
	}

	/**
	 * Renders the upsell block.
	 *
	 * @return void
	 */
	public function render() {

		$is_woocommerce_active = ( new WooCommerce_Conditional() )->is_met();
		$url                   = ( $is_woocommerce_active ) ? WPSEO_Shortlinker::get( 'https://yoa.st/admin-footer-upsell-woocommerce' ) : WPSEO_Shortlinker::get( 'https://yoa.st/17h' );

		[ $header_text, $header_icon ] = $this->get_header( $is_woocommerce_active );

		$arguments = $this->get_arguments( $is_woocommerce_active );

		$now_including = [ 'Local SEO', 'News SEO', 'Video SEO', __( 'Google Docs add-on (1 seat)', 'wordpress-seo' ) ];
		if ( $is_woocommerce_active ) {
			array_unshift( $now_including, 'Yoast SEO Premium' );
		}

		$header_class   = ( $is_woocommerce_active ) ? 'woo-header' : '';
		$arguments_html = implode( '', array_map( [ $this, 'get_argument_html' ], $arguments ) );
		$badge_class    = ( $is_woocommerce_active ) ? 'woo-badge' : '';

		$class = $this->get_html_class();

		/* translators: %s expands to Yoast SEO Premium */
		$button_text = $this->get_button_text( $is_woocommerce_active );
		/* translators: Hidden accessibility text. */
		$button_text .= '<span class="screen-reader-text">' . esc_html__( '(Opens in a new browser tab)', 'wordpress-seo' ) . '</span>'
			. '<span aria-hidden="true" class="yoast-button-upsell__caret"></span>';

		$upgrade_button = sprintf(
			'<a id="%1$s" class="yoast-button-upsell" data-action="load-nfd-ctb" data-ctb-id="f6a84663-465f-4cb5-8ba5-f7a6d72224b2" href="%2$s" target="_blank">%3$s</a>',
			esc_attr( 'wpseo-' . $this->identifier . '-popup-button' ),
			esc_url( $url ),
			$button_text
		);

		echo '<div class="' . esc_attr( $class ) . '">';

		if ( YoastSEO()->classes->get( Promotion_Manager::class )->is( 'black-friday-promotion' ) ) {
			$bf_label   = esc_html__( 'BLACK FRIDAY', 'wordpress-seo' );
			$sale_label = esc_html__( '30% OFF', 'wordpress-seo' );
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped above.
			echo "<div class='black-friday-container'><span>$sale_label</span> <span style='margin-left: auto;'>$bf_label</span> </div>";
		}

		echo '<div class="' . esc_attr( $class . '--container' ) . '">';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Correctly escaped in get_header() method.
		echo '<h2 class="' . esc_attr( $class . '--header' ) . ' ' . esc_attr( $header_class ) . ' ">' . $header_text . $header_icon . '</h2>';

		echo '<div class="' . esc_attr( $class . '--subheader' ) . '">';
		echo '<span style="margin-right: 8px">' . esc_html__( 'Now includes:', 'wordpress-seo' ) . '</span>';
		echo '<div style="display: inline-block;">';
		foreach ( $now_including as $value ) {
			echo '<span class="yoast-badge ' . esc_attr( $class . '--badge' ) . ' ' . esc_attr( $badge_class ) . '">' . esc_html( $value ) . '</span>';
		}
		echo '</div>';
		echo '</div>';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Correctly escaped in $this->get_argument_html() method.
		echo '<ul class="' . esc_attr( $class . '--motivation' ) . '">' . $arguments_html . '</ul>';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Correctly escaped in $upgrade_button and $button_text above.
		echo '<p style="max-width: inherit; margin-top: 24px; margin-bottom: 0;">' . $upgrade_button . '</p>';
		echo '</div>';

		echo '</div>';
	}

	/**
	 * Formats the argument to a HTML list item.
	 *
	 * @param string $argument The argument to format.
	 *
	 * @return string Formatted argument in HTML.
	 */
	protected function get_argument_html( $argument ) {
		$assets_uri = trailingslashit( plugin_dir_url( WPSEO_FILE ) );
		$class      = $this->get_html_class();

		return sprintf(
			'<li style="line-height: 19.5px"><img src="%1$s" alt="" width="19.5" height="19.5"><div class="%2$s">%3$s</div></li>',
			esc_url( $assets_uri . 'packages/js/images/icon-check-circle-green.svg' ),
			esc_attr( $class . '--argument' ),
			$argument
		);
	}

	/**
	 * Returns the HTML base class to use.
	 *
	 * @return string The HTML base class.
	 */
	protected function get_html_class() {
		return 'yoast_' . $this->identifier;
	}

	/**
	 * Returns the arguments based on whether WooCommerce is active.
	 *
	 * @param bool $is_woocommerce_active Whether WooCommerce is active.
	 *
	 * @return array<string> The arguments list.
	 */
	private function get_arguments( bool $is_woocommerce_active ) {
		$arguments = [
			esc_html__( 'Generate SEO optimized metadata in seconds with AI', 'wordpress-seo' ),
			esc_html__( 'Make your articles visible, be seen in Google News', 'wordpress-seo' ),
			esc_html__( 'Built to get found by search, AI, and real users', 'wordpress-seo' ),
			esc_html__( 'Easy Local SEO. Show up in Google Maps results', 'wordpress-seo' ),
			esc_html__( 'Internal links and redirect management, easy', 'wordpress-seo' ),
			esc_html__( 'Access to friendly help when you need it, day or night', 'wordpress-seo' ),
		];

		if ( $is_woocommerce_active ) {
			$arguments[1] = esc_html__( 'Boost visibility for your products, from 10 or 10,000+', 'wordpress-seo' );
		}

		return $arguments;
	}

	/**
	 * Returns the header text and icon based on whether WooCommerce is active.
	 *
	 * @param bool $is_woocommerce_active Whether WooCommerce is active.
	 *
	 * @return array<string, string> The header text and icon.
	 */
	private function get_header( bool $is_woocommerce_active ) {
		$assets_uri = trailingslashit( plugin_dir_url( WPSEO_FILE ) );
		if ( $is_woocommerce_active ) {
			$header_text = sprintf(
			/* translators: %s expands to Yoast WooCommerce SEO */
				esc_html__( 'Upgrade to %s', 'wordpress-seo' ),
				'Yoast WooCommerce SEO'
			);
			$header_icon = sprintf(
				'<img src="%s" alt="" width="14" height="14" style="margin-inline-start: 8px;">',
				esc_url( $assets_uri . 'packages/js/images/icon-trolley.svg' ),
			);
		}
		else {
			$header_text = sprintf(
			/* translators: %s expands to Yoast SEO Premium*/
				esc_html__( 'Upgrade to %s', 'wordpress-seo' ),
				'Yoast SEO Premium'
			);

			$header_icon = sprintf(
				'<img src="%s" alt="" width="14" height="14" style="margin-inline-start: 8px;">',
				esc_url( $assets_uri . 'packages/js/images/icon-crown.svg' )
			);
		}
		return [ $header_text, $header_icon ];
	}

	/**
	 * Returns the button text based on whether WooCommerce is active.
	 *
	 * @param bool $is_woocommerce_active Whether WooCommerce is active.
	 *
	 * @return string The button text.
	 */
	private function get_button_text( bool $is_woocommerce_active ): string {
		if ( YoastSEO()->classes->get( Promotion_Manager::class )->is( 'black-friday-promotion' ) ) {
			return esc_html__( 'Get 30% off now!', 'wordpress-seo' );
		}
		else {
			// phpcs:disable Squiz.ControlStructures.InlineIfDeclaration.NotSingleLine -- needed to add translators comments.
			return $is_woocommerce_active
				/* translators: %s expands to Yoast WooCommerce SEO */
				? sprintf( esc_html__( 'Explore %s now!', 'wordpress-seo' ), 'Yoast WooCommerce SEO' )
				/* translators: %s expands to Yoast SEO Premium */
				: sprintf( esc_html__( 'Explore %s now!', 'wordpress-seo' ), 'Yoast SEO Premium' );
		}
	}
}

<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Views
 *
 * @uses Yoast_Form $yform Form object.
 */

use Yoast\WP\SEO\Presenters\Admin\Beta_Badge_Presenter;
use Yoast\WP\SEO\Presenters\Admin\Premium_Badge_Presenter;

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

$feature_toggles = Yoast_Feature_Toggles::instance()->get_all();

?>
<h2><?php esc_html_e( 'Features', 'wordpress-seo' ); ?></h2>
<div class="yoast-measure">
	<?php
	printf(
		/* translators: %s expands to Yoast SEO */
		esc_html__( 'This tab allows you to selectively disable %s features for all sites in the network. By default all features are enabled, which allows site admins to choose for themselves if they want to toggle a feature on or off for their site. When you disable a feature here, site admins will not be able to use that feature at all.', 'wordpress-seo' ),
		'Yoast SEO'
	);

	foreach ( $feature_toggles as $feature ) {
		$is_premium      = YoastSEO()->helpers->product->is_premium();
		$premium_version = YoastSEO()->helpers->product->get_premium_version();

		if ( $feature->premium && $feature->premium_version ) {
			$not_supported_in_current_premium_version = $is_premium && version_compare( $premium_version, $feature->premium_version, '<' );

			if ( $not_supported_in_current_premium_version ) {
				continue;
			}
		}

		$help_text = esc_html( $feature->label );
		if ( ! empty( $feature->extra ) ) {
			$help_text .= ' ' . $feature->extra;
		}
		if ( ! empty( $feature->read_more_label ) ) {
			$url = $feature->read_more_url;
			if ( ! empty( $feature->premium ) && $feature->premium === true ) {
				$url = $feature->premium_url;
			}
			$help_text .= sprintf(
				'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
				esc_url( WPSEO_Shortlinker::get( $url ) ),
				esc_html( $feature->read_more_label )
			);
		}

		$feature_help = new WPSEO_Admin_Help_Panel(
			WPSEO_Option::ALLOW_KEY_PREFIX . $feature->setting,
			/* translators: Hidden accessibility text; %s expands to a feature's name. */
			sprintf( esc_html__( 'Help on: %s', 'wordpress-seo' ), esc_html( $feature->name ) ),
			$help_text
		);

		$name = $feature->name;
		if ( ! empty( $feature->premium ) && $feature->premium === true ) {
			$name .= ' ' . new Premium_Badge_Presenter( $feature->name );
		}

		if ( ! empty( $feature->in_beta ) && $feature->in_beta === true ) {
			$name .= ' ' . new Beta_Badge_Presenter( $feature->name );
		}

		$disabled            = false;
		$show_premium_upsell = false;
		$premium_upsell_url  = '';
		$note_when_disabled  = '';

		if ( $feature->premium === true && YoastSEO()->helpers->product->is_premium() === false ) {
			$disabled            = true;
			$show_premium_upsell = true;
			$premium_upsell_url  = WPSEO_Shortlinker::get( $feature->premium_upsell_url );
		}

		$preserve_disabled_value = false;
		if ( $disabled ) {
			$preserve_disabled_value = true;
		}

		$yform->toggle_switch(
			WPSEO_Option::ALLOW_KEY_PREFIX . $feature->setting,
			[
				'on'  => __( 'Allow Control', 'wordpress-seo' ),
				'off' => __( 'Disable', 'wordpress-seo' ),
			],
			$name,
			$feature_help->get_button_html() . $feature_help->get_panel_html(),
			[
				'disabled'                => $disabled,
				'preserve_disabled_value' => $preserve_disabled_value,
				'show_premium_upsell'     => $show_premium_upsell,
				'premium_upsell_url'      => $premium_upsell_url,
				'note_when_disabled'      => $note_when_disabled,
			]
		);
	}
	?>
</div>
<?php
/*
 * Required to prevent our settings framework from saving the default because the field
 * isn't explicitly set when saving the Dashboard page.
 */
$yform->hidden( 'show_onboarding_notice', 'wpseo_show_onboarding_notice' );

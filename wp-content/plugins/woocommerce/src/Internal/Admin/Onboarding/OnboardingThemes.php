<?php
/**
 * WooCommerce Onboarding Themes
 */

namespace Automattic\WooCommerce\Internal\Admin\Onboarding;

use Automattic\WooCommerce\Admin\Loader;
use Automattic\WooCommerce\Admin\PageController;
use Automattic\WooCommerce\Admin\WCAdminHelper;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Init as OnboardingTasks;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;
use Automattic\WooCommerce\Admin\Schedulers\MailchimpScheduler;

/**
 * Logic around onboarding themes.
 */
class OnboardingThemes {
	/**
	 * Name of themes transient.
	 *
	 * @var string
	 */
	const THEMES_TRANSIENT = 'wc_onboarding_themes';

	/**
	 * Init.
	 */
	public static function init() {
		add_action( 'woocommerce_theme_installed', array( __CLASS__, 'delete_themes_transient' ) );
		add_action( 'after_switch_theme', array( __CLASS__, 'delete_themes_transient' ) );
		add_filter( 'woocommerce_rest_prepare_themes', array( __CLASS__, 'add_uploaded_theme_data' ) );
		add_filter( 'woocommerce_admin_onboarding_preloaded_data', array( __CLASS__, 'preload_data' ) );
	}

	/**
	 * Get puchasable theme by slug.
	 *
	 * @param string $price_string string of price.
	 * @return float|null
	 */
	private static function get_price_from_string( $price_string ) {
		$price_match = null;
		// Parse price from string as it includes the currency symbol.
		preg_match( '/\\d+\.\d{2}\s*/', $price_string, $price_match );
		if ( count( $price_match ) > 0 ) {
			return (float) $price_match[0];
		}
		return null;
	}

	/**
	 * Get puchasable theme by slug.
	 *
	 * @param string $slug from theme.
	 * @return array|null
	 */
	public static function get_paid_theme_by_slug( $slug ) {
		$themes    = self::get_themes();
		$theme_key = array_search( $slug, array_column( $themes, 'slug' ), true );
		$theme     = false !== $theme_key ? $themes[ $theme_key ] : null;
		if ( $theme && isset( $theme['id'] ) && isset( $theme['price'] ) ) {
			$price = self::get_price_from_string( $theme['price'] );
			if ( $price && $price > 0 ) {
				return $themes[ $theme_key ];
			}
		}
		return null;
	}

	/**
	 * Sort themes returned from WooCommerce.com
	 *
	 * @param  array $themes Array of themes from WooCommerce.com.
	 * @return array
	 */
	public static function sort_woocommerce_themes( $themes ) {
		usort(
			$themes,
			function ( $product_1, $product_2 ) {
				if ( ! property_exists( $product_1, 'id' ) || ! property_exists( $product_1, 'slug' ) ) {
					return 1;
				}
				if ( ! property_exists( $product_2, 'id' ) || ! property_exists( $product_2, 'slug' ) ) {
					return 1;
				}
				if ( in_array( 'Storefront', array( $product_1->slug, $product_2->slug ), true ) ) {
					return 'Storefront' === $product_1->slug ? -1 : 1;
				}
				return $product_1->id < $product_2->id ? 1 : -1;
			}
		);
		return $themes;
	}

	/**
	 * Get a list of themes for the onboarding wizard.
	 *
	 * @return array
	 */
	public static function get_themes() {
		$themes = get_transient( self::THEMES_TRANSIENT );
		if ( false === $themes ) {
			$theme_data = wp_remote_get(
				'https://woocommerce.com/wp-json/wccom-extensions/1.0/search?category=themes',
				array(
					'user-agent' => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
				)
			);
			$themes     = array();

			if ( ! is_wp_error( $theme_data ) ) {
				$theme_data    = json_decode( $theme_data['body'] );
				$woo_themes    = property_exists( $theme_data, 'products' ) ? $theme_data->products : array();
				$sorted_themes = self::sort_woocommerce_themes( $woo_themes );

				foreach ( $sorted_themes as $theme ) {
					$slug                                       = sanitize_title_with_dashes( $theme->slug );
					$themes[ $slug ]                            = (array) $theme;
					$themes[ $slug ]['is_installed']            = false;
					$themes[ $slug ]['has_woocommerce_support'] = true;
					$themes[ $slug ]['slug']                    = $slug;
				}
			}

			$installed_themes = wp_get_themes();
			foreach ( $installed_themes as $slug => $theme ) {
				$theme_data = self::get_theme_data( $theme );
				if ( isset( $themes[ $slug ] ) ) {
					$themes[ $slug ]['is_installed'] = true;
					$themes[ $slug ]['image']        = $theme_data['image'];
				} else {
					$themes[ $slug ] = $theme_data;
				}
			}

			$active_theme = get_option( 'stylesheet' );

			/**
			 * The active theme may no be set if active_theme is not compatible with current version of WordPress.
			 * In this case, we should not add active theme to onboarding themes.
			 */
			if ( isset( $themes[ $active_theme ] ) ) {
				// Add the WooCommerce support tag for default themes that don't explicitly declare support.
				if ( function_exists( 'wc_is_wp_default_theme_active' ) && wc_is_wp_default_theme_active() ) {
					$themes[ $active_theme ]['has_woocommerce_support'] = true;
				}

				$themes = array( $active_theme => $themes[ $active_theme ] ) + $themes;
			}

			set_transient( self::THEMES_TRANSIENT, $themes, DAY_IN_SECONDS );
		}

		$themes = apply_filters( 'woocommerce_admin_onboarding_themes', $themes );
		return array_values( $themes );
	}

	/**
	 * Get theme data used in onboarding theme browser.
	 *
	 * @param WP_Theme $theme Theme to gather data from.
	 * @return array
	 */
	public static function get_theme_data( $theme ) {
		return array(
			'slug'                    => sanitize_text_field( $theme->stylesheet ),
			'title'                   => $theme->get( 'Name' ),
			'price'                   => '0.00',
			'is_installed'            => true,
			'image'                   => $theme->get_screenshot(),
			'has_woocommerce_support' => true,
		);
	}

	/**
	 * Add theme data to response from themes controller.
	 *
	 * @param WP_REST_Response $response Rest response.
	 * @return WP_REST_Response
	 */
	public static function add_uploaded_theme_data( $response ) {
		if ( ! isset( $response->data['theme'] ) ) {
			return $response;
		}

		$theme                        = wp_get_theme( $response->data['theme'] );
		$response->data['theme_data'] = self::get_theme_data( $theme );

		return $response;
	}

	/**
	 * Delete the stored themes transient.
	 */
	public static function delete_themes_transient() {
		delete_transient( self::THEMES_TRANSIENT );
	}

	/**
	 * Add preloaded data to onboarding.
	 *
	 * @param array $settings Component settings.
	 *
	 * @return array
	 */
	public static function preload_data( $settings ) {
		$settings['onboarding']['activeTheme'] = get_option( 'stylesheet' );
		$settings['onboarding']['themes']      = self::get_themes();
		return $settings;
	}

	/**
	 * Gets an array of themes that can be installed & activated via the onboarding wizard.
	 *
	 * @return array
	 */
	public static function get_allowed_themes() {
		$allowed_themes = array();
		$themes         = self::get_themes();

		foreach ( $themes as $theme ) {
			$price = preg_replace( '/&#?[a-z0-9]+;/i', '', $theme['price'] );

			if ( $theme['is_installed'] || '0.00' === $price ) {
				$allowed_themes[] = $theme['slug'];
			}
		}

		return apply_filters( 'woocommerce_admin_onboarding_themes_whitelist', $allowed_themes );
	}

}

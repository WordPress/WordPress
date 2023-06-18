<?php
/**
 * Addons Page
 *
 * @package  WooCommerce\Admin
 * @version  2.5.0
 */

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Admin\RemoteInboxNotifications as PromotionRuleEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Admin_Addons Class.
 */
class WC_Admin_Addons {

	/**
	 * Get featured for the addons screen
	 *
	 * @deprecated 5.9.0 No longer used in In-App Marketplace
	 *
	 * @return array of objects
	 */
	public static function get_featured() {
		$locale   = get_user_locale();
		$featured = self::get_locale_data_from_transient( 'wc_addons_featured_2', $locale );
		if ( false === $featured ) {
			$headers = array();
			$auth    = WC_Helper_Options::get( 'auth' );

			if ( ! empty( $auth['access_token'] ) ) {
				$headers['Authorization'] = 'Bearer ' . $auth['access_token'];
			}

			$raw_featured = wp_safe_remote_get(
				'https://woocommerce.com/wp-json/wccom-extensions/1.0/featured',
				array(
					'headers'    => $headers,
					'user-agent' => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
				)
			);

			if ( ! is_wp_error( $raw_featured ) ) {
				$featured = json_decode( wp_remote_retrieve_body( $raw_featured ) );
				if ( $featured ) {
					self::set_locale_data_in_transient( 'wc_addons_featured_2', $featured, $locale, DAY_IN_SECONDS );
				}
			}
		}

		if ( is_object( $featured ) ) {
			self::output_featured_sections( $featured->sections );
			return $featured;
		}
	}

	/**
	 * Render featured products and banners using WCCOM's the Featured 2.0 Endpoint
	 *
	 * @return void
	 */
	public static function render_featured() {
		$locale   = get_user_locale();
		$featured = self::get_locale_data_from_transient( 'wc_addons_featured', $locale );
		if ( false === $featured ) {
			$headers = array();
			$auth    = WC_Helper_Options::get( 'auth' );

			if ( ! empty( $auth['access_token'] ) ) {
				$headers['Authorization'] = 'Bearer ' . $auth['access_token'];
			}

			$parameter_string = '?' . http_build_query( array( 'locale' => get_user_locale() ) );
			$country          = WC()->countries->get_base_country();
			if ( ! empty( $country ) ) {
				$parameter_string = $parameter_string . '&' . http_build_query( array( 'country' => $country ) );
			}

			// Important: WCCOM Extensions API v2.0 is used.
			$raw_featured = wp_safe_remote_get(
				'https://woocommerce.com/wp-json/wccom-extensions/2.0/featured' . $parameter_string,
				array(
					'headers'    => $headers,
					'user-agent' => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
				)
			);

			if ( is_wp_error( $raw_featured ) ) {
				do_action( 'woocommerce_page_wc-addons_connection_error', $raw_featured->get_error_message() );

				$message = self::is_ssl_error( $raw_featured->get_error_message() )
					? __( 'We encountered an SSL error. Please ensure your site supports TLS version 1.2 or above.', 'woocommerce' )
					: $raw_featured->get_error_message();

				self::output_empty( $message );

				return;
			}

			$response_code = (int) wp_remote_retrieve_response_code( $raw_featured );
			if ( 200 !== $response_code ) {
				do_action( 'woocommerce_page_wc-addons_connection_error', $response_code );

				/* translators: %d: HTTP error code. */
				$message = sprintf(
					esc_html(
						/* translators: Error code  */
						__(
							'Our request to the featured API got error code %d.',
							'woocommerce'
						)
					),
					$response_code
				);

				self::output_empty( $message );

				return;
			}

			$featured = json_decode( wp_remote_retrieve_body( $raw_featured ) );
			if ( empty( $featured ) || ! is_array( $featured ) ) {
				do_action( 'woocommerce_page_wc-addons_connection_error', 'Empty or malformed response' );
				$message = __( 'Our request to the featured API got a malformed response.', 'woocommerce' );
				self::output_empty( $message );

				return;
			}

			if ( $featured ) {
				self::set_locale_data_in_transient( 'wc_addons_featured', $featured, $locale, DAY_IN_SECONDS );
			}
		}

		self::output_featured( $featured );
	}

	/**
	 * Check if the error is due to an SSL error
	 *
	 * @param string $error_message Error message.
	 *
	 * @return bool True if SSL error, false otherwise
	 */
	public static function is_ssl_error( $error_message ) {
		return false !== stripos( $error_message, 'cURL error 35' );
	}

	/**
	 * Build url parameter string
	 *
	 * @param  string $category Addon (sub) category.
	 * @param  string $term     Search terms.
	 * @param  string $country  Store country.
	 *
	 * @return string url parameter string
	 */
	public static function build_parameter_string( $category, $term, $country ) {

		$parameters = array(
			'category' => $category,
			'term'     => $term,
			'country'  => $country,
			'locale'   => get_user_locale(),
		);

		return '?' . http_build_query( $parameters );
	}

	/**
	 * Call API to get extensions
	 *
	 * @param  string $category Addon (sub) category.
	 * @param  string $term     Search terms.
	 * @param  string $country  Store country.
	 *
	 * @return object|WP_Error  Object with products and promotions properties, or WP_Error
	 */
	public static function get_extension_data( $category, $term, $country ) {
		$parameters = self::build_parameter_string( $category, $term, $country );

		$headers = array();
		$auth    = WC_Helper_Options::get( 'auth' );

		if ( ! empty( $auth['access_token'] ) ) {
			$headers['Authorization'] = 'Bearer ' . $auth['access_token'];
		}

		$raw_extensions = wp_safe_remote_get(
			'https://woocommerce.com/wp-json/wccom-extensions/1.0/search' . $parameters,
			array(
				'headers'    => $headers,
				'user-agent' => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
			)
		);

		if ( is_wp_error( $raw_extensions ) ) {
			do_action( 'woocommerce_page_wc-addons_connection_error', $raw_extensions->get_error_message() );
			return $raw_extensions;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $raw_extensions );
		if ( 200 !== $response_code ) {
			do_action( 'woocommerce_page_wc-addons_connection_error', $response_code );
			return new WP_Error(
				'error',
				sprintf(
					esc_html(
						/* translators: Error code  */
						__( 'Our request to the search API got response code %s.', 'woocommerce' )
					),
					$response_code
				)
			);
		}

		$addons = json_decode( wp_remote_retrieve_body( $raw_extensions ) );

		if ( ! is_object( $addons ) || ! isset( $addons->products ) ) {
			do_action( 'woocommerce_page_wc-addons_connection_error', 'Empty or malformed response' );
			return new WP_Error( 'error', __( 'Our request to the search API got a malformed response.', 'woocommerce' ) );
		}
		return $addons;
	}

	/**
	 * Get sections for the addons screen
	 *
	 * @return array of objects
	 */
	public static function get_sections() {
		$locale         = get_user_locale();
		$addon_sections = self::get_locale_data_from_transient( 'wc_addons_sections', $locale );
		if ( false === ( $addon_sections ) ) {
			$parameter_string = '?' . http_build_query( array( 'locale' => get_user_locale() ) );
			$raw_sections     = wp_safe_remote_get(
				'https://woocommerce.com/wp-json/wccom-extensions/1.0/categories' . $parameter_string,
				array(
					'user-agent' => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
				)
			);
			if ( ! is_wp_error( $raw_sections ) ) {
				$addon_sections = json_decode( wp_remote_retrieve_body( $raw_sections ) );
				if ( $addon_sections ) {
					self::set_locale_data_in_transient( 'wc_addons_sections', $addon_sections, $locale, WEEK_IN_SECONDS );
				}
			}
		}
		return apply_filters( 'woocommerce_addons_sections', $addon_sections );
	}

	/**
	 * Get section for the addons screen.
	 *
	 * @param  string $section_id Required section ID.
	 *
	 * @return object|bool
	 */
	public static function get_section( $section_id ) {
		$sections = self::get_sections();
		if ( isset( $sections[ $section_id ] ) ) {
			return $sections[ $section_id ];
		}
		return false;
	}


	/**
	 * Get section content for the addons screen.
	 *
	 * @deprecated 5.9.0 No longer used in In-App Marketplace
	 *
	 * @param  string $section_id Required section ID.
	 *
	 * @return array
	 */
	public static function get_section_data( $section_id ) {
		$section      = self::get_section( $section_id );
		$section_data = '';

		if ( ! empty( $section->endpoint ) ) {
			$section_data = get_transient( 'wc_addons_section_' . $section_id );
			if ( false === $section_data ) {
				$raw_section = wp_safe_remote_get(
					esc_url_raw( $section->endpoint ),
					array(
						'user-agent' => 'WooCommerce/' . WC()->version . '; ' . get_bloginfo( 'url' ),
					)
				);

				if ( ! is_wp_error( $raw_section ) ) {
					$section_data = json_decode( wp_remote_retrieve_body( $raw_section ) );

					if ( ! empty( $section_data->products ) ) {
						set_transient( 'wc_addons_section_' . $section_id, $section_data, WEEK_IN_SECONDS );
					}
				}
			}
		}

		return apply_filters( 'woocommerce_addons_section_data', $section_data->products, $section_id );
	}

	/**
	 * Handles the outputting of a contextually aware Storefront link (points to child themes if Storefront is already active).
	 *
	 * @deprecated 5.9.0 No longer used in In-App Marketplace
	 */
	public static function output_storefront_button() {
		$template   = get_option( 'template' );
		$stylesheet = get_option( 'stylesheet' );

		if ( 'storefront' === $template ) {
			if ( 'storefront' === $stylesheet ) {
				$url         = 'https://woocommerce.com/product-category/themes/storefront-child-theme-themes/';
				$text        = __( 'Need a fresh look? Try Storefront child themes', 'woocommerce' );
				$utm_content = 'nostorefrontchildtheme';
			} else {
				$url         = 'https://woocommerce.com/product-category/themes/storefront-child-theme-themes/';
				$text        = __( 'View more Storefront child themes', 'woocommerce' );
				$utm_content = 'hasstorefrontchildtheme';
			}
		} else {
			$url         = 'https://woocommerce.com/storefront/';
			$text        = __( 'Need a theme? Try Storefront', 'woocommerce' );
			$utm_content = 'nostorefront';
		}

		$url = add_query_arg(
			array(
				'utm_source'   => 'addons',
				'utm_medium'   => 'product',
				'utm_campaign' => 'woocommerceplugin',
				'utm_content'  => $utm_content,
			),
			$url
		);

		echo '<a href="' . esc_url( $url ) . '" class="add-new-h2">' . esc_html( $text ) . '</a>' . "\n";
	}

	/**
	 * Handles the outputting of a banner block.
	 *
	 * @deprecated 5.9.0 No longer used in In-App Marketplace
	 *
	 * @param object $block Banner data.
	 */
	public static function output_banner_block( $block ) {
		?>
		<div class="addons-banner-block">
			<h1><?php echo esc_html( $block->title ); ?></h1>
			<p><?php echo esc_html( $block->description ); ?></p>
			<div class="addons-banner-block-items">
				<?php foreach ( $block->items as $item ) : ?>
					<?php if ( self::show_extension( $item ) ) : ?>
						<div class="addons-banner-block-item">
							<div class="addons-banner-block-item-icon">
								<img class="addons-img" src="<?php echo esc_url( $item->image ); ?>" />
							</div>
							<div class="addons-banner-block-item-content">
								<h3><?php echo esc_html( $item->title ); ?></h3>
								<p><?php echo esc_html( $item->description ); ?></p>
								<?php
									self::output_button(
										$item->href,
										$item->button,
										'addons-button-solid',
										$item->plugin
									);
								?>
							</div>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Handles the outputting of a column.
	 *
	 * @deprecated 5.9.0 No longer used in In-App Marketplace
	 *
	 * @param object $block Column data.
	 */
	public static function output_column( $block ) {
		if ( isset( $block->container ) && 'column_container_start' === $block->container ) {
			?>
			<div class="addons-column-section">
			<?php
		}
		if ( 'column_start' === $block->module ) {
			?>
			<div class="addons-column">
			<?php
		} else {
			?>
			</div>
			<?php
		}
		if ( isset( $block->container ) && 'column_container_end' === $block->container ) {
			?>
			</div>
			<?php
		}
	}

	/**
	 * Handles the outputting of a column block.
	 *
	 * @deprecated 5.9.0 No longer used in In-App Marketplace
	 *
	 * @param object $block Column block data.
	 */
	public static function output_column_block( $block ) {
		?>
		<div class="addons-column-block">
			<h1><?php echo esc_html( $block->title ); ?></h1>
			<p><?php echo esc_html( $block->description ); ?></p>
			<?php foreach ( $block->items as $item ) : ?>
				<?php if ( self::show_extension( $item ) ) : ?>
					<div class="addons-column-block-item">
						<div class="addons-column-block-item-icon">
							<img class="addons-img" src="<?php echo esc_url( $item->image ); ?>" />
						</div>
						<div class="addons-column-block-item-content">
							<h2><?php echo esc_html( $item->title ); ?></h2>
							<?php
								self::output_button(
									$item->href,
									$item->button,
									'addons-button-solid',
									$item->plugin
								);
							?>
							<p><?php echo esc_html( $item->description ); ?></p>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<?php
	}

	/**
	 * Handles the outputting of a small light block.
	 *
	 * @deprecated 5.9.0 No longer used in In-App Marketplace
	 *
	 * @param object $block Block data.
	 */
	public static function output_small_light_block( $block ) {
		?>
		<div class="addons-small-light-block">
			<img class="addons-img" src="<?php echo esc_url( $block->image ); ?>" />
			<div class="addons-small-light-block-content">
				<h1><?php echo esc_html( $block->title ); ?></h1>
				<p><?php echo esc_html( $block->description ); ?></p>
				<div class="addons-small-light-block-buttons">
					<?php foreach ( $block->buttons as $button ) : ?>
						<?php
							self::output_button(
								$button->href,
								$button->text,
								'addons-button-solid'
							);
						?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handles the outputting of a small dark block.
	 *
	 * @deprecated 5.9.0 No longer used in In-App Marketplace
	 *
	 * @param object $block Block data.
	 */
	public static function output_small_dark_block( $block ) {
		?>
		<div class="addons-small-dark-block">
			<h1><?php echo esc_html( $block->title ); ?></h1>
			<p><?php echo esc_html( $block->description ); ?></p>
			<div class="addons-small-dark-items">
				<?php foreach ( $block->items as $item ) : ?>
					<div class="addons-small-dark-item">
						<?php if ( ! empty( $item->image ) ) : ?>
							<div class="addons-small-dark-item-icon">
								<img class="addons-img" src="<?php echo esc_url( $item->image ); ?>" />
							</div>
						<?php endif; ?>
						<?php
							self::output_button(
								$item->href,
								$item->button,
								'addons-button-outline-white'
							);
						?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Handles the outputting of the WooCommerce Services banner block.
	 *
	 * @deprecated 5.9.0 No longer used in In-App Marketplace
	 *
	 * @param object $block Block data.
	 */
	public static function output_wcs_banner_block( $block = array() ) {
		$is_active = is_plugin_active( 'woocommerce-services/woocommerce-services.php' );
		$location  = wc_get_base_location();

		if (
			! in_array( $location['country'], array( 'US' ), true ) ||
			$is_active ||
			! current_user_can( 'install_plugins' ) ||
			! current_user_can( 'activate_plugins' )
		) {
			return;
		}

		$button_url = wp_nonce_url(
			add_query_arg(
				array(
					'install-addon' => 'woocommerce-services',
				)
			),
			'install-addon_woocommerce-services'
		);

		$defaults = array(
			'image'       => WC()->plugin_url() . '/assets/images/wcs-extensions-banner-3x.jpg',
			'image_alt'   => __( 'WooCommerce Shipping', 'woocommerce' ),
			'title'       => __( 'Save time and money with WooCommerce Shipping', 'woocommerce' ),
			'description' => __( 'Print discounted USPS and DHL labels straight from your WooCommerce dashboard and save on shipping.', 'woocommerce' ),
			'button'      => __( 'Free - Install now', 'woocommerce' ),
			'href'        => $button_url,
			'logos'       => array(),
		);

		switch ( $location['country'] ) {
			case 'US':
				$local_defaults = array(
					'logos' => array_merge(
						$defaults['logos'],
						array(
							array(
								'link' => WC()->plugin_url() . '/assets/images/wcs-usps-logo.png',
								'alt'  => 'USPS logo',
							),
							array(
								'link' => WC()->plugin_url() . '/assets/images/wcs-dhlexpress-logo.png',
								'alt'  => 'DHL Express logo',
							),
						)
					),
				);
				break;
			default:
				$local_defaults = array();
		}

		$block_data = array_merge( $defaults, $local_defaults, $block );
		?>
		<div class="addons-wcs-banner-block">
			<div class="addons-wcs-banner-block-image is-full-image">
				<img
					class="addons-img"
					src="<?php echo esc_url( $block_data['image'] ); ?>"
					alt="<?php echo esc_attr( $block_data['image_alt'] ); ?>"
				/>
			</div>
			<div class="addons-wcs-banner-block-content">
				<h1><?php echo esc_html( $block_data['title'] ); ?></h1>
				<p><?php echo esc_html( $block_data['description'] ); ?></p>
				<ul class="wcs-logos-container">
					<?php foreach ( $block_data['logos'] as $logo ) : ?>
						<li>
							<img
								alt="<?php echo esc_attr( $logo['alt'] ); ?>"
								class="wcs-service-logo"
								src="<?php echo esc_url( $logo['link'] ); ?>"
							>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php
					self::output_button(
						$block_data['href'],
						$block_data['button'],
						'addons-button-outline-purple'
					);
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Handles the outputting of the WooCommerce Pay banner block.
	 *
	 * @deprecated 5.9.0 No longer used in In-App Marketplace
	 *
	 * @param object $block Block data.
	 */
	public static function output_wcpay_banner_block( $block = array() ) {
		$is_active = is_plugin_active( 'woocommerce-payments/woocommerce-payments.php' );
		$location  = wc_get_base_location();

		if (
			! in_array( $location['country'], array( 'US' ), true ) ||
			$is_active ||
			! current_user_can( 'install_plugins' ) ||
			! current_user_can( 'activate_plugins' )
		) {
			return;
		}

		$button_url = wp_nonce_url(
			add_query_arg(
				array(
					'install-addon' => 'woocommerce-payments',
				)
			),
			'install-addon_woocommerce-payments'
		);

		$defaults = array(
			'image'       => WC()->plugin_url() . '/assets/images/wcpayments-icon-secure.png',
			'image_alt'   => __( 'WooCommerce Payments', 'woocommerce' ),
			'title'       => __( 'Payments made simple, with no monthly fees &mdash; exclusively for WooCommerce stores.', 'woocommerce' ),
			'description' => __( 'Securely accept cards in your store. See payments, track cash flow into your bank account, and stay on top of disputes – right from your dashboard.', 'woocommerce' ),
			'button'      => __( 'Free - Install now', 'woocommerce' ),
			'href'        => $button_url,
			'logos'       => array(),
		);

		$block_data = array_merge( $defaults, $block );
		?>
		<div class="addons-wcs-banner-block">
			<div class="addons-wcs-banner-block-image">
				<img
					class="addons-img"
					src="<?php echo esc_url( $block_data['image'] ); ?>"
					alt="<?php echo esc_attr( $block_data['image_alt'] ); ?>"
				/>
			</div>
			<div class="addons-wcs-banner-block-content">
				<h1><?php echo esc_html( $block_data['title'] ); ?></h1>
				<p><?php echo esc_html( $block_data['description'] ); ?></p>
				<?php
					self::output_button(
						$block_data['href'],
						$block_data['button'],
						'addons-button-outline-purple'
					);
				?>
			</div>
		</div>
		<?php
	}


	/**
	 * Output the HTML for the promotion block.
	 *
	 * @param array $promotion Array of promotion block data.
	 * @return void
	 */
	public static function output_search_promotion_block( array $promotion ) {
		?>
		<div class="addons-wcs-banner-block">
			<div class="addons-wcs-banner-block-image">
				<img
					class="addons-img"
					src="<?php echo esc_url( $promotion['image'] ); ?>"
					alt="<?php echo esc_attr( $promotion['image_alt'] ); ?>"
				/>
			</div>
			<div class="addons-wcs-banner-block-content">
				<h1><?php echo esc_html( $promotion['title'] ); ?></h1>
				<p><?php echo esc_html( $promotion['description'] ); ?></p>
				<?php
				if ( ! empty( $promotion['actions'] ) ) {
					foreach ( $promotion['actions'] as $action ) {
						self::output_promotion_action( $action );
					}
				}
				?>
			</div>
		</div>
		<?php
	}


	/**
	 * Handles the output of a full-width block.
	 *
	 * @deprecated 5.9.0 No longer used in In-App Marketplace
	 *
	 * @param array $section Section data.
	 */
	public static function output_promotion_block( $section ) {
		if (
			! current_user_can( 'install_plugins' ) ||
			! current_user_can( 'activate_plugins' )
		) {
			return;
		}

		$section_object = (object) $section;

		if ( ! empty( $section_object->geowhitelist ) ) {
			$section_object->geowhitelist = explode( ',', $section_object->geowhitelist );
		}

		if ( ! empty( $section_object->geoblacklist ) ) {
			$section_object->geoblacklist = explode( ',', $section_object->geoblacklist );
		}

		if ( ! self::show_extension( $section_object ) ) {
			return;
		}

		?>
		<div class="addons-banner-block addons-promotion-block">
			<img
				class="addons-img"
				src="<?php echo esc_url( $section['image'] ); ?>"
				alt="<?php echo esc_attr( $section['image_alt'] ); ?>"
			/>
			<div class="addons-promotion-block-content">
				<h1 class="addons-promotion-block-title"><?php echo esc_html( $section['title'] ); ?></h1>
				<div class="addons-promotion-block-description">
					<?php echo wp_kses_post( $section['description'] ); ?>
				</div>
				<div class="addons-promotion-block-buttons">
					<?php
					if ( $section['button_1'] ) {
						self::output_button(
							$section['button_1_href'],
							$section['button_1'],
							'addons-button-expandable addons-button-solid',
							$section['plugin']
						);
					}

					if ( $section['button_2'] ) {
						self::output_button(
							$section['button_2_href'],
							$section['button_2'],
							'addons-button-expandable addons-button-outline-purple',
							$section['plugin']
						);
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handles the outputting of featured sections
	 *
	 * @param array $sections Section data.
	 */
	public static function output_featured_sections( $sections ) {
		foreach ( $sections as $section ) {
			switch ( $section->module ) {
				case 'banner_block':
					self::output_banner_block( $section );
					break;
				case 'column_start':
					self::output_column( $section );
					break;
				case 'column_end':
					self::output_column( $section );
					break;
				case 'column_block':
					self::output_column_block( $section );
					break;
				case 'small_light_block':
					self::output_small_light_block( $section );
					break;
				case 'small_dark_block':
					self::output_small_dark_block( $section );
					break;
				case 'wcs_banner_block':
					self::output_wcs_banner_block( (array) $section );
					break;
				case 'wcpay_banner_block':
					self::output_wcpay_banner_block( (array) $section );
					break;
				case 'promotion_block':
					self::output_promotion_block( (array) $section );
					break;
			}
		}
	}

	/**
	 * Handles the outputting of featured page
	 *
	 * @param array $blocks Featured page's blocks.
	 */
	private static function output_featured( $blocks ) {
		foreach ( $blocks as $block ) {
			$block_type = $block->type ?? null;
			switch ( $block_type ) {
				case 'group':
					self::output_group( $block );
					break;
				case 'banner':
					self::output_banner( $block );
					break;
			}
		}
	}

	/**
	 * Render a group block including products
	 *
	 * @param mixed $block Block of the page for rendering.
	 *
	 * @return void
	 */
	private static function output_group( $block ) {
		$capacity             = $block->capacity ?? 3;
		$product_list_classes = 3 === $capacity ? 'three-column' : 'two-column';
		$product_list_classes = 'products addons-products-' . $product_list_classes;
		?>
			<section class="addon-product-group">
				<h2 class="addon-product-group-title"><?php echo esc_html( $block->title ); ?></h2>
				<div class="addon-product-group-description-container">
					<?php if ( ! empty( $block->description ) ) : ?>
					<div class="addon-product-group-description">
						<?php echo esc_html( $block->description ); ?>
					</div>
					<?php endif; ?>
					<?php if ( null !== $block->url ) : ?>
					<a class="addon-product-group-see-more" href="<?php echo esc_url( $block->url ); ?>">
						<?php esc_html_e( 'See more', 'woocommerce' ); ?>
					</a>
					<?php endif; ?>
				</div>
				<div class="addon-product-group__items">
					<ul class="<?php echo esc_attr( $product_list_classes ); ?>">
					<?php
					$products = array_slice( $block->items, 0, $capacity );
					foreach ( $products as $item ) {
						self::render_product_card( $item );
					}
					?>
					</ul>
				<div>
			</section>
		<?php
	}

	/**
	 * Render a banner contains a product
	 *
	 * @param mixed $block Block of the page for rendering.
	 *
	 * @return void
	 */
	private static function output_banner( $block ) {
		if ( empty( $block->buttons ) ) {
			// Render a product-like banner.
			?>
			<ul class="products">
				<?php self::render_product_card( $block, $block->type ); ?>
			</ul>
			<?php
		} else {
			// Render a banner with buttons.
			?>
			<ul class="products">
				<li class="product addons-buttons-banner">
					<div class="addons-buttons-banner-image"
						style="background-image:url(<?php echo esc_url( $block->image ); ?>)"
						title="<?php echo esc_attr( $block->image_alt ); ?>"></div>
					<div class="product-details addons-buttons-banner-details-container">
						<div class="addons-buttons-banner-details">
							<h2><?php echo esc_html( $block->title ); ?></h2>
							<p><?php echo wp_kses( $block->description, array() ); ?></p>
						</div>
						<div class="addons-buttons-banner-button-container">
						<?php
						foreach ( $block->buttons as $button ) {
							$button_classes = array( 'button', 'addons-buttons-banner-button' );
							$type           = $button->type ?? null;
							if ( 'primary' === $type ) {
								$button_classes[] = 'addons-buttons-banner-button-primary';
							}
							?>
							<a class="<?php echo esc_attr( implode( ' ', $button_classes ) ); ?>"
								href="<?php echo esc_url( $button->href ); ?>">
								<?php echo esc_html( $button->title ); ?>
							</a>
						<?php } ?>
						</div>
					</div>
				</li>
			</ul>
			<?php
		}
	}

	/**
	 * Returns in-app-purchase URL params.
	 */
	public static function get_in_app_purchase_url_params() {
		// Get url (from path onward) for the current page,
		// so WCCOM "back" link returns user to where they were.
		$back_admin_path = add_query_arg( array() );
		return array(
			'wccom-site'          => site_url(),
			'wccom-back'          => rawurlencode( $back_admin_path ),
			'wccom-woo-version'   => Constants::get_constant( 'WC_VERSION' ),
			'wccom-connect-nonce' => wp_create_nonce( 'connect' ),
		);
	}

	/**
	 * Add in-app-purchase URL params to link.
	 *
	 * Adds various url parameters to a url to support a streamlined
	 * flow for obtaining and setting up WooCommerce extensons.
	 *
	 * @param string $url    Destination URL.
	 */
	public static function add_in_app_purchase_url_params( $url ) {
		return add_query_arg(
			self::get_in_app_purchase_url_params(),
			$url
		);
	}

	/**
	 * Outputs a button.
	 *
	 * @param string $url    Destination URL.
	 * @param string $text   Button label text.
	 * @param string $style  Button style class.
	 * @param string $plugin The plugin the button is promoting.
	 */
	public static function output_button( $url, $text, $style, $plugin = '' ) {
		$style = __( 'Free', 'woocommerce' ) === $text ? 'addons-button-outline-purple' : $style;
		$style = is_plugin_active( $plugin ) ? 'addons-button-installed' : $style;
		$text  = is_plugin_active( $plugin ) ? __( 'Installed', 'woocommerce' ) : $text;
		$url   = self::add_in_app_purchase_url_params( $url );
		?>
		<a
			class="addons-button <?php echo esc_attr( $style ); ?>"
			href="<?php echo esc_url( $url ); ?>">
			<?php echo esc_html( $text ); ?>
		</a>
		<?php
	}

	/**
	 * Output HTML for a promotion action.
	 *
	 * @param array $action Array of action properties.
	 *
	 * @return void
	 */
	public static function output_promotion_action( array $action ) {
		if ( empty( $action ) ) {
			return;
		}
		$style = ( ! empty( $action['primary'] ) && $action['primary'] ) ? 'addons-button-solid' : 'addons-button-outline-purple';
		?>
		<a
			class="addons-button <?php echo esc_attr( $style ); ?>"
			href="<?php echo esc_url( $action['url'] ); ?>">
			<?php echo esc_html( $action['label'] ); ?>
		</a>
		<?php
	}

	/**
	 * Output HTML for a promotion action if data couldn't be fetched.
	 *
	 * @param string $message Error message.
	 *
	 * @return void
	 */
	public static function output_empty( $message = '' ) {
		?>
		<div class="wc-addons__empty">
			<h2><?php echo wp_kses_post( __( 'Oh no! We\'re having trouble connecting to the extensions catalog right now.', 'woocommerce' ) ); ?></h2>
			<?php if ( ! empty( $message ) ) : ?>
				<p><?php echo esc_html( $message ); ?></p>
			<?php endif; ?>
			<p>
				<?php
				printf(
					wp_kses_post(
						/* translators: a url */
						__(
							'To start growing your business, head over to <a href="%s">WooCommerce.com</a>, where you\'ll find the most popular WooCommerce extensions.',
							'woocommerce'
						)
					),
					'https://woocommerce.com/products/?utm_source=extensionsscreen&utm_medium=product&utm_campaign=connectionerror'
				);
				?>
			</p>
		</div>
		<?php
	}


	/**
	 * Handles output of the addons page in admin.
	 */
	public static function output() {
		$section = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : '_featured';
		$search  = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

		if ( isset( $_GET['section'] ) && 'helper' === $_GET['section'] ) {
			do_action( 'woocommerce_helper_output' );
			return;
		}

		if ( isset( $_GET['install-addon'] ) ) {
			switch ( $_GET['install-addon'] ) {
				case 'woocommerce-services':
					self::install_woocommerce_services_addon();
					break;
				case 'woocommerce-payments':
					self::install_woocommerce_payments_addon( $section );
					break;
				default:
					// Do nothing.
					break;
			}
		}

		$sections        = self::get_sections();
		$theme           = wp_get_theme();
		$current_section = isset( $_GET['section'] ) ? $section : '_featured';
		$promotions      = array();
		$addons          = array();

		if ( '_featured' !== $current_section ) {
			$category       = $section ? $section : null;
			$term           = $search ? $search : null;
			$country        = WC()->countries->get_base_country();
			$extension_data = self::get_extension_data( $category, $term, $country );
			$addons         = is_wp_error( $extension_data ) ? $extension_data : $extension_data->products;
			$promotions     = ! empty( $extension_data->promotions ) ? $extension_data->promotions : array();
		}

		// We need Automattic\WooCommerce\Admin\RemoteInboxNotifications for the next part, if not remove all promotions.
		if ( ! WC()->is_wc_admin_active() ) {
			$promotions = array();
		}
		// Check for existence of promotions and evaluate out if we should show them.
		if ( ! empty( $promotions ) ) {
			foreach ( $promotions as $promo_id => $promotion ) {
				$evaluator = new PromotionRuleEngine\RuleEvaluator();
				$passed    = $evaluator->evaluate( $promotion->rules );
				if ( ! $passed ) {
					unset( $promotions[ $promo_id ] );
				}
			}
			// Transform promotions to the correct format ready for output.
			$promotions = self::format_promotions( $promotions );
		}

		/**
		 * Addon page view.
		 *
		 * @uses $addons
		 * @uses $search
		 * @uses $sections
		 * @uses $theme
		 * @uses $current_section
		 */
		include_once dirname( __FILE__ ) . '/views/html-admin-page-addons.php';
	}

	/**
	 * Install WooCommerce Services from Extensions screens.
	 */
	public static function install_woocommerce_services_addon() {
		check_admin_referer( 'install-addon_woocommerce-services' );

		$services_plugin_id = 'woocommerce-services';
		$services_plugin    = array(
			'name'      => __( 'WooCommerce Services', 'woocommerce' ),
			'repo-slug' => 'woocommerce-services',
		);

		WC_Install::background_installer( $services_plugin_id, $services_plugin );

		wp_safe_redirect( remove_query_arg( array( 'install-addon', '_wpnonce' ) ) );
		exit;
	}

	/**
	 * Install WooCommerce Payments from the Extensions screens.
	 *
	 * @param string $section Optional. Extensions tab.
	 *
	 * @return void
	 */
	public static function install_woocommerce_payments_addon( $section = '_featured' ) {
		check_admin_referer( 'install-addon_woocommerce-payments' );

		$wcpay_plugin_id = 'woocommerce-payments';
		$wcpay_plugin    = array(
			'name'      => __( 'WooCommerce Payments', 'woocommerce' ),
			'repo-slug' => 'woocommerce-payments',
		);

		WC_Install::background_installer( $wcpay_plugin_id, $wcpay_plugin );

		do_action( 'woocommerce_addon_installed', $wcpay_plugin_id, $section );

		wp_safe_redirect( remove_query_arg( array( 'install-addon', '_wpnonce' ) ) );
		exit;
	}

	/**
	 * We're displaying page=wc-addons and page=wc-addons&section=helper as two separate pages.
	 * When we're on those pages, add body classes to distinguishe them.
	 *
	 * @param string $admin_body_class Unfiltered body class.
	 *
	 * @return string Body class with added class for Marketplace or My Subscriptions page.
	 */
	public static function filter_admin_body_classes( string $admin_body_class = '' ): string {
		if ( isset( $_GET['section'] ) && 'helper' === $_GET['section'] ) {
			return " $admin_body_class woocommerce-page-wc-subscriptions ";
		}

		return " $admin_body_class woocommerce-page-wc-marketplace ";
	}

	/**
	 * Determine which class should be used for a rating star:
	 * - golden
	 * - half-filled (50/50 golden and gray)
	 * - gray
	 *
	 * Consider ratings from 3.0 to 4.0 as an example
	 * 3.0 will produce 3 stars
	 * 3.1 to 3.5 will produce 3 stars and a half star
	 * 3.6 to 4.0 will product 4 stars
	 *
	 * @param float $rating Rating of a product.
	 * @param int   $index  Index of a star in a row.
	 *
	 * @return string CSS class to use.
	 */
	public static function get_star_class( $rating, $index ) {
		if ( $rating >= $index ) {
			// Rating more that current star to show.
			return 'fill';
		} elseif (
			abs( $index - 1 - floor( $rating ) ) < 0.0000001 &&
			0 < ( $rating - floor( $rating ) )
		) {
			// For rating more than x.0 and less than x.5 or equal it will show a half star.
			return 50 >= floor( ( $rating - floor( $rating ) ) * 100 )
				? 'half-fill'
				: 'fill';
		}

		// Don't show a golden star otherwise.
		return 'no-fill';
	}

	/**
	 * Take an action object and return the URL based on properties of the action.
	 *
	 * @param object $action Action object.
	 * @return string URL.
	 */
	public static function get_action_url( $action ): string {
		if ( ! isset( $action->url ) ) {
			return '';
		}

		if ( isset( $action->url_is_admin_query ) && $action->url_is_admin_query ) {
			return wc_admin_url( $action->url );
		}

		if ( isset( $action->url_is_admin_nonce_query ) && $action->url_is_admin_nonce_query ) {
			if ( empty( $action->nonce ) ) {
				return '';
			}
			return wp_nonce_url(
				admin_url( $action->url ),
				$action->nonce
			);
		}

		return $action->url;
	}

	/**
	 * Format the promotion data ready for display, ie fetch locales and actions.
	 *
	 * @param array $promotions Array of promotoin objects.
	 * @return array Array of formatted promotions ready for output.
	 */
	public static function format_promotions( array $promotions ): array {
		$formatted_promotions = array();
		foreach ( $promotions as $promotion ) {
			// Get the matching locale or fall back to en-US.
			$locale = PromotionRuleEngine\SpecRunner::get_locale( $promotion->locales );
			if ( null === $locale ) {
				continue;
			}

			$promotion_actions = array();
			if ( ! empty( $promotion->actions ) ) {
				foreach ( $promotion->actions as $action ) {
					$action_locale = PromotionRuleEngine\SpecRunner::get_action_locale( $action->locales );
					$url           = self::get_action_url( $action );

					$promotion_actions[] = array(
						'name'    => $action->name,
						'label'   => $action_locale->label,
						'url'     => $url,
						'primary' => isset( $action->is_primary ) ? $action->is_primary : false,
					);
				}
			}

			$formatted_promotions[] = array(
				'title'       => $locale->title,
				'description' => $locale->description,
				'image'       => ( 'http' === substr( $locale->image, 0, 4 ) ) ? $locale->image : WC()->plugin_url() . $locale->image,
				'image_alt'   => $locale->image_alt,
				'actions'     => $promotion_actions,
			);
		}
		return $formatted_promotions;
	}

	/**
	 * Map data from different endpoints to a universal format
	 *
	 * Search and featured products has a slightly different products' field names.
	 * Mapping converts different data structures into a universal one for further processing.
	 *
	 * @param mixed $data Product Card Data.
	 *
	 * @return object Converted data.
	 */
	public static function map_product_card_data( $data ) {
		$mapped = (object) null;

		$type = $data->type ?? null;

		// Icon.
		$mapped->icon = $data->icon ?? null;
		if ( null === $mapped->icon && 'banner' === $type ) {
			// For product-related banners icon is a product's image.
			$mapped->icon = $data->image ?? null;
		}

		// URL.
		$mapped->url = $data->link ?? null;
		if ( empty( $mapped->url ) ) {
			$mapped->url = $data->url ?? null;
		}

		// Title.
		$mapped->title = $data->title ?? null;

		// Vendor Name.
		$mapped->vendor_name = $data->vendor_name ?? null;
		if ( empty( $mapped->vendor_name ) ) {
			$mapped->vendor_name = $data->vendorName ?? null; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}

		// Vendor URL.
		$mapped->vendor_url = $data->vendor_url ?? null;
		if ( empty( $mapped->vendor_url ) ) {
			$mapped->vendor_url = $data->vendorUrl ?? null; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}

		// Description.
		$mapped->description = $data->excerpt ?? null;
		if ( empty( $mapped->description ) ) {
			$mapped->description = $data->description ?? null;
		}

		$has_currency = ! empty( $data->currency ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		// Is Free.
		if ( $has_currency ) {
			$mapped->is_free = 0 === (int) $data->price;
		} else {
			$mapped->is_free = '&#36;0.00' === $data->price;
		}

		// Price.
		if ( $has_currency ) {
			$mapped->price = wc_price( $data->price, array( 'currency' => $data->currency ) );
		} else {
			$mapped->price = $data->price;
		}

		// Price suffix, e.g. "per month".
		$mapped->price_suffix = $data->price_suffix ?? null;

		// Rating.
		$mapped->rating = $data->rating ?? null;
		if ( null === $mapped->rating ) {
			$mapped->rating = $data->averageRating ?? null; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}

		// Reviews Count.
		$mapped->reviews_count = $data->reviews_count ?? null;
		if ( null === $mapped->reviews_count ) {
			$mapped->reviews_count = $data->reviewsCount ?? null; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
		// Featured & Promoted product card.
		// Label.
		$mapped->label = $data->label ?? null;
		// Primary color.
		$mapped->primary_color = $data->primary_color ?? null;
		// Text color.
		$mapped->text_color = $data->text_color ?? null;
		// Button text.
		$mapped->button = $data->button ?? null;

		return $mapped;
	}

	/**
	 * Render a product card
	 *
	 * There's difference in data structure (e.g. field names) between endpoints such as search and
	 * featured. Inner mapping helps to use universal field names for further work.
	 *
	 * @param mixed  $data       Product data.
	 * @param string $block_type Block type that's different from the default product card, e.g. a banner.
	 *
	 * @return void
	 */
	public static function render_product_card( $data, $block_type = null ) {
		$mapped      = self::map_product_card_data( $data );
		$product_url = self::add_in_app_purchase_url_params( $mapped->url );
		$class_names = array( 'product' );
		// Specify a class name according to $block_type (if it's specified).
		if ( null !== $block_type ) {
			$class_names[] = 'addons-product-' . $block_type;
		}

		$product_details_classes = 'product-details';
		if ( 'banner' === $block_type ) {
			$product_details_classes .= ' addon-product-banner-details';
		}

		if ( isset( $mapped->label ) && 'promoted' === $mapped->label ) {
			$product_details_classes .= ' promoted';
		} elseif ( isset( $mapped->label ) && 'featured' === $mapped->label ) {
			$product_details_classes .= ' featured';
		}

		if ( 'promoted' === $mapped->label
			&& ! empty( $mapped->primary_color )
			&& ! empty( $mapped->text_color )
			&& ! empty( $mapped->button ) ) {
			// Promoted product card.
			?>
			<li class="product">
				<div class="<?php echo esc_attr( $product_details_classes ); ?>" style="border-top: 5px  solid <?php echo esc_html( $mapped->primary_color ); ?>;">
					<span class="label promoted"><?php esc_attr_e( 'Promoted', 'woocommerce' ); ?></span>
					<a href="<?php echo esc_url( $product_url ); ?>">
						<h2><?php echo esc_html( $mapped->title ); ?></h2>
					</a>
					<p><?php echo wp_kses_post( $mapped->description ); ?></p>
				</div>
				<div class="product-footer-promoted">
					<span class="icon"><img src="<?php echo esc_url( $mapped->icon ); ?>" /></span>
					<a class="addons-button addons-button-promoted" style="background: <?php echo esc_html( $mapped->primary_color ); ?>; color: <?php echo esc_html( $mapped->text_color ); ?>;" href="<?php echo esc_url( $product_url ); ?>">
						<?php echo esc_html( $mapped->button ); ?>
					</a>
				</div>
			</li>
			<?php
		} else {
			// Normal or "featured" product card.
			?>
			<li class="<?php echo esc_attr( implode( ' ', $class_names ) ); ?>">
				<div class="<?php echo esc_attr( $product_details_classes ); ?>">
					<div class="product-text-container">
						<?php if ( isset( $mapped->label ) && 'featured' === $mapped->label ) { ?>
							<span class="label featured"><?php esc_attr_e( 'Featured', 'woocommerce' ); ?></span>
						<?php } ?>
						<a href="<?php echo esc_url( $product_url ); ?>">
							<h2><?php echo esc_html( $mapped->title ); ?></h2>
						</a>
						<?php if ( ! empty( $mapped->vendor_name ) && ! empty( $mapped->vendor_url ) ) : ?>
							<div class="product-developed-by">
								<?php
								$vendor_url = add_query_arg(
									array(
										'utm_source'   => 'extensionsscreen',
										'utm_medium'   => 'product',
										'utm_campaign' => 'wcaddons',
										'utm_content'  => 'devpartner',
									),
									$mapped->vendor_url
								);

								printf(
								/* translators: %s vendor link */
									esc_html__( 'Developed by %s', 'woocommerce' ),
									sprintf(
										'<a class="product-vendor-link" href="%1$s" target="_blank">%2$s</a>',
										esc_url_raw( $vendor_url ),
										esc_html( $mapped->vendor_name )
									)
								);
								?>
							</div>
						<?php endif; ?>
						<p><?php echo wp_kses_post( $mapped->description ); ?></p>
					</div>
					<?php if ( ! empty( $mapped->icon ) ) : ?>
						<span class="product-img-wrap">
							<?php /* Show an icon if it exists */ ?>
							<img src="<?php echo esc_url( $mapped->icon ); ?>" />
						</span>
					<?php endif; ?>
				</div>
				<div class="product-footer">
					<div class="product-price-and-reviews-container">
						<div class="product-price-block">
							<?php if ( $mapped->is_free ) : ?>
								<span class="price"><?php esc_html_e( 'Free', 'woocommerce' ); ?></span>
							<?php else : ?>
								<span class="price">
									<?php
									echo wp_kses(
										$mapped->price,
										array(
											'span' => array(
												'class' => array(),
											),
											'bdi'  => array(),
										)
									);
									?>
								</span>
								<span class="price-suffix">
									<?php
									$price_suffix = __( 'per year', 'woocommerce' );
									if ( ! empty( $mapped->price_suffix ) ) {
										$price_suffix = $mapped->price_suffix;
									}
									echo esc_html( $price_suffix );
									?>
								</span>
							<?php endif; ?>
						</div>
						<?php if ( ! empty( $mapped->reviews_count ) && ! empty( $mapped->rating ) ) : ?>
							<?php /* Show rating and the number of reviews */ ?>
							<div class="product-reviews-block">
								<?php for ( $index = 1; $index <= 5; ++$index ) : ?>
									<?php $rating_star_class = 'product-rating-star product-rating-star__' . self::get_star_class( $mapped->rating, $index ); ?>
									<div class="<?php echo esc_attr( $rating_star_class ); ?>"></div>
								<?php endfor; ?>
								<span class="product-reviews-count">(<?php echo (int) $mapped->reviews_count; ?>)</span>
							</div>
						<?php endif; ?>
					</div>
					<a class="button" href="<?php echo esc_url( $product_url ); ?>">
						<?php esc_html_e( 'View details', 'woocommerce' ); ?>
					</a>
				</div>
			</li>
			<?php
		}
	}

	/**
	 * Retrieves the locale data from a transient.
	 *
	 * Transient value is an array of locale data in the following format:
	 * array(
	 *    'en_US' => ...,
	 *    'fr_FR' => ...,
	 * )
	 *
	 * If the transient does not exist, does not have a value, or has expired,
	 * then the return value will be false.
	 *
	 * @param string $transient Transient name. Expected to not be SQL-escaped.
	 * @param string $locale  Locale to retrieve.
	 * @return mixed Value of transient.
	 */
	private static function get_locale_data_from_transient( $transient, $locale ) {
		$transient_value = get_transient( $transient );
		$transient_value = is_array( $transient_value ) ? $transient_value : array();
		return $transient_value[ $locale ] ?? false;
	}

	/**
	 * Sets the locale data in a transient.
	 *
	 * Transient value is an array of locale data in the following format:
	 * array(
	 *    'en_US' => ...,
	 *    'fr_FR' => ...,
	 * )
	 *
	 * @param string $transient  Transient name. Expected to not be SQL-escaped.
	 *                           Must be 172 characters or fewer in length.
	 * @param mixed  $value      Transient value. Must be serializable if non-scalar.
	 *                           Expected to not be SQL-escaped.
	 * @param string $locale  Locale to set.
	 * @param int    $expiration Optional. Time until expiration in seconds. Default 0 (no expiration).
	 * @return bool True if the value was set, false otherwise.
	 */
	private static function set_locale_data_in_transient( $transient, $value, $locale, $expiration = 0 ) {
		$transient_value            = get_transient( $transient );
		$transient_value            = is_array( $transient_value ) ? $transient_value : array();
		$transient_value[ $locale ] = $value;
		return set_transient( $transient, $transient_value, $expiration );
	}
}

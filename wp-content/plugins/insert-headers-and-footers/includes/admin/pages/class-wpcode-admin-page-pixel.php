<?php
/**
 * The Conversion Pixels page.
 *
 * @package WPCode
 */

/**
 * Class WPCode_Admin_Page_Pixel.
 */
class WPCode_Admin_Page_Pixel extends WPCode_Admin_Page {

	/**
	 * The page slug to be used when adding the submenu.
	 *
	 * @var string
	 */
	public $page_slug = 'wpcode-pixel';

	/**
	 * Default view.
	 *
	 * @var string
	 */
	public $view = 'facebook';

	/**
	 * Call this just to set the page title translatable.
	 */
	public function __construct() {
		$this->page_title = __( 'Conversion Pixels', 'insert-headers-and-footers' );
		parent::__construct();
	}

	/**
	 * The Conversion Pixels page output.
	 *
	 * @return void
	 */
	public function output_content() {
		if ( method_exists( $this, 'output_view_' . $this->view ) ) {
			echo '<div class="wpcode-blur-area">';
			call_user_func( array( $this, 'output_view_' . $this->view ) );
			echo '</div>';
			echo $this->get_pixel_overlay();
		}
	}

	/**
	 * Get the overlay for the pixel settings.
	 *
	 * @return string
	 */
	public function get_pixel_overlay() {

		$text = sprintf(
		// translators: %1$s and %2$s are <u> tags.
			'<p>' . __( 'While you can always add pixels manually using code snippets, our Conversion Pixels addon helps you %1$ssave time%2$s while %1$sreducing errors%2$s. It lets you properly implement Facebook, Google, Pinterest, and TikTok ads tracking with deep integrations for eCommerce events, interaction measurement, and more. This addon is available on WPCode Plus plan or higher.', 'insert-headers-and-footers' ) . '</p>',
			'<u>',
			'</u>'
		);

		return self::get_upsell_box(
			__( 'Conversion Pixels Addon is a PRO Feature', 'insert-headers-and-footers' ),
			$text,
			array(
				'text' => __( 'Upgrade to WPCode PRO', 'insert-headers-and-footers' ),
				'url'  => wpcode_utm_url( 'https://wpcode.com/lite/', 'conversion-pixels', 'tab-' . $this->view, 'upgrade-to-pro' ),
			),
			array(),
			array(
				__( 'Seamless integration with WooCommerce, Easy Digital Downloads and MemberPress', 'insert-headers-and-footers' ),
				__( 'Works with Facebook, Google Ads, Pinterest, and TikTok', 'insert-headers-and-footers' ),
				__( 'No coding required', 'insert-headers-and-footers' ),
				__( '1-click setup for conversion tracking', 'insert-headers-and-footers' ),
			)
		);
	}

	/**
	 * Setup page-specific views.
	 *
	 * @return void
	 */
	protected function setup_views() {
		$this->views = array(
			'facebook'  => __( 'Facebook', 'insert-headers-and-footers' ),
			'google'    => __( 'Google', 'insert-headers-and-footers' ),
			'pinterest' => __( 'Pinterest', 'insert-headers-and-footers' ),
			'tiktok'    => __( 'TikTok', 'insert-headers-and-footers' ),
		);
	}

	/**
	 * For this page we output a menu.
	 *
	 * @return void
	 */
	public function output_header_bottom() {
		?>
		<ul class="wpcode-admin-tabs">
			<?php
			foreach ( $this->views as $slug => $label ) {
				$class = $this->view === $slug ? 'active' : '';
				?>
				<li>
					<a href="<?php echo esc_url( $this->get_view_link( $slug ) ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $label ); ?></a>
				</li>
			<?php } ?>
		</ul>
		<?php
	}

	/**
	 * Form for Facebook Pixel.
	 *
	 * @return void
	 */
	public function output_view_facebook() {
		?>
		<h2><?php esc_html_e( 'Facebook Pixel', 'insert-headers-and-footers' ); ?></h2>
		<?php
		$this->metabox_row(
			__( 'Facebook Pixel ID', 'insert-headers-and-footers' ),
			$this->get_input_text(
				'facebook_pixel_id',
				$this->get_option( 'facebook_pixel_id', '' ),
				sprintf(
				// translators: %1$s and %2$s are the opening and closing anchor tags.
					__( 'You can find your Facebook Pixel ID in the Facebook Ads Manager. %1$sRead our step by step directions%2$s. ', 'insert-headers-and-footers' ),
					'<a target="_blank" href="' . wpcode_utm_url( 'https://wpcode.com/docs/how-to-find-your-facebook-pixel-id-and-conversions-api-token/', 'conversion-pixels', 'facebook', 'pixel' ) . '">',
					'</a>'
				)
			),
			'facebook_pixel_id'
		);
		$this->metabox_row(
			__( 'Conversions API Token', 'insert-headers-and-footers' ),
			$this->get_input_text(
				'facebook_pixel_api_token',
				$this->get_option( 'facebook_pixel_api_token', '' ),
				__( 'The Conversions API token allows you to send API events that are more reliable and can improve audience targeting.', 'insert-headers-and-footers' ),
				true
			),
			'facebook_pixel_api_token'
		);
		$this->metabox_row(
			__( 'Facebook Pixel Events', 'insert-headers-and-footers' ),
			$this->get_checkbox_inputs(
				array(
					array(
						'label'       => __( 'PageView Event', 'insert-headers-and-footers' ),
						'name'        => 'page_view',
						'description' => __( 'Enable the PageView event to track and record page visits on all the pages of your website using the Facebook Pixel.', 'insert-headers-and-footers' ),
						'ecommerce'   => false,
					),
				),
				'facebook_pixel_events' )
		);
		$this->metabox_row(
			__( 'eCommerce Events Tracking', 'insert-headers-and-footers' ),
			$this->get_ecommerce_events_input() . $this->get_checkbox_inputs( $this->get_fb_pixel_events_inputs(), 'facebook_pixel_events' )
		);
		wp_nonce_field( 'wpcode-save-facebook-pixel-data', 'wpcode-pixel-nonce' );
		?>
		<button type="submit" class="wpcode-button">
			<?php esc_html_e( 'Save Changes', 'insert-headers-and-footers' ); ?>
		</button>
		<?php
	}

	/**
	 * Form for Google Pixel.
	 *
	 * @return void
	 */
	public function output_view_google() {
		?>
		<h2><?php esc_html_e( 'Google Analytics & Ads Tracking', 'insert-headers-and-footers' ); ?></h2>
		<?php
		$this->metabox_row(
			__( 'Google Analytics ID', 'insert-headers-and-footers' ),
			$this->get_input_text(
				'google_analytics_id',
				$this->get_option( 'google_analytics_id', '' ),
				sprintf(
				// translators: %1$s and %2$s are the opening and closing anchor tags.
					__( 'You can find your Google Analytics ID in the Google Analytics Admin panel. %1$sRead our step by step directions%2$s. ', 'insert-headers-and-footers' ),
					'<a target="_blank" href="' . wpcode_utm_url( 'https://wpcode.com/docs/how-to-find-your-google-analytics-id/', 'conversion-pixels', 'google', 'pixel' ) . '">',
					'</a>'
				)
			),
			'google_analytics_id'
		);
		$this->metabox_row(
			__( 'Google Ads Tag ID', 'insert-headers-and-footers' ),
			$this->get_input_text(
				'google_ads_id',
				$this->get_option( 'google_ads_id', '' ),
				sprintf(
				// translators: %1$s and %2$s are the opening and closing anchor tags.
					__( 'You can find your Google Ads Tag ID in the Google Ads Settings under Google Tag. %1$sRead our step by step directions%2$s. ', 'insert-headers-and-footers' ),
					'<a target="_blank" href="' . wpcode_utm_url( 'https://wpcode.com/docs/how-to-find-your-google-ads-tag-id/', 'conversion-pixels', 'google', 'pixel' ) . '">',
					'</a>'
				)
			),
			'google_ads_id'
		);
		$this->metabox_row(
			__( 'Ads Conversion Label', 'insert-headers-and-footers' ),
			$this->get_input_text(
				'google_ads_label',
				$this->get_option( 'google_ads_label', '' ),
				sprintf(
				// translators: %1$s and %2$s are the opening and closing anchor tags.
					__( 'Add your Google Ads Conversion Label for tracking conversion events. %1$sLearn More%2$s.', 'insert-headers-and-footers' ),
					'<a target="_blank" href="' . wpcode_utm_url( 'https://wpcode.com/docs/how-to-find-your-google-ads-tag-id/', 'conversion-pixels', 'google', 'pixel' ) . '">',
					'</a>'
				)
			),
			'google_ads_label'
		);
		$this->metabox_row(
			__( 'Google Events', 'insert-headers-and-footers' ),
			$this->get_checkbox_inputs(
				array(
					array(
						'label'       => __( 'PageView Event', 'insert-headers-and-footers' ),
						'name'        => 'page_view',
						'description' => __( 'Enable PageView event on all pages.', 'insert-headers-and-footers' ),
						'ecommerce'   => false,
					),
				),
				'google_pixel_events'
			)
		);
		$this->metabox_row(
			__( 'eCommerce Events Tracking', 'insert-headers-and-footers' ),
			$this->get_ecommerce_events_input() . $this->get_checkbox_inputs( $this->get_google_pixel_events_inputs(), 'google_pixel_events' )
		);
		wp_nonce_field( 'wpcode-save-google-pixel-data', 'wpcode-pixel-nonce' );
		?>
		<button type="submit" class="wpcode-button">
			<?php esc_html_e( 'Save Changes', 'insert-headers-and-footers' ); ?>
		</button>
		<?php
	}

	/**
	 * Settings for the Pinterest pixel.
	 *
	 * @return void
	 */
	public function output_view_pinterest() {
		?>
		<h2><?php esc_html_e( 'Pinterest Tag', 'insert-headers-and-footers' ); ?></h2>
		<?php
		$this->metabox_row(
			__( 'Tag ID', 'insert-headers-and-footers' ),
			$this->get_input_text(
				'pinterest_id',
				$this->get_option( 'pinterest_id', '' ),
				sprintf(
				// translators: %1$s and %2$s are the opening and closing anchor tags.
					__( 'You can find your Tag id in your Pinterest Business account. %1$sRead our step by step directions%2$s. ', 'insert-headers-and-footers' ),
					'<a target="_blank" href="' . wpcode_utm_url( 'https://wpcode.com/docs/how-to-find-your-pinterest-tag-id-and-conversion-access-token/', 'conversion-pixels', 'pinterest', 'pixel' ) . '">',
					'</a>'
				)
			),
			'pinterest_id'
		);
		$this->metabox_row(
			__( 'Ad Account ID', 'insert-headers-and-footers' ),
			$this->get_input_text(
				'pinterest_ad_account_id',
				$this->get_option( 'pinterest_ad_account_id', '' ),
				sprintf(
				// translators: %1$s and %2$s are the opening and closing anchor tags.
					__( 'You can find your Ad Account ID in your Pinterest Business account. %1$sRead more%2$s. ', 'insert-headers-and-footers' ),
					'<a target="_blank" href="' . wpcode_utm_url( 'https://wpcode.com/docs/how-to-find-your-pinterest-tag-id-and-conversion-access-token/', 'conversion-pixels', 'pinterest', 'pixel' ) . '">',
					'</a>'
				)
			),
			'pinterest_ad_account_id'
		);
		$this->metabox_row(
			__( 'Conversion Access Token', 'insert-headers-and-footers' ),
			$this->get_input_text(
				'pinterest_conversion_token',
				$this->get_option( 'pinterest_conversion_token', '' ),
				sprintf(
				// translators: %1$s and %2$s are the opening and closing anchor tags.
					__( 'You can find your Conversion Access Token under Ads > Conversions > Conversion access token. %1$sRead more%2$s. ', 'insert-headers-and-footers' ),
					'<a target="_blank" href="' . wpcode_utm_url( 'https://wpcode.com/docs/how-to-find-your-pinterest-tag-id-and-conversion-access-token/', 'conversion-pixels', 'pinterest', 'pixel' ) . '">',
					'</a>'
				),
				true
			),
			'pinterest_conversion_token'
		);
		$this->metabox_row(
			__( 'eCommerce Events Tracking', 'insert-headers-and-footers' ),
			$this->get_ecommerce_events_input() . $this->get_checkbox_inputs( $this->get_pinterest_pixel_events_inputs(), 'pinterest_pixel_events' )
		);
		wp_nonce_field( 'wpcode-save-pinterest-pixel-data', 'wpcode-pixel-nonce' );
		?>
		<button type="submit" class="wpcode-button">
			<?php esc_html_e( 'Save Changes', 'insert-headers-and-footers' ); ?>
		</button>
		<?php
	}

	/**
	 * Settings for the Pinterest pixel.
	 *
	 * @return void
	 */
	public function output_view_tiktok() {
		?>
		<h2><?php esc_html_e( 'TikTok Pixel', 'insert-headers-and-footers' ); ?></h2>
		<?php
		$this->metabox_row(
			__( 'Pixel ID', 'insert-headers-and-footers' ),
			$this->get_input_text(
				'tiktok_pixel_id',
				$this->get_option( 'tiktok_pixel_id', '' ),
				sprintf(
				// translators: %1$s and %2$s are the opening and closing anchor tags.
					__( 'You can find your Pixel id in your TikTok Business Account. %1$sRead our step by step directions%2$s.', 'insert-headers-and-footers' ),
					'<a target="_blank" href="' . wpcode_utm_url( 'https://wpcode.com/docs/how-to-find-your-tiktok-pixel-id-and-events-api-access-token/', 'conversion-pixels', 'tiktok', 'pixel' ) . '">',
					'</a>'
				)
			),
			'tiktok_pixel_id'
		);
		$this->metabox_row(
			__( 'Events API Access Token', 'insert-headers-and-footers' ),
			$this->get_input_text(
				'tiktok_access_token',
				$this->get_option( 'tiktok_access_token', '' ),
				sprintf(
				// translators: %1$s and %2$s are the opening and closing anchor tags.
					__( 'You can generate an access token in the Pixel Settings under Access Token Generation. %1$sRead more%2$s', 'insert-headers-and-footers' ),
					'<a target="_blank" href="' . wpcode_utm_url( 'https://wpcode.com/docs/how-to-find-your-tiktok-pixel-id-and-events-api-access-token/', 'conversion-pixels', 'tiktok', 'pixel' ) . '">',
					'</a>'
				),
				true
			),
			'tiktok_access_token'
		);
		$this->metabox_row(
			__( 'eCommerce Events Tracking', 'insert-headers-and-footers' ),
			$this->get_ecommerce_events_input() . $this->get_checkbox_inputs( $this->get_tiktok_pixel_events_inputs(), 'tiktok_pixel_events' )
		);
		wp_nonce_field( 'wpcode-save-tiktok-pixel-data', 'wpcode-pixel-nonce' );
		?>
		<button type="submit" class="wpcode-button">
			<?php esc_html_e( 'Save Changes', 'insert-headers-and-footers' ); ?>
		</button>
		<?php
	}

	/**
	 * Event options checkboxes for Facebook Pixel.
	 *
	 * @return array[]
	 */
	public function get_fb_pixel_events_inputs() {
		return array(
			array(
				'label'       => __( 'ViewContent Event', 'insert-headers-and-footers' ),
				'name'        => 'view_content',
				'description' => __( 'Turn on the "ViewContent" event to track views of product pages on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'view-content',
			),
			array(
				'label'       => __( 'AddtoCart Event', 'insert-headers-and-footers' ),
				'name'        => 'add_to_cart',
				'description' => __( 'Turn on the "AddToCart" event to track when items are added to a shopping cart on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'add-to-cart',
			),
			array(
				'label'       => __( 'InitiateCheckout Event', 'insert-headers-and-footers' ),
				'name'        => 'begin_checkout',
				'description' => __( 'Turn on the "InitiateCheckout" event to track when a user reaches the checkout page on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'begin-checkout',
			),
			array(
				'label'       => __( 'Purchase Event', 'insert-headers-and-footers' ),
				'name'        => 'purchase',
				'description' => __( 'Turn on the "Purchase" event to track successful purchases on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'purchase',
			),
		);
	}

	/**
	 * Events checkboxes for Google Analytics & Ads.
	 *
	 * @return array[]
	 */
	public function get_google_pixel_events_inputs() {
		return array(
			array(
				'label'       => __( 'View Item Event', 'insert-headers-and-footers' ),
				'name'        => 'view_item',
				'description' => __( 'Send the View Item event to track views of product pages on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'view-content',
			),
			array(
				'label'       => __( 'Add to Cart Event', 'insert-headers-and-footers' ),
				'name'        => 'add_to_cart',
				'description' => __( 'Send the Add to Cart event when a product is added to the cart.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'add-to-cart',
			),
			array(
				'label'       => __( 'Begin Checkout Event', 'insert-headers-and-footers' ),
				'name'        => 'begin_checkout',
				'description' => __( 'Send the Begin Checkout event when the user sees the checkout page.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'begin-checkout',
			),
			array(
				'label'       => __( 'Purchase Event', 'insert-headers-and-footers' ),
				'name'        => 'purchase',
				'description' => __( 'Send the Purchase event when the user completes a purchase.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'purchase',
			),
			array(
				'label'       => __( 'Conversion Event', 'insert-headers-and-footers' ),
				'name'        => 'conversion',
				'description' => __( 'Send the conversion event with the Google Ads label set above on a successful purchase.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'conversion',
			),
		);
	}

	/**
	 * Events checkboxes for Pinterest.
	 *
	 * @return array[]
	 */
	public function get_pinterest_pixel_events_inputs() {
		return array(
			array(
				'label'       => __( 'PageVisit Product Event', 'insert-headers-and-footers' ),
				'name'        => 'pagevisit_product',
				'description' => __( 'Turn on the "PageVisit" event to track views of product pages on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'view-content ',
			),
			array(
				'label'       => __( 'Add to Cart Event', 'insert-headers-and-footers' ),
				'name'        => 'add_to_cart',
				'description' => __( 'Turn on the Add to Cart event to track when items are added to a shopping cart on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'add-to-cart',
			),
			array(
				'label'       => __( 'Checkout PageVisit Event', 'insert-headers-and-footers' ),
				'name'        => 'begin_checkout',
				'description' => __( 'Enable the Checkout PageVisit event to track when a user reaches the checkout page on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'begin-checkout',
			),
			array(
				'label'       => __( 'Checkout Event', 'insert-headers-and-footers' ),
				'name'        => 'purchase',
				'description' => __( 'Turn on the "Checkout" event to track successful purchases on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'purchase',
			),
		);
	}

	/**
	 * Events checkboxes for TikTok.
	 *
	 * @return array[]
	 */
	public function get_tiktok_pixel_events_inputs() {
		return array(
			array(
				'label'       => __( 'ViewContent Product Event', 'insert-headers-and-footers' ),
				'name'        => 'view_content',
				'description' => __( 'Turn on the "ViewContent" event to track views of product pages on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'view-content',
			),
			array(
				'label'       => __( 'Add to Cart Event', 'insert-headers-and-footers' ),
				'name'        => 'add_to_cart',
				'description' => __( 'Turn on the "AddToCart" event to track when items are added to a shopping cart on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'add-to-cart',
			),
			array(
				'label'       => __( 'InitiateCheckout Event', 'insert-headers-and-footers' ),
				'name'        => 'begin_checkout',
				'description' => __( 'Turn on the "InitiateCheckout" event to track when a user reaches the checkout page on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'begin-checkout',
			),
			array(
				'label'       => __( 'PlaceAnOrder Event', 'insert-headers-and-footers' ),
				'name'        => 'purchase',
				'description' => __( 'Turn on the "PlaceAnOrder" event to track successful purchases on your website.', 'insert-headers-and-footers' ),
				'ecommerce'   => true,
				'css_class'   => 'purchase',
			),
		);
	}

	/**
	 * Get the markup for a list of checkboxes.
	 *
	 * @param array  $inputs The details for the checkbox inputs.
	 * @param string $name The name of the inputs.
	 * @param string $pixel_options_key The key of the pixel options (defaults to $name above if not set).
	 *
	 * @return string
	 */
	public function get_checkbox_inputs( $inputs, $name, $pixel_options_key = '' ) {
		$markup = '';
		if ( empty( $pixel_options_key ) ) {
			$pixel_options_key = $name;
		}
		$pixel_options     = $this->get_option( $pixel_options_key, array() );
		$ecommerce_vendors = $this->ecommerce_available();

		foreach ( $inputs as $input ) {
			$row_class  = isset( $input['css_class'] ) ? $input['css_class'] : '';
			$input_name = $name . '[' . $input['name'] . ']';
			$checked    = ! empty( $pixel_options[ $input['name'] ] );
			if ( $input['ecommerce'] && empty( $ecommerce_vendors ) ) {
				$row_class = ' wpcode-checkbox-row-disabled';
			}
			$markup .= '<div class="wpcode-checkbox-row ' . $row_class . '">';
			$markup .= $this->get_checkbox_toggle(
				$checked,
				$input_name,
				$input['description'],
				1,
				$input['label']
			);
			$markup .= '</div>';
		}

		return $markup;
	}

	/**
	 * Get a text field markup.
	 *
	 * @param string $id The id of the text field.
	 * @param string $value The value of the text field.
	 * @param string $description The description of the text field.
	 * @param bool   $wide Whether the text field should be wide.
	 *
	 * @return string
	 */
	public function get_input_text( $id, $value = '', $description = '', $wide = false ) {
		$class = 'wpcode-regular-text';
		if ( $wide ) {
			$class .= ' wpcode-wide-text';
		}
		$markup = '<input type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" value="' . esc_attr( $value ) . '" class="' . esc_attr( $class ) . '" autocomplete="off">';
		if ( ! empty( $description ) ) {
			$markup .= '<p>' . wp_kses_post( $description ) . '</p>';
		}

		return $markup;
	}

	/**
	 * There's no actual value to show in this instance.
	 *
	 * @param string $key The key of the option.
	 * @param mixed  $default The default value of the option.
	 *
	 * @return mixed
	 */
	public function get_option( $key, $default = false ) {
		return $default;
	}

	/**
	 * Whether we have a supported eCommerce plugin installed.
	 *
	 * @return array
	 */
	public function ecommerce_available() {
		return array();
	}

	/**
	 * Get the markup for the eCommerce events input.
	 *
	 * @return string
	 */
	public function get_ecommerce_events_input() {
		$providers = $this->ecommerce_available();

		$markup = '<div class="wpcode-label-text-row">';
		if ( empty( $providers ) ) {
			$markup .= '<p><strong>' . __( 'Disabled, no eCommerce Platform Detected', 'insert-headers-and-footers' ) . '</strong></p>';
		} else {
			foreach ( $providers as $provider ) {
				// translators: %s is the name of the eCommerce provider.
				$markup .= '<p><strong>' . sprintf( __( '%s Tracking Enabled', 'insert-headers-and-footers' ), $provider ) . '</strong></p>';
			}
		}
		$markup .= '<p>';
		$markup .= sprintf(
		// translators: %s a html break.
			__( 'Advanced eCommerce tracking is available for WooCommerce, Easy Digital Downloads and MemberPress. %s These plugins are detected automatically and when available you can toggle individual events using the options below.' ),
			'</br>'
		);
		$markup .= '</p>';
		$markup .= '</div>';

		return $markup;
	}
}

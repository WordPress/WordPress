<?php
/**
 * @package WPSEO\Admin
 */

/**
 * Admin form class.
 */
class Yoast_Form {

	/**
	 * @var object    Instance of this class
	 */
	public static $instance;

	/**
	 * @var string
	 */
	public $option_name;

	/**
	 * @var array
	 */
	public $options;

	/**
	 * Get the singleton instance of this class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Generates the header for admin pages
	 *
	 * @param bool   $form             Whether or not the form start tag should be included.
	 * @param string $option           The short name of the option to use for the current page.
	 * @param bool   $contains_files   Whether the form should allow for file uploads.
	 * @param bool   $option_long_name
	 */
	public function admin_header( $form = true, $option = 'wpseo', $contains_files = false, $option_long_name = false ) {
		if ( ! $option_long_name ) {
			$option_long_name = WPSEO_Options::get_group_name( $option );
		}
		?>
		<div class="wrap wpseo-admin-page page-<?php echo $option; ?>">
		<?php
		/**
		 * Display the updated/error messages
		 * Only needed as our settings page is not under options, otherwise it will automatically be included
		 * @see settings_errors()
		 */
		require_once( ABSPATH . 'wp-admin/options-head.php' );
		?>
		<h2 id="wpseo-title"><?php echo esc_html( get_admin_page_title() ); ?></h2>
		<div class="wpseo_content_wrapper">
		<div class="wpseo_content_cell" id="wpseo_content_top">
		<?php
		if ( $form === true ) {
			$enctype = ( $contains_files ) ? ' enctype="multipart/form-data"' : '';
			echo '<form action="' . esc_url( admin_url( 'options.php' ) ) . '" method="post" id="wpseo-conf"' . $enctype . ' accept-charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '">';
			settings_fields( $option_long_name );
		}
		$this->set_option( $option );
	}

	/**
	 * Set the option used in output for form elements
	 *
	 * @param string $option_name
	 */
	public function set_option( $option_name ) {
		$this->option_name = $option_name;
		$this->options     = $this->get_option();
	}

	/**
	 * Retrieve options based on whether we're on multisite or not.
	 *
	 * @since 1.2.4
	 *
	 * @return array
	 */
	private function get_option() {
		if ( is_network_admin() ) {
			return get_site_option( $this->option_name );
		}

		return get_option( $this->option_name );
	}

	/**
	 * Generates the footer for admin pages
	 *
	 * @param bool $submit       Whether or not a submit button and form end tag should be shown.
	 * @param bool $show_sidebar Whether or not to show the banner sidebar - used by premium plugins to disable it.
	 */
	public function admin_footer( $submit = true, $show_sidebar = true ) {
		if ( $submit ) {
			submit_button();

			echo '
			</form>';
		}

		do_action( 'wpseo_admin_footer' );

		echo '
			</div><!-- end of div wpseo_content_top -->';

		if ( $show_sidebar ) {
			$this->admin_sidebar();
		}

		echo '</div><!-- end of div wpseo_content_wrapper -->';


		if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) ) {
			$xdebug = ( extension_loaded( 'xdebug' ) ? true : false );
			echo '
			<div id="poststuff">
			<div id="wpseo-debug-info" class="postbox">

				<h3 class="hndle"><span>' . __( 'Debug Information', 'wordpress-seo' ) . '</span></h3>
				<div class="inside">
					<h4>' . esc_html( __( 'Current option:', 'wordpress-seo' ) ) . ' <span class="wpseo-debug">' . esc_html( $this->option_name ) . '</span></h4>
					' . ( ( $xdebug ) ? '' : '<pre>' );
			var_dump( $this->get_option() );
			echo '
					' . ( ( $xdebug ) ? '' : '</pre>' ) . '
				</div>
			</div>
			</div>';
		}

		echo '
			</div><!-- end of wrap -->';
	}

	/**
	 * Generates the sidebar for admin pages.
	 */
	public function admin_sidebar() {

		// No banners in Premium.
		if ( class_exists( 'WPSEO_Product_Premium' ) ) {
			$license_manager = new Yoast_Plugin_License_Manager( new WPSEO_Product_Premium() );
			if ( $license_manager->license_is_valid() ) {
				return;
			}
		}

		$service_banners = array(
			array(
				'url' => 'https://yoast.com/hire-us/website-review/#utm_source=wordpress-seo-config&utm_medium=banner&utm_campaign=website-review-banner',
				'img' => 'banner-website-review.png',
				'alt' => 'Website Review banner',
			),
		);

		$plugin_banners = array(
			array(
				'url' => 'https://yoast.com/wordpress/plugins/seo-premium/#utm_source=wordpress-seo-config&utm_medium=banner&utm_campaign=premium-seo-banner',
				'img' => 'banner-premium-seo.png',
				'alt' => 'Banner Yoast SEO Premium',
			),
		);

		if ( ! class_exists( 'wpseo_Video_Sitemap' ) ) {
			$plugin_banners[] = array(
				'url' => 'https://yoast.com/wordpress/plugins/video-seo/#utm_source=wordpress-seo-config&utm_medium=banner&utm_campaign=video-seo-banner',
				'img' => 'banner-video-seo.png',
				'alt' => 'Banner Yoast Video SEO plugin',
			);
		}

		if ( class_exists( 'Woocommerce' ) && ! class_exists( 'Yoast_WooCommerce_SEO' ) ) {
			$plugin_banners[] = array(
				'url' => 'https://yoast.com/wordpress/plugins/yoast-woocommerce-seo/#utm_source=wordpress-seo-config&utm_medium=banner&utm_campaign=woocommerce-seo-banner',
				'img' => 'banner-woocommerce-seo.png',
				'alt' => 'Banner Yoast WooCommerce SEO plugin',
			);
		}

		if ( ! defined( 'WPSEO_LOCAL_VERSION' ) ) {
			$plugin_banners[] = array(
				'url' => 'https://yoast.com/wordpress/plugins/local-seo/#utm_source=wordpress-seo-config&utm_medium=banner&utm_campaign=local-seo-banner',
				'img' => 'banner-local-seo.png',
				'alt' => 'Banner Yoast Local SEO plugin',
			);
		}

		if ( ! class_exists( 'WPSEO_News' ) ) {
			$plugin_banners[] = array(
				'url' => 'https://yoast.com/wordpress/plugins/news-seo/#utm_source=wordpress-seo-config&utm_medium=banner&utm_campaign=news-seo-banner',
				'img' => 'banner-news-seo.png',
				'alt' => 'Banner Yoast News SEO plugin',
			);
		}

		shuffle( $service_banners );
		shuffle( $plugin_banners );
		?>
		<div class="wpseo_content_cell" id="sidebar-container">
			<div id="sidebar">
		<?php

		$service_banner = $service_banners[0];

		echo '<a target="_blank" href="' . esc_url( $service_banner['url'] ) . '"><img width="261" height="190" src="' . plugins_url( 'images/' . $service_banner['img'], WPSEO_FILE ) . '" alt="' . esc_attr( $service_banner['alt'] ) . '"/></a><br/><br/>';

		$i = 0;
		foreach ( $plugin_banners as $banner ) {
			if ( $i == 2 ) {
				break;
			}
			echo '<a target="_blank" href="' . esc_url( $banner['url'] ) . '"><img width="261" height="130" src="' . plugins_url( 'images/' . $banner['img'], WPSEO_FILE ) . '" alt="' . esc_attr( $banner['alt'] ) . '"/></a><br/><br/>';
			$i ++;
		}
		?>
				<strong><?php _e( 'Remove these ads?', 'wordpress-seo' ); ?></strong><br/>
				<a target="_blank" href="https://yoast.com/wordpress/plugins/seo-premium/#utm_source=wordpress-seo-config&amp;utm_medium=textlink&amp;utm_campaign=remove-ads-link"><?php
				 /* translators: %1$s expands to Yoast SEO Premium */
				printf( __( 'Upgrade to %1$s &raquo;', 'wordpress-seo' ), 'Yoast SEO Premium' ); ?></a><br/><br/>
			</div>
		</div>
	<?php
	}

	/**
	 * Output a label element
	 *
	 * @param string $text
	 * @param array  $attr
	 */
	public function label( $text, $attr ) {
		$attr = wp_parse_args( $attr, array(
				'class' => 'checkbox',
				'close' => true,
				'for'   => '',
			)
		);
		echo "<label class='" . $attr['class'] . "' for='" . esc_attr( $attr['for'] ) . "'>$text";
		if ( $attr['close'] ) {
			echo '</label>';
		}
	}

	/**
	 * Create a Checkbox input field.
	 *
	 * @param string $var        The variable within the option to create the checkbox for.
	 * @param string $label      The label to show for the variable.
	 * @param bool   $label_left Whether the label should be left (true) or right (false).
	 */
	public function checkbox( $var, $label, $label_left = false ) {
		if ( ! isset( $this->options[ $var ] ) ) {
			$this->options[ $var ] = false;
		}

		if ( $this->options[ $var ] === true ) {
			$this->options[ $var ] = 'on';
		}

		$class = '';
		if ( $label_left !== false ) {
			if ( ! empty( $label_left ) ) {
				$label_left .= ':';
			}
			$this->label( $label_left, array( 'for' => $var ) );
		}
		else {
			$class = 'double';
		}

		echo '<input class="checkbox ', esc_attr( $class ), '" type="checkbox" id="', esc_attr( $var ), '" name="', esc_attr( $this->option_name ), '[', esc_attr( $var ), ']" value="on"', checked( $this->options[ $var ], 'on', false ), '/>';

		if ( ! empty( $label ) ) {
			$this->label( $label, array( 'for' => $var ) );
		}

		echo '<br class="clear" />';
	}

	/**
	 * Create a Text input field.
	 *
	 * @param string       $var   The variable within the option to create the text input field for.
	 * @param string       $label The label to show for the variable.
	 * @param array|string $attr  Extra class to add to the input field.
	 */
	public function textinput( $var, $label, $attr = array() ) {
		if ( ! is_array( $attr ) ) {
			$attr = array(
				'class' => $attr,
			);
		}
		$attr = wp_parse_args( $attr, array(
			'placeholder' => '',
			'class'       => '',
		) );
		$val  = ( isset( $this->options[ $var ] ) ) ? $this->options[ $var ] : '';

		$this->label( $label . ':', array( 'for' => $var ) );
		echo '<input class="textinput ' . esc_attr( $attr['class'] ) . ' " placeholder="' . esc_attr( $attr['placeholder'] ) . '" type="text" id="', esc_attr( $var ), '" name="', esc_attr( $this->option_name ), '[', esc_attr( $var ), ']" value="', esc_attr( $val ), '"/>', '<br class="clear" />';
	}

	/**
	 * Create a textarea.
	 *
	 * @param string $var   The variable within the option to create the textarea for.
	 * @param string $label The label to show for the variable.
	 * @param array  $attr  The CSS class to assign to the textarea.
	 */
	public function textarea( $var, $label, $attr = array() ) {
		if ( ! is_array( $attr ) ) {
			$attr = array(
				'class' => $attr,
			);
		}
		$attr = wp_parse_args( $attr, array(
			'cols'  => '',
			'rows'  => '',
			'class' => '',
		) );
		$val  = ( isset( $this->options[ $var ] ) ) ? $this->options[ $var ] : '';

		$this->label( $label . ':', array( 'for' => $var, 'class' => 'textinput' ) );
		echo '<textarea cols="' . esc_attr( $attr['cols'] ) . '" rows="' . esc_attr( $attr['rows'] ) . '" class="textinput ' . esc_attr( $attr['class'] ) . '" id="' . esc_attr( $var ) . '" name="' . esc_attr( $this->option_name ) . '[' . esc_attr( $var ) . ']">' . esc_textarea( $val ) . '</textarea>' . '<br class="clear" />';
	}

	/**
	 * Create a hidden input field.
	 *
	 * @param string $var The variable within the option to create the hidden input for.
	 * @param string $id  The ID of the element.
	 */
	public function hidden( $var, $id = '' ) {
		$val = ( isset( $this->options[ $var ] ) ) ? $this->options[ $var ] : '';
		if ( is_bool( $val ) ) {
			$val = ( $val === true ) ? 'true' : 'false';
		}

		if ( '' === $id ) {
			$id = 'hidden_' . $var;
		}

		echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $this->option_name ) . '[' . esc_attr( $var ) . ']" value="' . esc_attr( $val ) . '"/>';
	}

	/**
	 * Create a Select Box.
	 *
	 * @param string $var    The variable within the option to create the select for.
	 * @param string $label  The label to show for the variable.
	 * @param array  $values The select options to choose from.
	 */
	public function select( $var, $label, $values ) {
		if ( ! is_array( $values ) || $values === array() ) {
			return;
		}
		$val = ( isset( $this->options[ $var ] ) ) ? $this->options[ $var ] : '';

		$this->label( $label . ':', array( 'for' => $var, 'class' => 'select' ) );
		echo '<select class="select" name="', esc_attr( $this->option_name ), '[', esc_attr( $var ), ']" id="', esc_attr( $var ), '">';

		foreach ( $values as $value => $label ) {
			if ( ! empty( $label ) ) {
				echo '<option value="', esc_attr( $value ), '"', selected( $val, $value, false ), '>', $label, '</option>';
			}
		}
		echo '</select>';

		echo '<br class="clear"/>';
	}

	/**
	 * Create a File upload field.
	 *
	 * @param string $var   The variable within the option to create the file upload field for.
	 * @param string $label The label to show for the variable.
	 */
	public function file_upload( $var, $label ) {
		$val = '';
		if ( isset( $this->options[ $var ] ) && is_array( $this->options[ $var ] ) ) {
			$val = $this->options[ $var ]['url'];
		}

		$var_esc = esc_attr( $var );
		$this->label( $label . ':', array( 'for' => $var, 'class' => 'select' ) );
		echo '<input type="file" value="' . esc_attr( $val ) . '" class="textinput" name="' . esc_attr( $this->option_name ) . '[' . $var_esc . ']" id="' . $var_esc . '"/>';

		// Need to save separate array items in hidden inputs, because empty file inputs type will be deleted by settings API.
		if ( ! empty( $this->options[ $var ] ) ) {
			$this->hidden( 'file', $this->option_name . '_file' );
			$this->hidden( 'url', $this->option_name . '_url' );
			$this->hidden( 'type', $this->option_name . '_type' );
		}
		echo '<br class="clear"/>';
	}

	/**
	 * Media input
	 *
	 * @param string $var
	 * @param string $label
	 */
	public function media_input( $var, $label ) {
		$val = '';
		if ( isset( $this->options[ $var ] ) ) {
			$val = $this->options[ $var ];
		}

		$var_esc = esc_attr( $var );

		$this->label( $label . ':', array( 'for' => 'wpseo_' . $var, 'class' => 'select' ) );
		echo '<input class="textinput" id="wpseo_', $var_esc, '" type="text" size="36" name="', esc_attr( $this->option_name ), '[', $var_esc, ']" value="', esc_attr( $val ), '" />';
		echo '<input id="wpseo_', $var_esc, '_button" class="wpseo_image_upload_button button" type="button" value="', __( 'Upload Image', 'wordpress-seo' ), '" />';
		echo '<br class="clear"/>';
	}

	/**
	 * Create a Radio input field.
	 *
	 * @param string $var    The variable within the option to create the file upload field for.
	 * @param array  $values The radio options to choose from.
	 * @param string $label  The label to show for the variable.
	 */
	public function radio( $var, $values, $label ) {
		if ( ! is_array( $values ) || $values === array() ) {
			return;
		}
		if ( ! isset( $this->options[ $var ] ) ) {
			$this->options[ $var ] = false;
		}

		$var_esc = esc_attr( $var );

		echo '<br/><div class="wpseo_radio_block" id="' . $var_esc . '">';
		if ( is_string( $label ) && $label !== '' ) {
			$this->label( $label . ':', array( 'class' => 'select' ) );
		}

		foreach ( $values as $key => $value ) {
			$key_esc = esc_attr( $key );
			echo '<input type="radio" class="radio" id="' . $var_esc . '-' . $key_esc . '" name="' . esc_attr( $this->option_name ) . '[' . $var_esc . ']" value="' . $key_esc . '" ' . checked( $this->options[ $var ], $key_esc, false ) . ' />';
			$this->label( $value, array( 'for' => $var_esc . '-' . $key_esc, 'class' => 'radio' ) );
		}
		echo '<div class="clear"></div>';
		echo '</div><br/>';
	}
}

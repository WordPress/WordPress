<?php
/**
 * Adds options to the customizer for WooCommerce.
 *
 * @version 3.3.0
 * @package WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Shop_Customizer class.
 */
class WC_Shop_Customizer {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'customize_register', array( $this, 'add_sections' ) );
		add_action( 'customize_controls_print_styles', array( $this, 'add_styles' ) );
		add_action( 'customize_controls_print_scripts', array( $this, 'add_scripts' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_frontend_scripts' ) );
	}

	/**
	 * Add settings to the customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function add_sections( $wp_customize ) {
		$wp_customize->add_panel(
			'woocommerce',
			array(
				'priority'       => 200,
				'capability'     => 'manage_woocommerce',
				'theme_supports' => '',
				'title'          => __( 'WooCommerce', 'woocommerce' ),
			)
		);

		$this->add_store_notice_section( $wp_customize );
		$this->add_product_catalog_section( $wp_customize );
		$this->add_product_images_section( $wp_customize );
		$this->add_checkout_section( $wp_customize );
	}

	/**
	 * Frontend CSS styles.
	 */
	public function add_frontend_scripts() {
		if ( ! is_customize_preview() || ! is_store_notice_showing() ) {
			return;
		}

		$css = '.woocommerce-store-notice, p.demo_store { display: block !important; }';
		wp_add_inline_style( 'customize-preview', $css );
	}

	/**
	 * CSS styles to improve our form.
	 */
	public function add_styles() {
		?>
		<style type="text/css">
			.woocommerce-cropping-control {
				margin: 0 40px 1em 0;
				padding: 0;
				display:inline-block;
				vertical-align: top;
			}

			.woocommerce-cropping-control input[type=radio] {
				margin-top: 1px;
			}

			.woocommerce-cropping-control span.woocommerce-cropping-control-aspect-ratio {
				margin-top: .5em;
				display:block;
			}

			.woocommerce-cropping-control span.woocommerce-cropping-control-aspect-ratio input {
				width: auto;
				display: inline-block;
			}
		</style>
		<?php
	}

	/**
	 * Scripts to improve our form.
	 */
	public function add_scripts() {
		$min_rows    = wc_get_theme_support( 'product_grid::min_rows', 1 );
		$max_rows    = wc_get_theme_support( 'product_grid::max_rows', '' );
		$min_columns = wc_get_theme_support( 'product_grid::min_columns', 1 );
		$max_columns = wc_get_theme_support( 'product_grid::max_columns', '' );

		/* translators: %d: Setting value */
		$min_notice = __( 'The minimum allowed setting is %d', 'woocommerce' );
		/* translators: %d: Setting value */
		$max_notice = __( 'The maximum allowed setting is %d', 'woocommerce' );
		?>
		<script type="text/javascript">
			jQuery( function( $ ) {
				$( document.body ).on( 'change', '.woocommerce-cropping-control input[type="radio"]', function() {
					var $wrapper = $( this ).closest( '.woocommerce-cropping-control' ),
						value    = $wrapper.find( 'input:checked' ).val();

					if ( 'custom' === value ) {
						$wrapper.find( '.woocommerce-cropping-control-aspect-ratio' ).slideDown( 200 );
					} else {
						$wrapper.find( '.woocommerce-cropping-control-aspect-ratio' ).hide();
					}

					return false;
				} );

				wp.customize.bind( 'ready', function() { // Ready?
					$( '.woocommerce-cropping-control' ).find( 'input:checked' ).trigger( 'change' );
				} );

				wp.customize( 'woocommerce_demo_store', function( setting ) {
					setting.bind( function( value ) {
						var notice = wp.customize( 'woocommerce_demo_store_notice' );

						if ( value && ! notice.callbacks.has( notice.preview ) ) {
							notice.bind( notice.preview );
						} else if ( ! value ) {
							notice.unbind( notice.preview );
						}
					} );
				} );

				wp.customize( 'woocommerce_demo_store_notice', function( setting ) {
					setting.bind( function( value ) {
						var checkbox = wp.customize( 'woocommerce_demo_store' );

						if ( checkbox.get() ) {
							$( '.woocommerce-store-notice' ).text( value );
						}
					} );
				} );

				wp.customize.section( 'woocommerce_store_notice', function( section ) {
					section.expanded.bind( function( isExpanded ) {
						if ( isExpanded ) {
							var notice   = wp.customize( 'woocommerce_demo_store_notice' ),
								checkbox = wp.customize( 'woocommerce_demo_store' );

							if ( checkbox.get() && ! notice.callbacks.has( notice.preview ) ) {
								notice.bind( notice.preview );
							} else if ( ! checkbox.get() ) {
								notice.unbind( notice.preview );
							}
						}
					} );
				} );

				wp.customize.section( 'woocommerce_product_catalog', function( section ) {
					section.expanded.bind( function( isExpanded ) {
						if ( isExpanded ) {
							wp.customize.previewer.previewUrl.set( '<?php echo esc_js( wc_get_page_permalink( 'shop' ) ); ?>' );
						}
					} );
				} );

				wp.customize.section( 'woocommerce_product_images', function( section ) {
					section.expanded.bind( function( isExpanded ) {
						if ( isExpanded ) {
							wp.customize.previewer.previewUrl.set( '<?php echo esc_js( wc_get_page_permalink( 'shop' ) ); ?>' );
						}
					} );
				} );

				wp.customize.section( 'woocommerce_checkout', function( section ) {
					section.expanded.bind( function( isExpanded ) {
						if ( isExpanded ) {
							wp.customize.previewer.previewUrl.set( '<?php echo esc_js( wc_get_page_permalink( 'checkout' ) ); ?>' );
						}
					} );
				} );

				wp.customize( 'woocommerce_catalog_columns', function( setting ) {
					setting.bind( function( value ) {
						var min = parseInt( '<?php echo esc_js( $min_columns ); ?>', 10 );
						var max = parseInt( '<?php echo esc_js( $max_columns ); ?>', 10 );

						value = parseInt( value, 10 );

						if ( max && value > max ) {
							setting.notifications.add( 'max_columns_error', new wp.customize.Notification(
								'max_columns_error',
								{
									type   : 'error',
									message: '<?php echo esc_js( sprintf( $max_notice, $max_columns ) ); ?>'
								}
							) );
						} else {
							setting.notifications.remove( 'max_columns_error' );
						}

						if ( min && value < min ) {
							setting.notifications.add( 'min_columns_error', new wp.customize.Notification(
								'min_columns_error',
								{
									type   : 'error',
									message: '<?php echo esc_js( sprintf( $min_notice, $min_columns ) ); ?>'
								}
							) );
						} else {
							setting.notifications.remove( 'min_columns_error' );
						}
					} );
				} );

				wp.customize( 'woocommerce_catalog_rows', function( setting ) {
					setting.bind( function( value ) {
						var min = parseInt( '<?php echo esc_js( $min_rows ); ?>', 10 );
						var max = parseInt( '<?php echo esc_js( $max_rows ); ?>', 10 );

						value = parseInt( value, 10 );

						if ( max && value > max ) {
							setting.notifications.add( 'max_rows_error', new wp.customize.Notification(
								'max_rows_error',
								{
									type   : 'error',
									message: '<?php echo esc_js( sprintf( $max_notice, $max_rows ) ); ?>'
								}
							) );
						} else {
							setting.notifications.remove( 'max_rows_error' );
						}

						if ( min && value < min ) {
							setting.notifications.add( 'min_rows_error', new wp.customize.Notification(
								'min_rows_error',
								{
									type   : 'error',
									message: '<?php echo esc_js( sprintf( $min_notice, $min_rows ) ); ?>'
								}
							) );
						} else {
							setting.notifications.remove( 'min_rows_error' );
						}
					} );
				} );
			} );
		</script>
		<?php
	}

	/**
	 * Sanitize the shop page & category display setting.
	 *
	 * @param string $value '', 'subcategories', or 'both'.
	 * @return string
	 */
	public function sanitize_archive_display( $value ) {
		$options = array( '', 'subcategories', 'both' );

		return in_array( $value, $options, true ) ? $value : '';
	}

	/**
	 * Sanitize the catalog orderby setting.
	 *
	 * @param string $value An array key from the below array.
	 * @return string
	 */
	public function sanitize_default_catalog_orderby( $value ) {
		$options = apply_filters(
			'woocommerce_default_catalog_orderby_options',
			array(
				'menu_order' => __( 'Default sorting (custom ordering + name)', 'woocommerce' ),
				'popularity' => __( 'Popularity (sales)', 'woocommerce' ),
				'rating'     => __( 'Average rating', 'woocommerce' ),
				'date'       => __( 'Sort by most recent', 'woocommerce' ),
				'price'      => __( 'Sort by price (asc)', 'woocommerce' ),
				'price-desc' => __( 'Sort by price (desc)', 'woocommerce' ),
			)
		);

		return array_key_exists( $value, $options ) ? $value : 'menu_order';
	}

	/**
	 * Store notice section.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	private function add_store_notice_section( $wp_customize ) {
		$wp_customize->add_section(
			'woocommerce_store_notice',
			array(
				'title'    => __( 'Store Notice', 'woocommerce' ),
				'priority' => 10,
				'panel'    => 'woocommerce',
			)
		);

		$wp_customize->add_setting(
			'woocommerce_demo_store',
			array(
				'default'              => 'no',
				'type'                 => 'option',
				'capability'           => 'manage_woocommerce',
				'sanitize_callback'    => 'wc_bool_to_string',
				'sanitize_js_callback' => 'wc_string_to_bool',
			)
		);

		$wp_customize->add_setting(
			'woocommerce_demo_store_notice',
			array(
				'default'           => __( 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'woocommerce' ),
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => 'wp_kses_post',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'woocommerce_demo_store_notice',
			array(
				'label'       => __( 'Store notice', 'woocommerce' ),
				'description' => __( 'If enabled, this text will be shown site-wide. You can use it to show events or promotions to visitors!', 'woocommerce' ),
				'section'     => 'woocommerce_store_notice',
				'settings'    => 'woocommerce_demo_store_notice',
				'type'        => 'textarea',
			)
		);

		$wp_customize->add_control(
			'woocommerce_demo_store',
			array(
				'label'    => __( 'Enable store notice', 'woocommerce' ),
				'section'  => 'woocommerce_store_notice',
				'settings' => 'woocommerce_demo_store',
				'type'     => 'checkbox',
			)
		);

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'woocommerce_demo_store_notice',
				array(
					'selector'            => '.woocommerce-store-notice',
					'container_inclusive' => true,
					'render_callback'     => 'woocommerce_demo_store',
				)
			);
		}
	}

	/**
	 * Product catalog section.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function add_product_catalog_section( $wp_customize ) {
		$wp_customize->add_section(
			'woocommerce_product_catalog',
			array(
				'title'    => __( 'Product Catalog', 'woocommerce' ),
				'priority' => 10,
				'panel'    => 'woocommerce',
			)
		);

		$wp_customize->add_setting(
			'woocommerce_shop_page_display',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => array( $this, 'sanitize_archive_display' ),
			)
		);

		$wp_customize->add_control(
			'woocommerce_shop_page_display',
			array(
				'label'       => __( 'Shop page display', 'woocommerce' ),
				'description' => __( 'Choose what to display on the main shop page.', 'woocommerce' ),
				'section'     => 'woocommerce_product_catalog',
				'settings'    => 'woocommerce_shop_page_display',
				'type'        => 'select',
				'choices'     => array(
					''              => __( 'Show products', 'woocommerce' ),
					'subcategories' => __( 'Show categories', 'woocommerce' ),
					'both'          => __( 'Show categories &amp; products', 'woocommerce' ),
				),
			)
		);

		$wp_customize->add_setting(
			'woocommerce_category_archive_display',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => array( $this, 'sanitize_archive_display' ),
			)
		);

		$wp_customize->add_control(
			'woocommerce_category_archive_display',
			array(
				'label'       => __( 'Category display', 'woocommerce' ),
				'description' => __( 'Choose what to display on product category pages.', 'woocommerce' ),
				'section'     => 'woocommerce_product_catalog',
				'settings'    => 'woocommerce_category_archive_display',
				'type'        => 'select',
				'choices'     => array(
					''              => __( 'Show products', 'woocommerce' ),
					'subcategories' => __( 'Show subcategories', 'woocommerce' ),
					'both'          => __( 'Show subcategories &amp; products', 'woocommerce' ),
				),
			)
		);

		$wp_customize->add_setting(
			'woocommerce_default_catalog_orderby',
			array(
				'default'           => 'menu_order',
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => array( $this, 'sanitize_default_catalog_orderby' ),
			)
		);

		$wp_customize->add_control(
			'woocommerce_default_catalog_orderby',
			array(
				'label'       => __( 'Default product sorting', 'woocommerce' ),
				'description' => __( 'How should products be sorted in the catalog by default?', 'woocommerce' ),
				'section'     => 'woocommerce_product_catalog',
				'settings'    => 'woocommerce_default_catalog_orderby',
				'type'        => 'select',
				'choices'     => apply_filters(
					'woocommerce_default_catalog_orderby_options',
					array(
						'menu_order' => __( 'Default sorting (custom ordering + name)', 'woocommerce' ),
						'popularity' => __( 'Popularity (sales)', 'woocommerce' ),
						'rating'     => __( 'Average rating', 'woocommerce' ),
						'date'       => __( 'Sort by most recent', 'woocommerce' ),
						'price'      => __( 'Sort by price (asc)', 'woocommerce' ),
						'price-desc' => __( 'Sort by price (desc)', 'woocommerce' ),
					)
				),
			)
		);

		// The following settings should be hidden if the theme is declaring the values.
		if ( has_filter( 'loop_shop_columns' ) ) {
			return;
		}

		$wp_customize->add_setting(
			'woocommerce_catalog_columns',
			array(
				'default'              => 4,
				'type'                 => 'option',
				'capability'           => 'manage_woocommerce',
				'sanitize_callback'    => 'absint',
				'sanitize_js_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			'woocommerce_catalog_columns',
			array(
				'label'       => __( 'Products per row', 'woocommerce' ),
				'description' => __( 'How many products should be shown per row?', 'woocommerce' ),
				'section'     => 'woocommerce_product_catalog',
				'settings'    => 'woocommerce_catalog_columns',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => wc_get_theme_support( 'product_grid::min_columns', 1 ),
					'max'  => wc_get_theme_support( 'product_grid::max_columns', '' ),
					'step' => 1,
				),
			)
		);

		// Only add this setting if something else isn't managing the number of products per page.
		if ( ! has_filter( 'loop_shop_per_page' ) ) {
			$wp_customize->add_setting(
				'woocommerce_catalog_rows',
				array(
					'default'              => 4,
					'type'                 => 'option',
					'capability'           => 'manage_woocommerce',
					'sanitize_callback'    => 'absint',
					'sanitize_js_callback' => 'absint',
				)
			);
		}

		$wp_customize->add_control(
			'woocommerce_catalog_rows',
			array(
				'label'       => __( 'Rows per page', 'woocommerce' ),
				'description' => __( 'How many rows of products should be shown per page?', 'woocommerce' ),
				'section'     => 'woocommerce_product_catalog',
				'settings'    => 'woocommerce_catalog_rows',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => wc_get_theme_support( 'product_grid::min_rows', 1 ),
					'max'  => wc_get_theme_support( 'product_grid::max_rows', '' ),
					'step' => 1,
				),
			)
		);
	}

	/**
	 * Product images section.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	private function add_product_images_section( $wp_customize ) {
		if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) ) {
			$regen_description = ''; // Nothing to report; Jetpack will handle magically.
		} elseif ( apply_filters( 'woocommerce_background_image_regeneration', true ) && ! is_multisite() ) {
			$regen_description = __( 'After publishing your changes, new image sizes will be generated automatically.', 'woocommerce' );
		} elseif ( apply_filters( 'woocommerce_background_image_regeneration', true ) && is_multisite() ) {
			/* translators: 1: tools URL 2: regen thumbs url */
			$regen_description = sprintf( __( 'After publishing your changes, new image sizes may not be shown until you regenerate thumbnails. You can do this from the <a href="%1$s" target="_blank">tools section in WooCommerce</a> or by using a plugin such as <a href="%2$s" target="_blank">Regenerate Thumbnails</a>.', 'woocommerce' ), admin_url( 'admin.php?page=wc-status&tab=tools' ), 'https://en-gb.wordpress.org/plugins/regenerate-thumbnails/' );
		} else {
			/* translators: %s: regen thumbs url */
			$regen_description = sprintf( __( 'After publishing your changes, new image sizes may not be shown until you <a href="%s" target="_blank">Regenerate Thumbnails</a>.', 'woocommerce' ), 'https://en-gb.wordpress.org/plugins/regenerate-thumbnails/' );
		}

		$wp_customize->add_section(
			'woocommerce_product_images',
			array(
				'title'       => __( 'Product Images', 'woocommerce' ),
				'description' => $regen_description,
				'priority'    => 20,
				'panel'       => 'woocommerce',
			)
		);

		if ( ! wc_get_theme_support( 'single_image_width' ) ) {
			$wp_customize->add_setting(
				'woocommerce_single_image_width',
				array(
					'default'              => 600,
					'type'                 => 'option',
					'capability'           => 'manage_woocommerce',
					'sanitize_callback'    => 'absint',
					'sanitize_js_callback' => 'absint',
				)
			);

			$wp_customize->add_control(
				'woocommerce_single_image_width',
				array(
					'label'       => __( 'Main image width', 'woocommerce' ),
					'description' => __( 'Image size used for the main image on single product pages. These images will remain uncropped.', 'woocommerce' ),
					'section'     => 'woocommerce_product_images',
					'settings'    => 'woocommerce_single_image_width',
					'type'        => 'number',
					'input_attrs' => array(
						'min'  => 0,
						'step' => 1,
					),
				)
			);
		}

		if ( ! wc_get_theme_support( 'thumbnail_image_width' ) ) {
			$wp_customize->add_setting(
				'woocommerce_thumbnail_image_width',
				array(
					'default'              => 300,
					'type'                 => 'option',
					'capability'           => 'manage_woocommerce',
					'sanitize_callback'    => 'absint',
					'sanitize_js_callback' => 'absint',
				)
			);

			$wp_customize->add_control(
				'woocommerce_thumbnail_image_width',
				array(
					'label'       => __( 'Thumbnail width', 'woocommerce' ),
					'description' => __( 'Image size used for products in the catalog.', 'woocommerce' ),
					'section'     => 'woocommerce_product_images',
					'settings'    => 'woocommerce_thumbnail_image_width',
					'type'        => 'number',
					'input_attrs' => array(
						'min'  => 0,
						'step' => 1,
					),
				)
			);
		}

		include_once WC_ABSPATH . 'includes/customizer/class-wc-customizer-control-cropping.php';

		$wp_customize->add_setting(
			'woocommerce_thumbnail_cropping',
			array(
				'default'           => '1:1',
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => 'wc_clean',
			)
		);

		$wp_customize->add_setting(
			'woocommerce_thumbnail_cropping_custom_width',
			array(
				'default'              => '4',
				'type'                 => 'option',
				'capability'           => 'manage_woocommerce',
				'sanitize_callback'    => 'absint',
				'sanitize_js_callback' => 'absint',
			)
		);

		$wp_customize->add_setting(
			'woocommerce_thumbnail_cropping_custom_height',
			array(
				'default'              => '3',
				'type'                 => 'option',
				'capability'           => 'manage_woocommerce',
				'sanitize_callback'    => 'absint',
				'sanitize_js_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new WC_Customizer_Control_Cropping(
				$wp_customize,
				'woocommerce_thumbnail_cropping',
				array(
					'section'  => 'woocommerce_product_images',
					'settings' => array(
						'cropping'      => 'woocommerce_thumbnail_cropping',
						'custom_width'  => 'woocommerce_thumbnail_cropping_custom_width',
						'custom_height' => 'woocommerce_thumbnail_cropping_custom_height',
					),
					'label'    => __( 'Thumbnail cropping', 'woocommerce' ),
					'choices'  => array(
						'1:1'       => array(
							'label'       => __( '1:1', 'woocommerce' ),
							'description' => __( 'Images will be cropped into a square', 'woocommerce' ),
						),
						'custom'    => array(
							'label'       => __( 'Custom', 'woocommerce' ),
							'description' => __( 'Images will be cropped to a custom aspect ratio', 'woocommerce' ),
						),
						'uncropped' => array(
							'label'       => __( 'Uncropped', 'woocommerce' ),
							'description' => __( 'Images will display using the aspect ratio in which they were uploaded', 'woocommerce' ),
						),
					),
				)
			)
		);
	}

	/**
	 * Checkout section.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function add_checkout_section( $wp_customize ) {
		$wp_customize->add_section(
			'woocommerce_checkout',
			array(
				'title'       => __( 'Checkout', 'woocommerce' ),
				'priority'    => 20,
				'panel'       => 'woocommerce',
				'description' => __( 'These options let you change the appearance of the WooCommerce checkout.', 'woocommerce' ),
			)
		);

		// Checkout field controls.
		$fields = array(
			'company'   => __( 'Company name', 'woocommerce' ),
			'address_2' => __( 'Address line 2', 'woocommerce' ),
			'phone'     => __( 'Phone', 'woocommerce' ),
		);
		foreach ( $fields as $field => $label ) {
			$wp_customize->add_setting(
				'woocommerce_checkout_' . $field . '_field',
				array(
					'default'           => 'phone' === $field ? 'required' : 'optional',
					'type'              => 'option',
					'capability'        => 'manage_woocommerce',
					'sanitize_callback' => array( $this, 'sanitize_checkout_field_display' ),
				)
			);
			$wp_customize->add_control(
				'woocommerce_checkout_' . $field . '_field',
				array(
					/* Translators: %s field name. */
					'label'    => sprintf( __( '%s field', 'woocommerce' ), $label ),
					'section'  => 'woocommerce_checkout',
					'settings' => 'woocommerce_checkout_' . $field . '_field',
					'type'     => 'select',
					'choices'  => array(
						'hidden'   => __( 'Hidden', 'woocommerce' ),
						'optional' => __( 'Optional', 'woocommerce' ),
						'required' => __( 'Required', 'woocommerce' ),
					),
				)
			);
		}

		// Register settings.
		$wp_customize->add_setting(
			'woocommerce_checkout_highlight_required_fields',
			array(
				'default'              => 'yes',
				'type'                 => 'option',
				'capability'           => 'manage_woocommerce',
				'sanitize_callback'    => 'wc_bool_to_string',
				'sanitize_js_callback' => 'wc_string_to_bool',
			)
		);

		$wp_customize->add_setting(
			'woocommerce_checkout_terms_and_conditions_checkbox_text',
			array(
				/* translators: %s terms and conditions page name and link */
				'default'           => sprintf( __( 'I have read and agree to the website %s', 'woocommerce' ), '[terms]' ),
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => 'wp_kses_post',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_setting(
			'woocommerce_checkout_privacy_policy_text',
			array(
				/* translators: %s privacy policy page name and link */
				'default'           => sprintf( __( 'Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our %s.', 'woocommerce' ), '[privacy_policy]' ),
				'type'              => 'option',
				'capability'        => 'manage_woocommerce',
				'sanitize_callback' => 'wp_kses_post',
				'transport'         => 'postMessage',
			)
		);

		// Register controls.
		$wp_customize->add_control(
			'woocommerce_checkout_highlight_required_fields',
			array(
				'label'    => __( 'Highlight required fields with an asterisk', 'woocommerce' ),
				'section'  => 'woocommerce_checkout',
				'settings' => 'woocommerce_checkout_highlight_required_fields',
				'type'     => 'checkbox',
			)
		);

		if ( current_user_can( 'manage_privacy_options' ) ) {
			$choose_pages = array(
				'wp_page_for_privacy_policy' => __( 'Privacy policy', 'woocommerce' ),
				'woocommerce_terms_page_id'  => __( 'Terms and conditions', 'woocommerce' ),
			);
		} else {
			$choose_pages = array(
				'woocommerce_terms_page_id' => __( 'Terms and conditions', 'woocommerce' ),
			);
		}
		$pages        = get_pages(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish,private,draft',
				'child_of'    => 0,
				'parent'      => -1,
				'exclude'     => array(
					wc_get_page_id( 'cart' ),
					wc_get_page_id( 'checkout' ),
					wc_get_page_id( 'myaccount' ),
				),
				'sort_order'  => 'asc',
				'sort_column' => 'post_title',
			)
		);
		$page_choices = array( '' => __( 'No page set', 'woocommerce' ) ) + array_combine( array_map( 'strval', wp_list_pluck( $pages, 'ID' ) ), wp_list_pluck( $pages, 'post_title' ) );

		foreach ( $choose_pages as $id => $name ) {
			$wp_customize->add_setting(
				$id,
				array(
					'default'    => '',
					'type'       => 'option',
					'capability' => 'manage_woocommerce',
				)
			);
			$wp_customize->add_control(
				$id,
				array(
					/* Translators: %s: page name. */
					'label'    => sprintf( __( '%s page', 'woocommerce' ), $name ),
					'section'  => 'woocommerce_checkout',
					'settings' => $id,
					'type'     => 'select',
					'choices'  => $page_choices,
				)
			);
		}

		$wp_customize->add_control(
			'woocommerce_checkout_privacy_policy_text',
			array(
				'label'           => __( 'Privacy policy', 'woocommerce' ),
				'description'     => __( 'Optionally add some text about your store privacy policy to show during checkout.', 'woocommerce' ),
				'section'         => 'woocommerce_checkout',
				'settings'        => 'woocommerce_checkout_privacy_policy_text',
				'active_callback' => array( $this, 'has_privacy_policy_page_id' ),
				'type'            => 'textarea',
			)
		);

		$wp_customize->add_control(
			'woocommerce_checkout_terms_and_conditions_checkbox_text',
			array(
				'label'           => __( 'Terms and conditions', 'woocommerce' ),
				'description'     => __( 'Optionally add some text for the terms checkbox that customers must accept.', 'woocommerce' ),
				'section'         => 'woocommerce_checkout',
				'settings'        => 'woocommerce_checkout_terms_and_conditions_checkbox_text',
				'active_callback' => array( $this, 'has_terms_and_conditions_page_id' ),
				'type'            => 'text',
			)
		);

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'woocommerce_checkout_privacy_policy_text',
				array(
					'selector'            => '.woocommerce-privacy-policy-text',
					'container_inclusive' => true,
					'render_callback'     => 'wc_checkout_privacy_policy_text',
				)
			);
			$wp_customize->selective_refresh->add_partial(
				'woocommerce_checkout_terms_and_conditions_checkbox_text',
				array(
					'selector'            => '.woocommerce-terms-and-conditions-checkbox-text',
					'container_inclusive' => false,
					'render_callback'     => 'wc_terms_and_conditions_checkbox_text',
				)
			);
		}
	}

	/**
	 * Sanitize field display.
	 *
	 * @param string $value '', 'subcategories', or 'both'.
	 * @return string
	 */
	public function sanitize_checkout_field_display( $value ) {
		$options = array( 'hidden', 'optional', 'required' );
		return in_array( $value, $options, true ) ? $value : '';
	}

	/**
	 * Whether or not a page has been chose for the privacy policy.
	 *
	 * @return bool
	 */
	public function has_privacy_policy_page_id() {
		return wc_privacy_policy_page_id() > 0;
	}

	/**
	 * Whether or not a page has been chose for the terms and conditions.
	 *
	 * @return bool
	 */
	public function has_terms_and_conditions_page_id() {
		return wc_terms_and_conditions_page_id() > 0;
	}
}

global $pagenow;
if (
	$pagenow === 'customize.php' ||
	isset( $_REQUEST['customize_theme'] ) || // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	! wc_current_theme_is_fse_theme()
) {
	new WC_Shop_Customizer();
}

<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

add_action( 'admin_menu', 'us_add_demo_import_page', 30 );
function us_add_demo_import_page() {
	add_submenu_page( 'us-theme-options', __( 'Demo Import', 'us' ), __( 'Demo Import', 'us' ), 'manage_options', 'us-demo-import', 'us_demo_import' );
}

function us_demo_import() {
	global $us_template_directory_uri;
	$config = us_config( 'demo-import', array() );
	if ( count( $config ) < 1 ) {
		return;
	}
	reset( $config );
	$default_demo = key( $config );
	?>
	<div class="w-message content" style="display:none;">
		<div class="g-preloader type_1"></div>

		<h1 class="w-message-title"><?php _e( 'Importing Demo Content...', 'us' ) ?></h1>

		<p class="w-message-text">
			<?php _e( 'Please be patient and do not navigate away from this page while the import is in&nbsp;progress.', 'us' ) ?>
			<?php _e( 'This can take a while if your server is slow (inexpensive hosting).', 'us' ) ?>
		</p>

		<p class="w-message-text">
			<?php _e( 'You will be notified via this page when the import is completed.', 'us' ) ?>
		</p>
	</div>

	<div class="w-message error" style="display:none;">
		<h1 class="w-message-title"><?php _e( 'Failed to import Demo Content', 'us' ) ?></h1>

		<p class="w-message-text">
			<?php _e( 'You will be notified via this page when the import is completed.', 'us' ) ?>
		</p>
	</div>

	<div class="w-message success" style="display:none;">
		<h1 class="w-message-title"><?php _e( 'Import completed', 'us' ) ?></h1>

		<p class="w-message-text">
			<?php
			echo sprintf( __( 'Now you can see the result at <a href="%s" target="_blank">your site</a><br> or start customize via <a href="%s">Theme Options</a>.', 'us' ), site_url(), admin_url( 'admin.php?page=us-theme-options' ) );
			?>
		</p>
	</div>

	<form class="w-importer" action="?page=us-demo-import" method="post">

		<?php if ( count( $config ) > 1 ): ?>
			<h1 class="w-importer-title"><?php _e( 'Choose the demo which you want to import', 'us' ) ?></h1>
			<div class="w-importer-list">
				<?php foreach ( $config as $name => $import ): ?>
					<div class="w-importer-item">
						<input class="w-importer-item-radio" id="demo_<?php echo $name; ?>" type="radio" value="<?php echo $name; ?>" name="demo">
						<label class="w-importer-item-preview" for="demo_<?php echo $name; ?>" title="<?php _e( 'Click to choose', 'us' ) ?>">
							<h2 class="w-importer-item-title"><?php echo $import['title']; ?></h2>
							<img src="<?php echo $us_template_directory_uri . '/demo-import/' . $name . '/preview.jpg' ?>" alt="<?php echo $import['title']; ?>">
						</label>

						<div class="w-importer-item-btn">
							<a class="button" href="<?php echo $import['preview_url']; ?>" target="_blank"><?php _e( 'Preview', 'us' ) ?></a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<?php
			$name = key( $config );
			?>
			<h1 class="w-importer-title"><?php _e( 'Demo Import', 'us' ) ?>
				(<a target="_blank" href="<?php echo $config[ $name ]['preview_url'] ?>"><?php _e( 'preview', 'us' ) ?></a>)
			</h1>
			<input type="hidden" name="demo" value="<?php echo $name ?>">
		<?php endif; ?>


		<div class="w-importer-options" style="<?php if ( count( $config ) > 1 ): ?>display: none;<?php endif; ?>">

			<div class="w-importer-option theme-options">
				<label class="w-importer-option-check">
					<input id="demo_content" type="checkbox" value="ON" name="demo_content" checked="checked">
					<span class="w-importer-option-title"><?php _ex( 'Import Demo Content', 'verb', 'us' ) ?></span>
				</label>
			</div>
			<div class="w-importer-option theme-options">
				<label class="w-importer-option-check">
					<input id="theme_options" type="checkbox" value="ON" name="theme_options" checked="checked">
					<span class="w-importer-option-title"><?php _e( 'Import Theme Options', 'us' ) ?></span>
				</label>
			</div>
			<div class="w-importer-option rev-slider">
				<label class="w-importer-option-check">
					<input id="rev_slider" type="checkbox" value="ON"
					       name="rev_slider"<?php if ( ! class_exists( 'RevSlider' ) ) {
						echo ' disabled="disabled"';
					} ?>>
					<span class="w-importer-option-title"><?php _e( 'Import Revolution Sliders', 'us' ) ?></span>
					<?php
					$sliders_avaliable_for = array();
					foreach ( $config as $name => $import ) {
						if ( isset( $import['sliders'] ) AND is_array( $import['sliders'] ) AND ! empty ( $import['sliders'] ) ) {
							$sliders_avaliable_for[ $name ] = $import['title'];
						}
					}
					?>
				</label>
				<?php if ( count( $sliders_avaliable_for ) > 0 AND ! class_exists( 'RevSlider' ) ): ?>
					<span class="w-importer-option-note"> &mdash;
						<?php echo sprintf( __( '<a href="%s">install and activate</a> %s plugin if you want sliders to be imported', 'us' ), admin_url( 'admin.php?page=us-addons' ), '<strong>Slider Revolution</strong>' ) ?>
					</span>
				<?php endif; ?>
			</div>
			<div class="w-importer-option woocommerce">
				<label class="w-importer-option-check">
					<input id="woocommerce" type="checkbox" value="ON"
					       name="woocommerce"<?php if ( ! class_exists( 'woocommerce' ) ) {
						echo ' disabled="disabled"';
					} ?>>
					<span class="w-importer-option-title"><?php _e( 'Import WooCommerce Products', 'us' ) ?></span>
					<?php
					$woocommerce_avaliable_for = array();
					foreach ( $config as $name => $import ) {
						if ( isset( $import['woocommerce'] ) AND $import['woocommerce'] ) {
							$woocommerce_avaliable_for[ $name ] = $import['title'];
						}
					}
					?>
				</label>
				<?php if ( count( $woocommerce_avaliable_for ) > 0 AND ! class_exists( 'woocommerce' ) ): ?>
					<span class="w-importer-option-note"> &mdash;
						<?php echo sprintf( __( '<a href="%s">install and activate</a> %s plugin if you want products to be imported', 'us' ), admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ), '<strong>WooCommerce</strong>' ) ?>
					</span>
				<?php endif; ?>
			</div>

			<div class="w-importer-note">
				<strong><?php _e( 'Important Notes', 'us' ) ?>:</strong>
				<ol>
					<li><?php _e( 'We recommend to run Demo Import on a clean WordPress installation.', 'us' ) ?></li>
					<li><?php _e( 'To reset your installation we recommend <a href="http://wordpress.org/plugins/wordpress-database-reset/" target="_blank">Wordpress Database Reset</a> plugin.', 'us' ) ?></li>
					<li><?php _e( 'The Demo Import will not import the images we have used in our live demos, due to copyright / license reasons.', 'us' ) ?></li>
					<li><?php _e( 'Do not run the Demo Import multiple times one after another, it will result in double content.', 'us' ) ?></li>
				</ol>
			</div>

			<input type="hidden" name="action" value="perform_import">
			<input class="button-primary size_big" type="submit" value="<?php _e( 'Import', 'us' ) ?>" id="import_demo_data">

		</div>

	</form>
	<script>
		jQuery(function($){
			var import_running = false,
				slidersAvailableFor = <?php echo json_encode( array_keys( $sliders_avaliable_for ) ); ?>,
				woocommerceAvailableFor = <?php echo json_encode( array_keys( $woocommerce_avaliable_for ) ); ?>,
				sliderOptionState = false;

			$('.w-importer-item').click(function(){
				$('html, body').stop(true, false).animate({
					scrollTop: Math.floor($('.w-importer-options').offset().top) + 'px'
				}, {
					duration: 800
				});
			});

			$('.w-importer-item-preview').click(function(){
				var demoName = $(this).attr('for').substr(5);
				if ($('.w-importer-options').css('display') == 'none') {
					$('.w-importer-options').slideDown();
				}

				if ($.inArray(demoName, slidersAvailableFor) !== -1) {
					$('.w-importer-option.rev-slider').slideDown();
				} else {
					$('.w-importer-option.rev-slider').slideUp();
				}

				if ($.inArray(demoName, woocommerceAvailableFor) !== -1) {
					$('.w-importer-option.woocommerce').slideDown();
				} else {
					$('.w-importer-option.woocommerce').slideUp();
				}
			});

			$('.w-importer-option-check').click(function(){
				var demo = $('input[name=demo]:checked').val() || '<?php echo $default_demo; ?>',
					$button = $('#import_demo_data');

				if ($('#demo_content').is(':checked') || $('#theme_options').is(':checked') || ($('#rev_slider').is(':checked') && $.inArray(demo, slidersAvailableFor) !== -1) || ($('#woocommerce').is(':checked') && $.inArray(demo, woocommerceAvailableFor) !== -1)) {
					$button.removeClass('disabled');
				} else {
					$button.addClass('disabled');
				}
			});
			$('#import_demo_data').click(function(){
				if (import_running) return false;
				$("html, body").animate({
					scrollTop: 0
				}, {
					duration: 300
				});
				var demo = $('input[name=demo]:checked').val() || '<?php echo $default_demo; ?>',
					importQueue = [],
					processQueue = function(){
						if (importQueue.length != 0) {
							// Importing something
							var importAction = importQueue.shift();
							$.ajax({
								type: 'POST',
								url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
								data: {
									action: importAction,
									demo: demo
								},
								success: function(data){
									if (data.success) {
										processQueue();
									} else {
										$('.w-message.error .w-message-title').html(data.error_title);
										$('.w-message.error .w-message-text').html(data.error_description);
										$('.w-message.content, .w-message.options, .w-message.sliders').slideUp();
										$('.w-message.error').slideDown();
									}
								}
							});
						}
						else {
							// Import is completed
							$('.w-message.content, .w-message.options, .w-message.sliders').slideUp();
							$('.w-message.success').slideDown();
							import_running = false;
						}
					};
				if ($('#demo_content').is(':checked')) importQueue.push('us_demo_import_content');
				if ($('#theme_options').is(':checked')) importQueue.push('us_demo_import_options');
				if ($('#rev_slider').is(':checked') && $.inArray(demo, slidersAvailableFor) !== -1) importQueue.push('us_demo_import_sliders');
				if ($('#woocommerce').is(':checked') && $.inArray(demo, woocommerceAvailableFor) !== -1) importQueue.push('us_demo_import_woocommerce');

				if (importQueue.length == 0) return false;

				import_running = true;
				$('.w-importer').slideUp(null, function(){
					$('.w-message.content').slideDown();
				});

				processQueue();

				return false;
			});
		});
	</script>
	<?php
}

// Content Import
add_action( 'wp_ajax_us_demo_import_content', 'us_demo_import_content' );
function us_demo_import_content() {
	global $us_template_directory;
	$config = us_config( 'demo-import', array() );

	set_time_limit( 0 );

	if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
		define( 'WP_LOAD_IMPORTERS', TRUE );
	}

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	if ( ! file_exists( $us_template_directory . '/demo-import/' . $demo_version . '/content.xml' ) ) {
		wp_send_json( array(
			'success' => FALSE,
			'error_title' => __( 'Failed to import Demo Content', 'us' ),
			'error_description' => __( 'Wrong path to the XML file or file is missing.', 'us' ),
		) );
	}

	require_once( $us_template_directory . '/framework/vendor/wordpress-importer/wordpress-importer.php' );

	$wp_import = new WP_Import();
	$wp_import->fetch_attachments = TRUE;

	ob_start();
	$wp_import->import( $us_template_directory . '/demo-import/' . $demo_version . '/content.xml' );
	ob_end_clean();

	// Set menu
	if ( isset( $config[ $demo_version ]['nav_menu_locations'] ) ) {
		$locations = get_theme_mod( 'nav_menu_locations' );
		$menus = array();
		foreach ( wp_get_nav_menus() as $menu ) {
			if ( is_object( $menu ) ) {
				$menus[ $menu->name ] = $menu->term_id;
			}
		}
		foreach ( $config[ $demo_version ]['nav_menu_locations'] as $nav_location_key => $menu_name ) {
			if ( isset( $menus[ $menu_name ] ) ) {
				$locations[ $nav_location_key ] = $menus[ $menu_name ];
			}
		}

		set_theme_mod( 'nav_menu_locations', $locations );
	}

	// Set Front Page
	if ( isset( $config[ $demo_version ]['front_page'] ) ) {
		$front_page = get_page_by_title( $config[ $demo_version ]['front_page'] );

		if ( isset( $front_page->ID ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $front_page->ID );
		}
	}

	// Add Widgets
	if ( isset( $config[ $demo_version ]['sidebars'] ) ) {
		$widget_areas = get_option( 'us_widget_areas' );
		if ( empty( $widget_areas ) ) {
			$widget_areas = array();
		}

		$args = array(
			'description' => __( 'Custom Widget Area', 'us' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>',
			'class' => 'us-custom-area',
		);

		foreach ( $config[ $demo_version ]['sidebars'] as $id => $name ) {
			if ( ! isset( $widget_areas[$id] ) ) {
				$args['name'] = $name;
				$args['id'] = $id;
				register_sidebar( $args );

				$widget_areas[$id] = $name;
			}

		}

		update_option( 'us_widget_areas', $widget_areas );
	}

	if ( file_exists( $us_template_directory . '/demo-import/' . $demo_version . '/widgets.json' ) ) {
		ob_start();
		require_once( $us_template_directory . '/framework/vendor/widget-importer-exporter/import.php' );
		us_wie_process_import_file( $us_template_directory . '/demo-import/' . $demo_version . '/widgets.json' );
		ob_end_clean();
	}

	wp_send_json_success();
}

// WooCommerce Import
add_action( 'wp_ajax_us_demo_import_woocommerce', 'us_demo_import_woocommerce' );
function us_demo_import_woocommerce() {
	global $us_template_directory;
	$config = us_config( 'demo-import', array() );

	set_time_limit( 0 );

	if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
		define( 'WP_LOAD_IMPORTERS', TRUE );
	}

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	if ( ! file_exists( $us_template_directory . '/demo-import/' . $demo_version . '/woocommerce.xml' ) ) {
		wp_send_json( array(
			'success' => FALSE,
			'error_title' => __( 'Failed to import Demo Content', 'us' ),
			'error_description' => __( 'Wrong path to the XML file or file is missing.', 'us' ),
		) );
	}

	require_once( $us_template_directory . '/framework/vendor/wordpress-importer/wordpress-importer.php' );

	$wp_import = new WP_Import();
	$wp_import->fetch_attachments = TRUE;

	// Creating attributes taxonomies
	global $wpdb;
	$parser      = new WXR_Parser();
	$import_data = $parser->parse( $us_template_directory . '/demo-import/' . $demo_version . '/woocommerce.xml' );

	if ( isset( $import_data['posts'] ) ) {

		$posts = $import_data['posts'];

		if ( $posts && sizeof( $posts ) > 0 ) {
			foreach ( $posts as $post ) {
				if ( 'product' === $post['post_type'] ) {
					if ( ! empty( $post['terms'] ) ) {
						foreach ( $post['terms'] as $term ) {
							if ( strstr( $term['domain'], 'pa_' ) ) {
								if ( ! taxonomy_exists( $term['domain'] ) ) {
									$attribute_name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $term['domain'] ) );

									// Create the taxonomy
									if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies() ) ) {
										$attribute = array(
											'attribute_label'   => $attribute_name,
											'attribute_name'    => $attribute_name,
											'attribute_type'    => 'select',
											'attribute_orderby' => 'menu_order',
											'attribute_public'  => 0
										);
										$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
										delete_transient( 'wc_attribute_taxonomies' );
									}

									// Register the taxonomy now so that the import works!
									register_taxonomy(
										$term['domain'],
										apply_filters( 'woocommerce_taxonomy_objects_' . $term['domain'], array( 'product' ) ),
										apply_filters( 'woocommerce_taxonomy_args_' . $term['domain'], array(
											'hierarchical' => true,
											'show_ui'      => false,
											'query_var'    => true,
											'rewrite'      => false,
										) )
									);
								}
							}
						}
					}
				}
			}
		}
	}

	ob_start();
	$wp_import->import( $us_template_directory . '/demo-import/' . $demo_version . '/woocommerce.xml' );
	ob_end_clean();

	// Set WooCommerce Pages
	$shop_page = get_page_by_title( 'Shop' );
	if ( isset( $shop_page->ID ) ) {
		update_option( 'woocommerce_shop_page_id', $shop_page->ID );
	}
	$cart_page = get_page_by_title( 'Cart' );
	if ( isset( $cart_page->ID ) ) {
		update_option( 'woocommerce_cart_page_id', $cart_page->ID );
	}
	$checkout_page = get_page_by_title( 'Checkout' );
	if ( isset( $checkout_page->ID ) ) {
		update_option( 'woocommerce_checkout_page_id', $checkout_page->ID );
	}
	$my_account_page = get_page_by_title( 'My Account' );
	if ( isset( $my_account_page->ID ) ) {
		update_option( 'woocommerce_myaccount_page_id', $my_account_page->ID );
	}

	wp_send_json_success();
}

//Import Options
add_action( 'wp_ajax_us_demo_import_options', 'us_demo_import_options' );
function us_demo_import_options() {
	global $us_template_directory;
	$config = us_config( 'demo-import', array() );

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	if ( ! file_exists( $us_template_directory . '/demo-import/' . $demo_version . '/theme-options.json' ) ) {
		wp_send_json( array(
			'success' => FALSE,
			'error_title' => __( 'Failed to import Theme Options', 'us' ),
			'error_description' => __( 'Wrong path to the JSON file or file is missing.', 'us' ),
		) );
	}
	$updated_options = json_decode( file_get_contents( $us_template_directory . '/demo-import/' . $demo_version . '/theme-options.json' ), TRUE );

	if ( ! is_array( $updated_options ) ) {
		// Wrong file configuration
		wp_send_json( array(
			'success' => FALSE,
			'error_title' => __( 'Failed to import Theme Options', 'us' ),
			'error_description' => __( 'Wrong file format of Theme Options data.', 'us' ),
		) );
	}

	usof_save_options( $updated_options );

	wp_send_json_success();
}

//Import Slider
add_action( 'wp_ajax_us_demo_import_sliders', 'us_demo_import_sliders' );
function us_demo_import_sliders() {
	global $us_template_directory;
	$config = us_config( 'demo-import', array() );

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	if ( ! class_exists( 'RevSlider' ) OR ! isset( $config[ $demo_version ]['sliders'] ) OR empty( $config[ $demo_version ]['sliders'] ) ) {
		wp_send_json( array(
			'success' => FALSE,
			'error_title' => __( 'Failed to import Revolution Sliders', 'us' ),
			'error_description' => __( 'Incorrect Demo Import configuration.', 'us' ),
		) );
	}

	ob_start();
	foreach ( $config[ $demo_version ]['sliders'] as $slider ) {
		echo $slider;
		$_FILES["import_file"]["tmp_name"] = $us_template_directory . '/demo-import/' . $demo_version . '/' . $slider;
		$slider = new RevSlider();
		$response = $slider->importSliderFromPost();
		unset( $slider );
	}
	ob_end_clean();

	wp_send_json_success();
}

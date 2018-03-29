<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/********* ePanel v.3.2 ************/

/* Admin scripts + ajax jquery code */
if ( ! function_exists( 'et_epanel_admin_js' ) ) {

	function et_epanel_admin_js(){
		global $themename;

		$epanel_jsfolder = get_template_directory_uri() . '/epanel/js';

		et_core_load_main_fonts();

		wp_register_script( 'epanel_colorpicker', $epanel_jsfolder . '/colorpicker.js', array(), et_get_theme_version() );
		wp_register_script( 'epanel_eye', $epanel_jsfolder . '/eye.js', array(), et_get_theme_version() );
		wp_register_script( 'epanel_checkbox', $epanel_jsfolder . '/checkbox.js', array(), et_get_theme_version() );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );

		$wp_color_picker_alpha_uri = defined( 'ET_BUILDER_URI' ) ? ET_BUILDER_URI . '/scripts/ext/wp-color-picker-alpha.min.js' : $epanel_jsfolder . '/wp-color-picker-alpha.min.js';

		wp_enqueue_script( 'wp-color-picker-alpha', $wp_color_picker_alpha_uri, array( 'jquery', 'wp-color-picker' ), et_get_theme_version(), true );

		wp_enqueue_script( 'epanel_functions_init', $epanel_jsfolder . '/functions-init.js', array( 'jquery', 'jquery-ui-tabs', 'jquery-form', 'epanel_colorpicker', 'epanel_eye', 'epanel_checkbox', 'wp-color-picker-alpha' ), et_get_theme_version() );
		wp_localize_script( 'epanel_functions_init', 'ePanelSettings', array(
			'clearpath'      => get_template_directory_uri() . '/epanel/images/empty.png',
			'epanel_nonce'   => wp_create_nonce( 'epanel_nonce' ),
			'help_label'     => esc_html__( 'Help', $themename ),
			'et_core_nonces' => et_core_get_nonces(),
		) );
	}

}
/* --------------------------------------------- */

/* Adds additional ePanel css */
if ( ! function_exists( 'et_epanel_css_admin' ) ) {

	function et_epanel_css_admin() {
		?>

		<?php do_action( 'et_epanel_css_admin_enqueue' ); ?>

		<!--[if IE 7]>
		<style type="text/css">
			#epanel-save, #epanel-reset { font-size: 0px; display:block; line-height: 0px; bottom: 18px;}
			.box-desc { width: 414px; }
			.box-desc-content { width: 340px; }
			.box-desc-bottom { height: 26px; }
			#epanel-content .epanel-box input, #epanel-content .epanel-box select, .epanel-box textarea {  width: 395px; }
			#epanel-content .epanel-box select { width:434px !important;}
			#epanel-content .epanel-box .box-content { padding: 8px 17px 15px 16px; }
		</style>
		<![endif]-->
		<!--[if IE 8]>
		<style type="text/css">
			#epanel-save, #epanel-reset { font-size: 0px; display:block; line-height: 0px; bottom: 18px;}
		</style>
		<![endif]-->
	<?php }

}

if ( ! function_exists( 'et_epanel_css_admin_style' ) ) {
	function et_epanel_css_admin_style() {
		wp_add_inline_style( 'epanel-style', '.lightboxclose { background: url("' . esc_url( get_template_directory_uri() ) . '/epanel/images/description-close.png") no-repeat; width: 19px; height: 20px; }' );
	}
	add_action( 'et_epanel_css_admin_enqueue', 'et_epanel_css_admin_style' );
}

if ( ! function_exists( 'et_epanel_admin_scripts' ) ) {
	function et_epanel_admin_scripts( $hook ) {
		$current_screen = get_current_screen();
		$is_divi        = ( 'toplevel_page_et_divi_options' === $current_screen->id );

		if ( ! wp_style_is( 'et-core-admin', 'enqueued' ) ) {
			wp_enqueue_style( 'et-core-admin-epanel', get_template_directory_uri() . '/core/admin/css/core.css', array(), et_get_theme_version() );
		}

		wp_enqueue_style( 'epanel-style', get_template_directory_uri() . '/epanel/css/panel.css', array(), et_get_theme_version() );

		// ePanel on theme others than Divi might want to add specific styling
		if ( ! apply_filters( 'et_epanel_is_divi', $is_divi ) ) {
			wp_enqueue_style( 'epanel-theme-style', apply_filters( 'et_epanel_style_url', get_template_directory_uri() . '/style-epanel.css'), array( 'epanel-style' ), et_get_theme_version() );
		}
	}
}

if ( ! function_exists( 'et_epanel_hook_scripts' ) ) {
	function et_epanel_hook_scripts() {
		add_action( 'admin_enqueue_scripts', 'et_epanel_admin_scripts' );
	}
}

/* --------------------------------------------- */

/* Save/Reset actions | Adds theme options to WP-Admin menu */
add_action( 'admin_menu', 'et_add_epanel' );

function et_add_epanel() {
	global $themename, $shortname, $options;
	$epanel = basename( __FILE__ );

	if ( isset( $_GET['page'] ) && $_GET['page'] == $epanel && isset( $_POST['action'] ) ) {
		if (
			( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'epanel_nonce' ) )
			||
			( 'reset' === $_POST['action'] && isset( $_POST['_wpnonce_reset'] ) && wp_verify_nonce( $_POST['_wpnonce_reset'], 'et-nojs-reset_epanel' ) )
		) {
			if ( ! isset( $GLOBALS['et_core_updates'] ) ) {
				et_register_updates_component();
			}
			epanel_save_data( 'js_disabled' ); //saves data when javascript is disabled
		}
	}

	$core_page = add_theme_page( $themename . ' ' . esc_html__( 'Options', $themename ), $themename . ' ' . esc_html__( 'Theme Options', $themename ), 'switch_themes', basename( __FILE__ ), 'et_build_epanel' );

	add_action( "admin_print_scripts-{$core_page}", 'et_epanel_admin_js' );
	add_action( "admin_head-{$core_page}", 'et_epanel_css_admin' );
	add_action( "load-{$core_page}", 'et_epanel_hook_scripts' );
}

/* --------------------------------------------- */

/* Displays ePanel */
if ( ! function_exists( 'et_build_epanel' ) ) {

	function et_build_epanel() {
		global $themename, $shortname, $options, $et_disabled_jquery, $epanelMainTabs;

		// load theme settings array
		et_load_core_options();

		$tabs              = array();
		$default_tab_names = array(
			'ad'           => _x( 'Ads', 'site ads placement areas', $themename ),
			'colorization' => _x( 'Colorization', 'site color scheme', $themename ),
			'general'      => _x( 'General', 'general options', $themename ),
			'integration'  => _x( 'Integration', 'integrate third-party code', $themename ),
			'layout'       => _x( 'Layout', 'page/post', $themename ),
			'navigation'   => _x( 'Navigation', 'navigation menu', $themename ),
			'seo'          => _x( 'SEO', 'search engine optimization', $themename ),
			'support'      => _x( 'Support', 'documentation links', $themename ),
			'updates'      => _x( 'Updates', 'theme updates', $themename ),
		);

		/**
		 * Filters the data used to construct ePanel's layout.
		 *
		 * @since 3.2.1
		 *
		 * @param array $options
		 */
		$options = apply_filters( 'et_epanel_layout_data', $options );

		/**
		 * Filters the slugs/ids for ePanel's tabs.
		 *
		 * @deprecated
		 *
		 * @since 1.0
		 * @since 3.2.1 Deprecated
		 *
		 * @param string[] $tab_slugs
		 */
		$epanelMainTabs = apply_filters( 'epanel_page_maintabs', $epanelMainTabs );


		foreach( $epanelMainTabs as $tab_slug ) {
			if ( isset( $default_tab_names[ $tab_slug ] ) ) {
				$tabs[ $tab_slug ] = $default_tab_names[ $tab_slug ];
			}
		}

		/**
		 * Filters ePanel's localized tab names.
		 *
		 * @since 3.2.1
		 *
		 * @param string[] $tabs {
		 *
		 *     @type string $tab_slug Localized tab name.
		 *     ...
		 * }
		 */
		$tabs = apply_filters( 'et_epanel_tab_names', $tabs );


		if ( isset($_GET['saved']) ) {
			if ( $_GET['saved'] ) echo '<div id="message" class="updated fade"><p><strong>' . esc_html( $themename ) . ' ' . esc_html__( 'settings saved.', $themename ) . '</strong></p></div>';
		}
		if ( isset($_GET['reset']) ) {
			if ( $_GET['reset'] ) echo '<div id="message" class="updated fade"><p><strong>' . esc_html( $themename ) . ' ' . esc_html__( 'settings reset.', $themename ) . '</strong></p></div>';
		}
	?>

		<div id="wrapper">
		  <div id="panel-wrap">


			<div id="epanel-top">
				<button class="save-button" id="epanel-save-top"><?php esc_html_e( 'Save Changes', $themename ); ?></button>
			</div>

			<form method="post" id="main_options_form" enctype="multipart/form-data">
				<div id="epanel-wrapper">
					<div id="epanel" class="onload">
						<div id="epanel-content-wrap">
							<div id="epanel-content">
								<div id="epanel-header">
									<h1 id="epanel-title"><?php printf( esc_html__( '%s Theme Options', $themename ), $themename ); ?></h1>
									<a href="#" class="defaults-button epanel-reset" title="<?php esc_attr_e( 'Reset to Defaults', $themename ); ?>"><span class="label"><?php esc_html_e( 'Reset to Defaults', $themename ); ?></span></a>
									<?php echo et_core_portability_link( 'epanel', array( 'class' => 'defaults-button epanel-portability' ) ); ?>
								</div>
								<ul id="epanel-mainmenu">
									<?php
										foreach ( $tabs as $tab_slug => $tab_name ) {
											if ( 'ad' === $tab_slug ) {
												$tab_slug = 'advertisements';
											}

											printf( '<li><a href="#wrap-%1$s">%2$s</a></li>', esc_attr( $tab_slug ), esc_html( $tab_name ) );
										}

										do_action( 'epanel_render_maintabs', $epanelMainTabs );
									?>
								</ul><!-- end epanel mainmenu -->

								<?php
								foreach ($options as $value) {
									if ( ! isset( $value['type'] ) ) {
										continue;
									}

									if ( ! empty( $value[ 'depends_on' ] ) ) {
										// function defined in 'depends on' key returns false, if a setting shouldn't be displayed
										if ( ! call_user_func( $value[ 'depends_on' ] ) ) {
											continue;
										}
									}

									if ( ! empty( $value['id'] ) ) {
										$is_new_global_setting    = false;
										$global_setting_main_name = $global_setting_sub_name = '';

										if ( isset( $value['is_global'] ) && $value['is_global'] ) {
											$is_new_global_setting    = true;
											$global_setting_main_name = isset( $value['main_setting_name'] ) ? sanitize_text_field( $value['main_setting_name'] ) : '';
											$global_setting_sub_name  = isset( $value['sub_setting_name'] ) ? sanitize_text_field( $value['sub_setting_name'] ) : '';
										}
									}

									if ( in_array( $value['type'], array( 'text', 'textlimit', 'textarea', 'select', 'checkboxes', 'different_checkboxes', 'colorpicker', 'textcolorpopup', 'upload', 'callback_function', 'et_color_palette', 'password' ) ) ) { ?>
											<div class="epanel-box">
												<div class="box-title">
													<h3><?php echo esc_html( $value['name'] ); ?></h3>
													<div class="box-descr">
														<p><?php
														echo wp_kses( $value['desc'],
															array(
																'a' => array(
																	'href'   => array(),
																	'title'  => array(),
																	'target' => array(),
																),
															)
														);
														?></p>
													</div> <!-- end box-desc-content div -->
												</div> <!-- end div box-title -->

												<div class="box-content">

													<?php if ( in_array( $value['type'], array( 'text', 'password' ) ) ) { ?>

														<?php
															$et_input_value = '';
															$et_input_value = ( '' != et_get_option( $value['id'], '', '', false, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name ) ) ? et_get_option( $value['id'], '', '', false, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name ) : $value['std'];
															$et_input_value = stripslashes( $et_input_value );
														?>

														<input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" type="<?php echo esc_attr( $value['type'] ); ?>" value="<?php echo esc_attr( $et_input_value ); ?>" />

													<?php } elseif ( 'textlimit' == $value['type'] ) { ?>

														<?php
															$et_input_value = '';
															$et_input_value = ( '' != et_get_option( $value['id'], '', '', false, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name ) ) ? et_get_option( $value['id'], '', '', false, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name ) : $value['std'];
															$et_input_value = stripslashes( $et_input_value );
														?>

														<input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" type="text" maxlength="<?php echo esc_attr( $value['max'] ); ?>" size="<?php echo esc_attr( $value['max'] ); ?>" value="<?php echo esc_attr( $et_input_value ); ?>" />

													<?php } elseif ( 'colorpicker' == $value['type'] ) { ?>

														<div id="colorpickerHolder"></div>

													<?php } elseif ( 'textcolorpopup' == $value['type'] ) { ?>

														<?php
															$et_input_value = '';
															$et_input_value = ( '' != et_get_option( $value['id'], '', '', false, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name ) ) ? et_get_option( $value['id'], '', '', false, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name ) : $value['std'];
														?>

														<input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" class="colorpopup" type="text" value="<?php echo esc_attr( $et_input_value ); ?>" />

													<?php } elseif ( 'textarea' == $value['type'] ) { ?>

														<?php
															// get the custom css value from WP custom CSS option if supported
															if ( ( $shortname . '_custom_css' ) === $value['id'] && function_exists( 'wp_get_custom_css') ) {
																$et_textarea_value = wp_get_custom_css();
																$et_textarea_value = strip_tags( $et_textarea_value );
															} else {
																$et_textarea_value = '';
																$et_textarea_value = ( '' != et_get_option( $value['id'], '', '', false, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name ) ) ? et_get_option( $value['id'], '', '', false, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name ) : $value['std'];
																$et_textarea_value = stripslashes( $et_textarea_value );
															}
														?>

														<textarea name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_textarea( $et_textarea_value ); ?></textarea>

													<?php } elseif ( 'upload' == $value['type'] ) { ?>

													<?php
														$et_upload_button_data = isset( $value['button_text'] ) ? sprintf( ' data-button_text="%1$s"', esc_attr( $value['button_text'] ) ) : '';
													?>

														<input id="<?php echo esc_attr( $value['id'] ); ?>" class="uploadfield" type="text" size="90" name="<?php echo esc_attr( $value['id'] ); ?>" value="<?php echo esc_url( et_get_option( $value['id'], '', '', false, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name ) ); ?>" />
														<div class="upload_buttons">
															<span class="upload_image_reset"><?php esc_html_e( 'Reset', $themename ); ?></span>
															<input class="upload_image_button" type="button"<?php echo $et_upload_button_data; ?> value="<?php esc_attr_e( 'Upload', $themename ); ?>" />
														</div>

														<div class="clear"></div>

													<?php } elseif ( 'select' == $value['type'] ) { ?>

														<select name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>">
															<?php foreach ( $value['options'] as $option_key => $option ) { ?>
																<?php
																	$et_select_active = '';
																	$et_use_option_values = ( isset( $value['et_array_for'] ) && in_array( $value['et_array_for'], array( 'pages', 'categories' ) ) ) ||
																	( isset( $value['et_save_values'] ) && $value['et_save_values'] ) ? true : false;

																	$et_option_db_value = et_get_option( $value['id'] );

																	if ( ( $et_use_option_values && ( $et_option_db_value == $option_key ) ) || ( stripslashes( $et_option_db_value ) == trim( stripslashes( $option ) ) ) || ( ! $et_option_db_value && isset( $value['std'] ) && stripslashes( $option ) == stripslashes( $value['std'] ) ) )
																		$et_select_active = ' selected="selected"';
																?>
																<option<?php if ( $et_use_option_values ) echo ' value="' . esc_attr( $option_key ) . '"'; ?> <?php echo $et_select_active; ?>><?php echo esc_html( trim( $option ) ); ?></option>
															<?php } ?>
														</select>

													<?php } elseif ( 'checkboxes' == $value['type'] ) { ?>

														<?php
														if ( empty( $value['options'] ) ) {
															esc_html_e( "You don't have pages", $themename );
														} else {
															$i = 1;
															$className = 'inputs';
															if ( isset( $value['excludeDefault'] ) && $value['excludeDefault'] == 'true' ) $className .= ' different';

															foreach ( $value['options'] as $option ) {
																$checked = "";
																$class_name_last = 0 == $i % 3 ? ' last' : '';

																if ( et_get_option( $value['id'] ) ) {
																	if ( in_array( $option, et_get_option( $value['id'] ) ) ) {
																		$checked = "checked=\"checked\"";
																	}
																}

																$et_checkboxes_label = $value['id'] . '-' . $option;
																if ( 'custom' == $value['usefor'] ) {
																	$et_helper = (array) $value['helper'];
																	$et_checkboxes_value = $et_helper[$option];
																} else {
																	if ( 'taxonomy_terms' == $value['usefor'] && isset( $value['taxonomy_name'] ) ) {
																		$et_checkboxes_term = get_term_by( 'id', $option, $value['taxonomy_name'] );
																		$et_checkboxes_value = sanitize_text_field( $et_checkboxes_term->name );
																	} else {
																		$et_checkboxes_value = ( 'pages' == $value['usefor'] ) ? get_pagename( $option ) : get_categname( $option );
																	}
																}
																?>

																<p class="<?php echo esc_attr( $className . $class_name_last ); ?>">
																	<input type="checkbox" class="usual-checkbox" name="<?php echo esc_attr( $value['id'] ); ?>[]" id="<?php echo esc_attr( $et_checkboxes_label ); ?>" value="<?php echo esc_attr( $option ); ?>" <?php echo esc_html( $checked ); ?> />
																	<label for="<?php echo esc_attr( $et_checkboxes_label ); ?>"><?php echo esc_html( $et_checkboxes_value ); ?></label>
																</p>

																<?php $i++;
															}
														}
														?>
														<br class="clearfix"/>

													<?php } elseif ( 'different_checkboxes' == $value['type'] ) { ?>

														<?php
														foreach ( $value['options'] as $option ) {
															$checked = '';
															if ( et_get_option( $value['id'] ) !== false ) {
																if ( in_array( $option, et_get_option( $value['id'] ) ) ) $checked = "checked=\"checked\"";
															} elseif ( isset( $value['std'] ) ) {
																if ( in_array( $option, $value['std'] ) ) {
																	$checked = "checked=\"checked\"";
																}
															} ?>

															<p class="postinfo <?php echo esc_attr( 'postinfo-' . $option ); ?>">
																<input type="checkbox" class="usual-checkbox" name="<?php echo esc_attr( $value['id'] ); ?>[]" id="<?php echo esc_attr( $value['id'] . '-' . $option ); ?>" value="<?php echo esc_attr( $option ); ?>" <?php echo esc_html( $checked ); ?> />
															</p>
														<?php } ?>
														<br class="clearfix"/>

													<?php } elseif ( 'callback_function' == $value['type'] ) {

														call_user_func( $value['function_name'] ); ?>

													<?php } elseif ( 'et_color_palette' == $value['type'] ) {
															$items_amount = isset( $value['items_amount'] ) ? $value['items_amount'] : 1;
															$et_input_value = '' !== str_replace( '|', '', et_get_option( $value['id'], '', '', false, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name ) ) ? et_get_option( $value['id'], '', '', false, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name ) : $value['std'];
														?>
															<div class="et_pb_colorpalette_overview">
														<?php
															for ( $colorpalette_index = 1; $colorpalette_index <= $items_amount; $colorpalette_index++ ) { ?>
																<span class="colorpalette-item colorpalette-item-<?php echo esc_attr( $colorpalette_index ); ?>" data-index="<?php echo esc_attr( $colorpalette_index ); ?>"></span>
														<?php } ?>

															</div>

														<?php for ( $colorpicker_index = 1; $colorpicker_index <= $items_amount; $colorpicker_index++ ) { ?>
																<div class="colorpalette-colorpicker" data-index="<?php echo esc_attr( $colorpicker_index ); ?>">
																	<input data-index="<?php echo esc_attr( $colorpicker_index ); ?>" type="text" class="input-colorpalette-colorpicker" data-alpha="true" />
																</div>
														<?php } ?>

														<input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" class="et_color_palette_main_input" type="hidden" value="<?php echo esc_attr( $et_input_value ); ?>" />

													<?php } ?>

												</div> <!-- end box-content div -->
												<span class="box-description"></span>
											</div> <!-- end epanel-box div -->

									<?php } elseif ( 'checkbox' == $value['type'] || 'checkbox2' == $value['type'] ) { ?>
										<?php
											$et_box_class = 'checkbox' == $value['type'] ? 'epanel-box-small-1' : 'epanel-box-small-2';
										?>
										<div class="<?php echo esc_attr( 'epanel-box ' . $et_box_class ); ?>">
											<div class="box-title"><h3><?php echo esc_html( $value['name'] ); ?></h3>
												<div class="box-descr">
													<p><?php
													echo wp_kses( $value['desc'],  array(
														'a' => array(
															'href'   => array(),
															'title'  => array(),
															'target' => array(),
														),
													) );
													?></p>
												</div> <!-- end box-desc-content div -->
											</div> <!-- end div box-title -->
											<div class="box-content">
												<?php
													$checked = '';
												if ( $is_new_global_setting && isset( $value['main_setting_name'] ) && isset( $value['sub_setting_name'] ) ) {
													$saved_checkbox = et_get_option( $value['id'], '', '', false, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name );
													$checked = ( 'on' === $saved_checkbox || (!$saved_checkbox && 'on' === $value['std']) ) ?
														'checked="checked"' : '';
												} else if ( '' != et_get_option( $value['id'] ) ) {
													if ( 'on' == et_get_option( $value['id'] ) ) {
														$checked = 'checked="checked"';
													} else {
														$checked = '';
													}
												} else if ( 'on' == $value['std'] ) {
													$checked = 'checked="checked"';
												}
												?>
												<input type="checkbox" class="checkbox yes_no_button" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] );?>" <?php echo $checked; ?> />

											</div> <!-- end box-content div -->
											<?php if ( 'et_pb_static_css_file' === $value['id'] ) { ?>
												<span class="button"><?php echo esc_html_x( 'Clear', 'clear static resources', $themename ); ?></span>
											<?php } ?>
											<span class="box-description"></span>
										</div> <!-- end epanel-box-small div -->

									<?php } elseif ( 'support' == $value['type'] ) { ?>

										<div class="inner-content">
											<?php include get_template_directory() . "/includes/functions/" . $value['name'] . ".php"; ?>
										</div>

									<?php } elseif ( 'contenttab-wrapstart' == $value['type'] || 'subcontent-start' == $value['type'] ) { ?>

										<?php $et_contenttab_class = 'contenttab-wrapstart' == $value['type'] ? 'content-div' : 'tab-content'; ?>

										<div id="<?php echo esc_attr( $value['name'] ); ?>" class="<?php echo esc_attr( $et_contenttab_class ); ?>">

									<?php } elseif ( 'contenttab-wrapend' == $value['type'] || 'subcontent-end' == $value['type'] ) { ?>

										</div> <!-- end <?php echo esc_html( $value['name'] ); ?> div -->

									<?php } elseif ( 'subnavtab-start' == $value['type'] ) { ?>

										<ul class="idTabs">

									<?php } elseif ( 'subnavtab-end' == $value['type'] ) { ?>

										</ul>

									<?php } elseif ( 'subnav-tab' == $value['type'] ) { ?>

										<li><a href="#<?php echo esc_attr( $value['name'] ); ?>"><span class="pngfix"><?php echo esc_html( $value['desc'] ); ?></span></a></li>

									<?php } elseif ($value['type'] == "clearfix") { ?>

										<div class="clearfix"></div>

									<?php } ?>

								<?php } //end foreach ($options as $value) ?>

							</div> <!-- end epanel-content div -->
						</div> <!-- end epanel-content-wrap div -->
					</div> <!-- end epanel div -->
				</div> <!-- end epanel-wrapper div -->

				<div id="epanel-bottom">
					<?php wp_nonce_field( 'epanel_nonce' ); ?>
					<button class="save-button" name="save" id="epanel-save"><?php esc_html_e( 'Save Changes', $themename ); ?></button>

					<input type="hidden" name="action" value="save_epanel" />
				</div><!-- end epanel-bottom div -->

			</form>

			<div class="reset-popup-overlay">
				<div class="defaults-hover">
					<div class="reset-popup-header"><?php esc_html_e( 'Reset', $themename ); ?></div>
					<?php _e( et_get_safe_localization( 'This will return all of the settings throughout the options page to their default values. <strong>Are you sure you want to do this?</strong>' ), $themename ); ?>
					<div class="clearfix"></div>
					<form method="post">
						<?php wp_nonce_field( 'et-nojs-reset_epanel', '_wpnonce_reset' ); ?>
						<input name="reset" type="submit" value="<?php esc_attr_e( 'Yes', $themename ); ?>" id="epanel-reset" />
						<input type="hidden" name="action" value="reset" />
					</form>
					<span class="no"><?php esc_html_e( 'No', $themename ); ?></span>
				</div>
			</div>

			</div> <!-- end panel-wrap div -->
		</div> <!-- end wrapper div -->

		<div id="epanel-ajax-saving">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/core/admin/images/ajax-loader.gif' ); ?>" alt="loading" id="loading" />
		</div>

		<script type="text/template" id="epanel-yes-no-button-template">
		<div class="et_pb_yes_no_button_wrapper">
			<div class="et_pb_yes_no_button"><!-- .et_pb_on_state || .et_pb_off_state -->
				<span class="et_pb_value_text et_pb_on_value"><?php esc_html_e( 'Enabled', $themename ); ?></span>
				<span class="et_pb_button_slider"></span>
				<span class="et_pb_value_text et_pb_off_value"><?php esc_html_e( 'Disabled', $themename ); ?></span>
			</div>
		</div>
		</script>

		<style type="text/css">
			#epanel p.postinfo-author .mark:after {
				content: '<?php esc_html_e( "Author", $themename ); ?>';
			}

			#epanel p.postinfo-date .mark:after {
				content: '<?php esc_html_e( "Date", $themename ); ?>';
			}

			#epanel p.postinfo-categories .mark:after {
				content: '<?php esc_html_e( "Categories", $themename ); ?>';
			}

			#epanel p.postinfo-comments .mark:after {
				content: '<?php esc_html_e( "Comments", $themename ); ?>';
			}

			#epanel p.postinfo-rating_stars .mark:after {
				content: '<?php esc_html_e( "Ratings", $themename ); ?>';
			}
		</style>

	<?php
	}

}
/* --------------------------------------------- */

add_action( 'wp_ajax_save_epanel', 'et_epanel_save_callback' );

function et_epanel_save_callback() {
	check_ajax_referer( 'epanel_nonce' );
	epanel_save_data( 'ajax' );

	die();
}

if ( ! function_exists( 'epanel_save_data' ) ) {

	function epanel_save_data( $source ){
		global $options, $shortname;

		if ( ! current_user_can( 'switch_themes' ) ) {
			die('-1');
		}

		if ( defined( 'ET_BUILDER_DIR' ) && file_exists( ET_BUILDER_DIR . 'class-et-builder-settings.php' ) ) {
			require_once ET_BUILDER_DIR . 'class-et-builder-settings.php';
			et_builder_settings_init();
		}

		// load theme settings array
		et_load_core_options();

		/** This filter is documented in {@see et_build_epanel()} */
		$options = apply_filters( 'et_epanel_layout_data', $options );

		if ( isset($_POST['action']) ) {
			do_action( 'et_epanel_changing_options' );

			$epanel = isset( $_GET['page'] ) ? $_GET['page'] : basename( __FILE__ );
			$redirect_url = esc_url_raw( add_query_arg( 'page', $epanel, admin_url( 'admin.php' ) ) );

			if ( 'save_epanel' == $_POST['action'] ) {
				if ( 'ajax' != $source ) check_admin_referer( 'epanel_nonce' );

				foreach ( $options as $value ) {
					$et_option_name   = $et_option_new_value = false;
					$is_builder_field = isset( $value['is_builder_field'] ) && $value['is_builder_field'];

					if ( isset( $value['id'] ) ) {
						$et_option_name = $value['id'];

						if ( isset( $_POST[ $value['id'] ] ) ) {
							if ( in_array( $value['type'], array( 'text', 'textlimit', 'password' ) ) ) {

								if ( isset( $value['validation_type'] ) ) {
									// saves the value as integer
									if ( 'number' == $value['validation_type'] ) {
										$et_option_new_value = intval( stripslashes( $_POST[$value['id']] ) );
									}

									// makes sure the option is a url
									if ( 'url' == $value['validation_type'] ) {
										$et_option_new_value = esc_url_raw( stripslashes( $_POST[ $value['id'] ] ) );
									}

									// option is a date format
									if ( 'date_format' == $value['validation_type'] ) {
										$et_option_new_value = sanitize_option( 'date_format', $_POST[ $value['id'] ] );
									}

									/*
									 * html is not allowed
									 * wp_strip_all_tags can't be used here, because it returns trimmed text, some options need spaces ( e.g 'character to separate BlogName and Post title' option )
									 */
									if ( 'nohtml' == $value['validation_type'] ) {
										$et_option_new_value = stripslashes( wp_filter_nohtml_kses( $_POST[$value['id']] ) );
									}
								} else {
									// use html allowed for posts if the validation type isn't provided
									$et_option_new_value = wp_kses_post( stripslashes( $_POST[ $value['id'] ] ) );
								}

							} elseif ( 'select' == $value['type'] ) {

								// select boxes that list pages / categories should save page/category ID ( as integer )
								if ( isset( $value['et_array_for'] ) && in_array( $value['et_array_for'], array( 'pages', 'categories' ) ) ) {
									$et_option_new_value = intval( stripslashes( $_POST[$value['id']] ) );
								} else { // html is not allowed in select boxes
									$et_option_new_value = sanitize_text_field( stripslashes( $_POST[$value['id']] ) );
								}

							} elseif ( in_array( $value['type'], array( 'checkbox', 'checkbox2' ) ) ) {

								// saves 'on' value to the database, if the option is enabled
								$et_option_new_value = 'on';

							} elseif ( 'upload' == $value['type'] ) {

								// makes sure the option is a url
								$et_option_new_value = esc_url_raw( stripslashes( $_POST[ $value['id'] ] ) );

							} elseif ( in_array( $value['type'], array( 'textcolorpopup', 'et_color_palette' ) ) ) {

								// the color value
								$et_option_new_value = sanitize_text_field( stripslashes( $_POST[$value['id']] ) );

							} elseif ( 'textarea' == $value['type'] ) {

								if ( isset( $value['validation_type'] ) ) {
									// html is not allowed
									if ( 'nohtml' == $value['validation_type'] ) {
										if ( $value['id'] === ( $shortname . '_custom_css' ) ) {
											// save custom css into wp custom css option if supported
											// fallback to legacy system otherwise
											if ( function_exists( 'wp_update_custom_css_post' ) ) {
												// Data sent via AJAX is automatically escaped by browser, thus it needs
												// to be unslashed befor being saved into custom CSS post
												wp_update_custom_css_post( wp_unslash( wp_strip_all_tags( $_POST[ $value['id'] ] ) ) );
											} else {
												// don't strip slashes from custom css, it should be possible to use \ for icon fonts
												$et_option_new_value = wp_strip_all_tags( $_POST[ $value['id'] ] );
											}
										} else {
											$et_option_new_value = wp_strip_all_tags( stripslashes( $_POST[ $value['id'] ] ) );
										}
									}
								} else {
									if ( current_user_can( 'switch_themes' ) ) {
										$et_option_new_value = stripslashes( $_POST[ $value['id'] ] );
									} else {
										$et_option_new_value = stripslashes( wp_filter_post_kses( addslashes( $_POST[ $value['id'] ] ) ) ); // wp_filter_post_kses() expects slashed value
									}
								}

							} elseif ( 'checkboxes' == $value['type'] ) {

								if ( 'sanitize_text_field' == $value['value_sanitize_function'] ) {
									// strings
									$et_option_new_value = array_map( 'sanitize_text_field', stripslashes_deep( $_POST[ $value['id'] ] ) );
								} else {
									// saves categories / pages IDs
									$et_option_new_value = array_map( 'intval', stripslashes_deep( $_POST[ $value['id'] ] ) );
								}

							} elseif ( 'different_checkboxes' == $value['type'] ) {

								// saves 'author/date/categories/comments' options
								$et_option_new_value = array_map( 'sanitize_text_field', array_map( 'wp_strip_all_tags', stripslashes_deep( $_POST[$value['id']] ) ) );

							}
						} else {
							if ( in_array( $value['type'], array( 'checkbox', 'checkbox2' ) ) ) {
								$et_option_new_value = $is_builder_field ? 'off' : 'false';
							} else if ( 'different_checkboxes' == $value['type'] ) {
								$et_option_new_value = array();
							} else {
								et_delete_option( $value['id'] );
							}
						}

						if ( false !== $et_option_name && false !== $et_option_new_value ) {
							$is_new_global_setting    = false;
							$global_setting_main_name = $global_setting_sub_name = '';

							if ( isset( $value['is_global'] ) && $value['is_global'] ) {
								$is_new_global_setting    = true;
								$global_setting_main_name = isset( $value['main_setting_name'] ) ? sanitize_text_field( $value['main_setting_name'] ) : '';
								$global_setting_sub_name  = isset( $value['sub_setting_name'] ) ? sanitize_text_field( $value['sub_setting_name'] ) : '';
							}

							/**
							 * Fires before updating an ePanel option in the database.
							 *
							 * @param string $et_option_name      The option name/id.
							 * @param string $et_new_option_value The new option value.
							 */
							do_action( 'et_epanel_update_option', $et_option_name, $et_option_new_value );

							et_update_option( $et_option_name, $et_option_new_value, $is_new_global_setting, $global_setting_main_name, $global_setting_sub_name );
						}
					}
				}

				$redirect_url = add_query_arg( 'saved', 'true', $redirect_url );

				if ( 'js_disabled' == $source ) {
					header( "Location: " . $redirect_url );
				}
				die('1');

			} else if ( 'reset' == $_POST['action'] ) {
				check_admin_referer( 'et-nojs-reset_epanel', '_wpnonce_reset' );

				foreach ($options as $value) {
					if ( isset($value['id']) ) {
						et_delete_option( $value['id'] );
						if ( isset( $value['std'] ) ) {
							et_update_option( $value['id'], $value['std'] );
						}
					}
				}

				// Reset Google Maps API Key
				update_option( 'et_google_api_settings', '' );

				// Resets WordPress custom CSS which is synced with Options Custom CSS as of WP 4.7
				if ( function_exists( 'wp_get_custom_css' ) ) {
					wp_update_custom_css_post('');
					set_theme_mod( 'et_pb_css_synced', 'no' );
				}

				$redirect_url = add_query_arg( 'reset', 'true', $redirect_url );
				header( "Location: " . $redirect_url );

				die('1');
			}
		}
	}

}

function et_epanel_media_upload_scripts() {
	global $themename;

	wp_enqueue_script( 'et_epanel_uploader', get_template_directory_uri().'/epanel/js/custom_uploader.js', array('jquery', 'media-upload', 'thickbox'), et_get_theme_version() );
	wp_enqueue_media();
	wp_localize_script( 'et_epanel_uploader', 'epanel_uploader', array(
		'media_window_title' => esc_html__( 'Choose an Image', $themename ),
	) );
}

function et_epanel_media_upload_styles() {
	wp_enqueue_style( 'thickbox' );
}

global $pagenow;
if ( 'themes.php' == $pagenow && isset( $_GET['page'] ) && ( $_GET['page'] == basename( __FILE__ ) ) ) {
	add_action( 'admin_print_scripts', 'et_epanel_media_upload_scripts' );
	add_action( 'admin_print_styles', 'et_epanel_media_upload_styles' );
}

/**
 * Register ePanel portability.
 *
 * @since To define
 *
 * @return bool Always return true.
 */
function et_epanel_register_portability() {
	global $shortname, $themename, $options;

	// Make sure the Portability is loaded.
	et_core_load_component( 'portability' );

	// Load ePanel options.
	et_load_core_options();

	// Include only ePanel options.
	$include = array();

	foreach ( $options as $option ) {
		if ( isset( $option['id'] ) ) {
			$include[ $option['id'] ] = true;
		}
	}

	// Register the portability.
	et_core_portability_register( 'epanel', array(
		'name'    => sprintf(
			esc_html__( '%s Theme Options', $themename ),
			$themename
		),
		'type'    => 'options',
		'target'  => "et_{$shortname}",
		'include' => $include,
		'view'    => ( isset( $_GET['page'] ) && $_GET['page'] == "et_{$shortname}_options" ),
	) );
}
add_action( 'admin_init', 'et_epanel_register_portability' );

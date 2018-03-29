<?php

function et_fb_shortcode_tags() {
	global $shortcode_tags;

	$shortcode_tag_names = array();
	foreach ( $shortcode_tags as $shortcode_tag_name => $shortcode_tag_cb ) {
		$shortcode_tag_names[] = $shortcode_tag_name;
	}
	return implode( '|', $shortcode_tag_names );
}

function et_fb_prepare_library_cats() {
	$raw_categories_array = apply_filters( 'et_pb_new_layout_cats_array', get_terms( 'layout_category', array( 'hide_empty' => false ) ) );
	$clean_categories_array = array();

	if ( is_array( $raw_categories_array ) && ! empty( $raw_categories_array ) ) {
		foreach( $raw_categories_array as $category ) {
			$clean_categories_array[] = array(
				'name' => html_entity_decode( $category->name ),
				'id' => $category->term_id,
				'slug' => $category->slug,
			);
		}
	}

	return $clean_categories_array;
}

function et_fb_get_layout_type( $post_id ) {
	return et_fb_get_layout_term_slug( $post_id, 'layout_type' );
}

function et_fb_get_layout_term_slug( $post_id, $term_name ) {
	$post_terms  = wp_get_post_terms( $post_id, $term_name );
	$slug = $post_terms[0]->slug;

	return $slug;
}

function et_fb_comments_template() {
	return ET_BUILDER_DIR . 'comments_template.php';
}

function et_fb_modify_comments_request( $params ) {
	// modify the request parameters the way it doesn't change the result just to make request with unique parameters
	$params->query_vars['type__not_in'] = 'et_pb_comments_random_type_9999';
}

function et_fb_comments_submit_button( $submit_button ) {
		return sprintf(
			'<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
			esc_attr( 'submit' ),
			esc_attr( 'et_pb_submit' ),
			esc_attr( 'submit et_pb_button' ),
			esc_html_x( 'Submit Comment', 'et_builder' )
		);
}

// comments template cannot be generated via AJAX so prepare it beforehand
function et_fb_get_comments_markup() {
	// Modify the comments request to make sure it's unique.
	// Otherwise WP generates SQL error and doesn't allow multiple comments sections on single page
	add_action( 'pre_get_comments', 'et_fb_modify_comments_request', 1 );

	// include custom comments_template to display the comment section with Divi style
	add_filter( 'comments_template', 'et_fb_comments_template' );

	// Modify submit button to be advanced button style ready
	add_filter( 'comment_form_submit_button', 'et_fb_comments_submit_button' );

	ob_start();
	comments_template( '', true );
	$comments_content = ob_get_contents();
	ob_end_clean();

	// remove all the actions and filters to not break the default comments section from theme
	remove_filter( 'comments_template', 'et_fb_comments_template' );
	remove_action( 'pre_get_comments', 'et_fb_modify_comments_request', 1 );

	return $comments_content;
}

// List of shortcode wrappers that requires adjustment in VB. Plugins which uses fullscreen dimension
// tend to apply negative positioning which looks inappropriate on VB's shortcode mechanism
function et_fb_known_shortcode_wrappers() {
	return apply_filters( 'et_fb_known_shortcode_wrappers', array(
		'removeLeft' => array(
			'.fullscreen-container', // revolution slider,
			'.esg-container-fullscreen-forcer', // essential grid
			'.ls-wp-fullwidth-helper', // layer slider
		),
	) );
}

function et_builder_autosave_interval() {
	return apply_filters( 'et_builder_autosave_interval', et_builder_heartbeat_interval() / 2 );
}

function et_fb_heartbeat_settings($settings) {
	$settings['suspension'] = 'disable';
	$settings['interval'] = et_builder_heartbeat_interval();
	return $settings;
}
add_filter( 'heartbeat_settings', 'et_fb_heartbeat_settings', 11 );

function et_fb_backend_helpers() {
	global $post, $paged, $wp_query;

	$layout_type = '';
	$layout_scope = '';

	$post_type    = isset( $post->post_type ) ? $post->post_type : false;
	$post_id      = isset( $post->ID ) ? $post->ID : false;
	$post_status  = isset( $post->post_status ) ? $post->post_status : false;
	$post_title   = isset( $post->post_title ) ? esc_attr( $post->post_title ) : false;

	if ( 'et_pb_layout' === $post_type ) {
		$layout_type = et_fb_get_layout_type( $post_id );
		$layout_scope = et_fb_get_layout_term_slug( $post_id, 'scope' );
	}

	$google_fonts = array_merge( array( 'Default' => array() ), et_builder_get_google_fonts() );
	$current_user = wp_get_current_user();
	$current_url  = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	$fb_modules_array = apply_filters( 'et_fb_modules_array', ET_Builder_Element::get_modules_array( $post_type, true, true ) );

	$helpers = array(
		'debug'                        => true,
		'autosaveInterval'             => et_builder_autosave_interval(),
		'postId'                       => $post_id,
		'postTitle'                    => $post_title,
		'postStatus'                   => $post_status,
		'postType'                     => $post_type,
		'layoutType'                   => $layout_type,
		'layoutScope'                  => $layout_scope,
		'publishCapability'            => ( is_page() && ! current_user_can( 'publish_pages' ) ) || ( ! is_page() && ! current_user_can( 'publish_posts' ) ) ? 'no_publish' : 'publish',
		'shortcodeObject'              => array(),
		'autosaveShortcodeObject'      => array(),
		'ajaxUrl'                      => is_ssl() ? admin_url( 'admin-ajax.php' ) : admin_url( 'admin-ajax.php', 'http' ),
		'tinymceSkinUrl'               => ET_FB_ASSETS_URI . '/vendors/tinymce-skin',
		'tinymceCSSFiles'              => esc_url( includes_url( 'js/tinymce' ) . '/skins/wordpress/wp-content.css' ),
		'images_uri'                   => ET_BUILDER_URI .'/images',
		'componentDefinitions'         => array(
			'generalFields'                => array(),
			'advancedFields'               => array(),
			'customCssFields'              => array(),
			'fieldsDefaults'               => array(),
			'defaults'                     => array(),
			'optionsToggles'               => array(),
		),
		'moduleParentShortcodes'       => ET_Builder_Element::get_parent_shortcodes( $post_type ),
		'moduleChildShortcodes'        => ET_Builder_Element::get_child_shortcodes( $post_type ),
		'moduleChildSlugs'             => ET_Builder_Element::get_child_slugs( $post_type ),
		'moduleRawContentShortcodes'   => ET_Builder_Element::get_raw_content_shortcodes( $post_type ),
		'modules'                      => $fb_modules_array,
		'modulesCount'                 => count( $fb_modules_array ),
		'modulesWithChildren'          => ET_Builder_Element::get_shortcodes_with_children( $post_type ),
		'modulesShowOnCancelDropClassname' => apply_filters( 'et_fb_modules_show_on_cancel_drop_classname', array( 'et_pb_gallery', 'et_pb_filterable_portfolio') ),
		'structureModules'             => ET_Builder_Element::get_structure_modules(),
		'et_builder_css_media_queries' => ET_Builder_Element::get_media_quries( 'for_js' ),
		'builderOptions'               => et_builder_options(),
		'builderVersion'               => ET_BUILDER_PRODUCT_VERSION,
		'commentsModuleMarkup'         => et_fb_get_comments_markup(),
		'shortcode_tags'               => et_fb_shortcode_tags(),
		'getFontIconSymbols'           => et_pb_get_font_icon_symbols(),
		'failureNotification'          => et_builder_get_failure_notification_modal(),
		'exitNotification'             => et_builder_get_exit_notification_modal(),
		'browserAutosaveNotification'  => et_builder_get_browser_autosave_notification_modal(),
		'serverAutosaveNotification'   => et_builder_get_server_autosave_notification_modal(),
		'unsavedNotification'          => et_builder_get_unsaved_notification_modal(),
		'backupLabel'                  => __( 'Backup of %s', 'et_builder' ),
		'getTaxonomies'                => apply_filters( 'et_fb_taxonomies', array(
			'category'                 => get_categories(),
			'project_category'         => get_categories( array( 'taxonomy' => 'project_category' ) ),
			'product_category'         => class_exists( 'WooCommerce' ) ? get_terms( 'product_cat' ) : '',
		) ),
		'googleAPIKey'                 => et_pb_is_allowed( 'theme_options' ) ? get_option( 'et_google_api_settings' ) : '',
		'googleFontsList'              => array_keys( $google_fonts ),
		'googleFonts'                  => $google_fonts,
		'gutterWidth'                  => et_get_option( 'gutter_width', 3 ),
		'fontIcons'                    => et_pb_get_font_icon_symbols(),
		'fontIconsDown'                => et_pb_get_font_down_icon_symbols(),
		'widgetAreas'                  => et_builder_get_widget_areas_list(),
		'site_url'                     => get_site_url(),
		'cookie_path'                  => SITECOOKIEPATH,
		'blog_id'                      => get_current_blog_id(),
		'etBuilderAccentColor'         => et_builder_accent_color(),
		'gmt_offset_string'            => et_pb_get_gmt_offset_string(),
		'et_builder_fonts_data'        => et_builder_get_fonts(),
		'currentUserDisplayName'       => $current_user->display_name,
		'locale'                       => get_locale(),
		'roleSettings'                 => et_pb_get_role_settings(),
		'currentRole'                  => et_pb_get_current_user_role(),
		'exportUrl'                    => et_fb_get_portability_export_url(),
		'urls'                         => array(
			'loginFormUrl'             => esc_url( site_url( 'wp-login.php', 'login_post' ) ),
			'forgotPasswordUrl'        => esc_url( wp_lostpassword_url() ),
			'logoutUrl'                => esc_url( wp_logout_url() ),
			'logoutUrlRedirect'        => esc_url( wp_logout_url( $current_url ) ),
			'themeOptionsUrl'          => esc_url( et_pb_get_options_page_link() ),
			'builderPreviewStyle'      => ET_BUILDER_URI . '/styles/preview.css',
		),
		'nonces'                       => et_fb_get_nonces(),
		'conditionalTags'              => et_fb_conditional_tag_params(),
		'currentPage'                  => et_fb_current_page_params(),
		'appPreferences'               => et_fb_app_preferences(),
		'classNames'                   => array(
			'hide_on_mobile_class'     => 'et-hide-mobile',
		),
		'columnLayouts'                => et_builder_get_columns(),
		'pageSettingsFields'           => ET_Builder_Settings::get_fields(),
		'pageSettingsValues'           => ET_Builder_Settings::get_values(),
		'splitTestSubjects'            => false !== ( $all_subjects_raw = get_post_meta( $post_id, '_et_pb_ab_subjects' , true ) ) ? explode( ',', $all_subjects_raw ) : array(),
		'defaults'                     => array(
			'contactFormInputs'        => array(),
		),
		'saveModuleLibraryCategories'  => et_fb_prepare_library_cats(),
		'columnSettingFields'          => array(
			'general' => array(
				'bg_img_%s' => array(
					'label'              => esc_html__( 'Column %s Background Image', 'et_builder' ),
					'type'               => 'upload',
					'option_category'    => 'basic_option',
					'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
					'choose_text'        => esc_attr__( 'Choose a Background Image', 'et_builder' ),
					'update_text'        => esc_attr__( 'Set As Background', 'et_builder' ),
					'description'        => esc_html__( 'If defined, this image will be used as the background for this module. To remove a background image, simply delete the URL from the settings field.', 'et_builder' ),
					'tab_slug'           => 'general',
					'toggle_slug'        => 'background',
					'sub_toggle'         => 'column_%s',
				),
				'background_color_%s' => array(
					'label'        => esc_html__( 'Column %s Background Color', 'et_builder' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'tab_slug'     => 'general',
					'toggle_slug'  => 'background',
					'sub_toggle'   => 'column_%s',
				),
				'parallax_%s' => array(
					'label'           => esc_html__( 'Column %s Parallax Effect', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default'         => 'off',
					'affects'         => array(
						'parallax_method_%s',
						'background_size_%s',
						'background_position_%s',
						'background_repeat_%s',
						'background_blend_%s',
					),
					'description'     => esc_html__( 'Here you can choose whether or not use parallax effect for the featured image', 'et_builder' ),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'parallax_method_%s' => array(
					'label'           => esc_html__( 'Column %s Parallax Method', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'configuration',
					'options'         => array(
						'off' => esc_html__( 'CSS', 'et_builder' ),
						'on'  => esc_html__( 'True Parallax', 'et_builder' ),
					),
					'depends_show_if' => 'on',
					'depends_to'      => array(
						'parallax_%s',
					),
					'description'     => esc_html__( 'Here you can choose which parallax method to use for the featured image', 'et_builder' ),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_size_%s' => array(
					'label'           => esc_html__( 'Column %s Background Image Size', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'layout',
					'options'         => array(
						'cover'   => esc_html__( 'Cover', 'et_builder' ),
						'contain' => esc_html__( 'Fit', 'et_builder' ),
						'initial' => esc_html__( 'Actual Size', 'et_builder' ),
					),
					'default'         => 'cover',
					'depends_to'      => array(
						'parallax_%s',
					),
					'depends_show_if' => 'off',
					'toggle_slug'     => 'background',
				),
				'background_position_%s' => array(
					'label'           => esc_html__( 'Column %s Background Image Position', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'layout',
					'options' => array(
						'top_left'      => esc_html__( 'Top Left', 'et_builder' ),
						'top_center'    => esc_html__( 'Top Center', 'et_builder' ),
						'top_right'     => esc_html__( 'Top Right', 'et_builder' ),
						'center_left'   => esc_html__( 'Center Left', 'et_builder' ),
						'center'        => esc_html__( 'Center', 'et_builder' ),
						'center_right'  => esc_html__( 'Center Right', 'et_builder' ),
						'bottom_left'   => esc_html__( 'Bottom Left', 'et_builder' ),
						'bottom_center' => esc_html__( 'Bottom Center', 'et_builder' ),
						'bottom_right'  => esc_html__( 'Bottom Right', 'et_builder' ),
					),
					'default'         => 'center',
					'depends_to'      => array(
						'parallax_%s',
					),
					'depends_show_if' => 'off',
					'toggle_slug'     => 'background',
				),
				'background_repeat_%s' => array(
					'label'           => esc_html__( 'Column %s Background Image Repeat', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'layout',
					'options'         => array(
						'repeat'    => esc_html__( 'Repeat', 'et_builder' ),
						'repeat-x'  => esc_html__( 'Repeat X (horizontal)', 'et_builder' ),
						'repeat-y'  => esc_html__( 'Repeat Y (vertical)', 'et_builder' ),
						'space'     => esc_html__( 'Space', 'et_builder' ),
						'round'     => esc_html__( 'Round', 'et_builder' ),
						'no-repeat' => esc_html__( 'No Repeat', 'et_builder' ),
					),
					'default'         => 'repeat',
					'depends_to'      => array(
						'parallax_%s',
					),
					'depends_show_if' => 'off',
					'toggle_slug'     => 'background',
				),
				'background_blend_%s' => array(
					'label'           => esc_html__( 'Column %s Background Image Blend', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'layout',
					'options' => array(
						'normal'      => esc_html__( 'Normal', 'et_builder' ),
						'multiply'    => esc_html__( 'Multiply', 'et_builder' ),
						'screen'      => esc_html__( 'Screen', 'et_builder' ),
						'overlay'     => esc_html__( 'Overlay', 'et_builder' ),
						'darken'      => esc_html__( 'Darken', 'et_builder' ),
						'lighten'     => esc_html__( 'Lighten', 'et_builder' ),
						'color-dodge' => esc_html__( 'Color Dodge', 'et_builder' ),
						'color-burn'  => esc_html__( 'Color Burn', 'et_builder' ),
						'hard-light'  => esc_html__( 'Hard Light', 'et_builder' ),
						'soft-light'  => esc_html__( 'Soft Light', 'et_builder' ),
						'difference'  => esc_html__( 'Difference', 'et_builder' ),
						'exclusion'   => esc_html__( 'Exclusion', 'et_builder' ),
						'hue'         => esc_html__( 'Hue', 'et_builder' ),
						'saturation'  => esc_html__( 'Saturation', 'et_builder' ),
						'color'       => esc_html__( 'Color', 'et_builder' ),
						'luminosity'  => esc_html__( 'Luminosity', 'et_builder' ),
					),
					'default'         => 'normal',
					'depends_to'      => array(
						'parallax_%s',
					),
					'depends_show_if' => 'off',
					'toggle_slug'     => 'background',
				),
				'use_background_color_gradient_%s' => array(
					'label'           => esc_html__( 'Column %s Use Background Color Gradient', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'off' => esc_html__( 'No', 'et_builder' ),
						'on'  => esc_html__( 'Yes', 'et_builder' ),
					),
					'default'         => 'off',
					'affects'         => array(
						'background_color_gradient_start_%s',
						'background_color_gradient_end_%s',
						'background_color_gradient_start_position_%s',
						'background_color_gradient_end_position_%s',
						'background_color_gradient_type_%s',
					),
					'description'     => '',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_start_%s' => array(
					'label'           => esc_html__( 'Column %s Gradient Start', 'et_builder' ),
					'type'            => 'color-alpha',
					'option_category' => 'configuration',
					'description'     => '',
					'depends_show_if' => 'on',
					'default'         => '#2b87da',
					'depends_to'      => array(
						'use_background_color_gradient_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_end_%s' => array(
					'label'           => esc_html__( 'Column %s Gradient End', 'et_builder' ),
					'type'            => 'color-alpha',
					'option_category' => 'configuration',
					'description'     => '',
					'depends_show_if' => 'on',
					'default'         => '#29c4a9',
					'depends_to'      => array(
						'use_background_color_gradient_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_type_%s' => array(
					'label'           => esc_html__( 'Column %s Gradient Type', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'configuration',
					'options'         => array(
						'linear' => esc_html__( 'Linear', 'et_builder' ),
						'radial' => esc_html__( 'Radial', 'et_builder' ),
					),
					'affects'         => array(
						'background_color_gradient_direction_%s',
						'background_color_gradient_direction_radial_%s',
					),
					'default'         => 'linear',
					'description'     => '',
					'depends_show_if' => 'on',
					'depends_to'      => array(
						'use_background_color_gradient_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_direction_%s' => array(
					'label'           => esc_html__( 'Column %s Gradient Direction', 'et_builder' ),
					'type'            => 'range',
					'option_category' => 'configuration',
					'range_settings'  => array(
						'min'  => 1,
						'max'  => 360,
						'step' => 1,
					),
					'default'         => '180deg',
					'validate_unit'   => true,
					'fixed_unit'      => 'deg',
					'fixed_range'     => true,
					'depends_show_if' => 'linear',
					'depends_to'      => array(
						'background_color_gradient_type_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_direction_radial_%s' => array(
					'label'           => esc_html__( 'Column %s Radial Direction', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'configuration',
					'options'         => array(
						'center'       => esc_html__( 'Center', 'et_builder' ),
						'top left'     => esc_html__( 'Top Left', 'et_builder' ),
						'top'          => esc_html__( 'Top', 'et_builder' ),
						'top right'    => esc_html__( 'Top Right', 'et_builder' ),
						'right'        => esc_html__( 'Right', 'et_builder' ),
						'bottom right' => esc_html__( 'Bottom Right', 'et_builder' ),
						'bottom'       => esc_html__( 'Bottom', 'et_builder' ),
						'bottom left'  => esc_html__( 'Bottom Left', 'et_builder' ),
						'left'         => esc_html__( 'Left', 'et_builder' ),
					),
					'default'         => '',
					'description'     => '',
					'depends_show_if' => 'radial',
					'depends_to'      => array(
						'background_color_gradient_type_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_start_position_%s' => array(
					'label'           => esc_html__( 'Column %s Start Position', 'et_builder' ),
					'type'            => 'range',
					'option_category' => 'configuration',
					'range_settings'  => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'default'         => 0,
					'validate_unit'   => true,
					'fixed_unit'      => '%',
					'fixed_range'     => true,
					'depends_show_if' => 'on',
					'depends_to'      => array(
						'use_background_color_gradient_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_end_position_%s' => array(
					'label'           => esc_html__( 'Column %s End Position', 'et_builder' ),
					'type'            => 'range',
					'option_category' => 'configuration',
					'range_settings'  => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'default'         => 100,
					'validate_unit'   => true,
					'fixed_unit'      => '%',
					'fixed_range'     => true,
					'depends_show_if' => 'on',
					'depends_to'      => array(
						'use_background_color_gradient_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_video_mp4_%s' => array(
					'label'              => esc_html__( 'Column %s Background Video MP4', 'et_builder' ),
					'type'               => 'upload',
					'option_category'    => 'basic_option',
					'data_type'          => 'video',
					'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
					'choose_text'        => esc_attr__( 'Choose a Background Video MP4 File', 'et_builder' ),
					'update_text'        => esc_attr__( 'Set As Background Video', 'et_builder' ),
					'tab_slug'           => 'general',
					'toggle_slug'        => 'background',
					'sub_toggle'         => 'column_%s',
				),
				'background_video_webm_%s' => array(
					'label'              => esc_html__( 'Column %s Background Video Webm', 'et_builder' ),
					'type'               => 'upload',
					'option_category'    => 'basic_option',
					'data_type'          => 'video',
					'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
					'choose_text'        => esc_attr__( 'Choose a Background Video WEBM File', 'et_builder' ),
					'update_text'        => esc_attr__( 'Set As Background Video', 'et_builder' ),
					'tab_slug'           => 'general',
					'toggle_slug'        => 'background',
					'sub_toggle'         => 'column_%s',
				),
				'background_video_width_%s' => array(
					'label'           => esc_html__( 'Column %s Background Video Width', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'basic_option',
					'tab_slug'        => 'general',
					'sub_toggle'      => 'column_%s',
				),
				'background_video_height_%s' => array(
					'label'           => esc_html__( 'Column %s Background Video Height', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'basic_option',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'allow_player_pause_%s' => array(
					'label'           => esc_html__( 'Column %s Pause Video', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'off' => esc_html__( 'No', 'et_builder' ),
						'on'  => esc_html__( 'Yes', 'et_builder' ),
					),
					'default'         => 'off',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'__video_background_%s' => array(
					'type'                => 'computed',
					'computed_callback'   => array( 'ET_Builder_Column', 'get_column_video_background' ),
					'computed_depends_on' => array(
						'background_video_mp4_%s',
						'background_video_webm_%s',
						'background_video_width_%s',
						'background_video_height_%s',
					),
				),
			),
			'advanced'                => array(
				'padding_%s'          => array(
					'label'           => esc_html__( 'Column %s Custom Padding', 'et_builder' ),
					'type'            => 'custom_padding',
					'mobile_options'  => true,
					'option_category' => 'layout',
					'description'     => esc_html__( 'Adjust padding to specific values, or leave blank to use the default padding.', 'et_builder' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'margin_padding',
					'sub_toggle'      => 'column_%s',
				),
			),
			'css'                     => array(
				'module_id_%s'        => array(
					'label'           => esc_html__( 'Column %s CSS ID', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'classes',
					'sub_toggle'      => 'column_%s',
					'option_class'    => 'et_pb_custom_css_regular',
				),
				'module_class_%s'     => array(
					'label'           => esc_html__( 'Column %s CSS Class', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'classes',
					'sub_toggle'      => 'column_%s',
					'option_class'    => 'et_pb_custom_css_regular',
				),
				'custom_css_before_%s'=> array(
					'label'           => esc_html__( 'Column %s before', 'et_builder' ),
					'no_space_before_selector' => true,
					'selector'        => ':before',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'custom_css',
					'sub_toggle'      => 'column_%s',
				),
				'custom_css_main_%s'  => array(
					'label'           => esc_html__( 'Column %s Main Element', 'et_builder' ),
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'custom_css',
					'sub_toggle'      => 'column_%s',
				),
				'custom_css_after_%s' => array(
					'label'           => esc_html__( 'Column %s After', 'et_builder' ),
					'no_space_before_selector' => true,
					'selector'        => ':after',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'custom_css',
					'sub_toggle'      => 'column_%s',
				),

			),
		),
		'knownShortcodeWrappers'           => et_fb_known_shortcode_wrappers(),
		'customizer'                   => array(
			'tablet' => array(
				'sectionHeight' => et_get_option( 'tablet_section_height' ),
			),
			'phone' => array(
				'sectionHeight' => et_get_option( 'phone_section_height' ),
			),
		),
	);

	// Internationalization.
	$helpers['i18n'] = array(
		'modules'      => array(
			'audio'    => array(
				'meta' => _x( 'by <strong>%1$s</strong>', 'Audio Module meta information', 'et_builder' ),
			),
			'background' => array(
				'label'       => __( 'Background', 'et_builder' ),
				'description' => '',
			),
			'column' => array(
				'backgroundColor' => esc_html__( 'Column %s Background', 'et_builder' ),
			),
			'contactForm' => array(
				'thankYou' => esc_html__( 'Thanks for contacting us', 'et_builder' ),
				'submit'   => esc_attr__( 'Submit', 'et_builder' ),
			),
			'contactFormItem' => array(
				'noOptions'     => esc_html__( 'No options added.', 'et_builder' ),
				'selectDefault' => esc_html__( '-- Please Select --', 'et_builder' ),
			),
			'countdownTimer' => array(
				'dayFull'     => esc_html__( 'Day(s)', 'et_builder' ),
				'dayShort'    => esc_html__( 'Day', 'et_builder' ),
				'hourFull'    => esc_html__( 'Hour(s)', 'et_builder' ),
				'hourShort'   => esc_html__( 'Hrs', 'et_builder' ),
				'minuteFull'  => esc_html__( 'Minute(s)', 'et_builder' ),
				'minuteShort' => esc_html__( 'Min', 'et_builder' ),
				'secondFull'  => esc_html__( 'Second(s)', 'et_builder' ),
				'secondShort' => esc_html__( 'Sec', 'et_builder' ),
			),
			'signup' => array(
				'emailAddress' => esc_attr__( 'Email Address', 'et_builder' ),
				'firstName'    => esc_attr__( 'First Name', 'et_builder' ),
				'lastName'     => esc_attr__( 'Last Name', 'et_builder' ),
				'name'         => esc_attr__( 'Name', 'et_builder' ),
				'email'        => esc_attr__( 'Email', 'et_builder' ),
			),
			'filterablePortfolio' => array(
				'all' => esc_html__( 'All', 'et_builder' ),
			),
			'login' => array(
				'loginAs'         => sprintf( esc_html__( 'Login as %s', 'et_builder' ), $current_user->display_name ),
				'login'           => esc_html__( 'Login', 'et_builder' ),
				'logout'          => esc_html__( 'Log out', 'et_builder' ),
				'forgotPassword'  => esc_html__( 'Forgot your password?', 'et_builder' ),
				'username'        => esc_html__( 'Username', 'et_builder' ),
				'password'        => esc_html__( 'Password', 'et_builder' ),
				'note_autofill'   => esc_attr__( 'Note: this field is used to disable browser autofill during the form editing in VB', 'et_builder' ),
			),
			'postTitle' => array(
				'by' => esc_html__( 'by ', 'et_builder' ),
			),
			'search' => array(
				'submitButtonText' => esc_html__( 'Search', 'et_builder' ),
				'searchfor' => esc_html__( 'Search for:', 'et_builder' ),
			),
			'fullwidthPostSlider' => array(
				'by' => esc_html__( 'by ', 'et_builder' ),
			),
			'socialFollow' => array(
				'follow' => esc_html__( 'Follow', 'et_builder' ),
			),
		),
		'saveButtonText'               => esc_attr__( 'Save', 'et_builder' ),
		'saveDraftButtonText'          => esc_attr__( 'Save Draft', 'et_builder' ),
		'publishButtonText'            => ( is_page() && ! current_user_can( 'publish_pages' ) ) || ( ! is_page() && ! current_user_can( 'publish_posts' ) ) ? esc_attr__( 'Submit', 'et_builder' ) : esc_attr__( 'Publish', 'et_builder' ),
		'controls'                     => array(
			'tinymce'                  => array(
				'visual'               => esc_html__( 'Visual', 'et_builder' ),
				'text'                 => esc_html__( 'Text', 'et_builder' ),
			),
			'moduleItem'               => array(
				'addNew'               => esc_html__( 'Add New Item', 'et_builder' ),
			),
			'upload'                   => array(
				'buttonText'           => esc_html__( 'Upload', 'et_builder' ),
			),
			'insertMedia'              => array(
				'buttonText'           => esc_html__( 'Add Media', 'et_builder' ),
				'modalTitleText'       => esc_html__( 'Insert Media', 'et_builder' ),
			),
			'inputMargin'              => array(
				'top'                  => esc_html__( 'Top', 'et_builder' ),
				'right'                => esc_html__( 'Right', 'et_builder' ),
				'bottom'               => esc_html__( 'Bottom', 'et_builder' ),
				'left'                 => esc_html__( 'Left', 'et_builder' ),
			),
			'colorpicker'              => array(
				'clear'                => esc_html__( 'Clear', 'et_builder' ),
				'select'               => esc_html__( 'Select', 'et_builder' ),
			),
			'uploadGallery'            => array(
				'uploadButtonText'     => esc_html__( 'Update Gallery', 'et_builder'),
			),
			'centerMap'                => array(
				'updateMapButtonText'  => esc_html__( 'Find', 'et_builder'),
				'geoCodeError'         => esc_html__( 'Geocode was not successful for the following reason', 'et_builder' ),
				'geoCodeError_2'       => esc_html__( 'Geocoder failed due to', 'et_builder' ),
				'noResults'            => esc_html__( 'No results found', 'et_builder' ),
				'mapPinAddressInvalid' => esc_html__( 'Invalid Pin and address data. Please try again.', 'et_builder' ),
			),
			'tabs'                     => array(
				'general'              => esc_html__( 'Content', 'et_builder' ),
				'design'               => esc_html__( 'Design', 'et_builder' ),
				'css'                  => esc_html__( 'Advanced', 'et_builder' ),
			),
			'additionalButton'         => array(
				'changeApiKey'         => esc_html__( 'Change API Key', 'et_builder' ),
				'generateImageUrlFromVideo' => esc_html__( 'Generate From Video', 'et_builder' ),
			),
			'conditionalLogic'         => array(
				'checked'              => esc_html__( 'checked', 'et_builder' ),
				'unchecked'            => esc_html__( 'not checked', 'et_builder' ),
				'is'                   => esc_html__( 'equals', 'et_builder' ),
				'isNot'                => esc_html__( 'does not equal', 'et_builder' ),
				'isGreater'            => esc_html__( 'is greater than', 'et_builder' ),
				'isLess'               => esc_html__( 'is less than', 'et_builder' ),
				'contains'             => esc_html__( 'contains', 'et_builder' ),
				'doesNotContain'       => esc_html__( 'does not contain', 'et_builder' ),
				'isEmpty'              => esc_html__( 'is empty', 'et_builder' ),
				'isNotEmpty'           => esc_html__( 'is not empty', 'et_builder' ),
			),
			'cssText'                  => esc_html__( 'CSS', 'et_builder'),
		),
		'rightClickMenuItems' => array(
			'undo'            => esc_html__( 'Undo', 'et_builder' ),
			'redo'            => esc_html__( 'Redo', 'et_builder' ),
			'lock'            => esc_html__( 'Lock', 'et_builder' ),
			'unlock'          => esc_html__( 'Unlock', 'et_builder' ),
			'copy'            => esc_html__( 'Copy', 'et_builder' ),
			'paste'           => esc_html__( 'Paste', 'et_builder' ),
			'copyStyle'       => esc_html__( 'Copy Style', 'et_builder' ),
			'pasteStyle'      => esc_html__( 'Paste Style', 'et_builder' ),
			'disable'         => esc_html__( 'Disable', 'et_builder' ),
			'enable'          => esc_html__( 'Enable', 'et_builder' ),
			'save'            => esc_html__( 'Save to Library', 'et_builder' ),
			'moduleType'      => array(
				'module'      => esc_html__( 'Module', 'et_builder' ),
				'row'         => esc_html__( 'Row', 'et_builder' ),
				'section'     => esc_html__( 'Section', 'et_builder' ),
			),
			'disableGlobal'   => esc_html__( 'Disable Global', 'et_builder' ),
		),
		'tooltips'            => array(
			'insertModule'     => esc_html__( 'Insert Module', 'et_builder' ),
			'insertColumn'     => esc_html__( 'Insert Columns', 'et_builder' ),
			'insertSection'    => esc_html__( 'Insert Section', 'et_builder' ),
			'insertRow'        => esc_html__( 'Insert Row', 'et_builder' ),
			'newModule'        => esc_html__( 'New Module', 'et_builder' ),
			'newRow'           => esc_html__( 'New Row', 'et_builder' ),
			'newSection'       => esc_html__( 'New Section', 'et_builder' ),
			'addFromLibrary'   => esc_html__( 'Add From Library', 'et_builder' ),
			'addToLibrary'     => esc_html__( 'Add to Library', 'et_builder' ),
			'loading'          => esc_html__( 'loading...', 'et_builder' ),
			'regular'          => esc_html__( 'Regular', 'et_builder' ),
			'fullwidth'        => esc_html__( 'Fullwidth', 'et_builder' ),
			'specialty'        => esc_html__( 'Specialty', 'et_builder' ),
			'changeRow'        => esc_html__( 'Choose Layout', 'et_builder' ),
			'clearLayout'      => esc_html__( 'Clear Layout', 'et_builder' ),
			'clearLayoutText'  => esc_html__( 'All of your current page content will be lost. Do you wish to proceed?', 'et_builder' ),
			'yes'              => esc_html__( 'Yes', 'et_builder' ),
			'loadLayout'       => esc_html__( 'Load From Library', 'et_builder' ),
			'predefinedLayout' => esc_html__( 'Predefined Layouts', 'et_builder' ),
			'replaceLayout'    => esc_html__( 'Replace existing content.', 'et_builder' ),
			'search'           => esc_html__( 'Search', 'et_builder' ) . '...',
			'portability'      => esc_html__( 'Portability', 'et_builder' ),
			'export'           => esc_html__( 'Export', 'et_builder' ),
			'import'           => esc_html__( 'Import', 'et_builder' ),
			'exportText'       => esc_html__( 'Exporting your Divi Builder Layout will create a JSON file that can be imported into a different website.', 'et_builder' ),
			'exportName'       => esc_html__( 'Export File Name', 'et_builder' ),
			'exportButton'     => esc_html__( 'Export Divi Builder Layout', 'et_builder' ),
			'importText'       => esc_html__( 'Importing a previously-exported Divi Builder Layout file will overwrite all content currently on this page.', 'et_builder' ),
			'importField'      => esc_html__( 'Select File To Import', 'et_builder' ),
			'importBackUp'     => esc_html__( 'Download backup before importing', 'et_builder' ),
			'importButton'     => esc_html__( 'Import Divi Builder Layout', 'et_builder' ),
			'noFile'           => esc_html__( 'No File Selected', 'et_builder' ),
			'chooseFile'       => esc_html__( 'Choose File', 'et_builder' ),
		),
		'saveModuleLibraryAttrs'        => array(
			'general'               => esc_html__( 'Include General Settings', 'et_builder' ),
			'advanced'              => esc_html__( 'Include Advanced Design Settings', 'et_builder' ),
			'css'                   => esc_html__( 'Include Custom CSS', 'et_builder' ),
			'selectCategoriesText'  => esc_html__( 'Select category(ies) for new template or type a new name ( optional )', 'et_builder' ),
			'templateName'          => esc_html__( 'Template Name', 'et_builder' ),
			'selectiveError'        => esc_html__( 'Please select at least 1 tab to save', 'et_builder' ),
			'globalTitle'           => esc_html__( 'Save as Global', 'et_builder' ),
			'globalText'            => esc_html__( 'Make this a global item', 'et_builder' ),
			'createCatText'         => esc_html__( 'Create New Category', 'et_builder' ),
			'addToCatText'          => esc_html__( 'Add To Categories', 'et_builder' ),
			'descriptionText'       => esc_html__( 'Here you can add the current item to your Divi Library for later use.', 'et_builder' ),
			'descriptionTextLayout' => esc_html__( 'Save your current page to the Divi Library for later use.', 'et_builder' ),
			'saveText'              => esc_html__( 'Save to Library', 'et_builder' ),
			'allCategoriesText'     => esc_html__( 'All Categories', 'et_builder' ),
		),
		'modals' => array(
			'tabItemTitles'  => array(
				'general' => esc_html__( 'General', 'et_builder' ),
				'design'  => esc_html__( 'Design', 'et_builder' ),
				'css'     => esc_html__( 'CSS', 'et_builder' ),
			),
			'moduleSettings' => array(
				'title' => esc_html__( '%s Settings', 'et_builder' ),
			),
			'pageSettings'   => array(
				'title'   => ET_Builder_Settings::get_title(),
				'tabs'    => ET_Builder_Settings::get_tabs(),
				'toggles' => ET_Builder_Settings::get_toggles(),
			),
			'searchOptions' => esc_html__( 'Search Options', 'et_builder' ),
		),
		'history' => array(
			'modal' => array(
				'title' => esc_html__( 'Editing History', 'et_builder' ),
				'tabs' => array(
					'states' => esc_html__( 'History States', 'et_builder' ),
				),
			),
			'meta' => et_pb_history_localization(),
		),
		'help' => array(
			'modal' => array(
				'title' => esc_html__( 'Divi Builder Helper', 'et_builder' ),
				'tabs' => array(
					'shortcut' => esc_html__( 'Shortcuts', 'et_builder' ),
				),
			),
			'shortcuts' => et_builder_get_shortcuts('fb'),
		),
		'sortable' => array(
			'has_no_ab_permission'                     => esc_html__( 'You do not have permission to edit the module, row or section in this split test.', 'et_builder' ),
			'cannot_move_goal_into_subject'            => esc_html__( 'A split testing goal cannot be moved inside of a split testing subject. To perform this action you must first end your split test.', 'et_builder' ),
			'cannot_move_subject_into_goal'            => esc_html__( 'A split testing subject cannot be moved inside of a split testing goal. To perform this action you must first end your split test.', 'et_builder' ),
			'cannot_move_row_goal_out_from_subject'    => esc_html__( 'Once set, a goal that has been placed inside a split testing subject cannot be moved outside the split testing subject. You can end your split test and start a new one if you would like to make this change.', 'et_builder' ),
			'section_only_row_dragged_away'            => esc_html__( 'The section should have at least one row.', 'et_builder' ),
			'global_module_alert'                      => esc_html__( 'You cannot add global modules into global sections or rows', 'et_builder' ),
			'cannot_move_module_goal_out_from_subject' => esc_html__( 'Once set, a goal that has been placed inside a split testing subject cannot be moved outside the split testing subject. You can end your split test and start a new one if you would like to make this change.', 'et_builder' ),
			'stop_dropping_3_col_row'                  => esc_html__( '3 column row can\'t be used in this column.', 'et_builder' ),
		),
		'tooltip' => array(
			'pageSettingsBar' => array(
				'responsive' => array(
					'wireframe'    => esc_html__( 'Wireframe View', 'et_builder' ),
					'zoom'    => esc_html__( 'Zoom Out', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop View', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet View', 'et_builder' ),
					'phone'   => esc_html__( 'Phone View', 'et_builder' ),
				),
				'main' => array(
					'loadLibrary'       => esc_html__( 'Load From Library', 'et_builder' ),
					'saveToLibrary'     => esc_html__( 'Save To Library', 'et_builder' ),
					'clearLayout'       => esc_html__( 'Clear Layout', 'et_builder' ),
					'pageSettingsModal' => esc_html__( 'Page Settings', 'et_builder' ),
					'history'           => esc_html__( 'Editing History', 'et_builder' ),
					'portability'       => esc_html__( 'Portability', 'et_builder' ),
					'open'              => esc_html__( 'Expand Settings', 'et_builder' ),
					'close'             => esc_html__( 'Collapse Settings', 'et_builder' ),
				),
				'save' => array(
					'saveDraft' => esc_html__( 'Save as Draft', 'et_builder' ),
					'save'      => esc_html__( 'Save', 'et_builder' ),
					'publish'   => esc_html__( 'Publish', 'et_builder' ),
				)
			),
			'modal' => array(
				'expandModal'   => esc_html__( 'Expand Modal', 'et_builder' ),
				'contractModal' => esc_html__( 'Contract Modal', 'et_builder' ),
				'resize'        => esc_html__( 'Resize Modal', 'et_builder' ),
				'snapModal'     => esc_html__( 'Snap to Left', 'et_builder' ),
				'separateModal' => esc_html__( 'Separate Modal', 'et_builder' ),
				'redo'          => esc_html__( 'Redo', 'et_builder' ),
				'undo'          => esc_html__( 'Undo', 'et_builder' ),
				'cancel'        => esc_html__( 'Discard All Changes', 'et_builder' ),
				'save'          => esc_html__( 'Save Changes', 'et_builder' ),
			),
			'inlineEditor' => array(
				'back'             => esc_html__( 'Go Back', 'et_builder' ),
				'increaseFontSize' => esc_html__( 'Decrease Font Size', 'et_builder' ),
				'decreaseFontSize' => esc_html__( 'Increase Font Size', 'et_builder' ),
				'bold'             => esc_html__( 'Bold Text', 'et_builder' ),
				'italic'           => esc_html__( 'Italic Text', 'et_builder' ),
				'underline'        => esc_html__( 'Underline Text', 'et_builder' ),
				'link'             => esc_html__( 'Insert Link', 'et_builder' ),
				'quote'            => esc_html__( 'Insert Quote', 'et_builder' ),
				'alignment'        => esc_html__( 'Text Alignment', 'et_builder' ),
				'centerText'       => esc_html__( 'Center Text', 'et_builder' ),
				'rightText'        => esc_html__( 'Right Text', 'et_builder' ),
				'leftText'         => esc_html__( 'Left Text', 'et_builder' ),
				'justifyText'      => esc_html__( 'Justify Text', 'et_builder' ),
				'list'             => esc_html__( 'List Settings', 'et_builder' ),
				'indent'           => esc_html__( 'Indent List', 'et_builder' ),
				'undent'           => esc_html__( 'Undent List', 'et_builder' ),
				'orderedList'      => esc_html__( 'Insert Ordered List', 'et_builder' ),
				'unOrderedList'    => esc_html__( 'Insert Unordered List', 'et_builder' ),
				'text'             => esc_html__( 'Text Settings', 'et_builder' ),
				'textColor'        => esc_html__( 'Text Color', 'et_builder' ),
				'heading' => array(
					'one'   => esc_html__( 'Insert Heading One', 'et_builder' ),
					'two'   => esc_html__( 'Insert Heading Two', 'et_builder' ),
					'three' => esc_html__( 'Insert Heading Three', 'et_builder' ),
					'four'  => esc_html__( 'Insert Heading Four', 'et_builder' ),
				),
			),
			'section' => array(
				'tab' => array(
					'move'         => esc_html__( 'Move Section', 'et_builder' ),
					'settings'     => esc_html__( 'Section Settings', 'et_builder' ),
					'duplicate'    => esc_html__( 'Duplicate Section', 'et_builder' ),
					'addToLibrary' => esc_html__( 'Save Section To Library', 'et_builder' ),
					'delete'       => esc_html__( 'Delete Section', 'et_builder' ),
				),
				'addButton' => esc_html__( 'Add New Section', 'et_builder' ),
			),
			'row' => array(
				'tab' => array(
					'move'         => esc_html__( 'Move Row', 'et_builder' ),
					'settings'     => esc_html__( 'Row Settings', 'et_builder' ),
					'duplicate'    => esc_html__( 'Duplicate Row', 'et_builder' ),
					'addToLibrary' => esc_html__( 'Save Row To Library', 'et_builder' ),
					'delete'       => esc_html__( 'Delete Row', 'et_builder' ),
					'update'       => esc_html__( 'Change Column Structure', 'et_builder' ),
				),
				'addButton' => esc_html__( 'Add New Row', 'et_builder' ),
				'chooseColumn' => esc_html__( 'Choose Column Structure', 'et_builder' ),
			),
			'module' => array(
				'tab' => array(
					'move'         => esc_html__( 'Move Module', 'et_builder' ),
					'settings'     => esc_html__( 'Module Settings', 'et_builder' ),
					'duplicate'    => esc_html__( 'Duplicate Module', 'et_builder' ),
					'addToLibrary' => esc_html__( 'Save Module To Library', 'et_builder' ),
					'delete'       => esc_html__( 'Delete Module', 'et_builder' ),
				),
				'addButton' => esc_html__( 'Add New Module', 'et_builder' ),
			),
		),
		'unsavedConfirmation' => esc_html__( 'Unsaved changes will be lost if you leave the Divi Builder at this time.', 'et_builder' ),
		'libraryLoadError'    => esc_html__( 'Error loading Library items from server. Please refresh the page and try again.', 'et_builder' ),
	);

	// Pass helpers via localization.
	wp_localize_script( 'et-frontend-builder', 'ETBuilderBackend', $helpers );
}

if ( ! function_exists( 'et_fb_fix_plugin_conflicts' ) ) :
function et_fb_fix_plugin_conflicts() {
	// Disable Autoptimize plugin
	remove_action( 'init', 'autoptimize_start_buffering', -1 );
	remove_action( 'template_redirect', 'autoptimize_start_buffering', 2 );
}
endif;

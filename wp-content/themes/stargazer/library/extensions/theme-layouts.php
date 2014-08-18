<?php
/**
 * Theme Layouts - A WordPress script for creating dynamic layouts.
 *
 * Theme Layouts was created to allow theme developers to easily style themes with dynamic layout 
 * structures.  It gives users the ability to control how each post (or any post type) is displayed on the 
 * front end of the site.  The layout can also be filtered for any page of a WordPress site.  
 *
 * The script will filter the WordPress body_class to provide a layout class for the given page.  Themes 
 * must support this hook or its accompanying body_class() function for the Theme Layouts script to work. 
 * Themes must also handle the CSS based on the layout class.  This script merely provides the logic.  The 
 * design should be handled on a theme-by-theme basis.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   ThemeLayouts
 * @version   0.6.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2014, Justin Tadlock
 * @link      http://justintadlock.com
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register metadata with WordPress. */
add_action( 'init', 'theme_layouts_register_meta' );

/* Add post type support for theme layouts. */
add_action( 'init', 'theme_layouts_add_post_type_support',    5 );
add_action( 'init', 'theme_layouts_remove_post_type_support', 5 );

/* Set up the custom post layouts. */
add_action( 'admin_init', 'theme_layouts_admin_setup' );

/* Add layout option in Customize. */
add_action( 'customize_register', 'theme_layouts_customize_register' );

/* Filters the theme layout mod. */
add_filter( 'theme_mod_theme_layout', 'theme_layouts_filter_layout', 5 );

/* Filters the body_class hook to add a custom class. */
add_filter( 'body_class', 'theme_layouts_body_class' );

/**
 * Registers the theme layouts meta key ('Layout') for specific object types and provides a function to 
 * sanitize the metadata on update.
 *
 * @since  0.4.0
 * @access public
 * @return void
 */
function theme_layouts_register_meta() {
	register_meta( 'post', theme_layouts_get_meta_key(), 'theme_layouts_sanitize_meta' );
	register_meta( 'user', theme_layouts_get_meta_key(), 'theme_layouts_sanitize_meta' );
}

/**
 * Callback function for sanitizing meta when add_metadata() or update_metadata() is called by WordPress. 
 * If a developer wants to set up a custom method for sanitizing the data, they should use the 
 * "sanitize_{$meta_type}_meta_{$meta_key}" filter hook to do so.
 *
 * @since  0.4.0
 * @access public
 * @param  mixed  $meta_value The value of the data to sanitize.
 * @param  string $meta_key   The meta key name.
 * @param  string $meta_type  The type of metadata (post, comment, user, etc.)
 * @return mixed  $meta_value
 */
function theme_layouts_sanitize_meta( $meta_value, $meta_key, $meta_type ) {
	return sanitize_html_class( $meta_value );
}

/**
 * Adds post type support to all 'public' post types.  This allows themes to remove support for the 
 * 'theme-layouts' feature with remove_post_type_support().
 *
 * @since  0.4.0
 * @access public
 * @return void
 */
function theme_layouts_add_post_type_support() {

	/* Gets available public post types. */
	$post_types = get_post_types( array( 'public' => true ) );

	/* For each available post type, create a meta box on its edit page if it supports '$prefix-post-settings'. */
	foreach ( $post_types as $type )
		add_post_type_support( $type, 'theme-layouts' );
}

/**
 * Removes theme layouts support from specific post types created by plugins.
 *
 * @since  0.4.0
 * @access public
 * @return void
 */
function theme_layouts_remove_post_type_support() {

	/* Removes theme layouts support of the bbPress 'reply' post type. */
	if ( function_exists( 'bbp_get_reply_post_type' ) )
		remove_post_type_support( bbp_get_reply_post_type(), 'theme-layouts' );
}

/**
 * Gets all the available layouts for the theme.
 *
 * @since  0.5.0
 * @access public
 * @return array  Either theme-supported layouts or the default layouts.
 */
function theme_layouts_get_layouts() {

	$layouts = get_theme_support( 'theme-layouts' );

	return isset( $layouts[0] ) ? array_keys( $layouts[0] ) : array_keys( theme_layouts_strings() );
}

/**
 * Returns an array of arguments for setting up the theme layouts script.  The defaults are merged 
 * with the theme-supported arguments.
 *
 * @since  0.5.0
 * @access public
 * @return array  Arguments for the theme layouts script.
 */
function theme_layouts_get_args() {

	$defaults = array( 
		'customize' => true, 
		'post_meta' => true, 
		'default'   => 'default' 
	);

	$layouts = get_theme_support( 'theme-layouts' );

	$args = isset( $layouts[1] ) ? $layouts[1] : array();

	return apply_filters( 'theme_layouts_args', wp_parse_args( $args, $defaults ) );
}

/**
 * Filters the 'theme_mods_theme_layout' hook to alter the layout based on post and user metadata.  
 * Theme authors should also use this hook to filter the layout if need be.
 *
 * @since  0.5.0
 * @access public
 * @param  string  $theme_layout
 * @return string
 */
function theme_layouts_filter_layout( $theme_layout ) {

	/* If viewing a singular post, get the post layout. */
	if ( is_singular() )
		$layout = get_post_layout( get_queried_object_id() );

	/* If viewing an author archive, get the user layout. */
	elseif ( is_author() )
		$layout = get_user_layout( get_queried_object_id() );

	/* If a layout was found, set it. */
	if ( !empty( $layout ) && 'default' !== $layout ) {
		$theme_layout = $layout;
	}

	/* Else, if no layout option has yet been saved, return the theme default. */
	elseif ( empty( $theme_layout ) ) {
		$args = theme_layouts_get_args();
		$theme_layout = $args['default'];
	}

	return $theme_layout;
}

/**
 * Gets the layout for the current post based off the 'Layout' custom field key if viewing a singular post 
 * entry.  All other pages are given a default layout of 'layout-default'.
 *
 * @since  0.2.0
 * @access public
 * @return string The layout for the given page.
 */
function theme_layouts_get_layout() {

	/* Get the available theme layouts. */
	$layouts = theme_layouts_get_layouts();

	/* Get the theme layout arguments. */
	$args = theme_layouts_get_args();

	/* Set the layout to an empty string. */
	$layout = get_theme_mod( 'theme_layout', $args['default'] );

	/* Make sure the given layout is in the array of available post layouts for the theme. */
	if ( empty( $layout ) || !in_array( $layout, $layouts ) || 'default' == $layout )
		$layout = $args['default'];

	/* @deprecated 0.2.0. Use the 'get_theme_layout' hook. */
	$layout = apply_filters( 'get_post_layout', "layout-{$layout}" );

	/* @deprecated 0.5.0.  Use the 'theme_mods_theme_layout' hook. */
	return esc_attr( apply_filters( 'get_theme_layout', $layout ) );
}

/**
 * Get the post layout based on the given post ID.
 *
 * @since  0.2.0
 * @param  int    $post_id The ID of the post to get the layout for.
 * @return string $layout The name of the post's layout.
 */
function get_post_layout( $post_id ) {

	/* Get the post layout. */
	$layout = get_post_meta( $post_id, theme_layouts_get_meta_key(), true );

	/* Return the layout if one is found.  Otherwise, return 'default'. */
	return ( !empty( $layout ) ? $layout : 'default' );
}

/**
 * Update/set the post layout based on the given post ID and layout.
 *
 * @since  0.2.0
 * @access public
 * @param  int    $post_id The ID of the post to set the layout for.
 * @param  string $layout  The name of the layout to set.
 * @return bool            True on successful update, false on failure.
 */
function set_post_layout( $post_id, $layout ) {
	return update_post_meta( $post_id, theme_layouts_get_meta_key(), $layout );
}

/**
 * Deletes a post layout.
 *
 * @since  0.4.0
 * @access public
 * @param  int    $post_id The ID of the post to delete the layout for.
 * @return bool            True on successful delete, false on failure.
 */
function delete_post_layout( $post_id ) {
	return delete_post_meta( $post_id, theme_layouts_get_meta_key() );
}

/**
 * Checks if a specific post's layout matches that of the given layout.
 *
 * @since  0.3.0
 * @access public
 * @param  string $layout  The name of the layout to check if the post has.
 * @param  int    $post_id The ID of the post to check the layout for.
 * @return bool            Whether the given layout matches the post's layout.
 */
function has_post_layout( $layout, $post_id = '' ) {

	/* If no post ID is given, use WP's get_the_ID() to get it and assume we're in the post loop. */
	if ( empty( $post_id ) )
		$post_id = get_the_ID();

	/* Return true/false based on whether the layout matches. */
	return ( $layout == get_post_layout( $post_id ) ? true : false );
}

/**
 * Get the layout for a user/author archive page based on a specific user ID.
 *
 * @since  0.3.0
 * @access public
 * @param  int    $user_id The ID of the user to get the layout for.
 * @return string          The layout if one exists, 'default' if one doesn't.
 */
function get_user_layout( $user_id ) {

	/* Get the user layout. */
	$layout = get_user_meta( $user_id, theme_layouts_get_meta_key(), true );

	/* Return the layout if one is found.  Otherwise, return 'default'. */
	return ( !empty( $layout ) ? $layout : 'default' );
}

/**
 * Update/set the layout for a user/author archive paged based on the user ID.
 *
 * @since  0.3.0
 * @access public
 * @param  int    $user_id The ID of the user to set the layout for.
 * @param  string $layout  The name of the layout to set.
 * @return bool            True on successful update, false on failure.
 */
function set_user_layout( $user_id, $layout ) {
	return update_user_meta( $user_id, theme_layouts_get_meta_key(), $layout );
}

/**
 * Deletes a user layout.
 *
 * @since  0.4.0
 * @access public
 * @param  int    $user_id The ID of the user to delete the layout for.
 * @return bool            True on successful delete, false on failure.
 */
function delete_user_layout( $user_id ) {
	return delete_user_meta( $user_id, theme_layouts_get_meta_key() );
}

/**
 * Checks if a specific user's layout matches that of the given layout.
 *
 * @since  0.3.0
 * @access public
 * @param  string $layout  The name of the layout to check if the user has.
 * @param  int    $user_id The ID of the user to check the layout for.
 * @return bool            Whether the given layout matches the user's layout.
 */
function has_user_layout( $layout, $user_id = '' ) {

	/* If no user ID is given, assume we're viewing an author archive page and get the user ID. */
	if ( empty( $user_id ) )
		$user_id = get_query_var( 'author' );

	/* Return true/false based on whether the layout matches. */
	return ( $layout == get_user_layout( $user_id ) ? true : false );
}

/**
 * Adds the post layout class to the WordPress body class in the form of "layout-$layout".  This allows 
 * theme developers to design their theme layouts based on the layout class.  If designing a theme with 
 * this extension, the theme should make sure to handle all possible layout classes.
 *
 * @since  0.2.0
 * @access public
 * @param  array $classes
 * @return array $classes
 */
function theme_layouts_body_class( $classes ) {

	/* Adds the layout to array of body classes. */
	$classes[] = sanitize_html_class( theme_layouts_get_layout() );

	/* Return the $classes array. */
	return $classes;
}

/**
 * Creates default text strings based on the default post layouts.  Theme developers that add custom 
 * layouts should filter 'post_layouts_strings' to add strings to match the custom layouts, but it's not 
 * required.  The layout name will be used if no text string is found.
 *
 * @since  0.2.0
 * @access public
 * @return array  $strings
 */
function theme_layouts_strings() {

	/* Set up the default layout strings. */
	$strings = array(
		/* Translators: Default theme layout option. */
		'default' => _x( 'Default', 'theme layout', 'theme-layouts' )
	);

	/* Get theme-supported layouts. */
	$layouts = get_theme_support( 'theme-layouts' );

	/* Assign the strings passed in by the theme author. */
	if ( isset( $layouts[0] ) )
		$strings = array_merge( $layouts[0], $strings );

	/* Allow devs to filter the strings for custom layouts. */
	return apply_filters( 'theme_layouts_strings', $strings );
}

/**
 * Get a specific layout's text string.
 *
 * @since  0.2.0
 * @param  string $layout
 * @return string
 */
function theme_layouts_get_string( $layout ) {

	/* Get an array of post layout strings. */
	$strings = theme_layouts_strings();

	/* Return the layout's string if it exists. Else, return the layout slug. */
	return ( ( isset( $strings[ $layout ] ) ) ? $strings[ $layout ] : $layout );
}

/**
 * Post layouts admin setup.  Registers the post layouts meta box for the post editing screen.  Adds the 
 * metadata save function to the 'save_post' hook.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function theme_layouts_admin_setup() {

	/* Get the extension arguments. */
	$args = theme_layouts_get_args();

	/* Return if the theme doesn't support the post meta box. */
	if ( false === $args['post_meta'] )
		return;

	/* Load the post meta boxes on the new post and edit post screens. */
	add_action( 'load-post.php',     'theme_layouts_load_meta_boxes' );
	add_action( 'load-post-new.php', 'theme_layouts_load_meta_boxes' );

	/* If the attachment post type supports 'theme-layouts', add form fields for it. */
	if ( post_type_supports( 'attachment', 'theme-layouts' ) ) {

		/* Adds a theme layout <select> element to the attachment edit form. */
		add_filter( 'attachment_fields_to_edit', 'theme_layouts_attachment_fields_to_edit', 10, 2 );

		/* Saves the theme layout for attachments. */
		add_filter( 'attachment_fields_to_save', 'theme_layouts_attachment_fields_to_save', 10, 2 );
	}
}

/**
 * Hooks into the 'add_meta_boxes' hook to add the theme layouts meta box and the 'save_post' hook 
 * to save the metadata.
 *
 * @since  0.4.0
 * @access public
 * @return void
 */
function theme_layouts_load_meta_boxes() {

	/* Add the layout meta box on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'theme_layouts_add_meta_boxes', 10, 2 );

	/* Saves the post format on the post editing page. */
	add_action( 'save_post',       'theme_layouts_save_post', 10, 2 );
	add_action( 'add_attachment',  'theme_layouts_save_post'        );
	add_action( 'edit_attachment', 'theme_layouts_save_post'        );
}

/**
 * Adds the theme layouts meta box if the post type supports 'theme-layouts' and the current user has 
 * permission to edit post meta.
 *
 * @since  0.4.0
 * @access public
 * @param  string $post_type The post type of the current post being edited.
 * @param  object $post      The current post object.
 * @return void
 */
function theme_layouts_add_meta_boxes( $post_type, $post ) {

	/* Add the meta box if the post type supports 'post-stylesheets'. */
	if ( ( post_type_supports( $post_type, 'theme-layouts' ) ) && ( current_user_can( 'edit_post_meta', $post->ID ) || current_user_can( 'add_post_meta', $post->ID ) || current_user_can( 'delete_post_meta', $post->ID ) ) )
		add_meta_box( 'theme-layouts-post-meta-box', __( 'Layout', 'theme-layouts' ), 'theme_layouts_post_meta_box', $post_type, 'side', 'default' );
}

/**
 * Displays a meta box of radio selectors on the post editing screen, which allows theme users to select 
 * the layout they wish to use for the specific post.
 *
 * @since  0.2.0
 * @access public
 * @param  object $post The post object currently being edited.
 * @param  array  $box  Specific information about the meta box being loaded.
 * @return void
 */
function theme_layouts_post_meta_box( $post, $box ) {

	/* Get theme-supported theme layouts. */
	$post_layouts = theme_layouts_get_layouts();

	/* Get the current post's layout. */
	$post_layout = get_post_layout( $post->ID ); ?>

	<div class="post-layout">

		<?php wp_nonce_field( basename( __FILE__ ), 'theme-layouts-nonce' ); ?>

		<div class="post-layout-wrap">
			<ul>
				<li><input type="radio" name="post-layout" id="post-layout-default" value="default" <?php checked( $post_layout, 'default' );?> /> <label for="post-layout-default"><?php echo esc_html( theme_layouts_get_string( 'default' ) ); ?></label></li>

				<?php foreach ( $post_layouts as $layout ) { ?>
					<li><input type="radio" name="post-layout" id="post-layout-<?php echo esc_attr( $layout ); ?>" value="<?php echo esc_attr( $layout ); ?>" <?php checked( $post_layout, $layout ); ?> /> <label for="post-layout-<?php echo esc_attr( $layout ); ?>"><?php echo esc_html( theme_layouts_get_string( $layout ) ); ?></label></li>
				<?php } ?>
			</ul>
		</div>
	</div><?php
}

/**
 * Saves the post layout metadata if on the post editing screen in the admin.
 *
 * @since  0.2.0
 * @access public
 * @param  int      $post_id The ID of the current post being saved.
 * @param  object   $post    The post object currently being saved.
 * @return void|int
 */
function theme_layouts_save_post( $post_id, $post = '' ) {

	/* Fix for attachment save issue in WordPress 3.5. @link http://core.trac.wordpress.org/ticket/21963 */
	if ( !is_object( $post ) )
		$post = get_post();

	/* Verify the nonce for the post formats meta box. */
	if ( !isset( $_POST['theme-layouts-nonce'] ) || !wp_verify_nonce( $_POST['theme-layouts-nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the meta key. */
	$meta_key = theme_layouts_get_meta_key();

	/* Get the previous post layout. */
	$meta_value = get_post_layout( $post_id );

	/* Get the submitted post layout. */
	$new_meta_value = $_POST['post-layout'];

	/* If there is no new meta value but an old value exists, delete it. */
	if ( current_user_can( 'delete_post_meta', $post_id, $meta_key ) && '' == $new_meta_value && $meta_value )
		delete_post_layout( $post_id );

	/* If a new meta value was added and there was no previous value, add it. */
	elseif ( current_user_can( 'add_post_meta', $post_id, $meta_key ) && $new_meta_value && '' == $meta_value )
		set_post_layout( $post_id, $new_meta_value );

	/* If the old layout doesn't match the new layout, update the post layout meta. */
	elseif ( current_user_can( 'edit_post_meta', $post_id, $meta_key ) && $meta_value !== $new_meta_value )
		set_post_layout( $post_id, $new_meta_value );
}

/**
 * Adds a select drop-down element to the attachment edit form for selecting the attachment layout.
 *
 * @since  0.3.0
 * @access public
 * @param  array  $fields Array of fields for the edit attachment form.
 * @param  object $post   The attachment post object.
 * @return array  $fields
 */
function theme_layouts_attachment_fields_to_edit( $fields, $post ) {

	/* Get theme-supported theme layouts. */
	$post_layouts = theme_layouts_get_layouts();

	/* Get the current post's layout. */
	$post_layout = get_post_layout( $post->ID );

	/* Set the default post layout. */
	$select = '<option id="post-layout-default" value="default" ' . selected( $post_layout, 'default', false ) . '>' . esc_html( theme_layouts_get_string( 'default' ) ) . '</option>';

	/* Loop through each theme-supported layout, adding it to the select element. */
	foreach ( $post_layouts as $layout )
		$select .= '<option id="post-layout-' . esc_attr( $layout ) . '" value="' . esc_attr( $layout ) . '" ' . selected( $post_layout, $layout, false ) . '>' . esc_html( theme_layouts_get_string( $layout ) ) . '</option>';

	/* Set the HTML for the post layout select drop-down. */
	$select = '<select name="attachments[' . $post->ID . '][theme-layouts-post-layout]" id="attachments[' . $post->ID . '][theme-layouts-post-layout]">' . $select . '</select>';

	/* Add the attachment layout field to the $fields array. */
	$fields['theme-layouts-post-layout'] = array(
		'label'         => __( 'Layout', 'theme-layouts' ),
		'input'         => 'html',
		'html'          => $select,
		'show_in_edit'  => false,
		'show_in_modal' => true
	);

	/* Return the $fields array back to WordPress. */
	return $fields;
}

/**
 * Saves the attachment layout for the attachment edit form.
 *
 * @since  0.3.0
 * @access public
 * @param  array  $post   The attachment post array (not the post object!).
 * @param  array  $fields Array of fields for the edit attachment form.
 * @return array  $post
 */
function theme_layouts_attachment_fields_to_save( $post, $fields ) {

	/* If the theme layouts field was submitted. */
	if ( isset( $fields['theme-layouts-post-layout'] ) ) {

		/* Get the meta key. */
		$meta_key = theme_layouts_get_meta_key();

		/* Get the previous post layout. */
		$meta_value = get_post_layout( $post['ID'] );

		/* Get the submitted post layout. */
		$new_meta_value = $fields['theme-layouts-post-layout'];

		/* If there is no new meta value but an old value exists, delete it. */
		if ( current_user_can( 'delete_post_meta', $post['ID'], $meta_key ) && '' == $new_meta_value && $meta_value )
			delete_post_layout( $post['ID'] );

		/* If a new meta value was added and there was no previous value, add it. */
		elseif ( current_user_can( 'add_post_meta', $post['ID'], $meta_key ) && $new_meta_value && '' == $meta_value )
			set_post_layout( $post['ID'], $new_meta_value );

		/* If the old layout doesn't match the new layout, update the post layout meta. */
		elseif ( current_user_can( 'edit_post_meta', $post['ID'], $meta_key ) && $meta_value !== $new_meta_value )
			set_post_layout( $post['ID'], $new_meta_value );
	}

	/* Return the attachment post array. */
	return $post;
}

/**
 * Wrapper function for returning the metadata key used for objects that can use layouts.
 *
 * @since  0.3.0
 * @access public
 * @return string The meta key used for theme layouts.
 */
function theme_layouts_get_meta_key() {
	return apply_filters( 'theme_layouts_meta_key', 'Layout' );
}

/**
 * Registers custom sections, settings, and controls for the $wp_customize instance.
 *
 * @since     0.1.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @author    Sami Keijonen  <sami.keijonen@foxnet.fi>
 * @copyright Copyright (c) 2012
 * @link      http://themehybrid.com/support/topic/add-theme-layout-in-theme-customize
 * @access    public
 * @param     object $wp_customize
 */
function theme_layouts_customize_register( $wp_customize ) {

	/* Get supported theme layouts. */
	$layouts = theme_layouts_get_layouts();
	$args = theme_layouts_get_args();

	if ( true === $args['customize'] ) {

		/* Add the layout section. */
		$wp_customize->add_section(
			'layout',
			array(
				'title'      => esc_html__( 'Layout', 'theme-layouts' ),
				'priority'   => 30,
				'capability' => 'edit_theme_options'
			)
		);

		/* Add the 'layout' setting. */
		$wp_customize->add_setting(
			'theme_layout',
			array(
				'default'           => get_theme_mod( 'theme_layout', $args['default'] ),
				'type'              => 'theme_mod',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_html_class',
				'transport'         => 'postMessage'
			)
		);

		/* Set up an array for the layout choices and add in the 'default' layout. */
		$layout_choices = array();

		/* Only add 'default' if it's the actual default layout. */
		if ( 'default' == $args['default'] )
			$layout_choices['default'] = theme_layouts_get_string( 'default' );

		/* Loop through each of the layouts and add it to the choices array with proper key/value pairs. */
		foreach ( $layouts as $layout )
			$layout_choices[$layout] = theme_layouts_get_string( $layout );

		/* Add the layout control. */
		$wp_customize->add_control(
			'theme-layout-control',
			array(
				'label'    => esc_html__( 'Global Layout', 'theme-layouts' ),
				'section'  => 'layout',
				'settings' => 'theme_layout',
				'type'     => 'radio',
				'choices'  => $layout_choices
			)
		);

		/* If viewing the customize preview screen, add a script to show a live preview. */
		if ( $wp_customize->is_preview() && !is_admin() )
			add_action( 'wp_footer', 'theme_layouts_customize_preview_script', 21 );
	}
}

/**
 * JavaScript for handling the live preview editing of the theme layout in the theme customizer.  The 
 * script uses regex to remove all potential "layout-xyz" classes and replaces it with the user-selected 
 * layout.
 *
 * @since     0.1.0
 * @access    public
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @author    Sami Keijonen  <sami.keijonen@foxnet.fi>
 * @copyright Copyright (c) 2012
 * @link      http://themehybrid.com/support/topic/add-theme-layout-in-theme-customize
 * @return    void
 */
function theme_layouts_customize_preview_script() { ?>

	<script type="text/javascript">
	wp.customize(
		'theme_layout',
		function( value ) {
			value.bind( 
				function( to ) {
					var classes = jQuery( 'body' ).attr( 'class' ).replace( /layout-[a-zA-Z0-9_-]*/g, '' );
					jQuery( 'body' ).attr( 'class', classes ).addClass( 'layout-' + to );
				} 
			);
		}
	);
	</script>
	<?php
}

/**
 * @since      0.1.0
 * @deprecated 0.2.0 Use theme_layouts_get_layout().
 */
function post_layouts_get_layout() {
	return theme_layouts_get_layout();
}

<?php
/**
 * Ipsum functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

// Loads style.css in the editors too, then the few overrides only the editors need.
if ( ! function_exists( 'ipsum_register_editor_styles' ) ) :
	/**
	 * Adds style.css and editor-style.css as editor stylesheets.
	 *
	 * @since Ipsum 1.0
	 *
	 * @return void
	 */
	function ipsum_register_editor_styles() {
		add_editor_style( array( 'style.css', 'assets/css/editor-style.css' ) );
	}
endif;
add_action( 'after_setup_theme', 'ipsum_register_editor_styles' );

if ( ! function_exists( 'ipsum_add_theme_support' ) ) :
	/**
	 * Adds HTML5 support for navigation widgets, so their lists render inside a
	 * nav element. Block themes already get core's other HTML5 features, and this
	 * call merges with them.
	 *
	 * @since Ipsum 1.0
	 *
	 * @return void
	 */
	function ipsum_add_theme_support() {
		add_theme_support( 'html5', array( 'navigation-widgets' ) );
	}
endif;
add_action( 'after_setup_theme', 'ipsum_add_theme_support' );

if ( ! function_exists( 'ipsum_enqueue_styles' ) ) :
	/**
	 * Enqueues styles.
	 *
	 * @since Ipsum 1.0
	 *
	 * @return void
	 */
	function ipsum_enqueue_styles() {
		// Register theme stylesheet.
		wp_register_style(
			'ipsum-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'ipsum-style' );
	}
endif;
add_action( 'wp_enqueue_scripts', 'ipsum_enqueue_styles' );

if ( ! function_exists( 'ipsum_comments_cta_binding' ) ) :
	/**
	 * Returns the comments call to action for the current post.
	 *
	 * @since Ipsum 1.0
	 *
	 * @param array    $source_args    Block binding source arguments.
	 * @param WP_Block $block_instance The block instance.
	 * @return string The call to action text.
	 */
	function ipsum_comments_cta_binding( $source_args, $block_instance ) {
		$post_id = $block_instance->context['postId'] ?? get_the_ID();

		if ( get_comments_number( $post_id ) > 0 ) {
			$text = __( 'Join the conversation', 'ipsum' );
		} else {
			$text = __( 'Be the first to comment', 'ipsum' );
		}

		return '<a href="' . esc_url( get_comments_link( $post_id ) ) . '">' . esc_html( $text ) . '</a>';
	}
endif;

if ( ! function_exists( 'ipsum_register_block_bindings' ) ) :
	/**
	 * Registers the comments call-to-action block binding source.
	 *
	 * @since Ipsum 1.0
	 *
	 * @return void
	 */
	function ipsum_register_block_bindings() {
		register_block_bindings_source(
			'ipsum/comments-cta',
			array(
				'label'              => __( 'Comments call to action', 'ipsum' ),
				'get_value_callback' => 'ipsum_comments_cta_binding',
				'uses_context'       => array( 'postId' ),
			)
		);
	}
endif;
add_action( 'init', 'ipsum_register_block_bindings' );

if ( ! function_exists( 'ipsum_sidebar_template_types' ) ) :
	/**
	 * Registers the sidebar template variants as template types, so they are
	 * listed with proper titles and descriptions in the Site Editor.
	 *
	 * @since Ipsum 1.0
	 *
	 * @param array $default_template_types The default template types.
	 * @return array The template types, with the sidebar variants added.
	 */
	function ipsum_sidebar_template_types( $default_template_types ) {
		$default_template_types['index-sidebar']   = array(
			'title'       => _x( 'Index Sidebar', 'Template name', 'ipsum' ),
			'description' => __( 'Displays the latest posts beside a sidebar with categories and recent posts.', 'ipsum' ),
		);
		$default_template_types['archive-sidebar'] = array(
			'title'       => _x( 'All Archives Sidebar', 'Template name', 'ipsum' ),
			'description' => __( 'Displays post archives beside a sidebar with categories and recent posts.', 'ipsum' ),
		);
		return $default_template_types;
	}
endif;
add_filter( 'default_template_types', 'ipsum_sidebar_template_types' );

if ( ! function_exists( 'ipsum_register_pattern_categories' ) ) :
	/**
	 * Registers the pattern categories the theme's patterns use beyond core's.
	 *
	 * @since Ipsum 1.0
	 *
	 * @return void
	 */
	function ipsum_register_pattern_categories() {
		register_block_pattern_category(
			'ipsum_page',
			array(
				'label'       => _x( 'Pages', 'Block pattern category', 'ipsum' ),
				'description' => __( 'Full page layouts, ready to be applied to a page.', 'ipsum' ),
			)
		);
	}
endif;
add_action( 'init', 'ipsum_register_pattern_categories' );

if ( ! function_exists( 'ipsum_search_aria_label_support' ) ) :
	/**
	 * Lets the Search block take an ARIA label, so its search landmark can be named.
	 *
	 * Every Search block is a search landmark, and a page can hold more than one,
	 * such as the search bar next to the search call to action, or next to the
	 * search in the navigation overlay. Repeated landmarks need unique names, but
	 * the block has no ariaLabel support of its own, so this turns on core's and
	 * the patterns set the name.
	 *
	 * @since Ipsum 1.0
	 *
	 * @param array $metadata Metadata for the block type being registered.
	 * @return array The block metadata, with ARIA label support on the Search block.
	 */
	function ipsum_search_aria_label_support( $metadata ) {
		if ( isset( $metadata['name'] ) && 'core/search' === $metadata['name'] ) {
			$metadata['supports']['ariaLabel'] = true;
		}
		return $metadata;
	}
endif;
add_filter( 'block_type_metadata', 'ipsum_search_aria_label_support' );

if ( ! function_exists( 'ipsum_register_block_styles' ) ) :
	/**
	 * Registers block style variations.
	 *
	 * The Evening code style keeps the Evening palette's literals on purpose,
	 * so the block reads as a dark slab under every style variation.
	 *
	 * @since Ipsum 1.0
	 *
	 * @return void
	 */
	function ipsum_register_block_styles() {
		register_block_style(
			'core/code',
			array(
				'name'         => 'evening',
				'label'        => _x( 'Evening', 'Block style label', 'ipsum' ),
				'inline_style' => '.wp-block-code.is-style-evening{background-color:#000000;color:#fafafa;border-color:#fafafa40;}',
			)
		);

		register_block_style(
			'core/site-logo',
			array(
				'name'         => 'sharp',
				'label'        => _x( 'Sharp', 'Block style label', 'ipsum' ),
				'inline_style' => '.wp-block-site-logo.is-style-sharp{border-radius:0;}',
			)
		);
	}
endif;
add_action( 'init', 'ipsum_register_block_styles' );

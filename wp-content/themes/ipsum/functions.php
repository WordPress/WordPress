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
		$style_path     = get_parent_theme_file_path( 'style.css' );
		$style_min_path = get_parent_theme_file_path( 'style.min.css' );

		/*
		 * Use the minified stylesheet, unless script debugging is on or style.css
		 * has changed since style.min.css was built. That keeps edits made in the
		 * Theme File Editor working: saving style.css makes it the newer file.
		 */
		if (
			SCRIPT_DEBUG
			|| ! file_exists( $style_min_path )
			|| ( filemtime( $style_path ) > filemtime( $style_min_path ) )
		) {
			$src = 'style.css';
		} else {
			$src = 'style.min.css';
		}

		// Register theme stylesheet.
		wp_register_style(
			'ipsum-style',
			get_parent_theme_file_uri( $src ),
			array(),
			wp_get_theme( get_template() )->get( 'Version' )
		);

		// Let WordPress inline the stylesheet, so it is not a render-blocking request.
		wp_style_add_data( 'ipsum-style', 'path', get_parent_theme_file_path( $src ) );

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'ipsum-style' );
	}
endif;
add_action( 'wp_enqueue_scripts', 'ipsum_enqueue_styles' );

if ( ! function_exists( 'ipsum_comments_cta_binding' ) ) :
	/**
	 * Returns the comments call to action for the current post, or nothing when
	 * comments are closed, since readers can no longer comment.
	 *
	 * @since Ipsum 1.0
	 *
	 * @param array<string, mixed> $source_args    Block binding source arguments. Unused.
	 * @param WP_Block             $block_instance The block instance.
	 * @return string|null The call to action text, an empty string when comments
	 *                     are closed, or null to keep the block's original content
	 *                     when there is no post.
	 */
	function ipsum_comments_cta_binding( $source_args, $block_instance ) {
		unset( $source_args ); // Unused, but must be accepted to get the block instance.

		$post_id = $block_instance->context['postId'] ?? null;
		$post    = get_post( is_numeric( $post_id ) ? (int) $post_id : null );
		if ( ! $post ) {
			return null;
		}

		if ( ! comments_open( $post ) ) {
			return '';
		}

		$post_title = wp_strip_all_tags( get_the_title( $post ) );
		$has_title  = ! empty( $post_title );

		if ( get_comments_number( $post ) > 0 ) {
			$text = $has_title
				? sprintf(
					/* translators: %s: Post title. */
					__( 'Join the conversation<span class="screen-reader-text"> on %s</span>', 'ipsum' ),
					esc_html( $post_title )
				)
				: __( 'Join the conversation', 'ipsum' );
		} else {
			$text = $has_title
				? sprintf(
					/* translators: %s: Post title. */
					__( 'Be the first to comment<span class="screen-reader-text"> on %s</span>', 'ipsum' ),
					esc_html( $post_title )
				)
				: __( 'Be the first to comment', 'ipsum' );
		}

		return '<a href="' . esc_url( get_comments_link( $post ) ) . '">' . wp_kses_post( $text ) . '</a>';
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
	 * @param array<string, array<string, string>> $default_template_types The default template types, keyed by
	 *                                                                     slug, each with a title and description.
	 * @return array<string, array<string, string>> The template types, with the sidebar variants added.
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

		register_block_pattern_category(
			'ipsum_post-format',
			array(
				'label'       => _x( 'Post formats', 'Block pattern category', 'ipsum' ),
				'description' => __( 'Layouts for post formats, like audio and link posts.', 'ipsum' ),
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
	 * @param array<string, mixed> $metadata Metadata for the block type being registered.
	 * @return array<string, mixed> The block metadata, with ARIA label support on the Search block.
	 */
	function ipsum_search_aria_label_support( $metadata ) {
		if ( isset( $metadata['name'] ) && 'core/search' === $metadata['name'] ) {
			$supports              = isset( $metadata['supports'] ) && is_array( $metadata['supports'] ) ? $metadata['supports'] : array();
			$supports['ariaLabel'] = true;
			$metadata['supports']  = $supports;
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
	 * Each style's CSS is an inline style, as in the default themes. Row Hover
	 * can't move to styles/blocks/, because a theme.json css string cannot hold
	 * its at-rules and selector lists.
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

		/*
		 * Pins the part to the bottom of the viewport, so it belongs at the root
		 * of the template. It keeps to its content's width at the inline end, so
		 * the rest of the band doesn't block clicks on the page. The editors also
		 * need the override in editor-style.css.
		 */
		register_block_style(
			'core/template-part',
			array(
				'name'         => 'sticky-bottom',
				'label'        => _x( 'Pinned to Bottom', 'Block style label', 'ipsum' ),
				'inline_style' => '
				.wp-block-template-part.is-style-sticky-bottom {
					position: sticky;
					inset-block-end: 0;
					z-index: 10;
					width: fit-content;
					margin-inline-start: auto;
				}',
			)
		);

		/*
		 * Outlines the Search button until it is hovered, focused or pressed, when
		 * the global button states take back over. A theme.json css string would
		 * wrap this in :where() and only tie with the global button styles.
		 */
		register_block_style(
			'core/search',
			array(
				'name'         => 'outline',
				'label'        => _x( 'Outline', 'Block style label', 'ipsum' ),
				'inline_style' => '
				.wp-block-search.is-style-outline .wp-block-search__button.wp-element-button:not(:hover):not(:focus):not(:active) {
					background-color: var(--wp--preset--color--base);
					border: 1px dotted var(--wp--preset--color--contrast);
					color: var(--wp--preset--color--contrast);
				}',
			)
		);

		/*
		 * Hovering any part of a post row colors the whole row and tilts its
		 * featured image. Targets the row's child blocks, since the Group spans
		 * the full band. Needs each post's blocks wrapped in one Group.
		 */
		register_block_style(
			'core/post-template',
			array(
				'name'         => 'hover-together',
				'label'        => _x( 'Row Hover', 'Block style label', 'ipsum' ),
				'inline_style' => '
				.wp-block-post-template.is-style-hover-together .wp-block-post > .wp-block-group,
				.wp-block-post-template.is-style-hover-together .wp-block-post > .wp-block-group a {
					transition: color 0.15s ease-out;
				}

				.wp-block-post-template.is-style-hover-together .wp-block-post > .wp-block-group:has(> :hover),
				.wp-block-post-template.is-style-hover-together .wp-block-post > .wp-block-group:has(> :hover) a {
					color: var(--wp--preset--color--accent-3);
				}

				@media (prefers-reduced-motion: no-preference) {
					@keyframes tilt-settle {
						35% {
							transform: rotate(-1deg);
						}

						100% {
							transform: rotate(0deg);
						}
					}

					.wp-block-post-template.is-style-hover-together .wp-block-post > .wp-block-group:has(> :hover) .wp-block-post-featured-image img {
						animation: tilt-settle 0.5s ease-out;
					}
				}',
			)
		);
	}
endif;
add_action( 'init', 'ipsum_register_block_styles' );

// Registers the design guide ability for agents.
require get_parent_theme_file_path( 'inc/abilities.php' );

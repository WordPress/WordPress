<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since Twenty Seventeen 1.0
 */

if ( ! function_exists( 'twentyseventeen_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function twentyseventeen_posted_on() {

		// Get the author name; wrap it in a link.
		$byline = sprintf(
			/* translators: %s: Post author. */
			__( 'by %s', 'twentyseventeen' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_the_author() . '</a></span>'
		);

		// Finally, let's write all of this to the page.
		echo '<span class="posted-on">' . twentyseventeen_time_link() . '</span><span class="byline"> ' . $byline . '</span>';
	}
endif;


if ( ! function_exists( 'twentyseventeen_time_link' ) ) :
	/**
	 * Gets a nicely formatted string for the published date.
	 */
	function twentyseventeen_time_link() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			get_the_date( DATE_W3C ),
			get_the_date(),
			get_the_modified_date( DATE_W3C ),
			get_the_modified_date()
		);

		// Wrap the time string in a link, and preface it with 'Posted on'.
		return sprintf(
			/* translators: %s: Post date. */
			__( '<span class="screen-reader-text">Posted on</span> %s', 'twentyseventeen' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);
	}
endif;


if ( ! function_exists( 'twentyseventeen_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function twentyseventeen_entry_footer() {

		$separate_meta = wp_get_list_item_separator();

		// Get Categories for posts.
		$categories_list = get_the_category_list( $separate_meta );

		// Get Tags for posts.
		$tags_list = get_the_tag_list( '', $separate_meta );

		// We don't want to output .entry-footer if it will be empty, so make sure it is not.
		if ( ( ( twentyseventeen_categorized_blog() && $categories_list ) || $tags_list ) || get_edit_post_link() ) {

			echo '<footer class="entry-footer">';

			if ( 'post' === get_post_type() ) {
				if ( ( $categories_list && twentyseventeen_categorized_blog() ) || $tags_list ) {
					echo '<span class="cat-tags-links">';

					// Make sure there's more than one category before displaying.
					if ( $categories_list && twentyseventeen_categorized_blog() ) {
						echo '<span class="cat-links">' . twentyseventeen_get_svg( array( 'icon' => 'folder-open' ) ) .
							/* translators: Hidden accessibility text. */
							'<span class="screen-reader-text">' . __( 'Categories', 'twentyseventeen' ) . '</span>' .
							$categories_list .
						'</span>';
					}

					if ( $tags_list && ! is_wp_error( $tags_list ) ) {
						echo '<span class="tags-links">' . twentyseventeen_get_svg( array( 'icon' => 'hashtag' ) ) .
							/* translators: Hidden accessibility text. */
							'<span class="screen-reader-text">' . __( 'Tags', 'twentyseventeen' ) . '</span>' .
							$tags_list .
						'</span>';
					}

					echo '</span>';
				}
			}

			twentyseventeen_edit_link();

			echo '</footer> <!-- .entry-footer -->';
		}
	}
endif;


if ( ! function_exists( 'twentyseventeen_edit_link' ) ) :
	/**
	 * Returns an accessibility-friendly link to edit a post or page.
	 *
	 * This also gives a little context about what exactly we're editing
	 * (post or page?) so that users understand a bit more where they are in terms
	 * of the template hierarchy and their content. Helpful when/if the single-page
	 * layout with multiple posts/pages shown gets confusing.
	 */
	function twentyseventeen_edit_link() {
		edit_post_link(
			sprintf(
				/* translators: %s: Post title. Only visible to screen readers. */
				__( 'Edit<span class="screen-reader-text"> "%s"</span>', 'twentyseventeen' ),
				get_the_title()
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

/**
 * Displays a front page section.
 *
 * @global int|string $twentyseventeencounter Front page section counter.
 * @global WP_Post    $post                   Global post object.
 *
 * @param WP_Customize_Partial $partial Partial associated with a selective refresh request.
 * @param int                  $id      Front page section to display.
 */
function twentyseventeen_front_page_section( $partial = null, $id = 0 ) {
	if ( $partial instanceof WP_Customize_Partial ) {
		// Find out the ID and set it up during a selective refresh.
		global $twentyseventeencounter;

		$id = str_replace( 'panel_', '', $partial->id );

		$twentyseventeencounter = $id;
	}

	// Only when in Customizer, use a placeholder for an empty panel.
	$show_panel_placeholder = false;

	global $post; // Modify the global post object before setting up post data.
	if ( get_theme_mod( 'panel_' . $id ) ) {
		$post = get_post( get_theme_mod( 'panel_' . $id ) );
		setup_postdata( $post );
		set_query_var( 'panel', $id );

		if ( $post && in_array( $post->post_status, array( 'publish', 'private' ), true ) ) {
			get_template_part( 'template-parts/page/content', 'front-page-panels' );
		} elseif ( is_customize_preview() ) {
			$show_panel_placeholder = true;
		}

		wp_reset_postdata();
	} elseif ( is_customize_preview() ) {
		$show_panel_placeholder = true;
	}

	if ( $show_panel_placeholder ) {
		// The output placeholder anchor.
		printf(
			'<article class="panel-placeholder panel twentyseventeen-panel twentyseventeen-panel%1$s" id="panel%1$s">' .
			'<span class="twentyseventeen-panel-title">%2$s</span></article>',
			$id,
			/* translators: %s: The section ID. */
			sprintf( __( 'Front Page Section %s Placeholder', 'twentyseventeen' ), $id )
		);
	}
}

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function twentyseventeen_categorized_blog() {
	$category_count = get_transient( 'twentyseventeen_categories' );

	if ( false === $category_count ) {
		// Create an array of all the categories that are attached to posts.
		$categories = get_categories(
			array(
				'fields'     => 'ids',
				'hide_empty' => 1,
				// We only need to know if there is more than one category.
				'number'     => 2,
			)
		);

		// Count the number of categories that are attached to the posts.
		$category_count = count( $categories );

		set_transient( 'twentyseventeen_categories', $category_count );
	}

	// Allow viewing case of 0 or 1 categories in post preview.
	if ( is_preview() ) {
		return true;
	}

	return $category_count > 1;
}


/**
 * Flushes out the transients used in twentyseventeen_categorized_blog.
 */
function twentyseventeen_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'twentyseventeen_categories' );
}
add_action( 'edit_category', 'twentyseventeen_category_transient_flusher' );
add_action( 'save_post', 'twentyseventeen_category_transient_flusher' );

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Fires the wp_body_open action.
	 *
	 * Added for backward compatibility to support pre-5.2.0 WordPress versions.
	 *
	 * @since Twenty Seventeen 2.2
	 */
	function wp_body_open() {
		/**
		 * Fires after the opening <body> tag.
		 *
		 * @since Twenty Seventeen 2.2
		 */
		do_action( 'wp_body_open' );
	}
endif;

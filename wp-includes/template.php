<?php
/**
 * Template loading functions.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Retrieves path to a template.
 *
 * Used to quickly retrieve the path of a template without including the file
 * extension. It will also check the parent theme, if the file exists, with
 * the use of locate_template(). Allows for more generic template location
 * without the use of the other get_*_template() functions.
 *
 * @since 1.5.0
 *
 * @param string   $type      Filename without extension.
 * @param string[] $templates An optional list of template candidates.
 * @return string Full path to template file.
 */
function get_query_template( $type, $templates = array() ) {
	$type = preg_replace( '|[^a-z0-9-]+|', '', $type );

	if ( empty( $templates ) ) {
		$templates = array( "{$type}.php" );
	}

	/**
	 * Filters the list of template filenames that are searched for when retrieving a template to use.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the filename -- minus the file
	 * extension and any non-alphanumeric characters delimiting words -- of the file to load.
	 * The last element in the array should always be the fallback template for this query type.
	 *
	 * Possible hook names include:
	 *
	 *  - `404_template_hierarchy`
	 *  - `archive_template_hierarchy`
	 *  - `attachment_template_hierarchy`
	 *  - `author_template_hierarchy`
	 *  - `category_template_hierarchy`
	 *  - `date_template_hierarchy`
	 *  - `embed_template_hierarchy`
	 *  - `frontpage_template_hierarchy`
	 *  - `home_template_hierarchy`
	 *  - `index_template_hierarchy`
	 *  - `page_template_hierarchy`
	 *  - `paged_template_hierarchy`
	 *  - `privacypolicy_template_hierarchy`
	 *  - `search_template_hierarchy`
	 *  - `single_template_hierarchy`
	 *  - `singular_template_hierarchy`
	 *  - `tag_template_hierarchy`
	 *  - `taxonomy_template_hierarchy`
	 *
	 * @since 4.7.0
	 *
	 * @param string[] $templates A list of template candidates, in descending order of priority.
	 */
	$templates = apply_filters( "{$type}_template_hierarchy", $templates );

	$template = locate_template( $templates );

	$template = locate_block_template( $template, $type, $templates );

	/**
	 * Filters the path of the queried template by type.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the filename -- minus the file
	 * extension and any non-alphanumeric characters delimiting words -- of the file to load.
	 * This hook also applies to various types of files loaded as part of the Template Hierarchy.
	 *
	 * Possible hook names include:
	 *
	 *  - `404_template`
	 *  - `archive_template`
	 *  - `attachment_template`
	 *  - `author_template`
	 *  - `category_template`
	 *  - `date_template`
	 *  - `embed_template`
	 *  - `frontpage_template`
	 *  - `home_template`
	 *  - `index_template`
	 *  - `page_template`
	 *  - `paged_template`
	 *  - `privacypolicy_template`
	 *  - `search_template`
	 *  - `single_template`
	 *  - `singular_template`
	 *  - `tag_template`
	 *  - `taxonomy_template`
	 *
	 * @since 1.5.0
	 * @since 4.8.0 The `$type` and `$templates` parameters were added.
	 *
	 * @param string   $template  Path to the template. See locate_template().
	 * @param string   $type      Sanitized filename without extension.
	 * @param string[] $templates A list of template candidates, in descending order of priority.
	 */
	return apply_filters( "{$type}_template", $template, $type, $templates );
}

/**
 * Retrieves path of index template in current or parent template.
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'index'.
 *
 * @since 3.0.0
 *
 * @see get_query_template()
 *
 * @return string Full path to index template file.
 */
function get_index_template() {
	return get_query_template( 'index' );
}

/**
 * Retrieves path of 404 template in current or parent template.
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is '404'.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to 404 template file.
 */
function get_404_template() {
	return get_query_template( '404' );
}

/**
 * Retrieves path of archive template in current or parent template.
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'archive'.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to archive template file.
 */
function get_archive_template() {
	$post_types = array_filter( (array) get_query_var( 'post_type' ) );

	$templates = array();

	if ( count( $post_types ) === 1 ) {
		$post_type   = reset( $post_types );
		$templates[] = "archive-{$post_type}.php";
	}
	$templates[] = 'archive.php';

	return get_query_template( 'archive', $templates );
}

/**
 * Retrieves path of post type archive template in current or parent template.
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'archive'.
 *
 * @since 3.7.0
 *
 * @see get_archive_template()
 *
 * @return string Full path to archive template file.
 */
function get_post_type_archive_template() {
	$post_type = get_query_var( 'post_type' );
	if ( is_array( $post_type ) ) {
		$post_type = reset( $post_type );
	}

	$obj = get_post_type_object( $post_type );
	if ( ! ( $obj instanceof WP_Post_Type ) || ! $obj->has_archive ) {
		return '';
	}

	return get_archive_template();
}

/**
 * Retrieves path of author template in current or parent template.
 *
 * The hierarchy for this template looks like:
 *
 * 1. author-{nicename}.php
 * 2. author-{id}.php
 * 3. author.php
 *
 * An example of this is:
 *
 * 1. author-john.php
 * 2. author-1.php
 * 3. author.php
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'author'.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to author template file.
 */
function get_author_template() {
	$author = get_queried_object();

	$templates = array();

	if ( $author instanceof WP_User ) {
		$templates[] = "author-{$author->user_nicename}.php";
		$templates[] = "author-{$author->ID}.php";
	}
	$templates[] = 'author.php';

	return get_query_template( 'author', $templates );
}

/**
 * Retrieves path of category template in current or parent template.
 *
 * The hierarchy for this template looks like:
 *
 * 1. category-{slug}.php
 * 2. category-{id}.php
 * 3. category.php
 *
 * An example of this is:
 *
 * 1. category-news.php
 * 2. category-2.php
 * 3. category.php
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'category'.
 *
 * @since 1.5.0
 * @since 4.7.0 The decoded form of `category-{slug}.php` was added to the top of the
 *              template hierarchy when the category slug contains multibyte characters.
 *
 * @see get_query_template()
 *
 * @return string Full path to category template file.
 */
function get_category_template() {
	$category = get_queried_object();

	$templates = array();

	if ( ! empty( $category->slug ) ) {

		$slug_decoded = urldecode( $category->slug );
		if ( $slug_decoded !== $category->slug ) {
			$templates[] = "category-{$slug_decoded}.php";
		}

		$templates[] = "category-{$category->slug}.php";
		$templates[] = "category-{$category->term_id}.php";
	}
	$templates[] = 'category.php';

	return get_query_template( 'category', $templates );
}

/**
 * Retrieves path of tag template in current or parent template.
 *
 * The hierarchy for this template looks like:
 *
 * 1. tag-{slug}.php
 * 2. tag-{id}.php
 * 3. tag.php
 *
 * An example of this is:
 *
 * 1. tag-wordpress.php
 * 2. tag-3.php
 * 3. tag.php
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'tag'.
 *
 * @since 2.3.0
 * @since 4.7.0 The decoded form of `tag-{slug}.php` was added to the top of the
 *              template hierarchy when the tag slug contains multibyte characters.
 *
 * @see get_query_template()
 *
 * @return string Full path to tag template file.
 */
function get_tag_template() {
	$tag = get_queried_object();

	$templates = array();

	if ( ! empty( $tag->slug ) ) {

		$slug_decoded = urldecode( $tag->slug );
		if ( $slug_decoded !== $tag->slug ) {
			$templates[] = "tag-{$slug_decoded}.php";
		}

		$templates[] = "tag-{$tag->slug}.php";
		$templates[] = "tag-{$tag->term_id}.php";
	}
	$templates[] = 'tag.php';

	return get_query_template( 'tag', $templates );
}

/**
 * Retrieves path of custom taxonomy term template in current or parent template.
 *
 * The hierarchy for this template looks like:
 *
 * 1. taxonomy-{taxonomy_slug}-{term_slug}.php
 * 2. taxonomy-{taxonomy_slug}-{term_id}.php
 * 3. taxonomy-{taxonomy_slug}.php
 * 4. taxonomy.php
 *
 * An example of this is:
 *
 * 1. taxonomy-location-texas.php
 * 2. taxonomy-location-67.php
 * 3. taxonomy-location.php
 * 4. taxonomy.php
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'taxonomy'.
 *
 * @since 2.5.0
 * @since 4.7.0 The decoded form of `taxonomy-{taxonomy_slug}-{term_slug}.php` was added to the top of the
 *              template hierarchy when the term slug contains multibyte characters.
 * @since 6.9.0 Added `taxonomy-{taxonomy_slug}-{term_id}.php` to the hierarchy.
 *
 * @see get_query_template()
 *
 * @return string Full path to custom taxonomy term template file.
 */
function get_taxonomy_template() {
	$term = get_queried_object();

	$templates = array();

	if ( ! empty( $term->slug ) ) {
		$taxonomy = $term->taxonomy;

		$slug_decoded = urldecode( $term->slug );
		if ( $slug_decoded !== $term->slug ) {
			$templates[] = "taxonomy-$taxonomy-{$slug_decoded}.php";
		}

		$templates[] = "taxonomy-$taxonomy-{$term->slug}.php";
		$templates[] = "taxonomy-$taxonomy-{$term->term_id}.php";
		$templates[] = "taxonomy-$taxonomy.php";
	}
	$templates[] = 'taxonomy.php';

	return get_query_template( 'taxonomy', $templates );
}

/**
 * Retrieves path of date template in current or parent template.
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'date'.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to date template file.
 */
function get_date_template() {
	return get_query_template( 'date' );
}

/**
 * Retrieves path of home template in current or parent template.
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'home'.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to home template file.
 */
function get_home_template() {
	$templates = array( 'home.php', 'index.php' );

	return get_query_template( 'home', $templates );
}

/**
 * Retrieves path of front page template in current or parent template.
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'frontpage'.
 *
 * @since 3.0.0
 *
 * @see get_query_template()
 *
 * @return string Full path to front page template file.
 */
function get_front_page_template() {
	$templates = array( 'front-page.php' );

	return get_query_template( 'frontpage', $templates );
}

/**
 * Retrieves path of Privacy Policy page template in current or parent template.
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'privacypolicy'.
 *
 * @since 5.2.0
 *
 * @see get_query_template()
 *
 * @return string Full path to privacy policy template file.
 */
function get_privacy_policy_template() {
	$templates = array( 'privacy-policy.php' );

	return get_query_template( 'privacypolicy', $templates );
}

/**
 * Retrieves path of page template in current or parent template.
 *
 * Note: For block themes, use locate_block_template() function instead.
 *
 * The hierarchy for this template looks like:
 *
 * 1. {Page Template}.php
 * 2. page-{page_name}.php
 * 3. page-{id}.php
 * 4. page.php
 *
 * An example of this is:
 *
 * 1. page-templates/full-width.php
 * 2. page-about.php
 * 3. page-4.php
 * 4. page.php
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'page'.
 *
 * @since 1.5.0
 * @since 4.7.0 The decoded form of `page-{page_name}.php` was added to the top of the
 *              template hierarchy when the page name contains multibyte characters.
 *
 * @see get_query_template()
 *
 * @return string Full path to page template file.
 */
function get_page_template() {
	$id       = get_queried_object_id();
	$template = get_page_template_slug();
	$pagename = get_query_var( 'pagename' );

	if ( ! $pagename && $id ) {
		/*
		 * If a static page is set as the front page, $pagename will not be set.
		 * Retrieve it from the queried object.
		 */
		$post = get_queried_object();
		if ( $post ) {
			$pagename = $post->post_name;
		}
	}

	$templates = array();
	if ( $template && 0 === validate_file( $template ) ) {
		$templates[] = $template;
	}
	if ( $pagename ) {
		$pagename_decoded = urldecode( $pagename );
		if ( $pagename_decoded !== $pagename ) {
			$templates[] = "page-{$pagename_decoded}.php";
		}
		$templates[] = "page-{$pagename}.php";
	}
	if ( $id ) {
		$templates[] = "page-{$id}.php";
	}
	$templates[] = 'page.php';

	return get_query_template( 'page', $templates );
}

/**
 * Retrieves path of search template in current or parent template.
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'search'.
 *
 * @since 1.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to search template file.
 */
function get_search_template() {
	return get_query_template( 'search' );
}

/**
 * Retrieves path of single template in current or parent template. Applies to single Posts,
 * single Attachments, and single custom post types.
 *
 * The hierarchy for this template looks like:
 *
 * 1. {Post Type Template}.php
 * 2. single-{post_type}-{post_name}.php
 * 3. single-{post_type}.php
 * 4. single.php
 *
 * An example of this is:
 *
 * 1. templates/full-width.php
 * 2. single-post-hello-world.php
 * 3. single-post.php
 * 4. single.php
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'single'.
 *
 * @since 1.5.0
 * @since 4.4.0 `single-{post_type}-{post_name}.php` was added to the top of the template hierarchy.
 * @since 4.7.0 The decoded form of `single-{post_type}-{post_name}.php` was added to the top of the
 *              template hierarchy when the post name contains multibyte characters.
 * @since 4.7.0 `{Post Type Template}.php` was added to the top of the template hierarchy.
 *
 * @see get_query_template()
 *
 * @return string Full path to single template file.
 */
function get_single_template() {
	$object = get_queried_object();

	$templates = array();

	if ( ! empty( $object->post_type ) ) {
		$template = get_page_template_slug( $object );
		if ( $template && 0 === validate_file( $template ) ) {
			$templates[] = $template;
		}

		$name_decoded = urldecode( $object->post_name );
		if ( $name_decoded !== $object->post_name ) {
			$templates[] = "single-{$object->post_type}-{$name_decoded}.php";
		}

		$templates[] = "single-{$object->post_type}-{$object->post_name}.php";
		$templates[] = "single-{$object->post_type}.php";
	}

	$templates[] = 'single.php';

	return get_query_template( 'single', $templates );
}

/**
 * Retrieves an embed template path in the current or parent template.
 *
 * The hierarchy for this template looks like:
 *
 * 1. embed-{post_type}-{post_format}.php
 * 2. embed-{post_type}.php
 * 3. embed.php
 *
 * An example of this is:
 *
 * 1. embed-post-audio.php
 * 2. embed-post.php
 * 3. embed.php
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'embed'.
 *
 * @since 4.5.0
 *
 * @see get_query_template()
 *
 * @return string Full path to embed template file.
 */
function get_embed_template() {
	$object = get_queried_object();

	$templates = array();

	if ( ! empty( $object->post_type ) ) {
		$post_format = get_post_format( $object );
		if ( $post_format ) {
			$templates[] = "embed-{$object->post_type}-{$post_format}.php";
		}
		$templates[] = "embed-{$object->post_type}.php";
	}

	$templates[] = 'embed.php';

	return get_query_template( 'embed', $templates );
}

/**
 * Retrieves the path of the singular template in current or parent template.
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'singular'.
 *
 * @since 4.3.0
 *
 * @see get_query_template()
 *
 * @return string Full path to singular template file.
 */
function get_singular_template() {
	return get_query_template( 'singular' );
}

/**
 * Retrieves path of attachment template in current or parent template.
 *
 * The hierarchy for this template looks like:
 *
 * 1. {mime_type}-{sub_type}.php
 * 2. {sub_type}.php
 * 3. {mime_type}.php
 * 4. attachment.php
 *
 * An example of this is:
 *
 * 1. image-jpeg.php
 * 2. jpeg.php
 * 3. image.php
 * 4. attachment.php
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'attachment'.
 *
 * @since 2.0.0
 * @since 4.3.0 The order of the mime type logic was reversed so the hierarchy is more logical.
 *
 * @see get_query_template()
 *
 * @return string Full path to attachment template file.
 */
function get_attachment_template() {
	$attachment = get_queried_object();

	$templates = array();

	if ( $attachment ) {
		if ( str_contains( $attachment->post_mime_type, '/' ) ) {
			list( $type, $subtype ) = explode( '/', $attachment->post_mime_type );
		} else {
			list( $type, $subtype ) = array( $attachment->post_mime_type, '' );
		}

		if ( ! empty( $subtype ) ) {
			$templates[] = "{$type}-{$subtype}.php";
			$templates[] = "{$subtype}.php";
		}
		$templates[] = "{$type}.php";
	}
	$templates[] = 'attachment.php';

	return get_query_template( 'attachment', $templates );
}

/**
 * Set up the globals used for template loading.
 *
 * @since 6.5.0
 *
 * @global string $wp_stylesheet_path Path to current theme's stylesheet directory.
 * @global string $wp_template_path   Path to current theme's template directory.
 */
function wp_set_template_globals() {
	global $wp_stylesheet_path, $wp_template_path;

	$wp_stylesheet_path = get_stylesheet_directory();
	$wp_template_path   = get_template_directory();
}

/**
 * Retrieves the name of the highest priority template file that exists.
 *
 * Searches in the stylesheet directory before the template directory and
 * wp-includes/theme-compat so that themes which inherit from a parent theme
 * can just overload one file.
 *
 * @since 2.7.0
 * @since 5.5.0 The `$args` parameter was added.
 *
 * @global string $wp_stylesheet_path Path to current theme's stylesheet directory.
 * @global string $wp_template_path   Path to current theme's template directory.
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool         $load           If true the template file will be loaded if it is found.
 * @param bool         $load_once      Whether to require_once or require. Has no effect if `$load` is false.
 *                                     Default true.
 * @param array        $args           Optional. Additional arguments passed to the template.
 *                                     Default empty array.
 * @return string The template filename if one is located.
 */
function locate_template( $template_names, $load = false, $load_once = true, $args = array() ) {
	global $wp_stylesheet_path, $wp_template_path;

	if ( ! isset( $wp_stylesheet_path ) || ! isset( $wp_template_path ) ) {
		wp_set_template_globals();
	}

	$is_child_theme = is_child_theme();

	$located = '';
	foreach ( (array) $template_names as $template_name ) {
		if ( ! $template_name ) {
			continue;
		}
		if ( file_exists( $wp_stylesheet_path . '/' . $template_name ) ) {
			$located = $wp_stylesheet_path . '/' . $template_name;
			break;
		} elseif ( $is_child_theme && file_exists( $wp_template_path . '/' . $template_name ) ) {
			$located = $wp_template_path . '/' . $template_name;
			break;
		} elseif ( file_exists( ABSPATH . WPINC . '/theme-compat/' . $template_name ) ) {
			$located = ABSPATH . WPINC . '/theme-compat/' . $template_name;
			break;
		}
	}

	if ( $load && '' !== $located ) {
		load_template( $located, $load_once, $args );
	}

	return $located;
}

/**
 * Requires the template file with WordPress environment.
 *
 * The globals are set up for the template file to ensure that the WordPress
 * environment is available from within the function. The query variables are
 * also available.
 *
 * @since 1.5.0
 * @since 5.5.0 The `$args` parameter was added.
 *
 * @global array      $posts
 * @global WP_Post    $post          Global post object.
 * @global bool       $wp_did_header
 * @global WP_Query   $wp_query      WordPress Query object.
 * @global WP_Rewrite $wp_rewrite    WordPress rewrite component.
 * @global wpdb       $wpdb          WordPress database abstraction object.
 * @global string     $wp_version
 * @global WP         $wp            Current WordPress environment instance.
 * @global int        $id
 * @global WP_Comment $comment       Global comment object.
 * @global int        $user_ID
 *
 * @param string $_template_file Path to template file.
 * @param bool   $load_once      Whether to require_once or require. Default true.
 * @param array  $args           Optional. Additional arguments passed to the template.
 *                               Default empty array.
 */
function load_template( $_template_file, $load_once = true, $args = array() ) {
	global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

	if ( is_array( $wp_query->query_vars ) ) {
		/*
		 * This use of extract() cannot be removed. There are many possible ways that
		 * templates could depend on variables that it creates existing, and no way to
		 * detect and deprecate it.
		 *
		 * Passing the EXTR_SKIP flag is the safest option, ensuring globals and
		 * function variables cannot be overwritten.
		 */
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract( $wp_query->query_vars, EXTR_SKIP );
	}

	if ( isset( $s ) ) {
		$s = esc_attr( $s );
	}

	/**
	 * Fires before a template file is loaded.
	 *
	 * @since 6.1.0
	 *
	 * @param string $_template_file The full path to the template file.
	 * @param bool   $load_once      Whether to require_once or require.
	 * @param array  $args           Additional arguments passed to the template.
	 */
	do_action( 'wp_before_load_template', $_template_file, $load_once, $args );

	if ( $load_once ) {
		require_once $_template_file;
	} else {
		require $_template_file;
	}

	/**
	 * Fires after a template file is loaded.
	 *
	 * @since 6.1.0
	 *
	 * @param string $_template_file The full path to the template file.
	 * @param bool   $load_once      Whether to require_once or require.
	 * @param array  $args           Additional arguments passed to the template.
	 */
	do_action( 'wp_after_load_template', $_template_file, $load_once, $args );
}

/**
 * Checks whether the template should be output buffered for enhancement.
 *
 * By default, an output buffer is only started if a {@see 'wp_template_enhancement_output_buffer'} filter has been
 * added by the time a template is included at the {@see 'wp_before_include_template'} action. This allows template
 * responses to be streamed as much as possible when no template enhancements are registered to apply.
 *
 * @since 6.9.0
 *
 * @return bool Whether the template should be output-buffered for enhancement.
 */
function wp_should_output_buffer_template_for_enhancement(): bool {
	/**
	 * Filters whether the template should be output-buffered for enhancement.
	 *
	 * By default, an output buffer is only started if a {@see 'wp_template_enhancement_output_buffer'} filter has been
	 * added or if a plugin has added a {@see 'wp_finalized_template_enhancement_output_buffer'} action. For this
	 * default to apply, either of the hooks must be added by the time the template is included at the
	 * {@see 'wp_before_include_template'} action. This allows template responses to be streamed unless the there is
	 * code which depends on an output buffer being opened. This filter allows a site to opt in to adding such template
	 * enhancement filters later during the rendering of the template.
	 *
	 * @since 6.9.0
	 *
	 * @param bool $use_output_buffer Whether an output buffer is started.
	 */
	return (bool) apply_filters( 'wp_should_output_buffer_template_for_enhancement', has_filter( 'wp_template_enhancement_output_buffer' ) || has_action( 'wp_finalized_template_enhancement_output_buffer' ) );
}

/**
 * Starts the template enhancement output buffer.
 *
 * This function is called immediately before the template is included.
 *
 * @since 6.9.0
 *
 * @return bool Whether the output buffer successfully started.
 */
function wp_start_template_enhancement_output_buffer(): bool {
	if ( ! wp_should_output_buffer_template_for_enhancement() ) {
		return false;
	}

	$started = ob_start(
		'wp_finalize_template_enhancement_output_buffer',
		0, // Unlimited buffer size so that entire output is passed to the filter.
		/*
		 * Instead of the default PHP_OUTPUT_HANDLER_STDFLAGS (cleanable, flushable, and removable) being used for
		 * flags, the PHP_OUTPUT_HANDLER_FLUSHABLE flag must be omitted. If the buffer were flushable, then each time
		 * that ob_flush() is called, a fragment of the output would be sent into the output buffer callback. This
		 * output buffer is intended to capture the entire response for processing, as indicated by the chunk size of 0.
		 * So the buffer does not allow flushing to ensure the entire buffer can be processed, such as for optimizing an
		 * entire HTML document, where markup in the HEAD may need to be adjusted based on markup that appears late in
		 * the BODY.
		 *
		 * If this ends up being problematic, then PHP_OUTPUT_HANDLER_FLUSHABLE could be added to the $flags and the
		 * output buffer callback could check if the phase is PHP_OUTPUT_HANDLER_FLUSH and abort any subsequent
		 * processing while also emitting a _doing_it_wrong().
		 *
		 * The output buffer needs to be removable because WordPress calls wp_ob_end_flush_all() and then calls
		 * wp_cache_close(). If the buffers are not all flushed before wp_cache_close() is closed, then some output buffer
		 * handlers (e.g. for caching plugins) may fail to be able to store the page output in the object cache.
		 * See <https://github.com/WordPress/performance/pull/1317#issuecomment-2271955356>.
		 */
		PHP_OUTPUT_HANDLER_STDFLAGS ^ PHP_OUTPUT_HANDLER_FLUSHABLE
	);

	if ( $started ) {
		/**
		 * Fires when the template enhancement output buffer has started.
		 *
		 * @since 6.9.0
		 */
		do_action( 'wp_template_enhancement_output_buffer_started' );
	}

	return $started;
}

/**
 * Finalizes the template enhancement output buffer.
 *
 * Checks to see if the output buffer is complete and contains HTML. If so, runs the content through
 * the `wp_template_enhancement_output_buffer` filter.  If not, the original content is returned.
 *
 * @since 6.9.0
 *
 * @see wp_start_template_enhancement_output_buffer()
 *
 * @param string $output Output buffer.
 * @param int    $phase  Phase.
 * @return string Finalized output buffer.
 */
function wp_finalize_template_enhancement_output_buffer( string $output, int $phase ): string {
	// When the output is being cleaned (e.g. pending template is replaced with error page), do not send it through the filter.
	if ( ( $phase & PHP_OUTPUT_HANDLER_CLEAN ) !== 0 ) {
		return $output;
	}

	// Detect if the response is an HTML content type.
	$is_html_content_type = null;
	$html_content_types   = array( 'text/html', 'application/xhtml+xml' );
	foreach ( headers_list() as $header ) {
		$header_parts = explode( ':', strtolower( $header ), 2 );
		if (
			count( $header_parts ) === 2 &&
			'content-type' === $header_parts[0]
		) {
			/*
			 * This is looking for very specific content types, therefore it
			 * doesn’t need to fully parse the header’s value. Instead, it needs
			 * only assert that the content type is one of the static HTML types.
			 *
			 * Example:
			 *
			 *     Content-Type: text/html; charset=utf8
			 *     Content-Type: text/html  ;charset=latin4
			 *     Content-Type:application/xhtml+xml
			 */
			$media_type           = trim( strtok( $header_parts[1], ';' ), " \t" );
			$is_html_content_type = in_array( $media_type, $html_content_types, true );
			break; // PHP only sends the first Content-Type header in the list.
		}
	}
	if ( null === $is_html_content_type ) {
		$is_html_content_type = in_array( ini_get( 'default_mimetype' ), $html_content_types, true );
	}

	// If the content type is not HTML, short-circuit since it is not relevant for enhancement.
	if ( ! $is_html_content_type ) {
		/** This action is documented in wp-includes/template.php */
		do_action( 'wp_finalized_template_enhancement_output_buffer', $output );
		return $output;
	}

	$filtered_output = $output;

	$did_just_catch = false;

	$error_log = array();
	set_error_handler(
		static function ( int $level, string $message, ?string $file = null, ?int $line = null ) use ( &$error_log, &$did_just_catch ) {
			// Switch a user error to an exception so that it can be caught and the buffer can be returned.
			if ( E_USER_ERROR === $level ) {
				throw new Exception( __( 'User error triggered:' ) . ' ' . $message );
			}

			// Display a caught exception as an error since it prevents any of the output buffer filters from applying.
			if ( $did_just_catch ) { // @phpstan-ignore if.alwaysFalse (The variable is set in the catch block below.)
				$level = E_USER_ERROR;
			}

			// Capture a reported error to be displayed by appending to the processed output buffer if display_errors is enabled.
			if ( error_reporting() & $level ) {
				$error_log[] = compact( 'level', 'message', 'file', 'line' );
			}
			return false;
		}
	);
	$original_display_errors = ini_get( 'display_errors' );
	if ( $original_display_errors ) {
		ini_set( 'display_errors', 0 );
	}

	try {
		/**
		 * Filters the template enhancement output buffer prior to sending to the client.
		 *
		 * This filter only applies the HTML output of an included template. This filter is a progressive enhancement
		 * intended for applications such as optimizing markup to improve frontend page load performance. Sites must not
		 * depend on this filter applying since they may opt to stream the responses instead. Callbacks for this filter
		 * are highly discouraged from using regular expressions to do any kind of replacement on the output. Use the
		 * HTML API (either `WP_HTML_Tag_Processor` or `WP_HTML_Processor`), or else use {@see DOM\HtmlDocument} as of
		 * PHP 8.4 which fully supports HTML5.
		 *
		 * Do not print any output during this filter. While filters normally don't print anything, this is especially
		 * important since this applies during an output buffer callback. Prior to PHP 8.5, the output will be silently
		 * omitted, whereas afterward a deprecation notice will be emitted.
		 *
		 * Important: Because this filter is applied inside an output buffer callback (i.e. display handler), any
		 * callbacks added to the filter must not attempt to start their own output buffers. Otherwise, PHP will raise a
		 * fatal error: "Cannot use output buffering in output buffering display handlers."
		 *
		 * @since 6.9.0
		 *
		 * @param string $filtered_output HTML template enhancement output buffer.
		 * @param string $output          Original HTML template output buffer.
		 */
		$filtered_output = (string) apply_filters( 'wp_template_enhancement_output_buffer', $filtered_output, $output );
	} catch ( Throwable $throwable ) {
		// Emit to the error log as a warning not as an error to prevent halting execution.
		$did_just_catch = true;
		trigger_error(
			sprintf(
				/* translators: %s is the throwable class name */
				__( 'Uncaught "%s" thrown:' ),
				get_class( $throwable )
			) . ' ' . $throwable->getMessage(),
			E_USER_WARNING
		);
		$did_just_catch = false;
	}

	try {
		/**
		 * Fires after the template enhancement output buffer has been finalized.
		 *
		 * This happens immediately before the template enhancement output buffer is flushed. No output may be printed
		 * at this action; prior to PHP 8.5, the output will be silently omitted, whereas afterward a deprecation notice
		 * will be emitted. Nevertheless, HTTP headers may be sent, which makes this action complimentary to the
		 * {@see 'send_headers'} action, in which headers may be sent before the template has started rendering. In
		 * contrast, this `wp_finalized_template_enhancement_output_buffer` action is the possible point at which HTTP
		 * headers can be sent. This action does not fire if the "template enhancement output buffer" was not started.
		 * This output buffer is automatically started if this action is added before
		 * {@see wp_start_template_enhancement_output_buffer()} runs at the {@see 'wp_before_include_template'} action
		 * with priority 1000. Before this point, the output buffer will also be started automatically if there was a
		 * {@see 'wp_template_enhancement_output_buffer'} filter added, or if the
		 * {@see 'wp_should_output_buffer_template_for_enhancement'} filter is made to return `true`.
		 *
		 * Important: Because this action fires inside an output buffer callback (i.e. display handler), any callbacks
		 * added to the action must not attempt to start their own output buffers. Otherwise, PHP will raise a fatal
		 * error: "Cannot use output buffering in output buffering display handlers."
		 *
		 * @since 6.9.0
		 *
		 * @param string $output Finalized output buffer.
		 */
		do_action( 'wp_finalized_template_enhancement_output_buffer', $filtered_output );
	} catch ( Throwable $throwable ) {
		// Emit to the error log as a warning not as an error to prevent halting execution.
		$did_just_catch = true;
		trigger_error(
			sprintf(
				/* translators: %s is the class name */
				__( 'Uncaught "%s" thrown:' ),
				get_class( $throwable )
			) . ' ' . $throwable->getMessage(),
			E_USER_WARNING
		);
		$did_just_catch = false;
	}

	// Append any errors to be displayed before returning flushing the buffer.
	if ( $original_display_errors && 'stderr' !== $original_display_errors ) {
		foreach ( $error_log as $error ) {
			switch ( $error['level'] ) {
				case E_USER_NOTICE:
					$type = 'Notice';
					break;
				case E_USER_DEPRECATED:
					$type = 'Deprecated';
					break;
				case E_USER_WARNING:
					$type = 'Warning';
					break;
				default:
					$type = 'Error';
			}

			if ( ini_get( 'html_errors' ) ) {
				/*
				 * Adapted from PHP internals: <https://github.com/php/php-src/blob/a979e9f897a90a580e883b1f39ce5673686ffc67/main/main.c#L1478>.
				 * The self-closing tags are a vestige of the XHTML past!
				 */
				$format = "%s<br />\n<b>%s</b>:  %s in <b>%s</b> on line <b>%s</b><br />\n%s";
			} else {
				// Adapted from PHP internals: <https://github.com/php/php-src/blob/a979e9f897a90a580e883b1f39ce5673686ffc67/main/main.c#L1492>.
				$format = "%s\n%s: %s in %s on line %s\n%s";
			}
			$filtered_output .= sprintf(
				$format,
				ini_get( 'error_prepend_string' ),
				$type,
				$error['message'],
				$error['file'],
				$error['line'],
				ini_get( 'error_append_string' )
			);
		}

		ini_set( 'display_errors', $original_display_errors );
	}

	restore_error_handler();

	return $filtered_output;
}

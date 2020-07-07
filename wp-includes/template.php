<?php
/**
 * Template loading functions.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Retrieve path to a template
 *
 * Used to quickly retrieve the path of a template without including the file
 * extension. It will also check the parent theme, if the file exists, with
 * the use of locate_template(). Allows for more generic template location
 * without the use of the other get_*_template() functions.
 *
 * @since 1.5.0
 *
 * @param string $type      Filename without extension.
 * @param array  $templates An optional list of template candidates
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
	 * The last element in the array should always be the fallback template for this query type.
	 *
	 * Possible values for `$type` include: 'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date',
	 * 'embed', 'home', 'frontpage', 'privacypolicy', 'page', 'paged', 'search', 'single', 'singular', and 'attachment'.
	 *
	 * @since 4.7.0
	 *
	 * @param array $templates A list of template candidates, in descending order of priority.
	 */
	$templates = apply_filters( "{$type}_template_hierarchy", $templates );

	$template = locate_template( $templates );

	/**
	 * Filters the path of the queried template by type.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the filename -- minus the file
	 * extension and any non-alphanumeric characters delimiting words -- of the file to load.
	 * This hook also applies to various types of files loaded as part of the Template Hierarchy.
	 *
	 * Possible values for `$type` include: 'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date',
	 * 'embed', 'home', 'frontpage', 'privacypolicy', 'page', 'paged', 'search', 'single', 'singular', and 'attachment'.
	 *
	 * @since 1.5.0
	 * @since 4.8.0 The `$type` and `$templates` parameters were added.
	 *
	 * @param string $template  Path to the template. See locate_template().
	 * @param string $type      Sanitized filename without extension.
	 * @param array  $templates A list of template candidates, in descending order of priority.
	 */
	return apply_filters( "{$type}_template", $template, $type, $templates );
}

/**
 * Retrieve path of index template in current or parent template.
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
 * Retrieve path of 404 template in current or parent template.
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
 * Retrieve path of archive template in current or parent template.
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

	if ( count( $post_types ) == 1 ) {
		$post_type   = reset( $post_types );
		$templates[] = "archive-{$post_type}.php";
	}
	$templates[] = 'archive.php';

	return get_query_template( 'archive', $templates );
}

/**
 * Retrieve path of post type archive template in current or parent template.
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
 * Retrieve path of author template in current or parent template.
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
 * Retrieve path of category template in current or parent template.
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
 * Retrieve path of tag template in current or parent template.
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
 * Retrieve path of custom taxonomy term template in current or parent template.
 *
 * The hierarchy for this template looks like:
 *
 * 1. taxonomy-{taxonomy_slug}-{term_slug}.php
 * 2. taxonomy-{taxonomy_slug}.php
 * 3. taxonomy.php
 *
 * An example of this is:
 *
 * 1. taxonomy-location-texas.php
 * 2. taxonomy-location.php
 * 3. taxonomy.php
 *
 * The template hierarchy and template path are filterable via the {@see '$type_template_hierarchy'}
 * and {@see '$type_template'} dynamic hooks, where `$type` is 'taxonomy'.
 *
 * @since 2.5.0
 * @since 4.7.0 The decoded form of `taxonomy-{taxonomy_slug}-{term_slug}.php` was added to the top of the
 *              template hierarchy when the term slug contains multibyte characters.
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
		$templates[] = "taxonomy-$taxonomy.php";
	}
	$templates[] = 'taxonomy.php';

	return get_query_template( 'taxonomy', $templates );
}

/**
 * Retrieve path of date template in current or parent template.
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
 * Retrieve path of home template in current or parent template.
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
 * Retrieve path of front page template in current or parent template.
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
 * Retrieve path of Privacy Policy page template in current or parent template.
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
 * Retrieve path of page template in current or parent template.
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
		// If a static page is set as the front page, $pagename will not be set.
		// Retrieve it from the queried object.
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
 * Retrieve path of search template in current or parent template.
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
 * Retrieve path of single template in current or parent template. Applies to single Posts,
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
 * @return string Full path to singular template file
 */
function get_singular_template() {
	return get_query_template( 'singular' );
}

/**
 * Retrieve path of attachment template in current or parent template.
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
 * @global array $posts
 *
 * @return string Full path to attachment template file.
 */
function get_attachment_template() {
	$attachment = get_queried_object();

	$templates = array();

	if ( $attachment ) {
		if ( false !== strpos( $attachment->post_mime_type, '/' ) ) {
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
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the STYLESHEETPATH before TEMPLATEPATH and wp-includes/theme-compat
 * so that themes which inherit from a parent theme can just overload one file.
 *
 * @since 2.7.0
 * @since 5.5.0 The `$args` parameter was added.
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool         $load           If true the template file will be loaded if it is found.
 * @param bool         $require_once   Whether to require_once or require. Has no effect if `$load` is false.
 *                                     Default true.
 * @param array        $args           Optional. Additional arguments passed to the template.
 *                                     Default empty array.
 * @return string The template filename if one is located.
 */
function locate_template( $template_names, $load = false, $require_once = true, $args = array() ) {
	$located = '';
	foreach ( (array) $template_names as $template_name ) {
		if ( ! $template_name ) {
			continue;
		}
		if ( file_exists( STYLESHEETPATH . '/' . $template_name ) ) {
			$located = STYLESHEETPATH . '/' . $template_name;
			break;
		} elseif ( file_exists( TEMPLATEPATH . '/' . $template_name ) ) {
			$located = TEMPLATEPATH . '/' . $template_name;
			break;
		} elseif ( file_exists( ABSPATH . WPINC . '/theme-compat/' . $template_name ) ) {
			$located = ABSPATH . WPINC . '/theme-compat/' . $template_name;
			break;
		}
	}

	if ( $load && '' !== $located ) {
		load_template( $located, $require_once, $args );
	}

	return $located;
}

/**
 * Require the template file with WordPress environment.
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
 * @param bool   $require_once   Whether to require_once or require. Default true.
 * @param array  $args           Optional. Additional arguments passed to the template.
 *                               Default empty array.
 */
function load_template( $_template_file, $require_once = true, $args = array() ) {
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

	if ( $require_once ) {
		require_once $_template_file;
	} else {
		require $_template_file;
	}
}

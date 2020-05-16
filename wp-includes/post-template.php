<?php
/**
 * WordPress Post Template Functions.
 *
 * Gets content for the current post in the loop.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Display the ID of the current item in the WordPress Loop.
 *
 * @since 0.71
 */
function the_ID() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	echo get_the_ID();
}

/**
 * Retrieve the ID of the current item in the WordPress Loop.
 *
 * @since 2.1.0
 *
 * @return int|false The ID of the current item in the WordPress Loop. False if $post is not set.
 */
function get_the_ID() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	$post = get_post();
	return ! empty( $post ) ? $post->ID : false;
}

/**
 * Display or retrieve the current post title with optional markup.
 *
 * @since 0.71
 *
 * @param string $before Optional. Markup to prepend to the title. Default empty.
 * @param string $after  Optional. Markup to append to the title. Default empty.
 * @param bool   $echo   Optional. Whether to echo or return the title. Default true for echo.
 * @return void|string Void if `$echo` argument is true, current post title if `$echo` is false.
 */
function the_title( $before = '', $after = '', $echo = true ) {
	$title = get_the_title();

	if ( strlen( $title ) == 0 ) {
		return;
	}

	$title = $before . $title . $after;

	if ( $echo ) {
		echo $title;
	} else {
		return $title;
	}
}

/**
 * Sanitize the current title when retrieving or displaying.
 *
 * Works like the_title(), except the parameters can be in a string or
 * an array. See the function for what can be override in the $args parameter.
 *
 * The title before it is displayed will have the tags stripped and esc_attr()
 * before it is passed to the user or displayed. The default as with the_title(),
 * is to display the title.
 *
 * @since 2.3.0
 *
 * @param string|array $args {
 *     Title attribute arguments. Optional.
 *
 *     @type string  $before Markup to prepend to the title. Default empty.
 *     @type string  $after  Markup to append to the title. Default empty.
 *     @type bool    $echo   Whether to echo or return the title. Default true for echo.
 *     @type WP_Post $post   Current post object to retrieve the title for.
 * }
 * @return void|string Void if 'echo' argument is true, the title attribute if 'echo' is false.
 */
function the_title_attribute( $args = '' ) {
	$defaults    = array(
		'before' => '',
		'after'  => '',
		'echo'   => true,
		'post'   => get_post(),
	);
	$parsed_args = wp_parse_args( $args, $defaults );

	$title = get_the_title( $parsed_args['post'] );

	if ( strlen( $title ) == 0 ) {
		return;
	}

	$title = $parsed_args['before'] . $title . $parsed_args['after'];
	$title = esc_attr( strip_tags( $title ) );

	if ( $parsed_args['echo'] ) {
		echo $title;
	} else {
		return $title;
	}
}

/**
 * Retrieve post title.
 *
 * If the post is protected and the visitor is not an admin, then "Protected"
 * will be displayed before the post title. If the post is private, then
 * "Private" will be located before the post title.
 *
 * @since 0.71
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string
 */
function get_the_title( $post = 0 ) {
	$post = get_post( $post );

	$title = isset( $post->post_title ) ? $post->post_title : '';
	$id    = isset( $post->ID ) ? $post->ID : 0;

	if ( ! is_admin() ) {
		if ( ! empty( $post->post_password ) ) {

			/* translators: %s: Protected post title. */
			$prepend = __( 'Protected: %s' );

			/**
			 * Filters the text prepended to the post title for protected posts.
			 *
			 * The filter is only applied on the front end.
			 *
			 * @since 2.8.0
			 *
			 * @param string  $prepend Text displayed before the post title.
			 *                         Default 'Protected: %s'.
			 * @param WP_Post $post    Current post object.
			 */
			$protected_title_format = apply_filters( 'protected_title_format', $prepend, $post );
			$title                  = sprintf( $protected_title_format, $title );
		} elseif ( isset( $post->post_status ) && 'private' === $post->post_status ) {

			/* translators: %s: Private post title. */
			$prepend = __( 'Private: %s' );

			/**
			 * Filters the text prepended to the post title of private posts.
			 *
			 * The filter is only applied on the front end.
			 *
			 * @since 2.8.0
			 *
			 * @param string  $prepend Text displayed before the post title.
			 *                         Default 'Private: %s'.
			 * @param WP_Post $post    Current post object.
			 */
			$private_title_format = apply_filters( 'private_title_format', $prepend, $post );
			$title                = sprintf( $private_title_format, $title );
		}
	}

	/**
	 * Filters the post title.
	 *
	 * @since 0.71
	 *
	 * @param string $title The post title.
	 * @param int    $id    The post ID.
	 */
	return apply_filters( 'the_title', $title, $id );
}

/**
 * Display the Post Global Unique Identifier (guid).
 *
 * The guid will appear to be a link, but should not be used as a link to the
 * post. The reason you should not use it as a link, is because of moving the
 * blog across domains.
 *
 * URL is escaped to make it XML-safe.
 *
 * @since 1.5.0
 *
 * @param int|WP_Post $post Optional. Post ID or post object. Default is global $post.
 */
function the_guid( $post = 0 ) {
	$post = get_post( $post );

	$guid = isset( $post->guid ) ? get_the_guid( $post ) : '';
	$id   = isset( $post->ID ) ? $post->ID : 0;

	/**
	 * Filters the escaped Global Unique Identifier (guid) of the post.
	 *
	 * @since 4.2.0
	 *
	 * @see get_the_guid()
	 *
	 * @param string $guid Escaped Global Unique Identifier (guid) of the post.
	 * @param int    $id   The post ID.
	 */
	echo apply_filters( 'the_guid', $guid, $id );
}

/**
 * Retrieve the Post Global Unique Identifier (guid).
 *
 * The guid will appear to be a link, but should not be used as an link to the
 * post. The reason you should not use it as a link, is because of moving the
 * blog across domains.
 *
 * @since 1.5.0
 *
 * @param int|WP_Post $post Optional. Post ID or post object. Default is global $post.
 * @return string
 */
function get_the_guid( $post = 0 ) {
	$post = get_post( $post );

	$guid = isset( $post->guid ) ? $post->guid : '';
	$id   = isset( $post->ID ) ? $post->ID : 0;

	/**
	 * Filters the Global Unique Identifier (guid) of the post.
	 *
	 * @since 1.5.0
	 *
	 * @param string $guid Global Unique Identifier (guid) of the post.
	 * @param int    $id   The post ID.
	 */
	return apply_filters( 'get_the_guid', $guid, $id );
}

/**
 * Display the post content.
 *
 * @since 0.71
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool   $strip_teaser   Optional. Strip teaser content before the more text. Default is false.
 */
function the_content( $more_link_text = null, $strip_teaser = false ) {
	$content = get_the_content( $more_link_text, $strip_teaser );

	/**
	 * Filters the post content.
	 *
	 * @since 0.71
	 *
	 * @param string $content Content of the current post.
	 */
	$content = apply_filters( 'the_content', $content );
	$content = str_replace( ']]>', ']]&gt;', $content );
	echo $content;
}

/**
 * Retrieve the post content.
 *
 * @since 0.71
 * @since 5.2.0 Added the `$post` parameter.
 *
 * @global int   $page      Page number of a single post/page.
 * @global int   $more      Boolean indicator for whether single post/page is being viewed.
 * @global bool  $preview   Whether post/page is in preview mode.
 * @global array $pages     Array of all pages in post/page. Each array element contains
 *                          part of the content separated by the `<!--nextpage-->` tag.
 * @global int   $multipage Boolean indicator for whether multiple pages are in play.
 *
 * @param string             $more_link_text Optional. Content for when there is more text.
 * @param bool               $strip_teaser   Optional. Strip teaser content before the more text. Default is false.
 * @param WP_Post|object|int $post           Optional. WP_Post instance or Post ID/object. Default is null.
 * @return string
 */
function get_the_content( $more_link_text = null, $strip_teaser = false, $post = null ) {
	global $page, $more, $preview, $pages, $multipage;

	$_post = get_post( $post );

	if ( ! ( $_post instanceof WP_Post ) ) {
		return '';
	}

	if ( null === $post ) {
		$elements = compact( 'page', 'more', 'preview', 'pages', 'multipage' );
	} else {
		$elements = generate_postdata( $_post );
	}

	if ( null === $more_link_text ) {
		$more_link_text = sprintf(
			'<span aria-label="%1$s">%2$s</span>',
			sprintf(
				/* translators: %s: Post title. */
				__( 'Continue reading %s' ),
				the_title_attribute(
					array(
						'echo' => false,
						'post' => $_post,
					)
				)
			),
			__( '(more&hellip;)' )
		);
	}

	$output     = '';
	$has_teaser = false;

	// If post password required and it doesn't match the cookie.
	if ( post_password_required( $_post ) ) {
		return get_the_password_form( $_post );
	}

	// If the requested page doesn't exist.
	if ( $elements['page'] > count( $elements['pages'] ) ) {
		// Give them the highest numbered page that DOES exist.
		$elements['page'] = count( $elements['pages'] );
	}

	$page_no = $elements['page'];
	$content = $elements['pages'][ $page_no - 1 ];
	if ( preg_match( '/<!--more(.*?)?-->/', $content, $matches ) ) {
		if ( has_block( 'more', $content ) ) {
			// Remove the core/more block delimiters. They will be left over after $content is split up.
			$content = preg_replace( '/<!-- \/?wp:more(.*?) -->/', '', $content );
		}

		$content = explode( $matches[0], $content, 2 );

		if ( ! empty( $matches[1] ) && ! empty( $more_link_text ) ) {
			$more_link_text = strip_tags( wp_kses_no_null( trim( $matches[1] ) ) );
		}

		$has_teaser = true;
	} else {
		$content = array( $content );
	}

	if ( false !== strpos( $_post->post_content, '<!--noteaser-->' ) && ( ! $elements['multipage'] || 1 == $elements['page'] ) ) {
		$strip_teaser = true;
	}

	$teaser = $content[0];

	if ( $elements['more'] && $strip_teaser && $has_teaser ) {
		$teaser = '';
	}

	$output .= $teaser;

	if ( count( $content ) > 1 ) {
		if ( $elements['more'] ) {
			$output .= '<span id="more-' . $_post->ID . '"></span>' . $content[1];
		} else {
			if ( ! empty( $more_link_text ) ) {

				/**
				 * Filters the Read More link text.
				 *
				 * @since 2.8.0
				 *
				 * @param string $more_link_element Read More link element.
				 * @param string $more_link_text    Read More text.
				 */
				$output .= apply_filters( 'the_content_more_link', ' <a href="' . get_permalink( $_post ) . "#more-{$_post->ID}\" class=\"more-link\">$more_link_text</a>", $more_link_text );
			}
			$output = force_balance_tags( $output );
		}
	}

	return $output;
}

/**
 * Display the post excerpt.
 *
 * @since 0.71
 */
function the_excerpt() {

	/**
	 * Filters the displayed post excerpt.
	 *
	 * @since 0.71
	 *
	 * @see get_the_excerpt()
	 *
	 * @param string $post_excerpt The post excerpt.
	 */
	echo apply_filters( 'the_excerpt', get_the_excerpt() );
}

/**
 * Retrieves the post excerpt.
 *
 * @since 0.71
 * @since 4.5.0 Introduced the `$post` parameter.
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string Post excerpt.
 */
function get_the_excerpt( $post = null ) {
	if ( is_bool( $post ) ) {
		_deprecated_argument( __FUNCTION__, '2.3.0' );
	}

	$post = get_post( $post );
	if ( empty( $post ) ) {
		return '';
	}

	if ( post_password_required( $post ) ) {
		return __( 'There is no excerpt because this is a protected post.' );
	}

	/**
	 * Filters the retrieved post excerpt.
	 *
	 * @since 1.2.0
	 * @since 4.5.0 Introduced the `$post` parameter.
	 *
	 * @param string  $post_excerpt The post excerpt.
	 * @param WP_Post $post         Post object.
	 */
	return apply_filters( 'get_the_excerpt', $post->post_excerpt, $post );
}

/**
 * Determines whether the post has a custom excerpt.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.3.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return bool True if the post has a custom excerpt, false otherwise.
 */
function has_excerpt( $post = 0 ) {
	$post = get_post( $post );
	return ( ! empty( $post->post_excerpt ) );
}

/**
 * Displays the classes for the post container element.
 *
 * @since 2.7.0
 *
 * @param string|array $class   One or more classes to add to the class list.
 * @param int|WP_Post  $post_id Optional. Post ID or post object. Defaults to the global `$post`.
 */
function post_class( $class = '', $post_id = null ) {
	// Separates classes with a single space, collates classes for post DIV.
	echo 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
}

/**
 * Retrieves an array of the class names for the post container element.
 *
 * The class names are many. If the post is a sticky, then the 'sticky'
 * class name. The class 'hentry' is always added to each post. If the post has a
 * post thumbnail, 'has-post-thumbnail' is added as a class. For each taxonomy that
 * the post belongs to, a class will be added of the format '{$taxonomy}-{$slug}' -
 * eg 'category-foo' or 'my_custom_taxonomy-bar'.
 *
 * The 'post_tag' taxonomy is a special
 * case; the class has the 'tag-' prefix instead of 'post_tag-'. All class names are
 * passed through the filter, {@see 'post_class'}, with the list of class names, followed by
 * $class parameter value, with the post ID as the last parameter.
 *
 * @since 2.7.0
 * @since 4.2.0 Custom taxonomy class names were added.
 *
 * @param string|string[] $class   Space-separated string or array of class names to add to the class list.
 * @param int|WP_Post     $post_id Optional. Post ID or post object.
 * @return string[] Array of class names.
 */
function get_post_class( $class = '', $post_id = null ) {
	$post = get_post( $post_id );

	$classes = array();

	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_map( 'esc_attr', $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	if ( ! $post ) {
		return $classes;
	}

	$classes[] = 'post-' . $post->ID;
	if ( ! is_admin() ) {
		$classes[] = $post->post_type;
	}
	$classes[] = 'type-' . $post->post_type;
	$classes[] = 'status-' . $post->post_status;

	// Post Format.
	if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
		$post_format = get_post_format( $post->ID );

		if ( $post_format && ! is_wp_error( $post_format ) ) {
			$classes[] = 'format-' . sanitize_html_class( $post_format );
		} else {
			$classes[] = 'format-standard';
		}
	}

	$post_password_required = post_password_required( $post->ID );

	// Post requires password.
	if ( $post_password_required ) {
		$classes[] = 'post-password-required';
	} elseif ( ! empty( $post->post_password ) ) {
		$classes[] = 'post-password-protected';
	}

	// Post thumbnails.
	if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail( $post->ID ) && ! is_attachment( $post ) && ! $post_password_required ) {
		$classes[] = 'has-post-thumbnail';
	}

	// Sticky for Sticky Posts.
	if ( is_sticky( $post->ID ) ) {
		if ( is_home() && ! is_paged() ) {
			$classes[] = 'sticky';
		} elseif ( is_admin() ) {
			$classes[] = 'status-sticky';
		}
	}

	// hentry for hAtom compliance.
	$classes[] = 'hentry';

	// All public taxonomies.
	$taxonomies = get_taxonomies( array( 'public' => true ) );
	foreach ( (array) $taxonomies as $taxonomy ) {
		if ( is_object_in_taxonomy( $post->post_type, $taxonomy ) ) {
			foreach ( (array) get_the_terms( $post->ID, $taxonomy ) as $term ) {
				if ( empty( $term->slug ) ) {
					continue;
				}

				$term_class = sanitize_html_class( $term->slug, $term->term_id );
				if ( is_numeric( $term_class ) || ! trim( $term_class, '-' ) ) {
					$term_class = $term->term_id;
				}

				// 'post_tag' uses the 'tag' prefix for backward compatibility.
				if ( 'post_tag' === $taxonomy ) {
					$classes[] = 'tag-' . $term_class;
				} else {
					$classes[] = sanitize_html_class( $taxonomy . '-' . $term_class, $taxonomy . '-' . $term->term_id );
				}
			}
		}
	}

	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filters the list of CSS class names for the current post.
	 *
	 * @since 2.7.0
	 *
	 * @param string[] $classes An array of post class names.
	 * @param string[] $class   An array of additional class names added to the post.
	 * @param int      $post_id The post ID.
	 */
	$classes = apply_filters( 'post_class', $classes, $class, $post->ID );

	return array_unique( $classes );
}

/**
 * Displays the class names for the body element.
 *
 * @since 2.8.0
 *
 * @param string|string[] $class Space-separated string or array of class names to add to the class list.
 */
function body_class( $class = '' ) {
	// Separates class names with a single space, collates class names for body element.
	echo 'class="' . join( ' ', get_body_class( $class ) ) . '"';
}

/**
 * Retrieves an array of the class names for the body element.
 *
 * @since 2.8.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param string|string[] $class Space-separated string or array of class names to add to the class list.
 * @return string[] Array of class names.
 */
function get_body_class( $class = '' ) {
	global $wp_query;

	$classes = array();

	if ( is_rtl() ) {
		$classes[] = 'rtl';
	}

	if ( is_front_page() ) {
		$classes[] = 'home';
	}
	if ( is_home() ) {
		$classes[] = 'blog';
	}
	if ( is_privacy_policy() ) {
		$classes[] = 'privacy-policy';
	}
	if ( is_archive() ) {
		$classes[] = 'archive';
	}
	if ( is_date() ) {
		$classes[] = 'date';
	}
	if ( is_search() ) {
		$classes[] = 'search';
		$classes[] = $wp_query->posts ? 'search-results' : 'search-no-results';
	}
	if ( is_paged() ) {
		$classes[] = 'paged';
	}
	if ( is_attachment() ) {
		$classes[] = 'attachment';
	}
	if ( is_404() ) {
		$classes[] = 'error404';
	}

	if ( is_singular() ) {
		$post_id   = $wp_query->get_queried_object_id();
		$post      = $wp_query->get_queried_object();
		$post_type = $post->post_type;

		if ( is_page_template() ) {
			$classes[] = "{$post_type}-template";

			$template_slug  = get_page_template_slug( $post_id );
			$template_parts = explode( '/', $template_slug );

			foreach ( $template_parts as $part ) {
				$classes[] = "{$post_type}-template-" . sanitize_html_class( str_replace( array( '.', '/' ), '-', basename( $part, '.php' ) ) );
			}
			$classes[] = "{$post_type}-template-" . sanitize_html_class( str_replace( '.', '-', $template_slug ) );
		} else {
			$classes[] = "{$post_type}-template-default";
		}

		if ( is_single() ) {
			$classes[] = 'single';
			if ( isset( $post->post_type ) ) {
				$classes[] = 'single-' . sanitize_html_class( $post->post_type, $post_id );
				$classes[] = 'postid-' . $post_id;

				// Post Format.
				if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
					$post_format = get_post_format( $post->ID );

					if ( $post_format && ! is_wp_error( $post_format ) ) {
						$classes[] = 'single-format-' . sanitize_html_class( $post_format );
					} else {
						$classes[] = 'single-format-standard';
					}
				}
			}
		}

		if ( is_attachment() ) {
			$mime_type   = get_post_mime_type( $post_id );
			$mime_prefix = array( 'application/', 'image/', 'text/', 'audio/', 'video/', 'music/' );
			$classes[]   = 'attachmentid-' . $post_id;
			$classes[]   = 'attachment-' . str_replace( $mime_prefix, '', $mime_type );
		} elseif ( is_page() ) {
			$classes[] = 'page';

			$page_id = $wp_query->get_queried_object_id();

			$post = get_post( $page_id );

			$classes[] = 'page-id-' . $page_id;

			if ( get_pages(
				array(
					'parent' => $page_id,
					'number' => 1,
				)
			) ) {
				$classes[] = 'page-parent';
			}

			if ( $post->post_parent ) {
				$classes[] = 'page-child';
				$classes[] = 'parent-pageid-' . $post->post_parent;
			}
		}
	} elseif ( is_archive() ) {
		if ( is_post_type_archive() ) {
			$classes[] = 'post-type-archive';
			$post_type = get_query_var( 'post_type' );
			if ( is_array( $post_type ) ) {
				$post_type = reset( $post_type );
			}
			$classes[] = 'post-type-archive-' . sanitize_html_class( $post_type );
		} elseif ( is_author() ) {
			$author    = $wp_query->get_queried_object();
			$classes[] = 'author';
			if ( isset( $author->user_nicename ) ) {
				$classes[] = 'author-' . sanitize_html_class( $author->user_nicename, $author->ID );
				$classes[] = 'author-' . $author->ID;
			}
		} elseif ( is_category() ) {
			$cat       = $wp_query->get_queried_object();
			$classes[] = 'category';
			if ( isset( $cat->term_id ) ) {
				$cat_class = sanitize_html_class( $cat->slug, $cat->term_id );
				if ( is_numeric( $cat_class ) || ! trim( $cat_class, '-' ) ) {
					$cat_class = $cat->term_id;
				}

				$classes[] = 'category-' . $cat_class;
				$classes[] = 'category-' . $cat->term_id;
			}
		} elseif ( is_tag() ) {
			$tag       = $wp_query->get_queried_object();
			$classes[] = 'tag';
			if ( isset( $tag->term_id ) ) {
				$tag_class = sanitize_html_class( $tag->slug, $tag->term_id );
				if ( is_numeric( $tag_class ) || ! trim( $tag_class, '-' ) ) {
					$tag_class = $tag->term_id;
				}

				$classes[] = 'tag-' . $tag_class;
				$classes[] = 'tag-' . $tag->term_id;
			}
		} elseif ( is_tax() ) {
			$term = $wp_query->get_queried_object();
			if ( isset( $term->term_id ) ) {
				$term_class = sanitize_html_class( $term->slug, $term->term_id );
				if ( is_numeric( $term_class ) || ! trim( $term_class, '-' ) ) {
					$term_class = $term->term_id;
				}

				$classes[] = 'tax-' . sanitize_html_class( $term->taxonomy );
				$classes[] = 'term-' . $term_class;
				$classes[] = 'term-' . $term->term_id;
			}
		}
	}

	if ( is_user_logged_in() ) {
		$classes[] = 'logged-in';
	}

	if ( is_admin_bar_showing() ) {
		$classes[] = 'admin-bar';
		$classes[] = 'no-customize-support';
	}

	if ( current_theme_supports( 'custom-background' )
		&& ( get_background_color() !== get_theme_support( 'custom-background', 'default-color' ) || get_background_image() ) ) {
		$classes[] = 'custom-background';
	}

	if ( has_custom_logo() ) {
		$classes[] = 'wp-custom-logo';
	}

	if ( current_theme_supports( 'responsive-embeds' ) ) {
		$classes[] = 'wp-embed-responsive';
	}

	$page = $wp_query->get( 'page' );

	if ( ! $page || $page < 2 ) {
		$page = $wp_query->get( 'paged' );
	}

	if ( $page && $page > 1 && ! is_404() ) {
		$classes[] = 'paged-' . $page;

		if ( is_single() ) {
			$classes[] = 'single-paged-' . $page;
		} elseif ( is_page() ) {
			$classes[] = 'page-paged-' . $page;
		} elseif ( is_category() ) {
			$classes[] = 'category-paged-' . $page;
		} elseif ( is_tag() ) {
			$classes[] = 'tag-paged-' . $page;
		} elseif ( is_date() ) {
			$classes[] = 'date-paged-' . $page;
		} elseif ( is_author() ) {
			$classes[] = 'author-paged-' . $page;
		} elseif ( is_search() ) {
			$classes[] = 'search-paged-' . $page;
		} elseif ( is_post_type_archive() ) {
			$classes[] = 'post-type-paged-' . $page;
		}
	}

	if ( ! empty( $class ) ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filters the list of CSS body class names for the current post or page.
	 *
	 * @since 2.8.0
	 *
	 * @param string[] $classes An array of body class names.
	 * @param string[] $class   An array of additional class names added to the body.
	 */
	$classes = apply_filters( 'body_class', $classes, $class );

	return array_unique( $classes );
}

/**
 * Whether post requires password and correct password has been provided.
 *
 * @since 2.7.0
 *
 * @param int|WP_Post|null $post An optional post. Global $post used if not provided.
 * @return bool false if a password is not required or the correct password cookie is present, true otherwise.
 */
function post_password_required( $post = null ) {
	$post = get_post( $post );

	if ( empty( $post->post_password ) ) {
		/** This filter is documented in wp-includes/post-template.php */
		return apply_filters( 'post_password_required', false, $post );
	}

	if ( ! isset( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] ) ) {
		/** This filter is documented in wp-includes/post-template.php */
		return apply_filters( 'post_password_required', true, $post );
	}

	require_once ABSPATH . WPINC . '/class-phpass.php';
	$hasher = new PasswordHash( 8, true );

	$hash = wp_unslash( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );
	if ( 0 !== strpos( $hash, '$P$B' ) ) {
		$required = true;
	} else {
		$required = ! $hasher->CheckPassword( $post->post_password, $hash );
	}

	/**
	 * Filters whether a post requires the user to supply a password.
	 *
	 * @since 4.7.0
	 *
	 * @param bool    $required Whether the user needs to supply a password. True if password has not been
	 *                          provided or is incorrect, false if password has been supplied or is not required.
	 * @param WP_Post $post     Post data.
	 */
	return apply_filters( 'post_password_required', $required, $post );
}

//
// Page Template Functions for usage in Themes.
//

/**
 * The formatted output of a list of pages.
 *
 * Displays page links for paginated posts (i.e. including the `<!--nextpage-->`
 * Quicktag one or more times). This tag must be within The Loop.
 *
 * @since 1.2.0
 * @since 5.1.0 Added the `aria_current` argument.
 *
 * @global int $page
 * @global int $numpages
 * @global int $multipage
 * @global int $more
 *
 * @param string|array $args {
 *     Optional. Array or string of default arguments.
 *
 *     @type string       $before           HTML or text to prepend to each link. Default is `<p> Pages:`.
 *     @type string       $after            HTML or text to append to each link. Default is `</p>`.
 *     @type string       $link_before      HTML or text to prepend to each link, inside the `<a>` tag.
 *                                          Also prepended to the current item, which is not linked. Default empty.
 *     @type string       $link_after       HTML or text to append to each Pages link inside the `<a>` tag.
 *                                          Also appended to the current item, which is not linked. Default empty.
 *     @type string       $aria_current     The value for the aria-current attribute. Possible values are 'page',
 *                                          'step', 'location', 'date', 'time', 'true', 'false'. Default is 'page'.
 *     @type string       $next_or_number   Indicates whether page numbers should be used. Valid values are number
 *                                          and next. Default is 'number'.
 *     @type string       $separator        Text between pagination links. Default is ' '.
 *     @type string       $nextpagelink     Link text for the next page link, if available. Default is 'Next Page'.
 *     @type string       $previouspagelink Link text for the previous page link, if available. Default is 'Previous Page'.
 *     @type string       $pagelink         Format string for page numbers. The % in the parameter string will be
 *                                          replaced with the page number, so 'Page %' generates "Page 1", "Page 2", etc.
 *                                          Defaults to '%', just the page number.
 *     @type int|bool     $echo             Whether to echo or not. Accepts 1|true or 0|false. Default 1|true.
 * }
 * @return string Formatted output in HTML.
 */
function wp_link_pages( $args = '' ) {
	global $page, $numpages, $multipage, $more;

	$defaults = array(
		'before'           => '<p class="post-nav-links">' . __( 'Pages:' ),
		'after'            => '</p>',
		'link_before'      => '',
		'link_after'       => '',
		'aria_current'     => 'page',
		'next_or_number'   => 'number',
		'separator'        => ' ',
		'nextpagelink'     => __( 'Next page' ),
		'previouspagelink' => __( 'Previous page' ),
		'pagelink'         => '%',
		'echo'             => 1,
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	/**
	 * Filters the arguments used in retrieving page links for paginated posts.
	 *
	 * @since 3.0.0
	 *
	 * @param array $parsed_args An array of arguments for page links for paginated posts.
	 */
	$parsed_args = apply_filters( 'wp_link_pages_args', $parsed_args );

	$output = '';
	if ( $multipage ) {
		if ( 'number' === $parsed_args['next_or_number'] ) {
			$output .= $parsed_args['before'];
			for ( $i = 1; $i <= $numpages; $i++ ) {
				$link = $parsed_args['link_before'] . str_replace( '%', $i, $parsed_args['pagelink'] ) . $parsed_args['link_after'];
				if ( $i != $page || ! $more && 1 == $page ) {
					$link = _wp_link_page( $i ) . $link . '</a>';
				} elseif ( $i === $page ) {
					$link = '<span class="post-page-numbers current" aria-current="' . esc_attr( $parsed_args['aria_current'] ) . '">' . $link . '</span>';
				}
				/**
				 * Filters the HTML output of individual page number links.
				 *
				 * @since 3.6.0
				 *
				 * @param string $link The page number HTML output.
				 * @param int    $i    Page number for paginated posts' page links.
				 */
				$link = apply_filters( 'wp_link_pages_link', $link, $i );

				// Use the custom links separator beginning with the second link.
				$output .= ( 1 === $i ) ? ' ' : $parsed_args['separator'];
				$output .= $link;
			}
			$output .= $parsed_args['after'];
		} elseif ( $more ) {
			$output .= $parsed_args['before'];
			$prev    = $page - 1;
			if ( $prev > 0 ) {
				$link = _wp_link_page( $prev ) . $parsed_args['link_before'] . $parsed_args['previouspagelink'] . $parsed_args['link_after'] . '</a>';

				/** This filter is documented in wp-includes/post-template.php */
				$output .= apply_filters( 'wp_link_pages_link', $link, $prev );
			}
			$next = $page + 1;
			if ( $next <= $numpages ) {
				if ( $prev ) {
					$output .= $parsed_args['separator'];
				}
				$link = _wp_link_page( $next ) . $parsed_args['link_before'] . $parsed_args['nextpagelink'] . $parsed_args['link_after'] . '</a>';

				/** This filter is documented in wp-includes/post-template.php */
				$output .= apply_filters( 'wp_link_pages_link', $link, $next );
			}
			$output .= $parsed_args['after'];
		}
	}

	/**
	 * Filters the HTML output of page links for paginated posts.
	 *
	 * @since 3.6.0
	 *
	 * @param string $output HTML output of paginated posts' page links.
	 * @param array  $args   An array of arguments.
	 */
	$html = apply_filters( 'wp_link_pages', $output, $args );

	if ( $parsed_args['echo'] ) {
		echo $html;
	}
	return $html;
}

/**
 * Helper function for wp_link_pages().
 *
 * @since 3.1.0
 * @access private
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @param int $i Page number.
 * @return string Link.
 */
function _wp_link_page( $i ) {
	global $wp_rewrite;
	$post       = get_post();
	$query_args = array();

	if ( 1 == $i ) {
		$url = get_permalink();
	} else {
		if ( ! get_option( 'permalink_structure' ) || in_array( $post->post_status, array( 'draft', 'pending' ), true ) ) {
			$url = add_query_arg( 'page', $i, get_permalink() );
		} elseif ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) == $post->ID ) {
			$url = trailingslashit( get_permalink() ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
		} else {
			$url = trailingslashit( get_permalink() ) . user_trailingslashit( $i, 'single_paged' );
		}
	}

	if ( is_preview() ) {

		if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
			$query_args['preview_id']    = wp_unslash( $_GET['preview_id'] );
			$query_args['preview_nonce'] = wp_unslash( $_GET['preview_nonce'] );
		}

		$url = get_preview_post_link( $post, $query_args, $url );
	}

	return '<a href="' . esc_url( $url ) . '" class="post-page-numbers">';
}

//
// Post-meta: Custom per-post fields.
//

/**
 * Retrieve post custom meta data field.
 *
 * @since 1.5.0
 *
 * @param string $key Meta data key name.
 * @return array|string|false Array of values, or single value if only one element exists.
 *                            False if the key does not exist.
 */
function post_custom( $key = '' ) {
	$custom = get_post_custom();

	if ( ! isset( $custom[ $key ] ) ) {
		return false;
	} elseif ( 1 == count( $custom[ $key ] ) ) {
		return $custom[ $key ][0];
	} else {
		return $custom[ $key ];
	}
}

/**
 * Display a list of post custom fields.
 *
 * @since 1.2.0
 *
 * @internal This will probably change at some point...
 */
function the_meta() {
	$keys = get_post_custom_keys();
	if ( $keys ) {
		$li_html = '';
		foreach ( (array) $keys as $key ) {
			$keyt = trim( $key );
			if ( is_protected_meta( $keyt, 'post' ) ) {
				continue;
			}

			$values = array_map( 'trim', get_post_custom_values( $key ) );
			$value  = implode( ', ', $values );

			$html = sprintf(
				"<li><span class='post-meta-key'>%s</span> %s</li>\n",
				/* translators: %s: Post custom field name. */
				sprintf( _x( '%s:', 'Post custom field name' ), $key ),
				$value
			);

			/**
			 * Filters the HTML output of the li element in the post custom fields list.
			 *
			 * @since 2.2.0
			 *
			 * @param string $html  The HTML output for the li element.
			 * @param string $key   Meta key.
			 * @param string $value Meta value.
			 */
			$li_html .= apply_filters( 'the_meta_key', $html, $key, $value );
		}

		if ( $li_html ) {
			echo "<ul class='post-meta'>\n{$li_html}</ul>\n";
		}
	}
}

//
// Pages.
//

/**
 * Retrieve or display a list of pages as a dropdown (select list).
 *
 * @since 2.1.0
 * @since 4.2.0 The `$value_field` argument was added.
 * @since 4.3.0 The `$class` argument was added.
 *
 * @see get_pages()
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments to generate a page dropdown. See `get_pages()` for additional arguments.
 *
 *     @type int          $depth                 Maximum depth. Default 0.
 *     @type int          $child_of              Page ID to retrieve child pages of. Default 0.
 *     @type int|string   $selected              Value of the option that should be selected. Default 0.
 *     @type bool|int     $echo                  Whether to echo or return the generated markup. Accepts 0, 1,
 *                                               or their bool equivalents. Default 1.
 *     @type string       $name                  Value for the 'name' attribute of the select element.
 *                                               Default 'page_id'.
 *     @type string       $id                    Value for the 'id' attribute of the select element.
 *     @type string       $class                 Value for the 'class' attribute of the select element. Default: none.
 *                                               Defaults to the value of `$name`.
 *     @type string       $show_option_none      Text to display for showing no pages. Default empty (does not display).
 *     @type string       $show_option_no_change Text to display for "no change" option. Default empty (does not display).
 *     @type string       $option_none_value     Value to use when no page is selected. Default empty.
 *     @type string       $value_field           Post field used to populate the 'value' attribute of the option
 *                                               elements. Accepts any valid post field. Default 'ID'.
 * }
 * @return string HTML dropdown list of pages.
 */
function wp_dropdown_pages( $args = '' ) {
	$defaults = array(
		'depth'                 => 0,
		'child_of'              => 0,
		'selected'              => 0,
		'echo'                  => 1,
		'name'                  => 'page_id',
		'id'                    => '',
		'class'                 => '',
		'show_option_none'      => '',
		'show_option_no_change' => '',
		'option_none_value'     => '',
		'value_field'           => 'ID',
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	$pages  = get_pages( $parsed_args );
	$output = '';
	// Back-compat with old system where both id and name were based on $name argument.
	if ( empty( $parsed_args['id'] ) ) {
		$parsed_args['id'] = $parsed_args['name'];
	}

	if ( ! empty( $pages ) ) {
		$class = '';
		if ( ! empty( $parsed_args['class'] ) ) {
			$class = " class='" . esc_attr( $parsed_args['class'] ) . "'";
		}

		$output = "<select name='" . esc_attr( $parsed_args['name'] ) . "'" . $class . " id='" . esc_attr( $parsed_args['id'] ) . "'>\n";
		if ( $parsed_args['show_option_no_change'] ) {
			$output .= "\t<option value=\"-1\">" . $parsed_args['show_option_no_change'] . "</option>\n";
		}
		if ( $parsed_args['show_option_none'] ) {
			$output .= "\t<option value=\"" . esc_attr( $parsed_args['option_none_value'] ) . '">' . $parsed_args['show_option_none'] . "</option>\n";
		}
		$output .= walk_page_dropdown_tree( $pages, $parsed_args['depth'], $parsed_args );
		$output .= "</select>\n";
	}

	/**
	 * Filters the HTML output of a list of pages as a drop down.
	 *
	 * @since 2.1.0
	 * @since 4.4.0 `$parsed_args` and `$pages` added as arguments.
	 *
	 * @param string    $output      HTML output for drop down list of pages.
	 * @param array     $parsed_args The parsed arguments array.
	 * @param WP_Post[] $pages       Array of the page objects.
	 */
	$html = apply_filters( 'wp_dropdown_pages', $output, $parsed_args, $pages );

	if ( $parsed_args['echo'] ) {
		echo $html;
	}

	return $html;
}

/**
 * Retrieve or display a list of pages (or hierarchical post type items) in list (li) format.
 *
 * @since 1.5.0
 * @since 4.7.0 Added the `item_spacing` argument.
 *
 * @see get_pages()
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments to generate a list of pages. See `get_pages()` for additional arguments.
 *
 *     @type int          $child_of     Display only the sub-pages of a single page by ID. Default 0 (all pages).
 *     @type string       $authors      Comma-separated list of author IDs. Default empty (all authors).
 *     @type string       $date_format  PHP date format to use for the listed pages. Relies on the 'show_date' parameter.
 *                                      Default is the value of 'date_format' option.
 *     @type int          $depth        Number of levels in the hierarchy of pages to include in the generated list.
 *                                      Accepts -1 (any depth), 0 (all pages), 1 (top-level pages only), and n (pages to
 *                                      the given n depth). Default 0.
 *     @type bool         $echo         Whether or not to echo the list of pages. Default true.
 *     @type string       $exclude      Comma-separated list of page IDs to exclude. Default empty.
 *     @type array        $include      Comma-separated list of page IDs to include. Default empty.
 *     @type string       $link_after   Text or HTML to follow the page link label. Default null.
 *     @type string       $link_before  Text or HTML to precede the page link label. Default null.
 *     @type string       $post_type    Post type to query for. Default 'page'.
 *     @type string|array $post_status  Comma-separated list or array of post statuses to include. Default 'publish'.
 *     @type string       $show_date    Whether to display the page publish or modified date for each page. Accepts
 *                                      'modified' or any other value. An empty value hides the date. Default empty.
 *     @type string       $sort_column  Comma-separated list of column names to sort the pages by. Accepts 'post_author',
 *                                      'post_date', 'post_title', 'post_name', 'post_modified', 'post_modified_gmt',
 *                                      'menu_order', 'post_parent', 'ID', 'rand', or 'comment_count'. Default 'post_title'.
 *     @type string       $title_li     List heading. Passing a null or empty value will result in no heading, and the list
 *                                      will not be wrapped with unordered list `<ul>` tags. Default 'Pages'.
 *     @type string       $item_spacing Whether to preserve whitespace within the menu's HTML. Accepts 'preserve' or 'discard'.
 *                                      Default 'preserve'.
 *     @type Walker       $walker       Walker instance to use for listing pages. Default empty (Walker_Page).
 * }
 * @return void|string Void if 'echo' argument is true, HTML list of pages if 'echo' is false.
 */
function wp_list_pages( $args = '' ) {
	$defaults = array(
		'depth'        => 0,
		'show_date'    => '',
		'date_format'  => get_option( 'date_format' ),
		'child_of'     => 0,
		'exclude'      => '',
		'title_li'     => __( 'Pages' ),
		'echo'         => 1,
		'authors'      => '',
		'sort_column'  => 'menu_order, post_title',
		'link_before'  => '',
		'link_after'   => '',
		'item_spacing' => 'preserve',
		'walker'       => '',
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	if ( ! in_array( $parsed_args['item_spacing'], array( 'preserve', 'discard' ), true ) ) {
		// Invalid value, fall back to default.
		$parsed_args['item_spacing'] = $defaults['item_spacing'];
	}

	$output       = '';
	$current_page = 0;

	// Sanitize, mostly to keep spaces out.
	$parsed_args['exclude'] = preg_replace( '/[^0-9,]/', '', $parsed_args['exclude'] );

	// Allow plugins to filter an array of excluded pages (but don't put a nullstring into the array).
	$exclude_array = ( $parsed_args['exclude'] ) ? explode( ',', $parsed_args['exclude'] ) : array();

	/**
	 * Filters the array of pages to exclude from the pages list.
	 *
	 * @since 2.1.0
	 *
	 * @param string[] $exclude_array An array of page IDs to exclude.
	 */
	$parsed_args['exclude'] = implode( ',', apply_filters( 'wp_list_pages_excludes', $exclude_array ) );

	$parsed_args['hierarchical'] = 0;

	// Query pages.
	$pages = get_pages( $parsed_args );

	if ( ! empty( $pages ) ) {
		if ( $parsed_args['title_li'] ) {
			$output .= '<li class="pagenav">' . $parsed_args['title_li'] . '<ul>';
		}
		global $wp_query;
		if ( is_page() || is_attachment() || $wp_query->is_posts_page ) {
			$current_page = get_queried_object_id();
		} elseif ( is_singular() ) {
			$queried_object = get_queried_object();
			if ( is_post_type_hierarchical( $queried_object->post_type ) ) {
				$current_page = $queried_object->ID;
			}
		}

		$output .= walk_page_tree( $pages, $parsed_args['depth'], $current_page, $parsed_args );

		if ( $parsed_args['title_li'] ) {
			$output .= '</ul></li>';
		}
	}

	/**
	 * Filters the HTML output of the pages to list.
	 *
	 * @since 1.5.1
	 * @since 4.4.0 `$pages` added as arguments.
	 *
	 * @see wp_list_pages()
	 *
	 * @param string    $output      HTML output of the pages list.
	 * @param array     $parsed_args An array of page-listing arguments.
	 * @param WP_Post[] $pages       Array of the page objects.
	 */
	$html = apply_filters( 'wp_list_pages', $output, $parsed_args, $pages );

	if ( $parsed_args['echo'] ) {
		echo $html;
	} else {
		return $html;
	}
}

/**
 * Displays or retrieves a list of pages with an optional home link.
 *
 * The arguments are listed below and part of the arguments are for wp_list_pages() function.
 * Check that function for more info on those arguments.
 *
 * @since 2.7.0
 * @since 4.4.0 Added `menu_id`, `container`, `before`, `after`, and `walker` arguments.
 * @since 4.7.0 Added the `item_spacing` argument.
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments to generate a page menu. See `wp_list_pages()` for additional arguments.
 *
 *     @type string          $sort_column  How to sort the list of pages. Accepts post column names.
 *                                         Default 'menu_order, post_title'.
 *     @type string          $menu_id      ID for the div containing the page list. Default is empty string.
 *     @type string          $menu_class   Class to use for the element containing the page list. Default 'menu'.
 *     @type string          $container    Element to use for the element containing the page list. Default 'div'.
 *     @type bool            $echo         Whether to echo the list or return it. Accepts true (echo) or false (return).
 *                                         Default true.
 *     @type int|bool|string $show_home    Whether to display the link to the home page. Can just enter the text
 *                                         you'd like shown for the home link. 1|true defaults to 'Home'.
 *     @type string          $link_before  The HTML or text to prepend to $show_home text. Default empty.
 *     @type string          $link_after   The HTML or text to append to $show_home text. Default empty.
 *     @type string          $before       The HTML or text to prepend to the menu. Default is '<ul>'.
 *     @type string          $after        The HTML or text to append to the menu. Default is '</ul>'.
 *     @type string          $item_spacing Whether to preserve whitespace within the menu's HTML. Accepts 'preserve'
 *                                         or 'discard'. Default 'discard'.
 *     @type Walker          $walker       Walker instance to use for listing pages. Default empty (Walker_Page).
 * }
 * @return void|string Void if 'echo' argument is true, HTML menu if 'echo' is false.
 */
function wp_page_menu( $args = array() ) {
	$defaults = array(
		'sort_column'  => 'menu_order, post_title',
		'menu_id'      => '',
		'menu_class'   => 'menu',
		'container'    => 'div',
		'echo'         => true,
		'link_before'  => '',
		'link_after'   => '',
		'before'       => '<ul>',
		'after'        => '</ul>',
		'item_spacing' => 'discard',
		'walker'       => '',
	);
	$args     = wp_parse_args( $args, $defaults );

	if ( ! in_array( $args['item_spacing'], array( 'preserve', 'discard' ), true ) ) {
		// Invalid value, fall back to default.
		$args['item_spacing'] = $defaults['item_spacing'];
	}

	if ( 'preserve' === $args['item_spacing'] ) {
		$t = "\t";
		$n = "\n";
	} else {
		$t = '';
		$n = '';
	}

	/**
	 * Filters the arguments used to generate a page-based menu.
	 *
	 * @since 2.7.0
	 *
	 * @see wp_page_menu()
	 *
	 * @param array $args An array of page menu arguments.
	 */
	$args = apply_filters( 'wp_page_menu_args', $args );

	$menu = '';

	$list_args = $args;

	// Show Home in the menu.
	if ( ! empty( $args['show_home'] ) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] ) {
			$text = __( 'Home' );
		} else {
			$text = $args['show_home'];
		}
		$class = '';
		if ( is_front_page() && ! is_paged() ) {
			$class = 'class="current_page_item"';
		}
		$menu .= '<li ' . $class . '><a href="' . home_url( '/' ) . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
		// If the front page is a page, add it to the exclude list.
		if ( 'page' === get_option( 'show_on_front' ) ) {
			if ( ! empty( $list_args['exclude'] ) ) {
				$list_args['exclude'] .= ',';
			} else {
				$list_args['exclude'] = '';
			}
			$list_args['exclude'] .= get_option( 'page_on_front' );
		}
	}

	$list_args['echo']     = false;
	$list_args['title_li'] = '';
	$menu                 .= wp_list_pages( $list_args );

	$container = sanitize_text_field( $args['container'] );

	// Fallback in case `wp_nav_menu()` was called without a container.
	if ( empty( $container ) ) {
		$container = 'div';
	}

	if ( $menu ) {

		// wp_nav_menu() doesn't set before and after.
		if ( isset( $args['fallback_cb'] ) &&
			'wp_page_menu' === $args['fallback_cb'] &&
			'ul' !== $container ) {
			$args['before'] = "<ul>{$n}";
			$args['after']  = '</ul>';
		}

		$menu = $args['before'] . $menu . $args['after'];
	}

	$attrs = '';
	if ( ! empty( $args['menu_id'] ) ) {
		$attrs .= ' id="' . esc_attr( $args['menu_id'] ) . '"';
	}

	if ( ! empty( $args['menu_class'] ) ) {
		$attrs .= ' class="' . esc_attr( $args['menu_class'] ) . '"';
	}

	$menu = "<{$container}{$attrs}>" . $menu . "</{$container}>{$n}";

	/**
	 * Filters the HTML output of a page-based menu.
	 *
	 * @since 2.7.0
	 *
	 * @see wp_page_menu()
	 *
	 * @param string $menu The HTML output.
	 * @param array  $args An array of arguments.
	 */
	$menu = apply_filters( 'wp_page_menu', $menu, $args );

	if ( $args['echo'] ) {
		echo $menu;
	} else {
		return $menu;
	}
}

//
// Page helpers.
//

/**
 * Retrieve HTML list content for page list.
 *
 * @uses Walker_Page to create HTML list content.
 * @since 2.1.0
 *
 * @param array $pages
 * @param int   $depth
 * @param int   $current_page
 * @param array $r
 * @return string
 */
function walk_page_tree( $pages, $depth, $current_page, $r ) {
	if ( empty( $r['walker'] ) ) {
		$walker = new Walker_Page;
	} else {
		$walker = $r['walker'];
	}

	foreach ( (array) $pages as $page ) {
		if ( $page->post_parent ) {
			$r['pages_with_children'][ $page->post_parent ] = true;
		}
	}

	return $walker->walk( $pages, $depth, $r, $current_page );
}

/**
 * Retrieve HTML dropdown (select) content for page list.
 *
 * @since 2.1.0
 * @since 5.3.0 Formalized the existing `...$args` parameter by adding it
 *              to the function signature.
 *
 * @uses Walker_PageDropdown to create HTML dropdown content.
 * @see Walker_PageDropdown::walk() for parameters and return description.
 *
 * @return string
 */
function walk_page_dropdown_tree( ...$args ) {
	if ( empty( $args[2]['walker'] ) ) { // The user's options are the third parameter.
		$walker = new Walker_PageDropdown;
	} else {
		$walker = $args[2]['walker'];
	}

	return $walker->walk( ...$args );
}

//
// Attachments.
//

/**
 * Display an attachment page link using an image or icon.
 *
 * @since 2.0.0
 *
 * @param int|WP_Post $id Optional. Post ID or post object.
 * @param bool        $fullsize     Optional, default is false. Whether to use full size.
 * @param bool        $deprecated   Deprecated. Not used.
 * @param bool        $permalink    Optional, default is false. Whether to include permalink.
 */
function the_attachment_link( $id = 0, $fullsize = false, $deprecated = false, $permalink = false ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.5.0' );
	}

	if ( $fullsize ) {
		echo wp_get_attachment_link( $id, 'full', $permalink );
	} else {
		echo wp_get_attachment_link( $id, 'thumbnail', $permalink );
	}
}

/**
 * Retrieve an attachment page link using an image or icon, if possible.
 *
 * @since 2.5.0
 * @since 4.4.0 The `$id` parameter can now accept either a post ID or `WP_Post` object.
 *
 * @param int|WP_Post  $id        Optional. Post ID or post object.
 * @param string|array $size      Optional. Image size. Accepts any valid image size, or an array
 *                                of width and height values in pixels (in that order).
 *                                Default 'thumbnail'.
 * @param bool         $permalink Optional, Whether to add permalink to image. Default false.
 * @param bool         $icon      Optional. Whether the attachment is an icon. Default false.
 * @param string|false $text      Optional. Link text to use. Activated by passing a string, false otherwise.
 *                                Default false.
 * @param array|string $attr      Optional. Array or string of attributes. Default empty.
 * @return string HTML content.
 */
function wp_get_attachment_link( $id = 0, $size = 'thumbnail', $permalink = false, $icon = false, $text = false, $attr = '' ) {
	$_post = get_post( $id );

	if ( empty( $_post ) || ( 'attachment' !== $_post->post_type ) || ! wp_get_attachment_url( $_post->ID ) ) {
		return __( 'Missing Attachment' );
	}

	$url = wp_get_attachment_url( $_post->ID );

	if ( $permalink ) {
		$url = get_attachment_link( $_post->ID );
	}

	if ( $text ) {
		$link_text = $text;
	} elseif ( $size && 'none' !== $size ) {
		$link_text = wp_get_attachment_image( $_post->ID, $size, $icon, $attr );
	} else {
		$link_text = '';
	}

	if ( '' === trim( $link_text ) ) {
		$link_text = $_post->post_title;
	}

	if ( '' === trim( $link_text ) ) {
		$link_text = esc_html( pathinfo( get_attached_file( $_post->ID ), PATHINFO_FILENAME ) );
	}
	/**
	 * Filters a retrieved attachment page link.
	 *
	 * @since 2.7.0
	 * @since 5.1.0 Added the $attr parameter.
	 *
	 * @param string       $link_html The page link HTML output.
	 * @param int          $id        Post ID.
	 * @param string|array $size      Size of the image. Image size or array of width and height values (in that order).
	 *                                Default 'thumbnail'.
	 * @param bool         $permalink Whether to add permalink to image. Default false.
	 * @param bool         $icon      Whether to include an icon. Default false.
	 * @param string|bool  $text      If string, will be link text. Default false.
	 * @param array|string $attr      Array or string of attributes. Default empty.
	 */
	return apply_filters( 'wp_get_attachment_link', "<a href='" . esc_url( $url ) . "'>$link_text</a>", $id, $size, $permalink, $icon, $text, $attr );
}

/**
 * Wrap attachment in paragraph tag before content.
 *
 * @since 2.0.0
 *
 * @param string $content
 * @return string
 */
function prepend_attachment( $content ) {
	$post = get_post();

	if ( empty( $post->post_type ) || 'attachment' !== $post->post_type ) {
		return $content;
	}

	if ( wp_attachment_is( 'video', $post ) ) {
		$meta = wp_get_attachment_metadata( get_the_ID() );
		$atts = array( 'src' => wp_get_attachment_url() );
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
			$atts['width']  = (int) $meta['width'];
			$atts['height'] = (int) $meta['height'];
		}
		if ( has_post_thumbnail() ) {
			$atts['poster'] = wp_get_attachment_url( get_post_thumbnail_id() );
		}
		$p = wp_video_shortcode( $atts );
	} elseif ( wp_attachment_is( 'audio', $post ) ) {
		$p = wp_audio_shortcode( array( 'src' => wp_get_attachment_url() ) );
	} else {
		$p = '<p class="attachment">';
		// Show the medium sized image representation of the attachment if available, and link to the raw file.
		$p .= wp_get_attachment_link( 0, 'medium', false );
		$p .= '</p>';
	}

	/**
	 * Filters the attachment markup to be prepended to the post content.
	 *
	 * @since 2.0.0
	 *
	 * @see prepend_attachment()
	 *
	 * @param string $p The attachment HTML output.
	 */
	$p = apply_filters( 'prepend_attachment', $p );

	return "$p\n$content";
}

//
// Misc.
//

/**
 * Retrieve protected post password form content.
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string HTML content for password form for password protected post.
 */
function get_the_password_form( $post = 0 ) {
	$post   = get_post( $post );
	$label  = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
	$output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
	<p>' . __( 'This content is password protected. To view it please enter your password below:' ) . '</p>
	<p><label for="' . $label . '">' . __( 'Password:' ) . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr_x( 'Enter', 'post password form' ) . '" /></p></form>
	';

	/**
	 * Filters the HTML output for the protected post password form.
	 *
	 * If modifying the password field, please note that the core database schema
	 * limits the password field to 20 characters regardless of the value of the
	 * size attribute in the form input.
	 *
	 * @since 2.7.0
	 *
	 * @param string $output The password form HTML output.
	 */
	return apply_filters( 'the_password_form', $output );
}

/**
 * Determines whether currently in a page template.
 *
 * This template tag allows you to determine if you are in a page template.
 * You can optionally provide a template filename or array of template filenames
 * and then the check will be specific to that template.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.5.0
 * @since 4.2.0 The `$template` parameter was changed to also accept an array of page templates.
 * @since 4.7.0 Now works with any post type, not just pages.
 *
 * @param string|array $template The specific template filename or array of templates to match.
 * @return bool True on success, false on failure.
 */
function is_page_template( $template = '' ) {
	if ( ! is_singular() ) {
		return false;
	}

	$page_template = get_page_template_slug( get_queried_object_id() );

	if ( empty( $template ) ) {
		return (bool) $page_template;
	}

	if ( $template == $page_template ) {
		return true;
	}

	if ( is_array( $template ) ) {
		if ( ( in_array( 'default', $template, true ) && ! $page_template )
			|| in_array( $page_template, $template, true )
		) {
			return true;
		}
	}

	return ( 'default' === $template && ! $page_template );
}

/**
 * Get the specific template filename for a given post.
 *
 * @since 3.4.0
 * @since 4.7.0 Now works with any post type, not just pages.
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string|false Page template filename. Returns an empty string when the default page template
 *                      is in use. Returns false if the post does not exist.
 */
function get_page_template_slug( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$template = get_post_meta( $post->ID, '_wp_page_template', true );

	if ( ! $template || 'default' === $template ) {
		return '';
	}

	return $template;
}

/**
 * Retrieve formatted date timestamp of a revision (linked to that revisions's page).
 *
 * @since 2.6.0
 *
 * @param int|object $revision Revision ID or revision object.
 * @param bool       $link     Optional, default is true. Link to revisions's page?
 * @return string|false i18n formatted datetimestamp or localized 'Current Revision'.
 */
function wp_post_revision_title( $revision, $link = true ) {
	$revision = get_post( $revision );
	if ( ! $revision ) {
		return $revision;
	}

	if ( ! in_array( $revision->post_type, array( 'post', 'page', 'revision' ), true ) ) {
		return false;
	}

	/* translators: Revision date format, see https://www.php.net/date */
	$datef = _x( 'F j, Y @ H:i:s', 'revision date format' );
	/* translators: %s: Revision date. */
	$autosavef = __( '%s [Autosave]' );
	/* translators: %s: Revision date. */
	$currentf = __( '%s [Current Revision]' );

	$date      = date_i18n( $datef, strtotime( $revision->post_modified ) );
	$edit_link = get_edit_post_link( $revision->ID );
	if ( $link && current_user_can( 'edit_post', $revision->ID ) && $edit_link ) {
		$date = "<a href='$edit_link'>$date</a>";
	}

	if ( ! wp_is_post_revision( $revision ) ) {
		$date = sprintf( $currentf, $date );
	} elseif ( wp_is_post_autosave( $revision ) ) {
		$date = sprintf( $autosavef, $date );
	}

	return $date;
}

/**
 * Retrieve formatted date timestamp of a revision (linked to that revisions's page).
 *
 * @since 3.6.0
 *
 * @param int|object $revision Revision ID or revision object.
 * @param bool       $link     Optional, default is true. Link to revisions's page?
 * @return string|false gravatar, user, i18n formatted datetimestamp or localized 'Current Revision'.
 */
function wp_post_revision_title_expanded( $revision, $link = true ) {
	$revision = get_post( $revision );
	if ( ! $revision ) {
		return $revision;
	}

	if ( ! in_array( $revision->post_type, array( 'post', 'page', 'revision' ), true ) ) {
		return false;
	}

	$author = get_the_author_meta( 'display_name', $revision->post_author );
	/* translators: Revision date format, see https://www.php.net/date */
	$datef = _x( 'F j, Y @ H:i:s', 'revision date format' );

	$gravatar = get_avatar( $revision->post_author, 24 );

	$date      = date_i18n( $datef, strtotime( $revision->post_modified ) );
	$edit_link = get_edit_post_link( $revision->ID );
	if ( $link && current_user_can( 'edit_post', $revision->ID ) && $edit_link ) {
		$date = "<a href='$edit_link'>$date</a>";
	}

	$revision_date_author = sprintf(
		/* translators: Post revision title. 1: Author avatar, 2: Author name, 3: Time ago, 4: Date. */
		__( '%1$s %2$s, %3$s ago (%4$s)' ),
		$gravatar,
		$author,
		human_time_diff( strtotime( $revision->post_modified_gmt ) ),
		$date
	);

	/* translators: %s: Revision date with author avatar. */
	$autosavef = __( '%s [Autosave]' );
	/* translators: %s: Revision date with author avatar. */
	$currentf = __( '%s [Current Revision]' );

	if ( ! wp_is_post_revision( $revision ) ) {
		$revision_date_author = sprintf( $currentf, $revision_date_author );
	} elseif ( wp_is_post_autosave( $revision ) ) {
		$revision_date_author = sprintf( $autosavef, $revision_date_author );
	}

	/**
	 * Filters the formatted author and date for a revision.
	 *
	 * @since 4.4.0
	 *
	 * @param string  $revision_date_author The formatted string.
	 * @param WP_Post $revision             The revision object.
	 * @param bool    $link                 Whether to link to the revisions page, as passed into
	 *                                      wp_post_revision_title_expanded().
	 */
	return apply_filters( 'wp_post_revision_title_expanded', $revision_date_author, $revision, $link );
}

/**
 * Display a list of a post's revisions.
 *
 * Can output either a UL with edit links or a TABLE with diff interface, and
 * restore action links.
 *
 * @since 2.6.0
 *
 * @param int|WP_Post $post_id Optional. Post ID or WP_Post object. Default is global $post.
 * @param string      $type    'all' (default), 'revision' or 'autosave'
 */
function wp_list_post_revisions( $post_id = 0, $type = 'all' ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	// $args array with (parent, format, right, left, type) deprecated since 3.6.
	if ( is_array( $type ) ) {
		$type = ! empty( $type['type'] ) ? $type['type'] : $type;
		_deprecated_argument( __FUNCTION__, '3.6.0' );
	}

	$revisions = wp_get_post_revisions( $post->ID );
	if ( ! $revisions ) {
		return;
	}

	$rows = '';
	foreach ( $revisions as $revision ) {
		if ( ! current_user_can( 'read_post', $revision->ID ) ) {
			continue;
		}

		$is_autosave = wp_is_post_autosave( $revision );
		if ( ( 'revision' === $type && $is_autosave ) || ( 'autosave' === $type && ! $is_autosave ) ) {
			continue;
		}

		$rows .= "\t<li>" . wp_post_revision_title_expanded( $revision ) . "</li>\n";
	}

	echo "<div class='hide-if-js'><p>" . __( 'JavaScript must be enabled to use this feature.' ) . "</p></div>\n";

	echo "<ul class='post-revisions hide-if-no-js'>\n";
	echo $rows;
	echo '</ul>';
}

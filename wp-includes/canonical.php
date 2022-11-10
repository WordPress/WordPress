<?php
/**
 * Canonical API to handle WordPress Redirecting
 *
 * Based on "Permalink Redirect" from Scott Yang and "Enforce www. Preference"
 * by Mark Jaquith
 *
 * @package WordPress
 * @since 2.3.0
 */

/**
 * Redirects incoming links to the proper URL based on the site url.
 *
 * Search engines consider www.somedomain.com and somedomain.com to be two
 * different URLs when they both go to the same location. This SEO enhancement
 * prevents penalty for duplicate content by redirecting all incoming links to
 * one or the other.
 *
 * Prevents redirection for feeds, trackbacks, searches, and
 * admin URLs. Does not redirect on non-pretty-permalink-supporting IIS 7+,
 * page/post previews, WP admin, Trackbacks, robots.txt, favicon.ico, searches,
 * or on POST requests.
 *
 * Will also attempt to find the correct link when a user enters a URL that does
 * not exist based on exact WordPress query. Will instead try to parse the URL
 * or query in an attempt to figure the correct page to go to.
 *
 * @since 2.3.0
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 * @global bool       $is_IIS
 * @global WP_Query   $wp_query   WordPress Query object.
 * @global wpdb       $wpdb       WordPress database abstraction object.
 * @global WP         $wp         Current WordPress environment instance.
 *
 * @param string $requested_url Optional. The URL that was requested, used to
 *                              figure if redirect is needed.
 * @param bool   $do_redirect   Optional. Redirect to the new URL.
 * @return string|void The string of the URL, if redirect needed.
 */
function redirect_canonical( $requested_url = null, $do_redirect = true ) {
	global $wp_rewrite, $is_IIS, $wp_query, $wpdb, $wp;

	if ( isset( $_SERVER['REQUEST_METHOD'] ) && ! in_array( strtoupper( $_SERVER['REQUEST_METHOD'] ), array( 'GET', 'HEAD' ), true ) ) {
		return;
	}

	// If we're not in wp-admin and the post has been published and preview nonce
	// is non-existent or invalid then no need for preview in query.
	if ( is_preview() && get_query_var( 'p' ) && 'publish' === get_post_status( get_query_var( 'p' ) ) ) {
		if ( ! isset( $_GET['preview_id'] )
			|| ! isset( $_GET['preview_nonce'] )
			|| ! wp_verify_nonce( $_GET['preview_nonce'], 'post_preview_' . (int) $_GET['preview_id'] )
		) {
			$wp_query->is_preview = false;
		}
	}

	if ( is_admin() || is_search() || is_preview() || is_trackback() || is_favicon()
		|| ( $is_IIS && ! iis7_supports_permalinks() )
	) {
		return;
	}

	if ( ! $requested_url && isset( $_SERVER['HTTP_HOST'] ) ) {
		// Build the URL in the address bar.
		$requested_url  = is_ssl() ? 'https://' : 'http://';
		$requested_url .= $_SERVER['HTTP_HOST'];
		$requested_url .= $_SERVER['REQUEST_URI'];
	}

	$original = parse_url( $requested_url );
	if ( false === $original ) {
		return;
	}

	$redirect     = $original;
	$redirect_url = false;
	$redirect_obj = false;

	// Notice fixing.
	if ( ! isset( $redirect['path'] ) ) {
		$redirect['path'] = '';
	}
	if ( ! isset( $redirect['query'] ) ) {
		$redirect['query'] = '';
	}

	/*
	 * If the original URL ended with non-breaking spaces, they were almost
	 * certainly inserted by accident. Let's remove them, so the reader doesn't
	 * see a 404 error with no obvious cause.
	 */
	$redirect['path'] = preg_replace( '|(%C2%A0)+$|i', '', $redirect['path'] );

	// It's not a preview, so remove it from URL.
	if ( get_query_var( 'preview' ) ) {
		$redirect['query'] = remove_query_arg( 'preview', $redirect['query'] );
	}

	$post_id = get_query_var( 'p' );

	if ( is_feed() && $post_id ) {
		$redirect_url = get_post_comments_feed_link( $post_id, get_query_var( 'feed' ) );
		$redirect_obj = get_post( $post_id );

		if ( $redirect_url ) {
			$redirect['query'] = _remove_qs_args_if_not_in_url(
				$redirect['query'],
				array( 'p', 'page_id', 'attachment_id', 'pagename', 'name', 'post_type', 'feed' ),
				$redirect_url
			);

			$redirect['path'] = parse_url( $redirect_url, PHP_URL_PATH );
		}
	}

	if ( is_singular() && $wp_query->post_count < 1 && $post_id ) {

		$vars = $wpdb->get_results( $wpdb->prepare( "SELECT post_type, post_parent FROM $wpdb->posts WHERE ID = %d", $post_id ) );

		if ( ! empty( $vars[0] ) ) {
			$vars = $vars[0];

			if ( 'revision' === $vars->post_type && $vars->post_parent > 0 ) {
				$post_id = $vars->post_parent;
			}

			$redirect_url = get_permalink( $post_id );
			$redirect_obj = get_post( $post_id );

			if ( $redirect_url ) {
				$redirect['query'] = _remove_qs_args_if_not_in_url(
					$redirect['query'],
					array( 'p', 'page_id', 'attachment_id', 'pagename', 'name', 'post_type' ),
					$redirect_url
				);
			}
		}
	}

	// These tests give us a WP-generated permalink.
	if ( is_404() ) {

		// Redirect ?page_id, ?p=, ?attachment_id= to their respective URLs.
		$post_id = max( get_query_var( 'p' ), get_query_var( 'page_id' ), get_query_var( 'attachment_id' ) );

		$redirect_post = $post_id ? get_post( $post_id ) : false;

		if ( $redirect_post ) {
			$post_type_obj = get_post_type_object( $redirect_post->post_type );

			if ( $post_type_obj && $post_type_obj->public && 'auto-draft' !== $redirect_post->post_status ) {
				$redirect_url = get_permalink( $redirect_post );
				$redirect_obj = get_post( $redirect_post );

				$redirect['query'] = _remove_qs_args_if_not_in_url(
					$redirect['query'],
					array( 'p', 'page_id', 'attachment_id', 'pagename', 'name', 'post_type' ),
					$redirect_url
				);
			}
		}

		$year  = get_query_var( 'year' );
		$month = get_query_var( 'monthnum' );
		$day   = get_query_var( 'day' );

		if ( $year && $month && $day ) {
			$date = sprintf( '%04d-%02d-%02d', $year, $month, $day );

			if ( ! wp_checkdate( $month, $day, $year, $date ) ) {
				$redirect_url = get_month_link( $year, $month );

				$redirect['query'] = _remove_qs_args_if_not_in_url(
					$redirect['query'],
					array( 'year', 'monthnum', 'day' ),
					$redirect_url
				);
			}
		} elseif ( $year && $month && $month > 12 ) {
			$redirect_url = get_year_link( $year );

			$redirect['query'] = _remove_qs_args_if_not_in_url(
				$redirect['query'],
				array( 'year', 'monthnum' ),
				$redirect_url
			);
		}

		// Strip off non-existing <!--nextpage--> links from single posts or pages.
		if ( get_query_var( 'page' ) ) {
			$post_id = 0;

			if ( $wp_query->queried_object instanceof WP_Post ) {
				$post_id = $wp_query->queried_object->ID;
			} elseif ( $wp_query->post ) {
				$post_id = $wp_query->post->ID;
			}

			if ( $post_id ) {
				$redirect_url = get_permalink( $post_id );
				$redirect_obj = get_post( $post_id );

				$redirect['path']  = rtrim( $redirect['path'], (int) get_query_var( 'page' ) . '/' );
				$redirect['query'] = remove_query_arg( 'page', $redirect['query'] );
			}
		}

		if ( ! $redirect_url ) {
			$redirect_url = redirect_guess_404_permalink();

			if ( $redirect_url ) {
				$redirect['query'] = _remove_qs_args_if_not_in_url(
					$redirect['query'],
					array( 'page', 'feed', 'p', 'page_id', 'attachment_id', 'pagename', 'name', 'post_type' ),
					$redirect_url
				);
			}
		}
	} elseif ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) {

		// Rewriting of old ?p=X, ?m=2004, ?m=200401, ?m=20040101.
		if ( is_attachment()
			&& ! array_diff( array_keys( $wp->query_vars ), array( 'attachment', 'attachment_id' ) )
			&& ! $redirect_url
		) {
			if ( ! empty( $_GET['attachment_id'] ) ) {
				$redirect_url = get_attachment_link( get_query_var( 'attachment_id' ) );
				$redirect_obj = get_post( get_query_var( 'attachment_id' ) );

				if ( $redirect_url ) {
					$redirect['query'] = remove_query_arg( 'attachment_id', $redirect['query'] );
				}
			} else {
				$redirect_url = get_attachment_link();
				$redirect_obj = get_post();
			}
		} elseif ( is_single() && ! empty( $_GET['p'] ) && ! $redirect_url ) {
			$redirect_url = get_permalink( get_query_var( 'p' ) );
			$redirect_obj = get_post( get_query_var( 'p' ) );

			if ( $redirect_url ) {
				$redirect['query'] = remove_query_arg( array( 'p', 'post_type' ), $redirect['query'] );
			}
		} elseif ( is_single() && ! empty( $_GET['name'] ) && ! $redirect_url ) {
			$redirect_url = get_permalink( $wp_query->get_queried_object_id() );
			$redirect_obj = get_post( $wp_query->get_queried_object_id() );

			if ( $redirect_url ) {
				$redirect['query'] = remove_query_arg( 'name', $redirect['query'] );
			}
		} elseif ( is_page() && ! empty( $_GET['page_id'] ) && ! $redirect_url ) {
			$redirect_url = get_permalink( get_query_var( 'page_id' ) );
			$redirect_obj = get_post( get_query_var( 'page_id' ) );

			if ( $redirect_url ) {
				$redirect['query'] = remove_query_arg( 'page_id', $redirect['query'] );
			}
		} elseif ( is_page() && ! is_feed() && ! $redirect_url
			&& 'page' === get_option( 'show_on_front' ) && get_queried_object_id() === (int) get_option( 'page_on_front' )
		) {
			$redirect_url = home_url( '/' );
		} elseif ( is_home() && ! empty( $_GET['page_id'] ) && ! $redirect_url
			&& 'page' === get_option( 'show_on_front' ) && get_query_var( 'page_id' ) === (int) get_option( 'page_for_posts' )
		) {
			$redirect_url = get_permalink( get_option( 'page_for_posts' ) );
			$redirect_obj = get_post( get_option( 'page_for_posts' ) );

			if ( $redirect_url ) {
				$redirect['query'] = remove_query_arg( 'page_id', $redirect['query'] );
			}
		} elseif ( ! empty( $_GET['m'] ) && ( is_year() || is_month() || is_day() ) ) {
			$m = get_query_var( 'm' );

			switch ( strlen( $m ) ) {
				case 4: // Yearly.
					$redirect_url = get_year_link( $m );
					break;
				case 6: // Monthly.
					$redirect_url = get_month_link( substr( $m, 0, 4 ), substr( $m, 4, 2 ) );
					break;
				case 8: // Daily.
					$redirect_url = get_day_link( substr( $m, 0, 4 ), substr( $m, 4, 2 ), substr( $m, 6, 2 ) );
					break;
			}

			if ( $redirect_url ) {
				$redirect['query'] = remove_query_arg( 'm', $redirect['query'] );
			}
			// Now moving on to non ?m=X year/month/day links.
		} elseif ( is_date() ) {
			$year  = get_query_var( 'year' );
			$month = get_query_var( 'monthnum' );
			$day   = get_query_var( 'day' );

			if ( is_day() && $year && $month && ! empty( $_GET['day'] ) ) {
				$redirect_url = get_day_link( $year, $month, $day );

				if ( $redirect_url ) {
					$redirect['query'] = remove_query_arg( array( 'year', 'monthnum', 'day' ), $redirect['query'] );
				}
			} elseif ( is_month() && $year && ! empty( $_GET['monthnum'] ) ) {
				$redirect_url = get_month_link( $year, $month );

				if ( $redirect_url ) {
					$redirect['query'] = remove_query_arg( array( 'year', 'monthnum' ), $redirect['query'] );
				}
			} elseif ( is_year() && ! empty( $_GET['year'] ) ) {
				$redirect_url = get_year_link( $year );

				if ( $redirect_url ) {
					$redirect['query'] = remove_query_arg( 'year', $redirect['query'] );
				}
			}
		} elseif ( is_author() && ! empty( $_GET['author'] ) && preg_match( '|^[0-9]+$|', $_GET['author'] ) ) {
			$author = get_userdata( get_query_var( 'author' ) );

			if ( false !== $author
				&& $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_author = %d AND $wpdb->posts.post_status = 'publish' LIMIT 1", $author->ID ) )
			) {
				$redirect_url = get_author_posts_url( $author->ID, $author->user_nicename );
				$redirect_obj = $author;

				if ( $redirect_url ) {
					$redirect['query'] = remove_query_arg( 'author', $redirect['query'] );
				}
			}
		} elseif ( is_category() || is_tag() || is_tax() ) { // Terms (tags/categories).
			$term_count = 0;

			foreach ( $wp_query->tax_query->queried_terms as $tax_query ) {
				if ( isset( $tax_query['terms'] ) && is_countable( $tax_query['terms'] ) ) {
					$term_count += count( $tax_query['terms'] );
				}
			}

			$obj = $wp_query->get_queried_object();

			if ( $term_count <= 1 && ! empty( $obj->term_id ) ) {
				$tax_url = get_term_link( (int) $obj->term_id, $obj->taxonomy );

				if ( $tax_url && ! is_wp_error( $tax_url ) ) {
					if ( ! empty( $redirect['query'] ) ) {
						// Strip taxonomy query vars off the URL.
						$qv_remove = array( 'term', 'taxonomy' );

						if ( is_category() ) {
							$qv_remove[] = 'category_name';
							$qv_remove[] = 'cat';
						} elseif ( is_tag() ) {
							$qv_remove[] = 'tag';
							$qv_remove[] = 'tag_id';
						} else {
							// Custom taxonomies will have a custom query var, remove those too.
							$tax_obj = get_taxonomy( $obj->taxonomy );
							if ( false !== $tax_obj->query_var ) {
								$qv_remove[] = $tax_obj->query_var;
							}
						}

						$rewrite_vars = array_diff( array_keys( $wp_query->query ), array_keys( $_GET ) );

						// Check to see if all the query vars are coming from the rewrite, none are set via $_GET.
						if ( ! array_diff( $rewrite_vars, array_keys( $_GET ) ) ) {
							// Remove all of the per-tax query vars.
							$redirect['query'] = remove_query_arg( $qv_remove, $redirect['query'] );

							// Create the destination URL for this taxonomy.
							$tax_url = parse_url( $tax_url );

							if ( ! empty( $tax_url['query'] ) ) {
								// Taxonomy accessible via ?taxonomy=...&term=... or any custom query var.
								parse_str( $tax_url['query'], $query_vars );
								$redirect['query'] = add_query_arg( $query_vars, $redirect['query'] );
							} else {
								// Taxonomy is accessible via a "pretty URL".
								$redirect['path'] = $tax_url['path'];
							}
						} else {
							// Some query vars are set via $_GET. Unset those from $_GET that exist via the rewrite.
							foreach ( $qv_remove as $_qv ) {
								if ( isset( $rewrite_vars[ $_qv ] ) ) {
									$redirect['query'] = remove_query_arg( $_qv, $redirect['query'] );
								}
							}
						}
					}
				}
			}
		} elseif ( is_single() && strpos( $wp_rewrite->permalink_structure, '%category%' ) !== false ) {
			$category_name = get_query_var( 'category_name' );

			if ( $category_name ) {
				$category = get_category_by_path( $category_name );

				if ( ! $category || is_wp_error( $category )
					|| ! has_term( $category->term_id, 'category', $wp_query->get_queried_object_id() )
				) {
					$redirect_url = get_permalink( $wp_query->get_queried_object_id() );
					$redirect_obj = get_post( $wp_query->get_queried_object_id() );
				}
			}
		}

		// Post paging.
		if ( is_singular() && get_query_var( 'page' ) ) {
			$page = get_query_var( 'page' );

			if ( ! $redirect_url ) {
				$redirect_url = get_permalink( get_queried_object_id() );
				$redirect_obj = get_post( get_queried_object_id() );
			}

			if ( $page > 1 ) {
				$redirect_url = trailingslashit( $redirect_url );

				if ( is_front_page() ) {
					$redirect_url .= user_trailingslashit( "$wp_rewrite->pagination_base/$page", 'paged' );
				} else {
					$redirect_url .= user_trailingslashit( $page, 'single_paged' );
				}
			}

			$redirect['query'] = remove_query_arg( 'page', $redirect['query'] );
		}

		if ( get_query_var( 'sitemap' ) ) {
			$redirect_url      = get_sitemap_url( get_query_var( 'sitemap' ), get_query_var( 'sitemap-subtype' ), get_query_var( 'paged' ) );
			$redirect['query'] = remove_query_arg( array( 'sitemap', 'sitemap-subtype', 'paged' ), $redirect['query'] );
		} elseif ( get_query_var( 'paged' ) || is_feed() || get_query_var( 'cpage' ) ) {
			// Paging and feeds.
			$paged = get_query_var( 'paged' );
			$feed  = get_query_var( 'feed' );
			$cpage = get_query_var( 'cpage' );

			while ( preg_match( "#/$wp_rewrite->pagination_base/?[0-9]+?(/+)?$#", $redirect['path'] )
				|| preg_match( '#/(comments/?)?(feed|rss2?|rdf|atom)(/+)?$#', $redirect['path'] )
				|| preg_match( "#/{$wp_rewrite->comments_pagination_base}-[0-9]+(/+)?$#", $redirect['path'] )
			) {
				// Strip off any existing paging.
				$redirect['path'] = preg_replace( "#/$wp_rewrite->pagination_base/?[0-9]+?(/+)?$#", '/', $redirect['path'] );
				// Strip off feed endings.
				$redirect['path'] = preg_replace( '#/(comments/?)?(feed|rss2?|rdf|atom)(/+|$)#', '/', $redirect['path'] );
				// Strip off any existing comment paging.
				$redirect['path'] = preg_replace( "#/{$wp_rewrite->comments_pagination_base}-[0-9]+?(/+)?$#", '/', $redirect['path'] );
			}

			$addl_path    = '';
			$default_feed = get_default_feed();

			if ( is_feed() && in_array( $feed, $wp_rewrite->feeds, true ) ) {
				$addl_path = ! empty( $addl_path ) ? trailingslashit( $addl_path ) : '';

				if ( ! is_singular() && get_query_var( 'withcomments' ) ) {
					$addl_path .= 'comments/';
				}

				if ( ( 'rss' === $default_feed && 'feed' === $feed ) || 'rss' === $feed ) {
					$format = ( 'rss2' === $default_feed ) ? '' : 'rss2';
				} else {
					$format = ( $default_feed === $feed || 'feed' === $feed ) ? '' : $feed;
				}

				$addl_path .= user_trailingslashit( 'feed/' . $format, 'feed' );

				$redirect['query'] = remove_query_arg( 'feed', $redirect['query'] );
			} elseif ( is_feed() && 'old' === $feed ) {
				$old_feed_files = array(
					'wp-atom.php'         => 'atom',
					'wp-commentsrss2.php' => 'comments_rss2',
					'wp-feed.php'         => $default_feed,
					'wp-rdf.php'          => 'rdf',
					'wp-rss.php'          => 'rss2',
					'wp-rss2.php'         => 'rss2',
				);

				if ( isset( $old_feed_files[ basename( $redirect['path'] ) ] ) ) {
					$redirect_url = get_feed_link( $old_feed_files[ basename( $redirect['path'] ) ] );

					wp_redirect( $redirect_url, 301 );
					die();
				}
			}

			if ( $paged > 0 ) {
				$redirect['query'] = remove_query_arg( 'paged', $redirect['query'] );

				if ( ! is_feed() ) {
					if ( ! is_single() ) {
						$addl_path = ! empty( $addl_path ) ? trailingslashit( $addl_path ) : '';

						if ( $paged > 1 ) {
							$addl_path .= user_trailingslashit( "$wp_rewrite->pagination_base/$paged", 'paged' );
						}
					}
				} elseif ( $paged > 1 ) {
					$redirect['query'] = add_query_arg( 'paged', $paged, $redirect['query'] );
				}
			}

			$default_comments_page = get_option( 'default_comments_page' );

			if ( get_option( 'page_comments' )
				&& ( 'newest' === $default_comments_page && $cpage > 0
					|| 'newest' !== $default_comments_page && $cpage > 1 )
			) {
				$addl_path  = ( ! empty( $addl_path ) ? trailingslashit( $addl_path ) : '' );
				$addl_path .= user_trailingslashit( $wp_rewrite->comments_pagination_base . '-' . $cpage, 'commentpaged' );

				$redirect['query'] = remove_query_arg( 'cpage', $redirect['query'] );
			}

			// Strip off trailing /index.php/.
			$redirect['path'] = preg_replace( '|/' . preg_quote( $wp_rewrite->index, '|' ) . '/?$|', '/', $redirect['path'] );
			$redirect['path'] = user_trailingslashit( $redirect['path'] );

			if ( ! empty( $addl_path )
				&& $wp_rewrite->using_index_permalinks()
				&& strpos( $redirect['path'], '/' . $wp_rewrite->index . '/' ) === false
			) {
				$redirect['path'] = trailingslashit( $redirect['path'] ) . $wp_rewrite->index . '/';
			}

			if ( ! empty( $addl_path ) ) {
				$redirect['path'] = trailingslashit( $redirect['path'] ) . $addl_path;
			}

			$redirect_url = $redirect['scheme'] . '://' . $redirect['host'] . $redirect['path'];
		}

		if ( 'wp-register.php' === basename( $redirect['path'] ) ) {
			if ( is_multisite() ) {
				/** This filter is documented in wp-login.php */
				$redirect_url = apply_filters( 'wp_signup_location', network_site_url( 'wp-signup.php' ) );
			} else {
				$redirect_url = wp_registration_url();
			}

			wp_redirect( $redirect_url, 301 );
			die();
		}
	}

	$redirect['query'] = preg_replace( '#^\??&*?#', '', $redirect['query'] );

	// Tack on any additional query vars.
	if ( $redirect_url && ! empty( $redirect['query'] ) ) {
		parse_str( $redirect['query'], $_parsed_query );
		$redirect = parse_url( $redirect_url );

		if ( ! empty( $_parsed_query['name'] ) && ! empty( $redirect['query'] ) ) {
			parse_str( $redirect['query'], $_parsed_redirect_query );

			if ( empty( $_parsed_redirect_query['name'] ) ) {
				unset( $_parsed_query['name'] );
			}
		}

		$_parsed_query = array_combine(
			rawurlencode_deep( array_keys( $_parsed_query ) ),
			rawurlencode_deep( array_values( $_parsed_query ) )
		);

		$redirect_url = add_query_arg( $_parsed_query, $redirect_url );
	}

	if ( $redirect_url ) {
		$redirect = parse_url( $redirect_url );
	}

	// www.example.com vs. example.com
	$user_home = parse_url( home_url() );

	if ( ! empty( $user_home['host'] ) ) {
		$redirect['host'] = $user_home['host'];
	}

	if ( empty( $user_home['path'] ) ) {
		$user_home['path'] = '/';
	}

	// Handle ports.
	if ( ! empty( $user_home['port'] ) ) {
		$redirect['port'] = $user_home['port'];
	} else {
		unset( $redirect['port'] );
	}

	// Trailing /index.php.
	$redirect['path'] = preg_replace( '|/' . preg_quote( $wp_rewrite->index, '|' ) . '/*?$|', '/', $redirect['path'] );

	$punctuation_pattern = implode(
		'|',
		array_map(
			'preg_quote',
			array(
				' ',
				'%20',  // Space.
				'!',
				'%21',  // Exclamation mark.
				'"',
				'%22',  // Double quote.
				"'",
				'%27',  // Single quote.
				'(',
				'%28',  // Opening bracket.
				')',
				'%29',  // Closing bracket.
				',',
				'%2C',  // Comma.
				'.',
				'%2E',  // Period.
				';',
				'%3B',  // Semicolon.
				'{',
				'%7B',  // Opening curly bracket.
				'}',
				'%7D',  // Closing curly bracket.
				'%E2%80%9C', // Opening curly quote.
				'%E2%80%9D', // Closing curly quote.
			)
		)
	);

	// Remove trailing spaces and end punctuation from the path.
	$redirect['path'] = preg_replace( "#($punctuation_pattern)+$#", '', $redirect['path'] );

	if ( ! empty( $redirect['query'] ) ) {
		// Remove trailing spaces and end punctuation from certain terminating query string args.
		$redirect['query'] = preg_replace( "#((^|&)(p|page_id|cat|tag)=[^&]*?)($punctuation_pattern)+$#", '$1', $redirect['query'] );

		// Clean up empty query strings.
		$redirect['query'] = trim( preg_replace( '#(^|&)(p|page_id|cat|tag)=?(&|$)#', '&', $redirect['query'] ), '&' );

		// Redirect obsolete feeds.
		$redirect['query'] = preg_replace( '#(^|&)feed=rss(&|$)#', '$1feed=rss2$2', $redirect['query'] );

		// Remove redundant leading ampersands.
		$redirect['query'] = preg_replace( '#^\??&*?#', '', $redirect['query'] );
	}

	// Strip /index.php/ when we're not using PATHINFO permalinks.
	if ( ! $wp_rewrite->using_index_permalinks() ) {
		$redirect['path'] = str_replace( '/' . $wp_rewrite->index . '/', '/', $redirect['path'] );
	}

	// Trailing slashes.
	if ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks()
		&& ! is_404() && ( ! is_front_page() || is_front_page() && get_query_var( 'paged' ) > 1 )
	) {
		$user_ts_type = '';

		if ( get_query_var( 'paged' ) > 0 ) {
			$user_ts_type = 'paged';
		} else {
			foreach ( array( 'single', 'category', 'page', 'day', 'month', 'year', 'home' ) as $type ) {
				$func = 'is_' . $type;
				if ( call_user_func( $func ) ) {
					$user_ts_type = $type;
					break;
				}
			}
		}

		$redirect['path'] = user_trailingslashit( $redirect['path'], $user_ts_type );
	} elseif ( is_front_page() ) {
		$redirect['path'] = trailingslashit( $redirect['path'] );
	}

	// Remove trailing slash for robots.txt or sitemap requests.
	if ( is_robots()
		|| ! empty( get_query_var( 'sitemap' ) ) || ! empty( get_query_var( 'sitemap-stylesheet' ) )
	) {
		$redirect['path'] = untrailingslashit( $redirect['path'] );
	}

	// Strip multiple slashes out of the URL.
	if ( strpos( $redirect['path'], '//' ) > -1 ) {
		$redirect['path'] = preg_replace( '|/+|', '/', $redirect['path'] );
	}

	// Always trailing slash the Front Page URL.
	if ( trailingslashit( $redirect['path'] ) === trailingslashit( $user_home['path'] ) ) {
		$redirect['path'] = trailingslashit( $redirect['path'] );
	}

	$original_host_low = strtolower( $original['host'] );
	$redirect_host_low = strtolower( $redirect['host'] );

	// Ignore differences in host capitalization, as this can lead to infinite redirects.
	// Only redirect no-www <=> yes-www.
	if ( $original_host_low === $redirect_host_low
		|| ( 'www.' . $original_host_low !== $redirect_host_low
			&& 'www.' . $redirect_host_low !== $original_host_low )
	) {
		$redirect['host'] = $original['host'];
	}

	$compare_original = array( $original['host'], $original['path'] );

	if ( ! empty( $original['port'] ) ) {
		$compare_original[] = $original['port'];
	}

	if ( ! empty( $original['query'] ) ) {
		$compare_original[] = $original['query'];
	}

	$compare_redirect = array( $redirect['host'], $redirect['path'] );

	if ( ! empty( $redirect['port'] ) ) {
		$compare_redirect[] = $redirect['port'];
	}

	if ( ! empty( $redirect['query'] ) ) {
		$compare_redirect[] = $redirect['query'];
	}

	if ( $compare_original !== $compare_redirect ) {
		$redirect_url = $redirect['scheme'] . '://' . $redirect['host'];

		if ( ! empty( $redirect['port'] ) ) {
			$redirect_url .= ':' . $redirect['port'];
		}

		$redirect_url .= $redirect['path'];

		if ( ! empty( $redirect['query'] ) ) {
			$redirect_url .= '?' . $redirect['query'];
		}
	}

	if ( ! $redirect_url || $redirect_url === $requested_url ) {
		return;
	}

	// Hex encoded octets are case-insensitive.
	if ( false !== strpos( $requested_url, '%' ) ) {
		if ( ! function_exists( 'lowercase_octets' ) ) {
			/**
			 * Converts the first hex-encoded octet match to lowercase.
			 *
			 * @since 3.1.0
			 * @ignore
			 *
			 * @param array $matches Hex-encoded octet matches for the requested URL.
			 * @return string Lowercased version of the first match.
			 */
			function lowercase_octets( $matches ) {
				return strtolower( $matches[0] );
			}
		}

		$requested_url = preg_replace_callback( '|%[a-fA-F0-9][a-fA-F0-9]|', 'lowercase_octets', $requested_url );
	}

	if ( $redirect_obj instanceof WP_Post ) {
		$post_status_obj = get_post_status_object( get_post_status( $redirect_obj ) );
		/*
		 * Unset the redirect object and URL if they are not readable by the user.
		 * This condition is a little confusing as the condition needs to pass if
		 * the post is not readable by the user. That's why there are ! (not) conditions
		 * throughout.
		 */
		if (
			// Private post statuses only redirect if the user can read them.
			! (
				$post_status_obj->private &&
				current_user_can( 'read_post', $redirect_obj->ID )
			) &&
			// For other posts, only redirect if publicly viewable.
			! is_post_publicly_viewable( $redirect_obj )
		) {
			$redirect_obj = false;
			$redirect_url = false;
		}
	}

	/**
	 * Filters the canonical redirect URL.
	 *
	 * Returning false to this filter will cancel the redirect.
	 *
	 * @since 2.3.0
	 *
	 * @param string $redirect_url  The redirect URL.
	 * @param string $requested_url The requested URL.
	 */
	$redirect_url = apply_filters( 'redirect_canonical', $redirect_url, $requested_url );

	// Yes, again -- in case the filter aborted the request.
	if ( ! $redirect_url || strip_fragment_from_url( $redirect_url ) === strip_fragment_from_url( $requested_url ) ) {
		return;
	}

	if ( $do_redirect ) {
		// Protect against chained redirects.
		if ( ! redirect_canonical( $redirect_url, false ) ) {
			wp_redirect( $redirect_url, 301 );
			exit;
		} else {
			// Debug.
			// die("1: $redirect_url<br />2: " . redirect_canonical( $redirect_url, false ) );
			return;
		}
	} else {
		return $redirect_url;
	}
}

/**
 * Removes arguments from a query string if they are not present in a URL
 * DO NOT use this in plugin code.
 *
 * @since 3.4.0
 * @access private
 *
 * @param string $query_string
 * @param array  $args_to_check
 * @param string $url
 * @return string The altered query string
 */
function _remove_qs_args_if_not_in_url( $query_string, array $args_to_check, $url ) {
	$parsed_url = parse_url( $url );

	if ( ! empty( $parsed_url['query'] ) ) {
		parse_str( $parsed_url['query'], $parsed_query );

		foreach ( $args_to_check as $qv ) {
			if ( ! isset( $parsed_query[ $qv ] ) ) {
				$query_string = remove_query_arg( $qv, $query_string );
			}
		}
	} else {
		$query_string = remove_query_arg( $args_to_check, $query_string );
	}

	return $query_string;
}

/**
 * Strips the #fragment from a URL, if one is present.
 *
 * @since 4.4.0
 *
 * @param string $url The URL to strip.
 * @return string The altered URL.
 */
function strip_fragment_from_url( $url ) {
	$parsed_url = wp_parse_url( $url );

	if ( ! empty( $parsed_url['host'] ) ) {
		$url = '';

		if ( ! empty( $parsed_url['scheme'] ) ) {
			$url = $parsed_url['scheme'] . ':';
		}

		$url .= '//' . $parsed_url['host'];

		if ( ! empty( $parsed_url['port'] ) ) {
			$url .= ':' . $parsed_url['port'];
		}

		if ( ! empty( $parsed_url['path'] ) ) {
			$url .= $parsed_url['path'];
		}

		if ( ! empty( $parsed_url['query'] ) ) {
			$url .= '?' . $parsed_url['query'];
		}
	}

	return $url;
}

/**
 * Attempts to guess the correct URL for a 404 request based on query vars.
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return string|false The correct URL if one is found. False on failure.
 */
function redirect_guess_404_permalink() {
	global $wpdb;

	/**
	 * Filters whether to attempt to guess a redirect URL for a 404 request.
	 *
	 * Returning a false value from the filter will disable the URL guessing
	 * and return early without performing a redirect.
	 *
	 * @since 5.5.0
	 *
	 * @param bool $do_redirect_guess Whether to attempt to guess a redirect URL
	 *                                for a 404 request. Default true.
	 */
	if ( false === apply_filters( 'do_redirect_guess_404_permalink', true ) ) {
		return false;
	}

	/**
	 * Short-circuits the redirect URL guessing for 404 requests.
	 *
	 * Returning a non-null value from the filter will effectively short-circuit
	 * the URL guessing, returning the passed value instead.
	 *
	 * @since 5.5.0
	 *
	 * @param null|string|false $pre Whether to short-circuit guessing the redirect for a 404.
	 *                               Default null to continue with the URL guessing.
	 */
	$pre = apply_filters( 'pre_redirect_guess_404_permalink', null );
	if ( null !== $pre ) {
		return $pre;
	}

	if ( get_query_var( 'name' ) ) {
		/**
		 * Filters whether to perform a strict guess for a 404 redirect.
		 *
		 * Returning a truthy value from the filter will redirect only exact post_name matches.
		 *
		 * @since 5.5.0
		 *
		 * @param bool $strict_guess Whether to perform a strict guess. Default false (loose guess).
		 */
		$strict_guess = apply_filters( 'strict_redirect_guess_404_permalink', false );

		if ( $strict_guess ) {
			$where = $wpdb->prepare( 'post_name = %s', get_query_var( 'name' ) );
		} else {
			$where = $wpdb->prepare( 'post_name LIKE %s', $wpdb->esc_like( get_query_var( 'name' ) ) . '%' );
		}

		// If any of post_type, year, monthnum, or day are set, use them to refine the query.
		if ( get_query_var( 'post_type' ) ) {
			if ( is_array( get_query_var( 'post_type' ) ) ) {
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
				$where .= " AND post_type IN ('" . join( "', '", esc_sql( get_query_var( 'post_type' ) ) ) . "')";
			} else {
				$where .= $wpdb->prepare( ' AND post_type = %s', get_query_var( 'post_type' ) );
			}
		} else {
			$where .= " AND post_type IN ('" . implode( "', '", get_post_types( array( 'public' => true ) ) ) . "')";
		}

		if ( get_query_var( 'year' ) ) {
			$where .= $wpdb->prepare( ' AND YEAR(post_date) = %d', get_query_var( 'year' ) );
		}
		if ( get_query_var( 'monthnum' ) ) {
			$where .= $wpdb->prepare( ' AND MONTH(post_date) = %d', get_query_var( 'monthnum' ) );
		}
		if ( get_query_var( 'day' ) ) {
			$where .= $wpdb->prepare( ' AND DAYOFMONTH(post_date) = %d', get_query_var( 'day' ) );
		}

		$publicly_viewable_statuses = array_filter( get_post_stati(), 'is_post_status_viewable' );
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$post_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE $where AND post_status IN ('" . implode( "', '", esc_sql( $publicly_viewable_statuses ) ) . "')" );

		if ( ! $post_id ) {
			return false;
		}

		if ( get_query_var( 'feed' ) ) {
			return get_post_comments_feed_link( $post_id, get_query_var( 'feed' ) );
		} elseif ( get_query_var( 'page' ) > 1 ) {
			return trailingslashit( get_permalink( $post_id ) ) . user_trailingslashit( get_query_var( 'page' ), 'single_paged' );
		} else {
			return get_permalink( $post_id );
		}
	}

	return false;
}

/**
 * Redirects a variety of shorthand URLs to the admin.
 *
 * If a user visits example.com/admin, they'll be redirected to /wp-admin.
 * Visiting /login redirects to /wp-login.php, and so on.
 *
 * @since 3.4.0
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 */
function wp_redirect_admin_locations() {
	global $wp_rewrite;

	if ( ! ( is_404() && $wp_rewrite->using_permalinks() ) ) {
		return;
	}

	$admins = array(
		home_url( 'wp-admin', 'relative' ),
		home_url( 'dashboard', 'relative' ),
		home_url( 'admin', 'relative' ),
		site_url( 'dashboard', 'relative' ),
		site_url( 'admin', 'relative' ),
	);

	if ( in_array( untrailingslashit( $_SERVER['REQUEST_URI'] ), $admins, true ) ) {
		wp_redirect( admin_url() );
		exit;
	}

	$logins = array(
		home_url( 'wp-login.php', 'relative' ),
		home_url( 'login', 'relative' ),
		site_url( 'login', 'relative' ),
	);

	if ( in_array( untrailingslashit( $_SERVER['REQUEST_URI'] ), $logins, true ) ) {
		wp_redirect( wp_login_url() );
		exit;
	}
}

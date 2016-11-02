<?php

/**
 * Based on breadcrumbs function by Dimox
 * http://dimox.net/wordpress-breadcrumbs-without-a-plugin/
 */
function us_breadcrumbs() {

	/* === OPTIONS === */
	$text['home'] = __( 'Home', 'us' ); // text for the 'Home' link
	$text['category'] = __( 'Archive by Category "%s"', 'us' ); // text for a category page
	$text['search'] = __( 'Search Results for "%s" Query', 'us' ); // text for a search results page
	$text['tag'] = __( 'Posts Tagged "%s"', 'us' ); // text for a tag page
	$text['author'] = __( 'Articles Posted by %s', 'us' ); // text for an author page
	$text['404'] = __( 'Error 404', 'us' ); // text for the 404 page
	$text['forums'] = __( 'Forums', 'us' ); // text for the 404 page

	$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
	$showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
	$delimiter = ' <span class="g-breadcrumbs-separator"></span> '; // delimiter between crumbs
	$before = '<span class="g-breadcrumbs-item">'; // tag before the current crumb
	$after = '</span>'; // tag after the current crumb
	/* === END OF OPTIONS === */

	// WooCommerce product breadcrumbs
	if ( function_exists( 'woocommerce_breadcrumb' ) AND ( is_shop() OR is_product_category() OR is_product_tag() OR is_product() OR is_account_page() ) ) {
		echo woocommerce_breadcrumb( array(
			'delimiter' => $delimiter,
			'wrap_before' => '<div class="g-breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">',
			'wrap_after' => '</div>',
			'before' => $before,
			'after' => $after,
		) );

		return;
	}

	// bbPress breadcrumbs
	if ( function_exists( 'bbp_get_breadcrumb' ) AND in_array( get_post_type(), array( 'topic', 'forum' ) ) ) {
		echo bbp_get_breadcrumb( array(
			'before' => '<div class="g-breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">',
			'after' => '</div>',
			'sep' => $delimiter,
			'crumb_before' => $before,
			'crumb_after' => $after,
		) );

		return;
	}

	global $post;
	$homeLink = home_url() . '/';
	$linkBefore = '<span typeof="v:Breadcrumb">';
	$linkAfter = '</span>';
	$linkAttr = ' rel="v:url" property="v:title"';
	$link = $linkBefore . '<a class="g-breadcrumbs-item"' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;

	if ( is_home() || is_front_page() ) {

		if ( $showOnHome == 1 ) {
			echo '<div id="crumbs"><a href="' . esc_url( $homeLink ) . '">' . $text['home'] . '</a></div>';
		}
	} else {

		echo '<div class="g-breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">' . sprintf( $link, $homeLink, $text['home'] ) . $delimiter;

		if ( is_category() ) {
			$thisCat = get_category( get_query_var( 'cat' ), FALSE );
			if ( $thisCat->parent != 0 ) {
				$cats = get_category_parents( $thisCat->parent, TRUE, $delimiter );
				$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, $cats );
				$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
				echo $cats;
			}
			echo $before . sprintf( $text['category'], single_cat_title( '', FALSE ) ) . $after;
		} elseif ( is_search() ) {
			echo $before . sprintf( $text['search'], get_search_query() ) . $after;
		} elseif ( is_day() ) {
			echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $delimiter;
			echo sprintf( $link, get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), __( get_the_time( 'F' ), 'us' ) ) . $delimiter;
			echo $before . get_the_time( 'd' ) . $after;
		} elseif ( is_month() ) {
			echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $delimiter;
			echo $before . __( get_the_time( 'F' ), 'us' ) . $after;
		} elseif ( is_year() ) {
			echo $before . get_the_time( 'Y' ) . $after;
		} elseif ( is_single() && ! is_attachment() ) {
			if ( get_post_type() == 'topic' OR get_post_type() == 'forum' ) {
				$forums_page = bbp_get_page_by_path( bbp_get_root_slug() );
				if ( ! empty( $forums_page ) ) {
					$forums_page_url = get_permalink( $forums_page->ID );
					echo sprintf( $link, $forums_page_url, $text['forums'] );
				}
				$parent_id = $post->post_parent;
				if ( $parent_id ) {
					$breadcrumbs = array();
					while ( $parent_id ){
						$page = get_page( $parent_id );
						$breadcrumbs[] = sprintf( $link, get_permalink( $page->ID ), get_the_title( $page->ID ) );
						$parent_id = $page->post_parent;
					}
					$breadcrumbs = array_reverse( $breadcrumbs );
					for ( $i = 0; $i < count( $breadcrumbs ); $i ++ ) {
						echo $delimiter . $breadcrumbs[ $i ];
						//                        if ($i != count($breadcrumbs)-1) echo $delimiter;
					}

					//                    if ( get_post_type() == 'forum' ) {
					//                        echo $delimiter;
					//                    }
				}

				//                if ( get_post_type() == 'forum' ) {
				//                    if ($showCurrent == 1) echo $before . get_the_title() . $after;
				//                }

			} elseif ( get_post_type() != 'post' ) {
				$post_type = get_post_type_object( get_post_type() );
				if ( ! empty( $post_type->labels->name ) ) {
					echo $post_type->labels->name;
				}
				if ( $showCurrent == 1 ) {
					echo $delimiter . $before . get_the_title() . $after;
				}
			} else {
				$cat = get_the_category();
				$cat = $cat[0];
				$cats = get_category_parents( $cat, TRUE, $delimiter );
				if ( $showCurrent == 0 ) {
					$cats = preg_replace( "#^(.+)$delimiter$#", "$1", $cats );
				}
				$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, $cats );
				$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
				echo $cats;
				if ( $showCurrent == 1 ) {
					echo $before . get_the_title() . $after;
				}
			}
		} elseif ( function_exists( 'is_shop' ) and is_shop() ) {
			if ( ! $post->post_parent ) {
				if ( $showCurrent == 1 ) {
					echo $before . get_the_title() . $after;
				}
			} elseif ( $post->post_parent ) {
				$parent_id = $post->post_parent;
				$breadcrumbs = array();
				while ( $parent_id ){
					$page = get_page( $parent_id );
					$breadcrumbs[] = sprintf( $link, get_permalink( $page->ID ), get_the_title( $page->ID ) );
					$parent_id = $page->post_parent;
				}
				$breadcrumbs = array_reverse( $breadcrumbs );
				for ( $i = 0; $i < count( $breadcrumbs ); $i ++ ) {
					echo $breadcrumbs[ $i ];
					if ( $i != count( $breadcrumbs ) - 1 ) {
						echo $delimiter;
					}
				}
				if ( $showCurrent == 1 ) {
					echo $delimiter . $before . get_the_title() . $after;
				}
			}
		} elseif ( ! is_single() && ! is_page() && get_post_type() != 'post' && ! is_404() ) {
			$post_type = get_post_type_object( get_post_type() );

			if ( isset( $post_type->labels->name ) ) {
				echo $before . $post_type->labels->name . $after;
			} else {
				// TODO: perhaps there is a fancier way to handle this
				if ( function_exists( 'bbp_is_single_user' ) and bbp_is_single_user() ) {
					echo us_translate_with_external_domain( 'Profile', 'bbpress' );
				} else {
					echo '<script>jQuery(document).ready(function() { jQuery(".g-breadcrumbs-separator").last().hide(); });</script>';
				}
			}
		} elseif ( is_attachment() ) {
			$parent = get_post( $post->post_parent );
			$cat = get_the_category( $parent->ID );
			if ( is_array( $cat ) AND count( $cat ) > 0 ) {
				$cat = $cat[0];
				$cats = get_category_parents( $cat, TRUE, $delimiter );
				$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, NULL );
				$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
				echo $cats;
			}
			printf( $link, get_permalink( $parent ), $parent->post_title );
			if ( $showCurrent == 1 ) {
				echo $delimiter . $before . get_the_title() . $after;
			}
		} elseif ( is_page() && ! $post->post_parent ) {
			if ( $showCurrent == 1 ) {
				echo $before . get_the_title() . $after;
			}
		} elseif ( is_page() && $post->post_parent ) {
			$parent_id = $post->post_parent;
			$breadcrumbs = array();
			while ( $parent_id ){
				$page = get_page( $parent_id );
				$breadcrumbs[] = sprintf( $link, get_permalink( $page->ID ), get_the_title( $page->ID ) );
				$parent_id = $page->post_parent;
			}
			$breadcrumbs = array_reverse( $breadcrumbs );
			for ( $i = 0; $i < count( $breadcrumbs ); $i ++ ) {
				echo $breadcrumbs[ $i ];
				if ( $i != count( $breadcrumbs ) - 1 ) {
					echo $delimiter;
				}
			}
			if ( $showCurrent == 1 ) {
				echo $delimiter . $before . get_the_title() . $after;
			}
		} elseif ( is_tag() ) {
			echo $before . sprintf( $text['tag'], single_tag_title( '', FALSE ) ) . $after;
		} elseif ( is_author() ) {
			global $author;
			$userdata = get_userdata( $author );
			echo $before . sprintf( $text['author'], $userdata->display_name ) . $after;
		} elseif ( is_404() ) {
			echo $before . $text['404'] . $after;
		}

		if ( get_query_var( 'paged' ) AND ! ( get_post_type() == 'topic' OR get_post_type() == 'forum' ) ) {
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
				echo ' (';
			} else {
				echo $delimiter;
			}
			echo __( 'Page', 'us' ) . ' ' . get_query_var( 'paged' );
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
				echo ')';
			}
		}

		echo '</div>';
	}
} // end dimox_breadcrumbs()

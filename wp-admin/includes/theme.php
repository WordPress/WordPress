<?php
/**
 * WordPress Theme Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Remove a theme
 *
 * @since 2.8.0
 *
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 *
 * @param string $stylesheet Stylesheet of the theme to delete.
 * @param string $redirect   Redirect to page when complete.
 * @return bool|null|WP_Error True on success, false if `$stylesheet` is empty, WP_Error on failure.
 *                            Null if filesystem credentials are required to proceed.
 */
function delete_theme( $stylesheet, $redirect = '' ) {
	global $wp_filesystem;

	if ( empty( $stylesheet ) ) {
		return false;
	}

	if ( empty( $redirect ) ) {
		$redirect = wp_nonce_url( 'themes.php?action=delete&stylesheet=' . urlencode( $stylesheet ), 'delete-theme_' . $stylesheet );
	}

	ob_start();
	$credentials = request_filesystem_credentials( $redirect );
	$data        = ob_get_clean();

	if ( false === $credentials ) {
		if ( ! empty( $data ) ) {
			require_once ABSPATH . 'wp-admin/admin-header.php';
			echo $data;
			require_once ABSPATH . 'wp-admin/admin-footer.php';
			exit;
		}
		return;
	}

	if ( ! WP_Filesystem( $credentials ) ) {
		ob_start();
		// Failed to connect. Error and request again.
		request_filesystem_credentials( $redirect, '', true );
		$data = ob_get_clean();

		if ( ! empty( $data ) ) {
			require_once ABSPATH . 'wp-admin/admin-header.php';
			echo $data;
			require_once ABSPATH . 'wp-admin/admin-footer.php';
			exit;
		}
		return;
	}

	if ( ! is_object( $wp_filesystem ) ) {
		return new WP_Error( 'fs_unavailable', __( 'Could not access filesystem.' ) );
	}

	if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->has_errors() ) {
		return new WP_Error( 'fs_error', __( 'Filesystem error.' ), $wp_filesystem->errors );
	}

	// Get the base plugin folder.
	$themes_dir = $wp_filesystem->wp_themes_dir();
	if ( empty( $themes_dir ) ) {
		return new WP_Error( 'fs_no_themes_dir', __( 'Unable to locate WordPress theme directory.' ) );
	}

	/**
	 * Fires immediately before a theme deletion attempt.
	 *
	 * @since 5.8.0
	 *
	 * @param string $stylesheet Stylesheet of the theme to delete.
	 */
	do_action( 'delete_theme', $stylesheet );

	$themes_dir = trailingslashit( $themes_dir );
	$theme_dir  = trailingslashit( $themes_dir . $stylesheet );
	$deleted    = $wp_filesystem->delete( $theme_dir, true );

	/**
	 * Fires immediately after a theme deletion attempt.
	 *
	 * @since 5.8.0
	 *
	 * @param string $stylesheet Stylesheet of the theme to delete.
	 * @param bool   $deleted    Whether the theme deletion was successful.
	 */
	do_action( 'deleted_theme', $stylesheet, $deleted );

	if ( ! $deleted ) {
		return new WP_Error(
			'could_not_remove_theme',
			/* translators: %s: Theme name. */
			sprintf( __( 'Could not fully remove the theme %s.' ), $stylesheet )
		);
	}

	$theme_translations = wp_get_installed_translations( 'themes' );

	// Remove language files, silently.
	if ( ! empty( $theme_translations[ $stylesheet ] ) ) {
		$translations = $theme_translations[ $stylesheet ];

		foreach ( $translations as $translation => $data ) {
			$wp_filesystem->delete( WP_LANG_DIR . '/themes/' . $stylesheet . '-' . $translation . '.po' );
			$wp_filesystem->delete( WP_LANG_DIR . '/themes/' . $stylesheet . '-' . $translation . '.mo' );

			$json_translation_files = glob( WP_LANG_DIR . '/themes/' . $stylesheet . '-' . $translation . '-*.json' );
			if ( $json_translation_files ) {
				array_map( array( $wp_filesystem, 'delete' ), $json_translation_files );
			}
		}
	}

	// Remove the theme from allowed themes on the network.
	if ( is_multisite() ) {
		WP_Theme::network_disable_theme( $stylesheet );
	}

	// Force refresh of theme update information.
	delete_site_transient( 'update_themes' );

	return true;
}

/**
 * Gets the page templates available in this theme.
 *
 * @since 1.5.0
 * @since 4.7.0 Added the `$post_type` parameter.
 *
 * @param WP_Post|null $post      Optional. The post being edited, provided for context.
 * @param string       $post_type Optional. Post type to get the templates for. Default 'page'.
 * @return string[] Array of template file names keyed by the template header name.
 */
function get_page_templates( $post = null, $post_type = 'page' ) {
	return array_flip( wp_get_theme()->get_page_templates( $post, $post_type ) );
}

/**
 * Tidies a filename for url display by the theme editor.
 *
 * @since 2.9.0
 * @access private
 *
 * @param string $fullpath Full path to the theme file
 * @param string $containingfolder Path of the theme parent folder
 * @return string
 */
function _get_template_edit_filename( $fullpath, $containingfolder ) {
	return str_replace( dirname( dirname( $containingfolder ) ), '', $fullpath );
}

/**
 * Check if there is an update for a theme available.
 *
 * Will display link, if there is an update available.
 *
 * @since 2.7.0
 *
 * @see get_theme_update_available()
 *
 * @param WP_Theme $theme Theme data object.
 */
function theme_update_available( $theme ) {
	echo get_theme_update_available( $theme );
}

/**
 * Retrieve the update link if there is a theme update available.
 *
 * Will return a link if there is an update available.
 *
 * @since 3.8.0
 *
 * @param WP_Theme $theme WP_Theme object.
 * @return string|false HTML for the update link, or false if invalid info was passed.
 */
function get_theme_update_available( $theme ) {
	static $themes_update = null;

	if ( ! current_user_can( 'update_themes' ) ) {
		return false;
	}

	if ( ! isset( $themes_update ) ) {
		$themes_update = get_site_transient( 'update_themes' );
	}

	if ( ! ( $theme instanceof WP_Theme ) ) {
		return false;
	}

	$stylesheet = $theme->get_stylesheet();

	$html = '';

	if ( isset( $themes_update->response[ $stylesheet ] ) ) {
		$update      = $themes_update->response[ $stylesheet ];
		$theme_name  = $theme->display( 'Name' );
		$details_url = add_query_arg(
			array(
				'TB_iframe' => 'true',
				'width'     => 1024,
				'height'    => 800,
			),
			$update['url']
		); // Theme browser inside WP? Replace this. Also, theme preview JS will override this on the available list.
		$update_url  = wp_nonce_url( admin_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( $stylesheet ) ), 'upgrade-theme_' . $stylesheet );

		if ( ! is_multisite() ) {
			if ( ! current_user_can( 'update_themes' ) ) {
				$html = sprintf(
					/* translators: 1: Theme name, 2: Theme details URL, 3: Additional link attributes, 4: Version number. */
					'<p><strong>' . __( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a>.' ) . '</strong></p>',
					$theme_name,
					esc_url( $details_url ),
					sprintf(
						'class="thickbox open-plugin-details-modal" aria-label="%s"',
						/* translators: 1: Theme name, 2: Version number. */
						esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $theme_name, $update['new_version'] ) )
					),
					$update['new_version']
				);
			} elseif ( empty( $update['package'] ) ) {
				$html = sprintf(
					/* translators: 1: Theme name, 2: Theme details URL, 3: Additional link attributes, 4: Version number. */
					'<p><strong>' . __( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a>. <em>Automatic update is unavailable for this theme.</em>' ) . '</strong></p>',
					$theme_name,
					esc_url( $details_url ),
					sprintf(
						'class="thickbox open-plugin-details-modal" aria-label="%s"',
						/* translators: 1: Theme name, 2: Version number. */
						esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $theme_name, $update['new_version'] ) )
					),
					$update['new_version']
				);
			} else {
				$html = sprintf(
					/* translators: 1: Theme name, 2: Theme details URL, 3: Additional link attributes, 4: Version number, 5: Update URL, 6: Additional link attributes. */
					'<p><strong>' . __( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a> or <a href="%5$s" %6$s>update now</a>.' ) . '</strong></p>',
					$theme_name,
					esc_url( $details_url ),
					sprintf(
						'class="thickbox open-plugin-details-modal" aria-label="%s"',
						/* translators: 1: Theme name, 2: Version number. */
						esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $theme_name, $update['new_version'] ) )
					),
					$update['new_version'],
					$update_url,
					sprintf(
						'aria-label="%s" id="update-theme" data-slug="%s"',
						/* translators: %s: Theme name. */
						esc_attr( sprintf( _x( 'Update %s now', 'theme' ), $theme_name ) ),
						$stylesheet
					)
				);
			}
		}
	}

	return $html;
}

/**
 * Retrieve list of WordPress theme features (aka theme tags).
 *
 * @since 3.1.0
 * @since 3.2.0 Added 'Gray' color and 'Featured Image Header', 'Featured Images',
 *              'Full Width Template', and 'Post Formats' features.
 * @since 3.5.0 Added 'Flexible Header' feature.
 * @since 3.8.0 Renamed 'Width' filter to 'Layout'.
 * @since 3.8.0 Renamed 'Fixed Width' and 'Flexible Width' options
 *              to 'Fixed Layout' and 'Fluid Layout'.
 * @since 3.8.0 Added 'Accessibility Ready' feature and 'Responsive Layout' option.
 * @since 3.9.0 Combined 'Layout' and 'Columns' filters.
 * @since 4.6.0 Removed 'Colors' filter.
 * @since 4.6.0 Added 'Grid Layout' option.
 *              Removed 'Fixed Layout', 'Fluid Layout', and 'Responsive Layout' options.
 * @since 4.6.0 Added 'Custom Logo' and 'Footer Widgets' features.
 *              Removed 'Blavatar' feature.
 * @since 4.6.0 Added 'Blog', 'E-Commerce', 'Education', 'Entertainment', 'Food & Drink',
 *              'Holiday', 'News', 'Photography', and 'Portfolio' subjects.
 *              Removed 'Photoblogging' and 'Seasonal' subjects.
 * @since 4.9.0 Reordered the filters from 'Layout', 'Features', 'Subject'
 *              to 'Subject', 'Features', 'Layout'.
 * @since 4.9.0 Removed 'BuddyPress', 'Custom Menu', 'Flexible Header',
 *              'Front Page Posting', 'Microformats', 'RTL Language Support',
 *              'Threaded Comments', and 'Translation Ready' features.
 * @since 5.5.0 Added 'Block Editor Patterns', 'Block Editor Styles',
 *              and 'Full Site Editing' features.
 * @since 5.5.0 Added 'Wide Blocks' layout option.
 *
 * @param bool $api Optional. Whether try to fetch tags from the WordPress.org API. Defaults to true.
 * @return array Array of features keyed by category with translations keyed by slug.
 */
function get_theme_feature_list( $api = true ) {
	// Hard-coded list is used if API is not accessible.
	$features = array(

		__( 'Subject' )  => array(
			'blog'           => __( 'Blog' ),
			'e-commerce'     => __( 'E-Commerce' ),
			'education'      => __( 'Education' ),
			'entertainment'  => __( 'Entertainment' ),
			'food-and-drink' => __( 'Food & Drink' ),
			'holiday'        => __( 'Holiday' ),
			'news'           => __( 'News' ),
			'photography'    => __( 'Photography' ),
			'portfolio'      => __( 'Portfolio' ),
		),

		__( 'Features' ) => array(
			'accessibility-ready'   => __( 'Accessibility Ready' ),
			'block-patterns'        => __( 'Block Editor Patterns' ),
			'block-styles'          => __( 'Block Editor Styles' ),
			'custom-background'     => __( 'Custom Background' ),
			'custom-colors'         => __( 'Custom Colors' ),
			'custom-header'         => __( 'Custom Header' ),
			'custom-logo'           => __( 'Custom Logo' ),
			'editor-style'          => __( 'Editor Style' ),
			'featured-image-header' => __( 'Featured Image Header' ),
			'featured-images'       => __( 'Featured Images' ),
			'footer-widgets'        => __( 'Footer Widgets' ),
			'full-site-editing'     => __( 'Full Site Editing' ),
			'full-width-template'   => __( 'Full Width Template' ),
			'post-formats'          => __( 'Post Formats' ),
			'sticky-post'           => __( 'Sticky Post' ),
			'theme-options'         => __( 'Theme Options' ),
		),

		__( 'Layout' )   => array(
			'grid-layout'   => __( 'Grid Layout' ),
			'one-column'    => __( 'One Column' ),
			'two-columns'   => __( 'Two Columns' ),
			'three-columns' => __( 'Three Columns' ),
			'four-columns'  => __( 'Four Columns' ),
			'left-sidebar'  => __( 'Left Sidebar' ),
			'right-sidebar' => __( 'Right Sidebar' ),
			'wide-blocks'   => __( 'Wide Blocks' ),
		),

	);

	if ( ! $api || ! current_user_can( 'install_themes' ) ) {
		return $features;
	}

	$feature_list = get_site_transient( 'wporg_theme_feature_list' );
	if ( ! $feature_list ) {
		set_site_transient( 'wporg_theme_feature_list', array(), 3 * HOUR_IN_SECONDS );
	}

	if ( ! $feature_list ) {
		$feature_list = themes_api( 'feature_list', array() );
		if ( is_wp_error( $feature_list ) ) {
			return $features;
		}
	}

	if ( ! $feature_list ) {
		return $features;
	}

	set_site_transient( 'wporg_theme_feature_list', $feature_list, 3 * HOUR_IN_SECONDS );

	$category_translations = array(
		'Layout'   => __( 'Layout' ),
		'Features' => __( 'Features' ),
		'Subject'  => __( 'Subject' ),
	);

	$wporg_features = array();

	// Loop over the wp.org canonical list and apply translations.
	foreach ( (array) $feature_list as $feature_category => $feature_items ) {
		if ( isset( $category_translations[ $feature_category ] ) ) {
			$feature_category = $category_translations[ $feature_category ];
		}

		$wporg_features[ $feature_category ] = array();

		foreach ( $feature_items as $feature ) {
			if ( isset( $features[ $feature_category ][ $feature ] ) ) {
				$wporg_features[ $feature_category ][ $feature ] = $features[ $feature_category ][ $feature ];
			} else {
				$wporg_features[ $feature_category ][ $feature ] = $feature;
			}
		}
	}

	return $wporg_features;
}

/**
 * Retrieves theme installer pages from the WordPress.org Themes API.
 *
 * It is possible for a theme to override the Themes API result with three
 * filters. Assume this is for themes, which can extend on the Theme Info to
 * offer more choices. This is very powerful and must be used with care, when
 * overriding the filters.
 *
 * The first filter, {@see 'themes_api_args'}, is for the args and gives the action
 * as the second parameter. The hook for {@see 'themes_api_args'} must ensure that
 * an object is returned.
 *
 * The second filter, {@see 'themes_api'}, allows a plugin to override the WordPress.org
 * Theme API entirely. If `$action` is 'query_themes', 'theme_information', or 'feature_list',
 * an object MUST be passed. If `$action` is 'hot_tags', an array should be passed.
 *
 * Finally, the third filter, {@see 'themes_api_result'}, makes it possible to filter the
 * response object or array, depending on the `$action` type.
 *
 * Supported arguments per action:
 *
 * | Argument Name      | 'query_themes' | 'theme_information' | 'hot_tags' | 'feature_list'   |
 * | -------------------| :------------: | :-----------------: | :--------: | :--------------: |
 * | `$slug`            | No             |  Yes                | No         | No               |
 * | `$per_page`        | Yes            |  No                 | No         | No               |
 * | `$page`            | Yes            |  No                 | No         | No               |
 * | `$number`          | No             |  No                 | Yes        | No               |
 * | `$search`          | Yes            |  No                 | No         | No               |
 * | `$tag`             | Yes            |  No                 | No         | No               |
 * | `$author`          | Yes            |  No                 | No         | No               |
 * | `$user`            | Yes            |  No                 | No         | No               |
 * | `$browse`          | Yes            |  No                 | No         | No               |
 * | `$locale`          | Yes            |  Yes                | No         | No               |
 * | `$fields`          | Yes            |  Yes                | No         | No               |
 *
 * @since 2.8.0
 *
 * @param string       $action API action to perform: 'query_themes', 'theme_information',
 *                             'hot_tags' or 'feature_list'.
 * @param array|object $args   {
 *     Optional. Array or object of arguments to serialize for the Themes API.
 *
 *     @type string  $slug     The theme slug. Default empty.
 *     @type int     $per_page Number of themes per page. Default 24.
 *     @type int     $page     Number of current page. Default 1.
 *     @type int     $number   Number of tags to be queried.
 *     @type string  $search   A search term. Default empty.
 *     @type string  $tag      Tag to filter themes. Default empty.
 *     @type string  $author   Username of an author to filter themes. Default empty.
 *     @type string  $user     Username to query for their favorites. Default empty.
 *     @type string  $browse   Browse view: 'featured', 'popular', 'updated', 'favorites'.
 *     @type string  $locale   Locale to provide context-sensitive results. Default is the value of get_locale().
 *     @type array   $fields   {
 *         Array of fields which should or should not be returned.
 *
 *         @type bool $description        Whether to return the theme full description. Default false.
 *         @type bool $sections           Whether to return the theme readme sections: description, installation,
 *                                        FAQ, screenshots, other notes, and changelog. Default false.
 *         @type bool $rating             Whether to return the rating in percent and total number of ratings.
 *                                        Default false.
 *         @type bool $ratings            Whether to return the number of rating for each star (1-5). Default false.
 *         @type bool $downloaded         Whether to return the download count. Default false.
 *         @type bool $downloadlink       Whether to return the download link for the package. Default false.
 *         @type bool $last_updated       Whether to return the date of the last update. Default false.
 *         @type bool $tags               Whether to return the assigned tags. Default false.
 *         @type bool $homepage           Whether to return the theme homepage link. Default false.
 *         @type bool $screenshots        Whether to return the screenshots. Default false.
 *         @type int  $screenshot_count   Number of screenshots to return. Default 1.
 *         @type bool $screenshot_url     Whether to return the URL of the first screenshot. Default false.
 *         @type bool $photon_screenshots Whether to return the screenshots via Photon. Default false.
 *         @type bool $template           Whether to return the slug of the parent theme. Default false.
 *         @type bool $parent             Whether to return the slug, name and homepage of the parent theme. Default false.
 *         @type bool $versions           Whether to return the list of all available versions. Default false.
 *         @type bool $theme_url          Whether to return theme's URL. Default false.
 *         @type bool $extended_author    Whether to return nicename or nicename and display name. Default false.
 *     }
 * }
 * @return object|array|WP_Error Response object or array on success, WP_Error on failure. See the
 *         {@link https://developer.wordpress.org/reference/functions/themes_api/ function reference article}
 *         for more information on the make-up of possible return objects depending on the value of `$action`.
 */
function themes_api( $action, $args = array() ) {
	// Include an unmodified $wp_version.
	require ABSPATH . WPINC . '/version.php';

	if ( is_array( $args ) ) {
		$args = (object) $args;
	}

	if ( 'query_themes' === $action ) {
		if ( ! isset( $args->per_page ) ) {
			$args->per_page = 24;
		}
	}

	if ( ! isset( $args->locale ) ) {
		$args->locale = get_user_locale();
	}

	if ( ! isset( $args->wp_version ) ) {
		$args->wp_version = substr( $wp_version, 0, 3 ); // x.y
	}

	/**
	 * Filters arguments used to query for installer pages from the WordPress.org Themes API.
	 *
	 * Important: An object MUST be returned to this filter.
	 *
	 * @since 2.8.0
	 *
	 * @param object $args   Arguments used to query for installer pages from the WordPress.org Themes API.
	 * @param string $action Requested action. Likely values are 'theme_information',
	 *                       'feature_list', or 'query_themes'.
	 */
	$args = apply_filters( 'themes_api_args', $args, $action );

	/**
	 * Filters whether to override the WordPress.org Themes API.
	 *
	 * Passing a non-false value will effectively short-circuit the WordPress.org API request.
	 *
	 * If `$action` is 'query_themes', 'theme_information', or 'feature_list', an object MUST
	 * be passed. If `$action` is 'hot_tags', an array should be passed.
	 *
	 * @since 2.8.0
	 *
	 * @param false|object|array $override Whether to override the WordPress.org Themes API. Default false.
	 * @param string             $action   Requested action. Likely values are 'theme_information',
	 *                                    'feature_list', or 'query_themes'.
	 * @param object             $args     Arguments used to query for installer pages from the Themes API.
	 */
	$res = apply_filters( 'themes_api', false, $action, $args );

	if ( ! $res ) {
		$url = 'http://api.wordpress.org/themes/info/1.2/';
		$url = add_query_arg(
			array(
				'action'  => $action,
				'request' => $args,
			),
			$url
		);

		$http_url = $url;
		$ssl      = wp_http_supports( array( 'ssl' ) );
		if ( $ssl ) {
			$url = set_url_scheme( $url, 'https' );
		}

		$http_args = array(
			'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
		);
		$request   = wp_remote_get( $url, $http_args );

		if ( $ssl && is_wp_error( $request ) ) {
			if ( ! wp_doing_ajax() ) {
				trigger_error(
					sprintf(
						/* translators: %s: Support forums URL. */
						__( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.' ),
						__( 'https://wordpress.org/support/forums/' )
					) . ' ' . __( '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)' ),
					headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE
				);
			}
			$request = wp_remote_get( $http_url, $http_args );
		}

		if ( is_wp_error( $request ) ) {
			$res = new WP_Error(
				'themes_api_failed',
				sprintf(
					/* translators: %s: Support forums URL. */
					__( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.' ),
					__( 'https://wordpress.org/support/forums/' )
				),
				$request->get_error_message()
			);
		} else {
			$res = json_decode( wp_remote_retrieve_body( $request ), true );
			if ( is_array( $res ) ) {
				// Object casting is required in order to match the info/1.0 format.
				$res = (object) $res;
			} elseif ( null === $res ) {
				$res = new WP_Error(
					'themes_api_failed',
					sprintf(
						/* translators: %s: Support forums URL. */
						__( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="%s">support forums</a>.' ),
						__( 'https://wordpress.org/support/forums/' )
					),
					wp_remote_retrieve_body( $request )
				);
			}

			if ( isset( $res->error ) ) {
				$res = new WP_Error( 'themes_api_failed', $res->error );
			}
		}

		// Back-compat for info/1.2 API, upgrade the theme objects in query_themes to objects.
		if ( 'query_themes' === $action ) {
			foreach ( $res->themes as $i => $theme ) {
				$res->themes[ $i ] = (object) $theme;
			}
		}
		// Back-compat for info/1.2 API, downgrade the feature_list result back to an array.
		if ( 'feature_list' === $action ) {
			$res = (array) $res;
		}
	}

	/**
	 * Filters the returned WordPress.org Themes API response.
	 *
	 * @since 2.8.0
	 *
	 * @param array|object|WP_Error $res    WordPress.org Themes API response.
	 * @param string                $action Requested action. Likely values are 'theme_information',
	 *                                      'feature_list', or 'query_themes'.
	 * @param object                $args   Arguments used to query for installer pages from the WordPress.org Themes API.
	 */
	return apply_filters( 'themes_api_result', $res, $action, $args );
}

/**
 * Prepare themes for JavaScript.
 *
 * @since 3.8.0
 *
 * @param WP_Theme[] $themes Optional. Array of theme objects to prepare.
 *                           Defaults to all allowed themes.
 *
 * @return array An associative array of theme data, sorted by name.
 */
function wp_prepare_themes_for_js( $themes = null ) {
	$current_theme = get_stylesheet();

	/**
	 * Filters theme data before it is prepared for JavaScript.
	 *
	 * Passing a non-empty array will result in wp_prepare_themes_for_js() returning
	 * early with that value instead.
	 *
	 * @since 4.2.0
	 *
	 * @param array           $prepared_themes An associative array of theme data. Default empty array.
	 * @param WP_Theme[]|null $themes          An array of theme objects to prepare, if any.
	 * @param string          $current_theme   The current theme slug.
	 */
	$prepared_themes = (array) apply_filters( 'pre_prepare_themes_for_js', array(), $themes, $current_theme );

	if ( ! empty( $prepared_themes ) ) {
		return $prepared_themes;
	}

	// Make sure the current theme is listed first.
	$prepared_themes[ $current_theme ] = array();

	if ( null === $themes ) {
		$themes = wp_get_themes( array( 'allowed' => true ) );
		if ( ! isset( $themes[ $current_theme ] ) ) {
			$themes[ $current_theme ] = wp_get_theme();
		}
	}

	$updates    = array();
	$no_updates = array();
	if ( ! is_multisite() && current_user_can( 'update_themes' ) ) {
		$updates_transient = get_site_transient( 'update_themes' );
		if ( isset( $updates_transient->response ) ) {
			$updates = $updates_transient->response;
		}
		if ( isset( $updates_transient->no_update ) ) {
			$no_updates = $updates_transient->no_update;
		}
	}

	WP_Theme::sort_by_name( $themes );

	$parents = array();

	$auto_updates = (array) get_site_option( 'auto_update_themes', array() );

	foreach ( $themes as $theme ) {
		$slug         = $theme->get_stylesheet();
		$encoded_slug = urlencode( $slug );

		$parent = false;
		if ( $theme->parent() ) {
			$parent           = $theme->parent();
			$parents[ $slug ] = $parent->get_stylesheet();
			$parent           = $parent->display( 'Name' );
		}

		$customize_action = null;
		if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
			$customize_action = esc_url(
				add_query_arg(
					array(
						'return' => urlencode( esc_url_raw( remove_query_arg( wp_removable_query_args(), wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ),
					),
					wp_customize_url( $slug )
				)
			);
		}

		$update_requires_wp  = isset( $updates[ $slug ]['requires'] ) ? $updates[ $slug ]['requires'] : null;
		$update_requires_php = isset( $updates[ $slug ]['requires_php'] ) ? $updates[ $slug ]['requires_php'] : null;

		$auto_update        = in_array( $slug, $auto_updates, true );
		$auto_update_action = $auto_update ? 'disable-auto-update' : 'enable-auto-update';

		if ( isset( $updates[ $slug ] ) ) {
			$auto_update_supported      = true;
			$auto_update_filter_payload = (object) $updates[ $slug ];
		} elseif ( isset( $no_updates[ $slug ] ) ) {
			$auto_update_supported      = true;
			$auto_update_filter_payload = (object) $no_updates[ $slug ];
		} else {
			$auto_update_supported = false;
			/*
			 * Create the expected payload for the auto_update_theme filter, this is the same data
			 * as contained within $updates or $no_updates but used when the Theme is not known.
			 */
			$auto_update_filter_payload = (object) array(
				'theme'        => $slug,
				'new_version'  => $theme->get( 'Version' ),
				'url'          => '',
				'package'      => '',
				'requires'     => $theme->get( 'RequiresWP' ),
				'requires_php' => $theme->get( 'RequiresPHP' ),
			);
		}

		$auto_update_forced = wp_is_auto_update_forced_for_item( 'theme', null, $auto_update_filter_payload );

		$prepared_themes[ $slug ] = array(
			'id'             => $slug,
			'name'           => $theme->display( 'Name' ),
			'screenshot'     => array( $theme->get_screenshot() ), // @todo Multiple screenshots.
			'description'    => $theme->display( 'Description' ),
			'author'         => $theme->display( 'Author', false, true ),
			'authorAndUri'   => $theme->display( 'Author' ),
			'tags'           => $theme->display( 'Tags' ),
			'version'        => $theme->get( 'Version' ),
			'compatibleWP'   => is_wp_version_compatible( $theme->get( 'RequiresWP' ) ),
			'compatiblePHP'  => is_php_version_compatible( $theme->get( 'RequiresPHP' ) ),
			'updateResponse' => array(
				'compatibleWP'  => is_wp_version_compatible( $update_requires_wp ),
				'compatiblePHP' => is_php_version_compatible( $update_requires_php ),
			),
			'parent'         => $parent,
			'active'         => $slug === $current_theme,
			'hasUpdate'      => isset( $updates[ $slug ] ),
			'hasPackage'     => isset( $updates[ $slug ] ) && ! empty( $updates[ $slug ]['package'] ),
			'update'         => get_theme_update_available( $theme ),
			'autoupdate'     => array(
				'enabled'   => $auto_update || $auto_update_forced,
				'supported' => $auto_update_supported,
				'forced'    => $auto_update_forced,
			),
			'actions'        => array(
				'activate'   => current_user_can( 'switch_themes' ) ? wp_nonce_url( admin_url( 'themes.php?action=activate&amp;stylesheet=' . $encoded_slug ), 'switch-theme_' . $slug ) : null,
				'customize'  => $customize_action,
				'delete'     => ( ! is_multisite() && current_user_can( 'delete_themes' ) ) ? wp_nonce_url( admin_url( 'themes.php?action=delete&amp;stylesheet=' . $encoded_slug ), 'delete-theme_' . $slug ) : null,
				'autoupdate' => wp_is_auto_update_enabled_for_type( 'theme' ) && ! is_multisite() && current_user_can( 'update_themes' )
					? wp_nonce_url( admin_url( 'themes.php?action=' . $auto_update_action . '&amp;stylesheet=' . $encoded_slug ), 'updates' )
					: null,
			),
		);
	}

	// Remove 'delete' action if theme has an active child.
	if ( ! empty( $parents ) && array_key_exists( $current_theme, $parents ) ) {
		unset( $prepared_themes[ $parents[ $current_theme ] ]['actions']['delete'] );
	}

	/**
	 * Filters the themes prepared for JavaScript, for themes.php.
	 *
	 * Could be useful for changing the order, which is by name by default.
	 *
	 * @since 3.8.0
	 *
	 * @param array $prepared_themes Array of theme data.
	 */
	$prepared_themes = apply_filters( 'wp_prepare_themes_for_js', $prepared_themes );
	$prepared_themes = array_values( $prepared_themes );
	return array_filter( $prepared_themes );
}

/**
 * Print JS templates for the theme-browsing UI in the Customizer.
 *
 * @since 4.2.0
 */
function customize_themes_print_templates() {
	?>
	<script type="text/html" id="tmpl-customize-themes-details-view">
		<div class="theme-backdrop"></div>
		<div class="theme-wrap wp-clearfix" role="document">
			<div class="theme-header">
				<button type="button" class="left dashicons dashicons-no"><span class="screen-reader-text"><?php _e( 'Show previous theme' ); ?></span></button>
				<button type="button" class="right dashicons dashicons-no"><span class="screen-reader-text"><?php _e( 'Show next theme' ); ?></span></button>
				<button type="button" class="close dashicons dashicons-no"><span class="screen-reader-text"><?php _e( 'Close details dialog' ); ?></span></button>
			</div>
			<div class="theme-about wp-clearfix">
				<div class="theme-screenshots">
				<# if ( data.screenshot && data.screenshot[0] ) { #>
					<div class="screenshot"><img src="{{ data.screenshot[0] }}" alt="" /></div>
				<# } else { #>
					<div class="screenshot blank"></div>
				<# } #>
				</div>

				<div class="theme-info">
					<# if ( data.active ) { #>
						<span class="current-label"><?php _e( 'Current Theme' ); ?></span>
					<# } #>
					<h2 class="theme-name">{{{ data.name }}}<span class="theme-version">
						<?php
						/* translators: %s: Theme version. */
						printf( __( 'Version: %s' ), '{{ data.version }}' );
						?>
					</span></h2>
					<h3 class="theme-author">
						<?php
						/* translators: %s: Theme author link. */
						printf( __( 'By %s' ), '{{{ data.authorAndUri }}}' );
						?>
					</h3>

					<# if ( data.stars && 0 != data.num_ratings ) { #>
						<div class="theme-rating">
							{{{ data.stars }}}
							<a class="num-ratings" target="_blank" href="{{ data.reviews_url }}">
								<?php
								printf(
									'%1$s <span class="screen-reader-text">%2$s</span>',
									/* translators: %s: Number of ratings. */
									sprintf( __( '(%s ratings)' ), '{{ data.num_ratings }}' ),
									/* translators: Accessibility text. */
									__( '(opens in a new tab)' )
								);
								?>
							</a>
						</div>
					<# } #>

					<# if ( data.hasUpdate ) { #>
						<# if ( data.updateResponse.compatibleWP && data.updateResponse.compatiblePHP ) { #>
							<div class="notice notice-warning notice-alt notice-large" data-slug="{{ data.id }}">
								<h3 class="notice-title"><?php _e( 'Update Available' ); ?></h3>
								{{{ data.update }}}
							</div>
						<# } else { #>
							<div class="notice notice-error notice-alt notice-large" data-slug="{{ data.id }}">
								<h3 class="notice-title"><?php _e( 'Update Incompatible' ); ?></h3>
								<p>
									<# if ( ! data.updateResponse.compatibleWP && ! data.updateResponse.compatiblePHP ) { #>
										<?php
										printf(
											/* translators: %s: Theme name. */
											__( 'There is a new version of %s available, but it doesn&#8217;t work with your versions of WordPress and PHP.' ),
											'{{{ data.name }}}'
										);
										if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
											printf(
												/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
												' ' . __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ),
												self_admin_url( 'update-core.php' ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										} elseif ( current_user_can( 'update_core' ) ) {
											printf(
												/* translators: %s: URL to WordPress Updates screen. */
												' ' . __( '<a href="%s">Please update WordPress</a>.' ),
												self_admin_url( 'update-core.php' )
											);
										} elseif ( current_user_can( 'update_php' ) ) {
											printf(
												/* translators: %s: URL to Update PHP page. */
												' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										}
										?>
									<# } else if ( ! data.updateResponse.compatibleWP ) { #>
										<?php
										printf(
											/* translators: %s: Theme name. */
											__( 'There is a new version of %s available, but it doesn&#8217;t work with your version of WordPress.' ),
											'{{{ data.name }}}'
										);
										if ( current_user_can( 'update_core' ) ) {
											printf(
												/* translators: %s: URL to WordPress Updates screen. */
												' ' . __( '<a href="%s">Please update WordPress</a>.' ),
												self_admin_url( 'update-core.php' )
											);
										}
										?>
									<# } else if ( ! data.updateResponse.compatiblePHP ) { #>
										<?php
										printf(
											/* translators: %s: Theme name. */
											__( 'There is a new version of %s available, but it doesn&#8217;t work with your version of PHP.' ),
											'{{{ data.name }}}'
										);
										if ( current_user_can( 'update_php' ) ) {
											printf(
												/* translators: %s: URL to Update PHP page. */
												' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
												esc_url( wp_get_update_php_url() )
											);
											wp_update_php_annotation( '</p><p><em>', '</em>' );
										}
										?>
									<# } #>
								</p>
							</div>
						<# } #>
					<# } #>

					<# if ( data.parent ) { #>
						<p class="parent-theme">
							<?php
							printf(
								/* translators: %s: Theme name. */
								__( 'This is a child theme of %s.' ),
								'<strong>{{{ data.parent }}}</strong>'
							);
							?>
						</p>
					<# } #>

					<# if ( ! data.compatibleWP || ! data.compatiblePHP ) { #>
						<div class="notice notice-error notice-alt notice-large"><p>
							<# if ( ! data.compatibleWP && ! data.compatiblePHP ) { #>
								<?php
								_e( 'This theme doesn&#8217;t work with your versions of WordPress and PHP.' );
								if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
									printf(
										/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
										' ' . __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ),
										self_admin_url( 'update-core.php' ),
										esc_url( wp_get_update_php_url() )
									);
									wp_update_php_annotation( '</p><p><em>', '</em>' );
								} elseif ( current_user_can( 'update_core' ) ) {
									printf(
										/* translators: %s: URL to WordPress Updates screen. */
										' ' . __( '<a href="%s">Please update WordPress</a>.' ),
										self_admin_url( 'update-core.php' )
									);
								} elseif ( current_user_can( 'update_php' ) ) {
									printf(
										/* translators: %s: URL to Update PHP page. */
										' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
										esc_url( wp_get_update_php_url() )
									);
									wp_update_php_annotation( '</p><p><em>', '</em>' );
								}
								?>
							<# } else if ( ! data.compatibleWP ) { #>
								<?php
								_e( 'This theme doesn&#8217;t work with your version of WordPress.' );
								if ( current_user_can( 'update_core' ) ) {
									printf(
										/* translators: %s: URL to WordPress Updates screen. */
										' ' . __( '<a href="%s">Please update WordPress</a>.' ),
										self_admin_url( 'update-core.php' )
									);
								}
								?>
							<# } else if ( ! data.compatiblePHP ) { #>
								<?php
								_e( 'This theme doesn&#8217;t work with your version of PHP.' );
								if ( current_user_can( 'update_php' ) ) {
									printf(
										/* translators: %s: URL to Update PHP page. */
										' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
										esc_url( wp_get_update_php_url() )
									);
									wp_update_php_annotation( '</p><p><em>', '</em>' );
								}
								?>
							<# } #>
						</p></div>
					<# } #>

					<p class="theme-description">{{{ data.description }}}</p>

					<# if ( data.tags ) { #>
						<p class="theme-tags"><span><?php _e( 'Tags:' ); ?></span> {{{ data.tags }}}</p>
					<# } #>
				</div>
			</div>

			<div class="theme-actions">
				<# if ( data.active ) { #>
					<button type="button" class="button button-primary customize-theme"><?php _e( 'Customize' ); ?></button>
				<# } else if ( 'installed' === data.type ) { #>
					<?php if ( current_user_can( 'delete_themes' ) ) { ?>
						<# if ( data.actions && data.actions['delete'] ) { #>
							<a href="{{{ data.actions['delete'] }}}" data-slug="{{ data.id }}" class="button button-secondary delete-theme"><?php _e( 'Delete' ); ?></a>
						<# } #>
					<?php } ?>

					<# if ( data.compatibleWP && data.compatiblePHP ) { #>
						<button type="button" class="button button-primary preview-theme" data-slug="{{ data.id }}"><?php _e( 'Live Preview' ); ?></button>
					<# } else { #>
						<button class="button button-primary disabled"><?php _e( 'Live Preview' ); ?></button>
					<# } #>
				<# } else { #>
					<# if ( data.compatibleWP && data.compatiblePHP ) { #>
						<button type="button" class="button theme-install" data-slug="{{ data.id }}"><?php _e( 'Install' ); ?></button>
						<button type="button" class="button button-primary theme-install preview" data-slug="{{ data.id }}"><?php _e( 'Install &amp; Preview' ); ?></button>
					<# } else { #>
						<button type="button" class="button disabled"><?php _ex( 'Cannot Install', 'theme' ); ?></button>
						<button type="button" class="button button-primary disabled"><?php _e( 'Install &amp; Preview' ); ?></button>
					<# } #>
				<# } #>
			</div>
		</div>
	</script>
	<?php
}

/**
 * Determines whether a theme is technically active but was paused while
 * loading.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 5.2.0
 *
 * @param string $theme Path to the theme directory relative to the themes directory.
 * @return bool True, if in the list of paused themes. False, not in the list.
 */
function is_theme_paused( $theme ) {
	if ( ! isset( $GLOBALS['_paused_themes'] ) ) {
		return false;
	}

	if ( get_stylesheet() !== $theme && get_template() !== $theme ) {
		return false;
	}

	return array_key_exists( $theme, $GLOBALS['_paused_themes'] );
}

/**
 * Gets the error that was recorded for a paused theme.
 *
 * @since 5.2.0
 *
 * @param string $theme Path to the theme directory relative to the themes
 *                      directory.
 * @return array|false Array of error information as it was returned by
 *                     `error_get_last()`, or false if none was recorded.
 */
function wp_get_theme_error( $theme ) {
	if ( ! isset( $GLOBALS['_paused_themes'] ) ) {
		return false;
	}

	if ( ! array_key_exists( $theme, $GLOBALS['_paused_themes'] ) ) {
		return false;
	}

	return $GLOBALS['_paused_themes'][ $theme ];
}

/**
 * Tries to resume a single theme.
 *
 * If a redirect was provided and a functions.php file was found, we first ensure that
 * functions.php file does not throw fatal errors anymore.
 *
 * The way it works is by setting the redirection to the error before trying to
 * include the file. If the theme fails, then the redirection will not be overwritten
 * with the success message and the theme will not be resumed.
 *
 * @since 5.2.0
 *
 * @param string $theme    Single theme to resume.
 * @param string $redirect Optional. URL to redirect to. Default empty string.
 * @return bool|WP_Error True on success, false if `$theme` was not paused,
 *                       `WP_Error` on failure.
 */
function resume_theme( $theme, $redirect = '' ) {
	list( $extension ) = explode( '/', $theme );

	/*
	 * We'll override this later if the theme could be resumed without
	 * creating a fatal error.
	 */
	if ( ! empty( $redirect ) ) {
		$functions_path = '';
		if ( strpos( STYLESHEETPATH, $extension ) ) {
			$functions_path = STYLESHEETPATH . '/functions.php';
		} elseif ( strpos( TEMPLATEPATH, $extension ) ) {
			$functions_path = TEMPLATEPATH . '/functions.php';
		}

		if ( ! empty( $functions_path ) ) {
			wp_redirect(
				add_query_arg(
					'_error_nonce',
					wp_create_nonce( 'theme-resume-error_' . $theme ),
					$redirect
				)
			);

			// Load the theme's functions.php to test whether it throws a fatal error.
			ob_start();
			if ( ! defined( 'WP_SANDBOX_SCRAPING' ) ) {
				define( 'WP_SANDBOX_SCRAPING', true );
			}
			include $functions_path;
			ob_clean();
		}
	}

	$result = wp_paused_themes()->delete( $extension );

	if ( ! $result ) {
		return new WP_Error(
			'could_not_resume_theme',
			__( 'Could not resume the theme.' )
		);
	}

	return true;
}

/**
 * Renders an admin notice in case some themes have been paused due to errors.
 *
 * @since 5.2.0
 *
 * @global string $pagenow
 */
function paused_themes_notice() {
	if ( 'themes.php' === $GLOBALS['pagenow'] ) {
		return;
	}

	if ( ! current_user_can( 'resume_themes' ) ) {
		return;
	}

	if ( ! isset( $GLOBALS['_paused_themes'] ) || empty( $GLOBALS['_paused_themes'] ) ) {
		return;
	}

	printf(
		'<div class="notice notice-error"><p><strong>%s</strong><br>%s</p><p><a href="%s">%s</a></p></div>',
		__( 'One or more themes failed to load properly.' ),
		__( 'You can find more details and make changes on the Themes screen.' ),
		esc_url( admin_url( 'themes.php' ) ),
		__( 'Go to the Themes screen' )
	);
}

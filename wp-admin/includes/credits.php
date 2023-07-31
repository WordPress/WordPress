<?php
/**
 * WordPress Credits Administration API.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.4.0
 */

/**
 * Retrieves the contributor credits.
 *
 * @since 3.2.0
 * @since 5.6.0 Added the `$version` and `$locale` parameters.
 *
 * @param string $version WordPress version. Defaults to the current version.
 * @param string $locale  WordPress locale. Defaults to the current user's locale.
 * @return array|false A list of all of the contributors, or false on error.
 */
function wp_credits( $version = '', $locale = '' ) {
	if ( ! $version ) {
		// Include an unmodified $wp_version.
		require ABSPATH . WPINC . '/version.php';

		$version = $wp_version;
	}

	if ( ! $locale ) {
		$locale = get_user_locale();
	}

	$results = get_site_transient( 'wordpress_credits_' . $locale );

	if ( ! is_array( $results )
		|| str_contains( $version, '-' )
		|| ( isset( $results['data']['version'] ) && ! str_starts_with( $version, $results['data']['version'] ) )
	) {
		$url     = "http://api.wordpress.org/core/credits/1.1/?version={$version}&locale={$locale}";
		$options = array( 'user-agent' => 'WordPress/' . $version . '; ' . home_url( '/' ) );

		if ( wp_http_supports( array( 'ssl' ) ) ) {
			$url = set_url_scheme( $url, 'https' );
		}

		$response = wp_remote_get( $url, $options );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$results = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $results ) ) {
			return false;
		}

		set_site_transient( 'wordpress_credits_' . $locale, $results, DAY_IN_SECONDS );
	}

	return $results;
}

/**
 * Retrieves the link to a contributor's WordPress.org profile page.
 *
 * @access private
 * @since 3.2.0
 *
 * @param string $display_name  The contributor's display name (passed by reference).
 * @param string $username      The contributor's username.
 * @param string $profiles      URL to the contributor's WordPress.org profile page.
 */
function _wp_credits_add_profile_link( &$display_name, $username, $profiles ) {
	$display_name = '<a href="' . esc_url( sprintf( $profiles, $username ) ) . '">' . esc_html( $display_name ) . '</a>';
}

/**
 * Retrieves the link to an external library used in WordPress.
 *
 * @access private
 * @since 3.2.0
 *
 * @param string $data External library data (passed by reference).
 */
function _wp_credits_build_object_link( &$data ) {
	$data = '<a href="' . esc_url( $data[1] ) . '">' . esc_html( $data[0] ) . '</a>';
}

/**
 * Displays the title for a given group of contributors.
 *
 * @since 5.3.0
 *
 * @param array $group_data The current contributor group.
 */
function wp_credits_section_title( $group_data = array() ) {
	if ( ! count( $group_data ) ) {
		return;
	}

	if ( $group_data['name'] ) {
		if ( 'Translators' === $group_data['name'] ) {
			// Considered a special slug in the API response. (Also, will never be returned for en_US.)
			$title = _x( 'Translators', 'Translate this to be the equivalent of English Translators in your language for the credits page Translators section' );
		} elseif ( isset( $group_data['placeholders'] ) ) {
			// phpcs:ignore WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText
			$title = vsprintf( translate( $group_data['name'] ), $group_data['placeholders'] );
		} else {
			// phpcs:ignore WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText
			$title = translate( $group_data['name'] );
		}

		echo '<h2 class="wp-people-group-title">' . esc_html( $title ) . "</h2>\n";
	}
}

/**
 * Displays a list of contributors for a given group.
 *
 * @since 5.3.0
 *
 * @param array  $credits The credits groups returned from the API.
 * @param string $slug    The current group to display.
 */
function wp_credits_section_list( $credits = array(), $slug = '' ) {
	$group_data   = isset( $credits['groups'][ $slug ] ) ? $credits['groups'][ $slug ] : array();
	$credits_data = $credits['data'];
	if ( ! count( $group_data ) ) {
		return;
	}

	if ( ! empty( $group_data['shuffle'] ) ) {
		shuffle( $group_data['data'] ); // We were going to sort by ability to pronounce "hierarchical," but that wouldn't be fair to Matt.
	}

	switch ( $group_data['type'] ) {
		case 'list':
			array_walk( $group_data['data'], '_wp_credits_add_profile_link', $credits_data['profiles'] );
			echo '<p class="wp-credits-list">' . wp_sprintf( '%l.', $group_data['data'] ) . "</p>\n\n";
			break;
		case 'libraries':
			array_walk( $group_data['data'], '_wp_credits_build_object_link' );
			echo '<p class="wp-credits-list">' . wp_sprintf( '%l.', $group_data['data'] ) . "</p>\n\n";
			break;
		default:
			$compact = 'compact' === $group_data['type'];
			$classes = 'wp-people-group ' . ( $compact ? 'compact' : '' );
			echo '<ul class="' . $classes . '" id="wp-people-group-' . $slug . '">' . "\n";
			foreach ( $group_data['data'] as $person_data ) {
				echo '<li class="wp-person" id="wp-person-' . esc_attr( $person_data[2] ) . '">' . "\n\t";
				echo '<a href="' . esc_url( sprintf( $credits_data['profiles'], $person_data[2] ) ) . '" class="web">';
				$size   = $compact ? 80 : 160;
				$data   = get_avatar_data( $person_data[1] . '@md5.gravatar.com', array( 'size' => $size ) );
				$data2x = get_avatar_data( $person_data[1] . '@md5.gravatar.com', array( 'size' => $size * 2 ) );
				echo '<span class="wp-person-avatar"><img src="' . esc_url( $data['url'] ) . '" srcset="' . esc_url( $data2x['url'] ) . ' 2x" class="gravatar" alt="" /></span>' . "\n";
				echo esc_html( $person_data[0] ) . "</a>\n\t";
				if ( ! $compact && ! empty( $person_data[3] ) ) {
					// phpcs:ignore WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText
					echo '<span class="title">' . translate( $person_data[3] ) . "</span>\n";
				}
				echo "</li>\n";
			}
			echo "</ul>\n";
			break;
	}
}

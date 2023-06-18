<?php
/**
 * Generic helpers used in the plugin.
 *
 * @package WPCode
 */

/**
 * Get a URL with UTM parameters.
 *
 * @param string $url The URL to add the params to.
 * @param string $medium The marketing medium.
 * @param string $campaign The campaign.
 * @param string $ad_content The utm_content param.
 *
 * @return string
 */
function wpcode_utm_url( $url, $medium = '', $campaign = '', $ad_content = '' ) {
	$args = array(
		'utm_source'   => class_exists( 'WPCode_License' ) ? 'proplugin' : 'liteplugin',
		'utm_medium'   => sanitize_key( $medium ),
		'utm_campaign' => sanitize_key( $campaign )
	);

	if ( ! empty( $ad_content ) ) {
		$args['utm_content'] = sanitize_key( $ad_content );
	}

	return add_query_arg(
		$args,
		$url
	);
}

/**
 * Get a standard auto-insert location select menu.
 *
 * @param string $selected_location The location currently selected.
 * @param string $code_type The code type for the current snippet (disables some locations).
 * @param string $name The name for the select (used in the form).
 *
 * @return string
 */
function wpcode_get_auto_insert_location_picker( $selected_location, $code_type = 'html', $name = 'wpcode_auto_insert_location' ) {
	$available_types = wpcode()->auto_insert->get_types();
	ob_start();
	?>
	<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" class="wpcode-select2 wpcode-auto-insert-location-picker">
		<?php
		foreach ( $available_types as $type ) {
			$options = $type->get_locations();
			if ( empty( $options ) ) {
				continue;
			}
			$extra_data = '';
			if ( ! empty( $type->upgrade_title ) ) {
				$extra_data = ' data-upgrade-title="' . esc_attr( $type->upgrade_title ) . '"';
			}
			if ( ! empty( $type->upgrade_text ) ) {
				$extra_data .= ' data-upgrade-text="' . esc_attr( $type->upgrade_text ) . '"';
			}
			if ( ! empty( $type->upgrade_link ) ) {
				$extra_data .= ' data-upgrade-link="' . esc_attr( $type->upgrade_link ) . '"';
			}
			if ( ! empty( $type->upgrade_button ) ) {
				$extra_data .= ' data-upgrade-button="' . esc_attr( $type->upgrade_button ) . '"';
			}
			?>
			<optgroup
					label="<?php echo esc_attr( $type->get_label() ); ?>"
					data-code-type="<?php echo esc_attr( $type->code_type ); ?>"
					data-label-pill="<?php echo esc_attr( $type->label_pill ); ?>"
				<?php echo $extra_data; ?>
			>
				<?php
				foreach ( $options as $key => $location ) {
					$disabled = false;
					if ( 'all' !== $type->code_type && $type->code_type !== $code_type ) {
						$disabled = true;
					}
					$label = wpcode_find_location_label( $key );
					?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected_location, $key ); ?> <?php disabled( $disabled ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php } ?>
			</optgroup>
			<?php
		}
		?>
	</select>
	<?php
	return ob_get_clean();
}

/**
 * Get the label for an auto-insert location.
 *
 * @param $location_slug
 *
 * @return string
 */
function wpcode_find_location_label( $location_slug ) {
	$available_types = wpcode()->auto_insert->get_types();
	$location_label  = '';
	foreach ( $available_types as $type ) {
		$options = $type->get_locations();
		foreach ( $options as $key => $location ) {
			if ( $key === $location_slug ) {
				if ( isset( $location['label'] ) ) {
					$label = $location['label'];
				} else {
					$label = $location;
				}
				$location_label = $label;
				break 2;
			}
		}
	}

	return $location_label;
}

/**
 * Get a checkbox wrapped with markup to be displayed as a toggle.
 *
 * @param bool       $checked Is it checked or not.
 * @param string     $name The name for the input.
 * @param string     $description Field description (optional).
 * @param string|int $value Field value (optional).
 * @param string     $label Field label (optional).
 *
 * @return string
 */
function wpcode_get_checkbox_toggle( $checked, $name, $description = '', $value = '', $label = '' ) {
	$markup = '<label class="wpcode-checkbox-toggle">';

	$markup .= '<input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" />';
	$markup .= '<span class="wpcode-checkbox-toggle-slider"></span>';
	$markup .= '</label>';
	if ( ! empty( $label ) ) {
		$markup .= '<label class="wpcode-checkbox-toggle-label" for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label>';
	}

	if ( ! empty( $description ) ) {
		$markup .= '<p class="description">' . wp_kses_post( $description ) . '</p>';
	}

	return $markup;
}

/**
 * Button that copies target input value to the clipboard.
 *
 * @param string $target The id of the input to copy from.
 * @param string $prefix If you want something prepended to the copied value.
 * @param string $suffix If you want something appended to the copied value.
 *
 * @return string
 */
function wpcode_get_copy_target_button( $target, $prefix = '', $suffix = '' ) {
	return sprintf(
		'<button class="wpcode-button wpcode-button-icon wpcode-button-secondary wpcode-copy-target" data-target="#%4$s" type="button" data-prefix="%5$s" data-suffix="%6$s"><span class="wpcode-default-icon">%1$s</span><span class="wpcode-success-icon">%2$s</span> %3$s</button>',
		get_wpcode_icon( 'copy', 16, 16 ),
		get_wpcode_icon( 'check', 16, 13 ),
		_x( 'Copy', 'Copy to clipboard', 'insert-headers-and-footers' ),
		esc_attr( $target ),
		esc_attr( $prefix ),
		esc_attr( $suffix )
	);
}

/**
 * Get a list of labels for the conditions used in conditional logic.
 *
 * @return array
 */
function wpcode_get_conditions_relation_labels() {
	return array(
		'='           => __( 'Is', 'insert-headers-and-footers' ),
		'!='          => __( 'Is not', 'insert-headers-and-footers' ),
		'contains'    => __( 'Contains', 'insert-headers-and-footers' ),
		'notcontains' => __( 'Doesn\'t Contain', 'insert-headers-and-footers' ),
		'before'      => __( 'Is Before', 'insert-headers-and-footers' ),
		'after'       => __( 'Is After', 'insert-headers-and-footers' ),
		'before-or'   => __( 'Is on or Before', 'insert-headers-and-footers' ),
		'after-or'    => __( 'Is on or After', 'insert-headers-and-footers' ),
	);
}

/**
 * Get an array of locations that support an insert number.
 *
 * @return string[]
 */
function wpcode_get_auto_insert_locations_with_number() {
	return array(
		'before_paragraph',
		'after_paragraph',
		'archive_before_post',
		'archive_after_post',
	);
}

/**
 * Returns the site domain.
 *
 * @return string
 */
function wpcode_get_site_domain() {
	return wp_parse_url( home_url(), PHP_URL_HOST );
}

/**
 * Check WP version and include the compatible upgrader skin.
 */
function wpcode_require_upgrader() {

	global $wp_version;

	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	// WP 5.3 changes the upgrader skin.
	if ( version_compare( $wp_version, '5.3', '<' ) ) {
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-skin-legacy.php';
	} else {
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-skin.php';
	}
}

/**
 * Get timezone string if the function doesn't exist. (WP < 5.3).
 *
 * @return string
 * @since 2.0.10
 */
function wpcode_wp_timezone_string() {
	if ( function_exists( 'wp_timezone_string' ) ) {
		return wp_timezone_string();
	}
	$timezone_string = get_option( 'timezone_string' );

	if ( $timezone_string ) {
		return $timezone_string;
	}

	$offset  = (float) get_option( 'gmt_offset' );
	$hours   = (int) $offset;
	$minutes = ( $offset - $hours );

	$sign      = ( $offset < 0 ) ? '-' : '+';
	$abs_hour  = abs( $hours );
	$abs_mins  = abs( $minutes * 60 );
	$tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

	return $tz_offset;
}

/**
 * Add a new library username to be loaded in the WPCode Library.
 *
 * @param string $slug The username to load snippets for from the WPCode Library.
 * @param string $label The label to display for the username in the library.
 * @param string $version The version of the plugin/theme. Used to filter snippets that should not be loaded for newer versions.
 *
 * @return void
 */
function wpcode_register_library_username( $slug, $label = '', $version = '' ) {
	if ( ! is_admin() || ! isset( wpcode()->library ) ) {
		return;
	}
	wpcode()->library->register_library_username( $slug, $label, $version );
}


/**
 * Load snippets from the library by a specific username.
 * This function also loads the library if it hasn't been loaded yet so that it can be used in API endpoints.
 * It also adds links to install snippets and checks for permissions.
 *
 * @param string $username The username to load snippets for from the WPCode Library.
 *
 * @return array
 */
function wpcode_get_library_snippets_by_username( $username ) {

	wpcode_maybe_load_library();

	$snippets = wpcode()->library->get_snippets_by_username( $username );
	// Let's prepare this a bit for easier output.

	// If there are no snippets just return an empty array.
	if ( empty( $snippets['snippets'] ) ) {
		return array();
	}

	$can_install           = current_user_can( 'wpcode_edit_snippets' );
	$prepared              = array();
	$used_library_snippets = wpcode()->library->get_used_library_snippets();
	foreach ( $snippets['snippets'] as $snippet ) {
		$snippet['installed'] = false;
		// If the user can't install snippets, don't provide an install link.
		if ( ! $can_install ) {
			$url = '';
		} elseif ( ! empty( $used_library_snippets[ $snippet['library_id'] ] ) ) {
			// If the snippet is already installed link to the snippet so they can edit it.
			$snippet['installed'] = true;
			$url                  = wpcode()->library->get_edit_snippet_url( $used_library_snippets[ $snippet['library_id'] ] );
		} else {
			// If the snippet is not yet installed, add a link to install it.
			$url = wpcode()->library->get_install_snippet_url( $snippet['library_id'] );
		}
		$snippet['install'] = $url;
		$prepared[]         = $snippet;
	}

	return $prepared;
}

/**
 * Make sure the WPCode library is loaded, along with the components it needs to run.
 *
 * @return void
 */
function wpcode_maybe_load_library() {
	if ( ! isset( wpcode()->library ) ) {
		// Snippet Library.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-library.php';
		// Load components needed for the library, if not loaded.
		if ( ! isset( wpcode()->file_cache ) ) {
			// File cache.
			require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-file-cache.php';
			wpcode()->file_cache = new WPCode_File_Cache();
		}
		if ( ! isset( wpcode()->library_auth ) ) {
			// Authentication for the library site.
			require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-library-auth.php';
			wpcode()->library_auth = new WPCode_Library_Auth();
		}
		wpcode()->library = new WPCode_Library();
	}
}

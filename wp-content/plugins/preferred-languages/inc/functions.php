<?php
/**
 * Plugin functions.
 *
 * @package PreferredLanguages
 */

/**
 * Adds all plugin actions and filters.
 *
 * @codeCoverageIgnore
 *
 * @since 2.0.1
 */
function preferred_languages_boot() {
	add_filter( 'lang_dir_for_domain', 'preferred_languages_filter_lang_dir_for_domain', 10, 3 );

	add_action( 'init', 'preferred_languages_register_setting' );
	add_action( 'init', 'preferred_languages_register_meta' );
	add_action( 'init', 'preferred_languages_register_scripts' );

	add_action( 'admin_init', 'preferred_languages_settings_field' );
	add_action( 'wpmu_options', 'preferred_languages_network_settings_field' );
	add_action( 'update_wpmu_options', 'preferred_languages_update_network_settings' );

	add_action( 'personal_options', 'preferred_languages_personal_options' );
	add_action( 'personal_options_update', 'preferred_languages_update_user_option' );
	add_action( 'edit_user_profile_update', 'preferred_languages_update_user_option' );

	add_filter( 'pre_update_option_preferred_languages', 'preferred_languages_pre_update_option', 10, 2 );
	add_filter( 'pre_update_site_option_preferred_languages', 'preferred_languages_pre_update_option', 10, 2 );
	add_action( 'add_option_preferred_languages', 'preferred_languages_add_option', 10, 2 );
	add_action( 'update_option_preferred_languages', 'preferred_languages_update_option', 10, 2 );
	add_action( 'add_site_option_preferred_languages', 'preferred_languages_update_site_option', 10, 2 );
	add_action( 'update_site_option_preferred_languages', 'preferred_languages_update_site_option', 10, 2 );
	add_filter( 'pre_option_WPLANG', 'preferred_languages_filter_option' );
	add_filter( 'pre_site_option_WPLANG', 'preferred_languages_filter_option' );
	add_action( 'add_user_meta', 'preferred_languages_add_user_meta', 10, 3 );
	add_action( 'update_user_meta', 'preferred_languages_update_user_meta', 10, 4 );
	add_filter( 'get_user_metadata', 'preferred_languages_filter_user_locale', 10, 3 );
	add_filter( 'locale', 'preferred_languages_filter_locale', 5 ); // Before WP_Locale_Switcher.
	add_filter( 'override_load_textdomain', 'preferred_languages_override_load_textdomain', 10, 4 );
	add_filter( 'load_textdomain_mofile', 'preferred_languages_load_textdomain_mofile', 10 );
	add_filter( 'pre_load_script_translations', 'preferred_languages_pre_load_script_translations', 10, 4 );
	add_filter( 'load_script_translation_file', 'preferred_languages_load_script_translation_file' );

	add_filter( 'debug_information', 'preferred_languages_filter_debug_information' );
}

/**
 * Registers the option for the preferred languages.
 *
 * @since 1.0.0
 */
function preferred_languages_register_setting() {
	register_setting(
		'general',
		'preferred_languages',
		array(
			'sanitize_callback' => 'preferred_languages_sanitize_list',
			'default'           => '',
			'show_in_rest'      => true,
			'type'              => 'string',
			'description'       => __( 'List of preferred locales.', 'preferred-languages' ),
		)
	);
}

/**
 * Registers the user meta key for the preferred languages.
 *
 * @since 1.0.0
 */
function preferred_languages_register_meta() {
	register_meta(
		'user',
		'preferred_languages',
		array(
			'type'              => 'string',
			'description'       => 'List of preferred languages',
			'single'            => true,
			'sanitize_callback' => 'preferred_languages_sanitize_list',
			'show_in_rest'      => true,
		)
	);
}

/**
 * Determines whether switch_to_locale() is in effect.
 *
 * Gracefully handles cases where the function is called too early for
 * locale switching to be ready.
 *
 * @since 2.0.0
 * @global WP_Locale_Switcher $wp_locale_switcher WordPress locale switcher object.
 * @see is_locale_switched
 *
 * @return bool True if the locale has been switched, false otherwise.
 */
function preferred_languages_is_locale_switched() {
	/* @var WP_Locale_Switcher $wp_locale_switcher */
	global $wp_locale_switcher;

	return $wp_locale_switcher && $wp_locale_switcher->is_switched();
}

/**
 * Returns the user ID if we're currently switched to a specific user's locale.
 *
 * @since 2.0.0
 * @global WP_Locale_Switcher $wp_locale_switcher WordPress locale switcher object.
 * @see switch_to_user_locale
 *
 * @return int|false User ID or false on failure.
 */
function preferred_languages_get_locale_switcher_user_id() {
	/* @var WP_Locale_Switcher $wp_locale_switcher */
	global $wp_locale_switcher;

	return $wp_locale_switcher instanceof WP_Locale_Switcher ?
		$wp_locale_switcher->get_switched_user_id() : false;
}

/**
 * Updates the user's set of preferred languages.
 *
 * @since 1.0.0
 *
 * @param int $user_id The user ID.
 */
function preferred_languages_update_user_option( $user_id ) {
	if ( ! isset( $_POST['_wpnonce'], $_POST['preferred_languages'] ) ) {
		return;
	}

	if ( ! is_string( $_POST['_wpnonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	update_user_meta( $user_id, 'preferred_languages', $_POST['preferred_languages'] );
}

/**
 * Returns the list of preferred languages of a user.
 *
 * @since 1.3.0
 *
 * @param int|string|WP_User $user_id User's ID or a WP_User object. Defaults to current user.
 * @return string[]|false Preferred languages or false if user does not exists.
 */
function preferred_languages_get_user_list( $user_id = 0 ) {
	$user = false;

	if ( 0 === $user_id && function_exists( 'wp_get_current_user' ) ) {
		$user = wp_get_current_user();
	} elseif ( $user_id instanceof WP_User ) {
		$user = $user_id;
	} elseif ( $user_id && is_numeric( $user_id ) ) {
		$user = get_user_by( 'id', $user_id );
	}

	if ( ! $user ) {
		return false;
	}

	$preferred_languages = get_user_meta( $user->ID, 'preferred_languages', true );

	if ( ! is_string( $preferred_languages ) ) {
		return false;
	}

	$preferred_languages = array_filter( explode( ',', $preferred_languages ) );

	if ( ! empty( $preferred_languages ) ) {
		return $preferred_languages;
	}

	remove_filter( 'get_user_metadata', 'preferred_languages_filter_user_locale' );
	$locale = get_user_meta( $user->ID, 'locale', true );
	add_filter( 'get_user_metadata', 'preferred_languages_filter_user_locale', 10, 3 );

	if ( empty( $locale ) || ! is_string( $locale ) ) {
		return false;
	}

	return array( $locale );
}

/**
 * Returns the list of preferred languages of the current site.
 *
 * @since 1.3.0
 *
 * @return string[] Preferred languages.
 */
function preferred_languages_get_site_list() {
	$preferred_languages = get_option( 'preferred_languages', '' );

	if ( ! is_string( $preferred_languages ) ) {
		return array();
	}

	return array_filter( explode( ',', $preferred_languages ) );
}

/**
 * Returns the list of preferred languages of the current site.
 *
 * @since 1.7.0
 *
 * @return string[] Preferred languages.
 */
function preferred_languages_get_network_list() {
	$preferred_languages = get_site_option( 'preferred_languages', '' );

	if ( ! is_string( $preferred_languages ) ) {
		return array();
	}

	return array_filter( explode( ',', $preferred_languages ) );
}

/**
 * Returns the list of preferred languages.
 *
 * If in the admin area, this returns the data for the current user.
 * Otherwise the site settings are used.
 *
 * @since 1.0.0
 *
 * @return string[] Preferred languages.
 */
function preferred_languages_get_list() {
	$preferred_languages = array();

	if ( preferred_languages_get_locale_switcher_user_id() ) {
		$preferred_languages = preferred_languages_get_user_list( preferred_languages_get_locale_switcher_user_id() );
	} elseif ( is_admin() ) {
		$preferred_languages = preferred_languages_get_user_list( get_current_user_id() );
	}

	if ( ! empty( $preferred_languages ) ) {
		return $preferred_languages;
	}

	// Fall back to site setting.
	$preferred_languages = preferred_languages_get_site_list();

	if ( ! empty( $preferred_languages ) ) {
		return $preferred_languages;
	}

	// Fallback to network setting.
	return preferred_languages_get_network_list();
}

/**
 * Hooks into user meta additions.
 *
 * Downloads language pack when populating list of preferred languages.
 *
 * Also updates the 'locale' user meta if the list is empty.
 *
 * @since 1.7.2
 *
 * @param int    $object_id  Object ID.
 * @param string $meta_key   Meta key.
 * @param mixed  $meta_value Meta value.
 */
function preferred_languages_add_user_meta( $object_id, $meta_key, $meta_value ) {
	if ( 'preferred_languages' !== $meta_key ) {
		return;
	}

	/*
	 * Clearing the preferred languages list should also clear the 'locale' user meta
	 * in order to prevent stale data.
	 */
	if ( empty( $meta_value ) ) {
		update_user_meta( $object_id, 'locale', '' );
	}

	if ( ! is_string( $meta_value ) ) {
		return;
	}

	$locales = array_filter( explode( ',', $meta_value ) );
	preferred_languages_download_language_packs( $locales );

	// Reload translations after save.
	if ( get_current_user_id() === $object_id ) {
		load_default_textdomain( determine_locale() );
	}
}

/**
 * Hooks into user meta updates.
 *
 * Downloads language pack when updating list of preferred languages.
 *
 * Also updates the 'locale' user meta if the list is empty.
 *
 * @since 1.3.0
 *
 * @param int    $meta_id    ID of the metadata entry to update.
 * @param int    $object_id  Object ID.
 * @param string $meta_key   Meta key.
 * @param mixed  $meta_value Meta value.
 */
function preferred_languages_update_user_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
	if ( 'preferred_languages' !== $meta_key ) {
		return;
	}

	/*
	 * Clearing the preferred languages list should also clear the 'locale' user meta
	 * in order to prevent stale data.
	 */
	if ( empty( $meta_value ) ) {
		update_user_meta( $object_id, 'locale', '' );
	}

	if ( ! is_string( $meta_value ) ) {
		return;
	}

	$locales = array_filter( explode( ',', $meta_value ) );
	preferred_languages_download_language_packs( $locales );

	// Reload translations after save.
	if ( get_current_user_id() === $object_id ) {
		load_default_textdomain( determine_locale() );
	}
}

/**
 * Downloads language packs when saving the site option without any changes.
 *
 * Makes sure the translations are downloaded when it didn't work the first time around.
 *
 * @since 1.4.0
 *
 * @param mixed $value     The new, unserialized option value.
 * @param mixed $old_value The old option value.
 * @return mixed
 */
function preferred_languages_pre_update_option( $value, $old_value ) {
	if ( ! is_string( $value ) ) {
		return $value;
	}

	if ( $value === $old_value ) {
		$locales = array_filter( explode( ',', $value ) );
		preferred_languages_download_language_packs( $locales );
	}

	return $value;
}

/**
 * Downloads language packs upon adding the site option.
 *
 * @since 2.1.0
 *
 * @param string $option Name of the option to add.
 * @param mixed  $value  Value of the option.
 */
function preferred_languages_add_option( $option, $value ) {
	/*
	 * Clearing the preferred languages list should also clear the 'WPLANG' option
	 * in order to prevent stale data.
	 */
	if ( empty( $value ) ) {
		update_option( 'WPLANG', '' );
	}

	if ( ! is_string( $value ) ) {
		return;
	}

	$locales = array_filter( explode( ',', $value ) );
	preferred_languages_download_language_packs( $locales );

	/*
	 * In addition to filtering the WPLANG option, also update it in the database,
	 * in case any plugin accesses it before this plugin is loaded.
	 */
	if ( ! empty( $locales ) ) {
		remove_filter( 'pre_option_WPLANG', 'preferred_languages_filter_option' );
		update_option( 'WPLANG', reset( $locales ) );
		add_filter( 'pre_option_WPLANG', 'preferred_languages_filter_option' );
	}

	// Reload translations after save.
	load_default_textdomain( determine_locale() );
}

/**
 * Downloads language packs upon updating the site option.
 *
 * @since 1.3.0
 *
 * @param string $old_value The old option value.
 * @param string $value     The new option value.
 */
function preferred_languages_update_option( $old_value, $value ) {
	/*
	 * Clearing the preferred languages list should also clear the 'WPLANG' option
	 * in order to prevent stale data.
	 */
	if ( empty( $value ) ) {
		update_option( 'WPLANG', '' );
	}

	$locales = array_filter( explode( ',', $value ) );
	preferred_languages_download_language_packs( $locales );

	/*
	 * In addition to filtering the WPLANG option, also update it in the database,
	 * in case any plugin accesses it before this plugin is loaded.
	 */
	if ( ! empty( $locales ) ) {
		remove_filter( 'pre_option_WPLANG', 'preferred_languages_filter_option' );
		update_option( 'WPLANG', reset( $locales ) );
		add_filter( 'pre_option_WPLANG', 'preferred_languages_filter_option' );
	}

	// Reload translations after save.
	load_default_textdomain( determine_locale() );
}

/**
 * Downloads language packs upon adding or updating the network option.
 *
 * @since 1.7.0
 *
 * @param string $option Name of the network option.
 * @param string $value  The new option value.
 */
function preferred_languages_update_site_option( $option, $value ) {
	if ( ! is_multisite() ) {
		return;
	}

	$locales = array_filter( explode( ',', $value ) );
	preferred_languages_download_language_packs( $locales );

	/*
	 * In addition to filtering the WPLANG site option, also update it in the database,
	 * in case any plugin accesses it before this plugin is loaded.
	 */
	if ( ! empty( $locales ) ) {
		remove_filter( 'pre_site_option_WPLANG', 'preferred_languages_filter_option' );
		update_site_option( 'WPLANG', reset( $locales ) );
		add_filter( 'pre_site_option_WPLANG', 'preferred_languages_filter_option' );
	}

	// Reload translations after save.
	load_default_textdomain( determine_locale() );
}

/**
 * Filters calls to get_option( 'WPLANG' ) to use the preferred languages setting.
 *
 * @since 2.1.0
 *
 * @param string $locale The current locale.
 * @return string
 */
function preferred_languages_filter_option( $locale ) {
	$preferred_languages = preferred_languages_get_site_list();

	if ( empty( $preferred_languages ) && is_multisite() ) {
		$preferred_languages = preferred_languages_get_network_list();
	}

	if ( ! empty( $preferred_languages ) ) {
		return reset( $preferred_languages );
	}

	return $locale;
}

/**
 * Downloads language packs upon updating the option.
 *
 * @since 1.0.0
 *
 * @param string[] $locales List of locales to install language packs for.
 * @return string[] The installed and available languages.
 */
function preferred_languages_download_language_packs( $locales ) {
	// @phpstan-ignore requireOnce.fileNotFound
	require_once ABSPATH . 'wp-admin/includes/translation-install.php';

	$installed_languages        = array();
	$available_languages        = get_available_languages();
	$user_can_install_languages = current_user_can( 'install_languages' );

	foreach ( $locales as $locale ) {
		if ( in_array( $locale, $available_languages, true ) ) {
			$installed_languages[] = $locale;
			continue;
		}

		if ( ! $user_can_install_languages ) {
			continue;
		}

		$language = wp_download_language_pack( $locale );

		if ( $language ) {
			$installed_languages[] = $language;
		}
	}

	/**
	 * Fires when downloading language packs upon updating preferences.
	 *
	 * @since 1.7.0
	 *
	 * @param array $locales             List of locales to install language packs for.
	 * @param array $installed_languages List of language packs that were successfully installed.
	 */
	do_action( 'preferred_languages_download_language_packs', $locales, $installed_languages );

	return $installed_languages;
}

/**
 * Sanitizes the preferred languages option.
 *
 * @since 1.0.0
 *
 * @param string $preferred_languages Comma separated list of preferred languages.
 *
 * @return string Sanitized list.
 */
function preferred_languages_sanitize_list( $preferred_languages ) {
	$locales = array_unique(
		array_map(
			'sanitize_locale_name',
			array_map( 'sanitize_text_field', wp_parse_list( $preferred_languages ) )
		)
	);

	return implode( ',', $locales );
}

/**
 * Filters calls to get_locale() to use the preferred languages setting.
 *
 * @since 1.0.0
 *
 * @param string $locale The current locale.
 *
 * @return string
 */
function preferred_languages_filter_locale( $locale ) {
	$preferred_languages = preferred_languages_get_site_list();

	if ( empty( $preferred_languages ) && is_multisite() ) {
		$preferred_languages = preferred_languages_get_network_list();
	}

	if ( ! empty( $preferred_languages ) ) {
		return reset( $preferred_languages );
	}

	return $locale;
}

/**
 * Filters calls to get_user_locale() to use the preferred languages setting.
 *
 * @since 1.0.0
 *
 * @param mixed  $value     The value get_metadata() should return - a single metadata value,
 *                          or an array of values.
 * @param int    $object_id Object ID.
 * @param string $meta_key  Meta key.
 *
 * @return mixed The meta value.
 */
function preferred_languages_filter_user_locale( $value, $object_id, $meta_key ) {
	if ( 'locale' !== $meta_key ) {
		return $value;
	}

	$preferred_languages = preferred_languages_get_user_list( $object_id );

	if ( ! empty( $preferred_languages ) ) {
		return reset( $preferred_languages );
	}

	/**
	 * Returning an empty string will force WordPress to fall back to {@see get_locale},
	 * which is filtered by {@see preferred_languages_filter_locale} to use the site's
	 * preferred languages list.
	 */
	return '';
}

/**
 * Filters whether to override the .mo file loading.
 *
 * Used for supporting translation merging.
 *
 * @since 1.7.1
 *
 * @param bool        $override         Whether to override the .mo file loading. Default false.
 * @param string      $domain           Text domain. Unique identifier for retrieving translated strings.
 * @param string      $mofile           Path to the MO file.
 * @param string|null $current_locale   Optional. Locale. Defaults to current locale.
 * @return bool Whether to override the .mo file loading.
 */
function preferred_languages_override_load_textdomain( $override, $domain, $mofile, $current_locale = null ) {
	if ( ! isset( $current_locale ) ) {
		$current_locale = determine_locale();
	}

	$preferred_locales = preferred_languages_get_list();

	if ( empty( $preferred_locales ) ) {
		return $override;
	}

	// Locale has been filtered by something else.
	if ( $preferred_locales[0] !== $current_locale && ! preferred_languages_is_locale_switched() ) {
		return $override;
	}

	/*
	 * If locale has been switched to a specific locale, ignore the ones before it.
	 * Example:
	 * Preferred Languages: fr_FR, de_CH, de_DE, es_ES.
	 * Switched to locale: de_CH
	 * In that case, only check for de_CH, de_DE, es_ES.
	 */
	if ( preferred_languages_is_locale_switched() ) {
		$offset = array_search( $current_locale, $preferred_locales, true );

		if ( ! is_int( $offset ) ) {
			return $override;
		}

		$preferred_locales = array_slice(
			$preferred_locales,
			$offset
		);
	}

	/**
	 * Filters whether translations should be merged with existing ones.
	 *
	 * @since 1.7.0
	 *
	 * @param bool   $merge          Whether translations should be merged. Defaults to true.
	 * @param string $domain         The text domain
	 * @param string $current_locale The current locale.
	 */
	$merge_translations = apply_filters( 'preferred_languages_merge_translations', true, $domain, $current_locale );

	$first_mofile = null;

	remove_filter( 'override_load_textdomain', 'preferred_languages_override_load_textdomain' );
	remove_filter( 'load_textdomain_mofile', 'preferred_languages_load_textdomain_mofile' );

	foreach ( $preferred_locales as $locale ) {
		$preferred_mofile = str_replace( $current_locale, $locale, $mofile );

		if ( is_readable( $preferred_mofile ) ) {
			$loaded = load_textdomain( $domain, $preferred_mofile );

			if ( ! $loaded ) {
				continue;
			}

			if ( null === $first_mofile ) {
				$first_mofile = $preferred_mofile;
			}

			if ( ! $merge_translations ) {
				break;
			}
		}
	}

	add_filter( 'override_load_textdomain', 'preferred_languages_override_load_textdomain', 10, 4 );
	add_filter( 'load_textdomain_mofile', 'preferred_languages_load_textdomain_mofile' );

	if ( null !== $first_mofile ) {
		return true;
	}

	return $override;
}

/**
 * Filters load_textdomain() calls to respect the list of preferred languages.
 *
 * @since 1.0.0
 *
 * @param string $mofile Path to the MO file.
 *
 * @return string The modified MO file path.
 */
function preferred_languages_load_textdomain_mofile( $mofile ) {
	$preferred_locales = preferred_languages_get_list();

	if ( empty( $preferred_locales ) ) {
		return $mofile;
	}

	$current_locale = determine_locale();

	// Locale has been filtered by something else.
	if ( $preferred_locales[0] !== $current_locale && ! preferred_languages_is_locale_switched() ) {
		return $mofile;
	}

	/*
	 * If locale has been switched to a specific locale,
	 * the right MO file has already been chosen. Bail early.
	 */
	if ( preferred_languages_is_locale_switched() ) {
		return $mofile;
	}

	foreach ( $preferred_locales as $locale ) {
		$preferred_mofile = str_replace( $current_locale, $locale, $mofile );

		if ( 'en_US' === $locale || is_readable( $preferred_mofile ) ) {
			return $preferred_mofile;
		}
	}

	return $mofile;
}

/**
 * Pre-filters script translations for the given file, script handle and text domain.
 *
 * Used for supporting translation merging.
 *
 * @since 1.7.1
 *
 * @param string|false|null $translations JSON-encoded translation data. Default null.
 * @param string|false      $file         Path to the translation file to load. False if there isn't one.
 * @param string            $handle       Name of the script to register a translation domain to.
 * @param string            $domain       The text domain.
 * @return string|false|null JSON-encoded translation data.
 */
function preferred_languages_pre_load_script_translations( $translations, $file, $handle, $domain ) {
	if ( ! $file ) {
		return $translations;
	}

	$current_locale = determine_locale();

	/** This filter is documented in inc/functions.php */
	$merge_translations = apply_filters( 'preferred_languages_merge_translations', true, $domain, $current_locale );

	if ( ! $merge_translations ) {
		return $translations;
	}

	$preferred_locales = preferred_languages_get_list();

	if ( empty( $preferred_locales ) ) {
		return $translations;
	}

	// Locale has been filtered by something else.
	if ( $preferred_locales[0] !== $current_locale && ! preferred_languages_is_locale_switched() ) {
		return $translations;
	}

	/*
	 * If locale has been switched to a specific locale, ignore the ones before it.
	 * Example:
	 * Preferred Languages: fr_FR, de_CH, de_DE, es_ES.
	 * Switched to locale: de_CH
	 * In that case, only check for de_CH, de_DE, es_ES.
	 */
	if ( preferred_languages_is_locale_switched() ) {
		$offset = array_search( $current_locale, $preferred_locales, true );

		if ( ! is_int( $offset ) ) {
			return $translations;
		}

		$preferred_locales = array_slice(
			$preferred_locales,
			$offset
		);
	}

	remove_filter( 'pre_load_script_translations', 'preferred_languages_pre_load_script_translations' );
	remove_filter( 'load_script_translation_file', 'preferred_languages_load_script_translation_file' );

	$all_translations = null;

	foreach ( $preferred_locales as $locale ) {
		$preferred_file = str_replace( $current_locale, $locale, $file );

		if ( ! is_readable( $preferred_file ) ) {
			continue;
		}

		$translations = load_script_translations( $preferred_file, $handle, $domain );

		if ( ! $translations ) {
			continue;
		}

		if ( ! $all_translations ) {
			$all_translations = $translations;
			continue;
		}

		// Some translations have already been loaded before, merge them.
		$all_translations_json = json_decode( $all_translations, true );
		$translations_json     = json_decode( $translations, true );

		if (
				! is_array( $all_translations_json ) ||
				! is_array( $translations_json ) ||
				! isset( $translations_json['locale_data']['messages'] ) ||
				! is_array( $translations_json['locale_data']['messages'] )
		) {
			return $translations;
		}

		foreach ( $translations_json['locale_data']['messages'] as $key => $translation ) {
			if (
				isset( $all_translations_json['locale_data']['messages'][ $key ] ) &&
				! empty( array_filter( (array) $all_translations_json['locale_data']['messages'][ $key ] ) )
			) {
				continue;
			}

			$all_translations_json['locale_data']['messages'][ $key ] = $translation;
		}

		$all_translations = wp_json_encode( $all_translations_json );
	}

	add_filter( 'pre_load_script_translations', 'preferred_languages_pre_load_script_translations', 10, 4 );
	add_filter( 'load_script_translation_file', 'preferred_languages_load_script_translation_file' );

	if ( $all_translations ) {
		return $all_translations;
	}

	return $translations;
}

/**
 * Filters load_script_translation_file() calls to respect the list of preferred languages.
 *
 * @since 1.6.0
 *
 * @param string|false $file Path to the translation file to load. False if there isn't one.
 *
 * @return string|false The modified JSON file path or false if there isn't one.
 */
function preferred_languages_load_script_translation_file( $file ) {
	if ( ! $file ) {
		return $file;
	}

	$preferred_locales = preferred_languages_get_list();

	if ( empty( $preferred_locales ) ) {
		return $file;
	}

	$current_locale = determine_locale();

	// Locale has been filtered by something else.
	if ( $preferred_locales[0] !== $current_locale && ! preferred_languages_is_locale_switched() ) {
		return $file;
	}

	foreach ( $preferred_locales as $locale ) {
		$preferred_file = str_replace( $current_locale, $locale, $file );

		if ( 'en_US' === $locale || is_readable( $preferred_file ) ) {
			return $preferred_file;
		}
	}

	return $file;
}

/**
 * Registers the needed scripts and styles.
 *
 * @since 1.0.0
 */
function preferred_languages_register_scripts() {
	$asset_file = dirname( __DIR__ ) . '/build/preferred-languages.asset.php';
	$asset      = is_readable( $asset_file ) ? require $asset_file : array();

	$asset['dependencies'] = $asset['dependencies'] ?? array();
	$asset['version']      = $asset['version'] ?? '';

	wp_register_script(
		'preferred-languages',
		plugins_url( 'build/preferred-languages.js', __DIR__ ),
		$asset['dependencies'],
		$asset['version'],
		true
	);

	wp_set_script_translations( 'preferred-languages', 'preferred-languages' );

	wp_register_style(
		'preferred-languages',
		plugins_url( 'build/preferred-languages.css', __DIR__ ),
		array( 'wp-components' ),
		$asset['version']
	);

	wp_style_add_data( 'preferred-languages', 'rtl', 'replace' );
}

/**
 * Adds a settings field for the preferred languages option.
 *
 * @since 1.0.0
 */
function preferred_languages_settings_field() {
	add_settings_field(
		'preferred_languages',
		'<span id="preferred-languages-label">' . __( 'Site Language', 'preferred-languages' ) . '<span/> <span class="dashicons dashicons-translation" aria-hidden="true"></span>',
		'preferred_languages_display_form',
		'general',
		'default',
		array(
			'class'    => 'site-preferred-languages-wrap',
			'selected' => preferred_languages_get_site_list(),
		)
	);

	if ( is_multisite() ) {
		add_settings_section(
			'preferred_languages',
			'',
			'__return_empty_string',
			'preferred_languages_network_settings'
		);

		add_settings_field(
			'preferred_languages',
			'<span id="preferred-languages-label">' . __( 'Default Language', 'preferred-languages' ) . '<span/> <span class="dashicons dashicons-translation" aria-hidden="true"></span>',
			'preferred_languages_display_form',
			'preferred_languages_network_settings',
			'preferred_languages',
			array(
				'class'    => 'network-preferred-languages-wrap',
				'selected' => preferred_languages_get_network_list(),
			)
		);
	}
}

/**
 * Adds a settings field for the preferred languages option.
 *
 * @since 1.7.0
 */
function preferred_languages_network_settings_field() {
	wp_nonce_field( 'preferred_languages_network_settings', 'preferred_languages_network_settings_nonce' );
	do_settings_sections( 'preferred_languages' );
	do_settings_sections( 'preferred_languages_network_settings' );
}

/**
 * Updates the preferred languages network settings.
 *
 * @since 1.7.0
 */
function preferred_languages_update_network_settings() {
	if ( ! is_multisite() ) {
		return;
	}

	if ( ! isset( $_POST['preferred_languages_network_settings_nonce'] ) ) {
		return;
	}

	if ( ! is_string( $_POST['preferred_languages_network_settings_nonce'] ) ) {
		return;
	}

	$nonce = wp_unslash( $_POST['preferred_languages_network_settings_nonce'] );

	if ( ! wp_verify_nonce( $nonce, 'preferred_languages_network_settings' ) ) {
		return;
	}

	if ( isset( $_POST['preferred_languages'] ) ) {
		update_site_option( 'preferred_languages', wp_unslash( $_POST['preferred_languages'] ) );
	}
}

/**
 * Adds a settings field for the preferred languages option to the user profile.
 *
 * @since 1.0.0
 *
 * @param WP_User $user The current WP_User object.
 */
function preferred_languages_personal_options( $user ) {
	$languages = get_available_languages();

	if ( ! $languages && ! current_user_can( 'install_languages' ) ) {
		return;
	}

	$selected = preferred_languages_get_user_list( $user );
	?>
	<tr class="user-preferred-languages-wrap">
		<th scope="row">
			<span id="preferred-languages-label">
				<?php _e( 'Language', 'preferred-languages' ); ?>
			</span>
			<span class="dashicons dashicons-translation" aria-hidden="true"></span>
		</th>
		<td>
			<?php
			preferred_languages_display_form(
				array(
					'selected'                 => is_array( $selected ) ? $selected : array(),
					'show_option_site_default' => true,
					'show_option_en_us'        => true,
				)
			);
			?>
		</td>
	</tr>
	<?php
}

/**
 * Displays the actual form to select the preferred languages.
 *
 * @since 1.0.0
 *
 * @phpstan-param array{selected?: string[], show_available_translations?: bool, show_option_en_us?: bool, show_option_site_default?: bool} $args
 *
 * @param array $args {
 *     Optional. Array of form arguments.
 *
 *     @type array $selected                    List of selected locales.
 *     @type bool  $show_available_translations Whether to show translations that are not currently installed.
 *                                              Default true if the user can install translations.
 *     @type bool  $show_option_en_us           Whether to show an "English (United States)" option.
 *                                              Default false.
 *     @type bool  $show_option_site_default    Whether to indicate a fallback to the Site Default if the list is empty.
 *                                              Default false.
 * }
 */
function preferred_languages_display_form( $args = array() ) {
	wp_enqueue_script( 'preferred-languages' );
	wp_enqueue_style( 'preferred-languages' );

	$args = (array) wp_parse_args(
		$args,
		array(
			'selected'                    => array(),
			'show_available_translations' => current_user_can( 'install_languages' ),
			'show_option_en_us'           => false,
			'show_option_site_default'    => false,
		)
	);

	// @phpstan-ignore requireOnce.fileNotFound
	require_once ABSPATH . 'wp-admin/includes/translation-install.php';

	$translations = wp_get_available_translations();
	$languages    = get_available_languages();

	$preferred_languages      = array();
	$has_missing_translations = false;

	foreach ( $args['selected'] as $locale ) {
		$is_installed = 'en_US' === $locale || in_array( $locale, $languages, true );

		if ( ! $is_installed ) {
			$has_missing_translations = true;
		}

		if ( isset( $translations[ $locale ] ) ) {
			$preferred_languages[] = array(
				'locale'     => $locale,
				'nativeName' => $translations[ $locale ]['native_name'],
				'lang'       => current( $translations[ $locale ]['iso'] ),
				'installed'  => $is_installed,
			);
		} elseif ( 'en_US' !== $locale ) {
			$preferred_languages[] = array(
				'locale'     => $locale,
				'nativeName' => $locale,
				'lang'       => '',
				'installed'  => $is_installed,
			);
		} else {
			$preferred_languages[] = array(
				'locale'     => $locale,
				'nativeName' => 'English (United States)',
				'lang'       => 'en',
				'installed'  => true,
			);
		}
	}

	$all_languages = array();

	if ( $args['show_option_en_us'] ) {
		$all_languages[] = array(
			'locale'     => 'en_US',
			'nativeName' => 'English (United States)',
			'lang'       => 'en',
			'installed'  => true,
		);
	}

	foreach ( $languages as $locale ) {
		if ( isset( $translations[ $locale ] ) ) {
			$all_languages[] = array(
				'locale'     => $locale,
				'nativeName' => $translations[ $locale ]['native_name'],
				'lang'       => current( $translations[ $locale ]['iso'] ),
				'installed'  => true,
			);
		} else {
			$all_languages[] = array(
				'locale'     => $locale,
				'nativeName' => $locale,
				'lang'       => '',
				'installed'  => true,
			);
		}
	}

	if ( $args['show_available_translations'] ) {
		foreach ( $translations as $translation ) {
			if ( in_array( $translation['language'], $languages, true ) ) {
				continue;
			}
			$all_languages[] = array(
				'locale'     => $translation['language'],
				'nativeName' => $translation['native_name'],
				'lang'       => current( $translation['iso'] ),
				'installed'  => false,
			);
		}
	}

	$props = array(
		'currentLocale'          => get_locale(),
		'preferredLanguages'     => $preferred_languages,
		'allLanguages'           => $all_languages,
		'hasMissingTranslations' => $has_missing_translations,
		'showOptionSiteDefault'  => (bool) $args['show_option_site_default'],
	);

	wp_add_inline_script(
		'preferred-languages',
		sprintf( 'var PreferredLanguages = Object.freeze( %s );', wp_json_encode( $props ) ),
		'before'
	);

	?>
	<div id="<?php echo esc_attr( 'preferred-languages-root' ); ?>"></div>
	<?php
}


/**
 * Filters the language directory path for a specific domain and locale.
 *
 * Used for hooking into just-in-time translation loading.
 *
 * @since 2.4.0
 *
 * @param string|false $path   Languages directory path for the given domain and locale.
 * @param string       $domain Text domain.
 * @param string       $locale Locale.
 *
 * @return string|false Filtered directory path.
 */
function preferred_languages_filter_lang_dir_for_domain( $path, $domain, $locale ) {
	global $wp_textdomain_registry;

	$preferred_locales = preferred_languages_get_list();

	if ( empty( $preferred_locales ) ) {
		return $path;
	}

	// Locale has been filtered by something else.
	if ( $preferred_locales[0] !== $locale && ! preferred_languages_is_locale_switched() ) {
		return $path;
	}

	/*
	 * If locale has been switched to a specific locale, ignore the ones before it.
	 * Example:
	 * Preferred Languages: fr_FR, de_CH, de_DE, es_ES.
	 * Switched to locale: de_CH
	 * In that case, only check for de_CH, de_DE, es_ES.
	 */
	if ( preferred_languages_is_locale_switched() ) {
		$offset = array_search( $locale, $preferred_locales, true );

		if ( ! is_int( $offset ) ) {
			return $path;
		}

		$preferred_locales = array_slice(
			$preferred_locales,
			$offset
		);
	}

	// From WP_Textdomain_Registry::get_path_from_lang_dir().
	$has_translation_in_folder = static function ( $location, $domain, $locale ) use ( $wp_textdomain_registry ) {
		$location = rtrim( $location, '/' );
		$files    = $wp_textdomain_registry->get_language_files_from_path( $location );

		$mo_path  = "$location/$domain-$locale.mo";
		$php_path = "$location/$domain-$locale.l10n.php";

		foreach ( $files as $file_path ) {
			if ( $file_path === $mo_path || $file_path === $php_path ) {
				return true;
			}
		}

		return false;
	};

	foreach ( $preferred_locales as $preferred_locale ) {
		remove_filter( 'lang_dir_for_domain', 'preferred_languages_filter_lang_dir_for_domain' );
		$new_path = $wp_textdomain_registry->get( $domain, $preferred_locale );
		add_filter( 'lang_dir_for_domain', 'preferred_languages_filter_lang_dir_for_domain', 10, 3 );

		if ( $new_path && $has_translation_in_folder( $new_path, $domain, $preferred_locale ) ) {
			return $new_path;
		}
	}

	return $path;
}

/**
 * Filters debug information to include Preferred Languages data.
 *
 * @since 1.8.0
 *
 * @phpstan-param array{wp-core: array{fields: array{site_language: array{value?: string}, user_language: array{value?: string}}}} $args
 * @phpstan-return array{wp-core: array{fields: array{site_language: array{value?: string}, user_language: array{value?: string}}}}
 *
 * @param array $args The debug information to be added to the core information page.
 *
 * @return array Filtered debug information.
 */
function preferred_languages_filter_debug_information( $args ) {
	if ( isset( $args['wp-core']['fields']['site_language']['value'] ) ) {
		$args['wp-core']['fields']['site_language']['value'] = implode( ', ', preferred_languages_get_site_list() );
	}

	if ( isset( $args['wp-core']['fields']['user_language']['value'] ) ) {
		$users = preferred_languages_get_user_list();
		if ( is_array( $users ) ) {
			$args['wp-core']['fields']['user_language']['value'] = implode( ', ', $users );
		}
	}

	return $args;
}

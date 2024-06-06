<?php
/**
 * WP_Theme_JSON_Schema class
 *
 * @package WordPress
 * @subpackage Theme
 * @since 5.9.0
 */

/**
 * Class that migrates a given theme.json structure to the latest schema.
 *
 * This class is for internal core usage and is not supposed to be used by extenders (plugins and/or themes).
 * This is a low-level API that may need to do breaking changes. Please,
 * use get_global_settings, get_global_styles, and get_global_stylesheet instead.
 *
 * @since 5.9.0
 * @access private
 */
#[AllowDynamicProperties]
class WP_Theme_JSON_Schema {

	/**
	 * Maps old properties to their new location within the schema's settings.
	 * This will be applied at both the defaults and individual block levels.
	 */
	const V1_TO_V2_RENAMED_PATHS = array(
		'border.customRadius'         => 'border.radius',
		'spacing.customMargin'        => 'spacing.margin',
		'spacing.customPadding'       => 'spacing.padding',
		'typography.customLineHeight' => 'typography.lineHeight',
	);

	/**
	 * Function that migrates a given theme.json structure to the last version.
	 *
	 * @since 5.9.0
	 * @since 6.6.0 Migrate up to v3 and add $origin parameter.
	 *
	 * @param array $theme_json The structure to migrate.
	 * @param string $origin    Optional. What source of data this object represents.
	 *                          One of 'blocks', 'default', 'theme', or 'custom'. Default 'theme'.
	 * @return array The structure in the last version.
	 */
	public static function migrate( $theme_json, $origin = 'theme' ) {
		if ( ! isset( $theme_json['version'] ) ) {
			$theme_json = array(
				'version' => WP_Theme_JSON::LATEST_SCHEMA,
			);
		}

		// Migrate each version in order starting with the current version.
		switch ( $theme_json['version'] ) {
			case 1:
				$theme_json = self::migrate_v1_to_v2( $theme_json );
				// Deliberate fall through. Once migrated to v2, also migrate to v3.
			case 2:
				$theme_json = self::migrate_v2_to_v3( $theme_json, $origin );
		}

		return $theme_json;
	}

	/**
	 * Removes the custom prefixes for a few properties
	 * that were part of v1:
	 *
	 * 'border.customRadius'         => 'border.radius',
	 * 'spacing.customMargin'        => 'spacing.margin',
	 * 'spacing.customPadding'       => 'spacing.padding',
	 * 'typography.customLineHeight' => 'typography.lineHeight',
	 *
	 * @since 5.9.0
	 *
	 * @param array $old Data to migrate.
	 *
	 * @return array Data without the custom prefixes.
	 */
	private static function migrate_v1_to_v2( $old ) {
		// Copy everything.
		$new = $old;

		// Overwrite the things that changed.
		if ( isset( $old['settings'] ) ) {
			$new['settings'] = self::rename_paths( $old['settings'], self::V1_TO_V2_RENAMED_PATHS );
		}

		// Set the new version.
		$new['version'] = 2;

		return $new;
	}

	/**
	 * Migrates from v2 to v3.
	 *
	 * - Sets settings.typography.defaultFontSizes to false if settings.typography.fontSizes are defined.
	 * - Sets settings.spacing.defaultSpacingSizes to false if settings.spacing.spacingSizes are defined.
	 * - Prevents settings.spacing.spacingSizes from merging with settings.spacing.spacingScale by
	 *   unsetting spacingScale when spacingSizes are defined.
	 *
	 * @since 6.6.0
	 *
	 * @param array $old     Data to migrate.
	 * @param string $origin What source of data this object represents.
	 *                       One of 'blocks', 'default', 'theme', or 'custom'.
	 * @return array Data with defaultFontSizes set to false.
	 */
	private static function migrate_v2_to_v3( $old, $origin ) {
		// Copy everything.
		$new = $old;

		// Set the new version.
		$new['version'] = 3;

		/*
		 * Remaining changes do not need to be applied to the custom origin,
		 * as they should take on the value of the theme origin.
		 */
		if ( 'custom' === $origin ) {
			return $new;
		}

		/*
		 * Even though defaultFontSizes and defaultSpacingSizes are new
		 * settings, we need to migrate them as they each control
		 * PRESETS_METADATA prevent_override values which were previously
		 * hardcoded to false. This only needs to happen when the theme provides
		 * fontSizes or spacingSizes as they could match the default ones and
		 * affect the generated CSS.
		 */
		if ( isset( $old['settings']['typography']['fontSizes'] ) ) {
			$new['settings']['typography']['defaultFontSizes'] = false;
		}

		/*
		 * Similarly to defaultFontSizes, we need to migrate defaultSpacingSizes
		 * as it controls the PRESETS_METADATA prevent_override which was
		 * previously hardcoded to false. This only needs to happen when the
		 * theme provided spacing sizes via spacingSizes or spacingScale.
		 */
		if (
			isset( $old['settings']['spacing']['spacingSizes'] ) ||
			isset( $old['settings']['spacing']['spacingScale'] )
		) {
			$new['settings']['spacing']['defaultSpacingSizes'] = false;
		}

		/*
		 * In v3 spacingSizes is merged with the generated spacingScale sizes
		 * instead of completely replacing them. The v3 behavior is what was
		 * documented for the v2 schema, but the code never actually did work
		 * that way. Instead of surprising users with a behavior change two
		 * years after the fact at the same time as a v3 update is introduced,
		 * we'll continue using the "bugged" behavior for v2 themes. And treat
		 * the "bug fix" as a breaking change for v3.
		 */
		if ( isset( $old['settings']['spacing']['spacingSizes'] ) ) {
			unset( $new['settings']['spacing']['spacingScale'] );
		}

		return $new;
	}

	/**
	 * Processes the settings subtree.
	 *
	 * @since 5.9.0
	 *
	 * @param array $settings        Array to process.
	 * @param array $paths_to_rename Paths to rename.
	 *
	 * @return array The settings in the new format.
	 */
	private static function rename_paths( $settings, $paths_to_rename ) {
		$new_settings = $settings;

		// Process any renamed/moved paths within default settings.
		self::rename_settings( $new_settings, $paths_to_rename );

		// Process individual block settings.
		if ( isset( $new_settings['blocks'] ) && is_array( $new_settings['blocks'] ) ) {
			foreach ( $new_settings['blocks'] as &$block_settings ) {
				self::rename_settings( $block_settings, $paths_to_rename );
			}
		}

		return $new_settings;
	}

	/**
	 * Processes a settings array, renaming or moving properties.
	 *
	 * @since 5.9.0
	 *
	 * @param array $settings        Reference to settings either defaults or an individual block's.
	 * @param array $paths_to_rename Paths to rename.
	 */
	private static function rename_settings( &$settings, $paths_to_rename ) {
		foreach ( $paths_to_rename as $original => $renamed ) {
			$original_path = explode( '.', $original );
			$renamed_path  = explode( '.', $renamed );
			$current_value = _wp_array_get( $settings, $original_path, null );

			if ( null !== $current_value ) {
				_wp_array_set( $settings, $renamed_path, $current_value );
				self::unset_setting_by_path( $settings, $original_path );
			}
		}
	}

	/**
	 * Removes a property from within the provided settings by its path.
	 *
	 * @since 5.9.0
	 *
	 * @param array $settings Reference to the current settings array.
	 * @param array $path Path to the property to be removed.
	 */
	private static function unset_setting_by_path( &$settings, $path ) {
		$tmp_settings = &$settings;
		$last_key     = array_pop( $path );
		foreach ( $path as $key ) {
			$tmp_settings = &$tmp_settings[ $key ];
		}

		unset( $tmp_settings[ $last_key ] );
	}
}

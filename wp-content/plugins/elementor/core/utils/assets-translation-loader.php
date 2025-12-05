<?php

namespace Elementor\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Assets_Translation_Loader {

	public static function for_handles( array $handles, $domain = null, $replace_callback = null ) {
		self::set_domain( $handles, $domain );
		self::replace_translation_path( $handles, $replace_callback );
	}

	private static function set_domain( array $handles, $domain = null ) {
		if ( empty( $domain ) || ! is_string( $domain ) ) {
			return;
		}

		foreach ( $handles as $handle ) {
			wp_set_script_translations( $handle, $domain );
		}
	}

	/**
	 * The purpose of this function is to replace the requested translation file
	 * with a file that contains all the translations for specific scripts.
	 *
	 * When developing a module and using Webpack's dynamic load feature, the script will be split into multiple chunks.
	 * As a result, the WordPress translations expressions will also be split into multiple files.
	 * Therefore, we replace the requested translation file with another file (generated in the build process)
	 * that contains all the translations for the specific script (including dynamically loaded chunks).
	 *
	 * Want to go deeper? Read the following article:
	 *
	 * @see https://developer.wordpress.com/2022/01/06/wordpress-plugin-i18n-webpack-and-composer/
	 *
	 * @param array         $handles
	 * @param callable|null $replace_callback
	 */
	private static function replace_translation_path( array $handles, $replace_callback = null ) {
		$sources = self::map_handles_to_src( $handles );

		add_filter( 'load_script_textdomain_relative_path', function ( $relative_path, $src ) use ( $sources, $replace_callback ) {
			if ( ! in_array( $src, $sources, true ) ) {
				return $relative_path;
			}

			if ( is_callable( $replace_callback ) ) {
				return $replace_callback( $relative_path, $src );
			}

			return self::default_replace_translation( $relative_path );
		}, 10, 2 );
	}

	private static function map_handles_to_src( array $handles ) {
		return array_map( function ( $handle ) {
			return wp_scripts()->registered[ $handle ]->src;
		}, $handles );
	}

	private static function default_replace_translation( $relative_path ) {
		// Translations are always based on the non-minified filename.
		$relative_path_without_ext = preg_replace( '/(\.min)?\.js$/i', '', $relative_path );

		// By default, we suffix the file with `.strings` (e.g 'assets/js/editor.js' => 'assets/js/editor.strings.js').
		return implode( '.', [
			$relative_path_without_ext,
			'strings',
			'js',
		] );
	}
}

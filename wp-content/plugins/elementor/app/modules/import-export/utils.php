<?php

namespace Elementor\App\Modules\ImportExport;

use Elementor\Core\Utils\Str;
use Elementor\Modules\LandingPages\Module as Landing_Pages_Module;
use Elementor\Modules\FloatingButtons\Module as Floating_Buttons_Module;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Utils as ElementorUtils;

class Utils {

	public static function read_json_file( $path ) {
		if ( ! Str::ends_with( $path, '.json' ) ) {
			$path .= '.json';
		}

		$file_content = ElementorUtils::file_get_contents( $path, true );

		return $file_content ? json_decode( $file_content, true ) : [];
	}

	public static function map_old_new_post_ids( array $imported_data ) {
		$result = [];

		$result += $imported_data['templates']['succeed'] ?? [];

		if ( isset( $imported_data['content'] ) ) {
			foreach ( $imported_data['content'] as $post_type ) {
				$result += $post_type['succeed'] ?? [];
			}
		}

		if ( isset( $imported_data['wp-content'] ) ) {
			foreach ( $imported_data['wp-content'] as $post_type ) {
				$result += $post_type['succeed'] ?? [];
			}
		}

		return $result;
	}

	public static function map_old_new_term_ids( array $imported_data ) {
		$result = [];

		if ( ! isset( $imported_data['taxonomies'] ) ) {
			return $result;
		}

		foreach ( $imported_data['taxonomies'] as $post_type_taxonomies ) {
			foreach ( $post_type_taxonomies as $taxonomy ) {
				foreach ( $taxonomy as $term ) {
					$result[ $term['old_id'] ] = $term['new_id'];
				}
			}
		}

		return $result;
	}

	public static function get_elementor_post_types() {
		$elementor_post_types = get_post_types_by_support( 'elementor' );

		return array_filter( $elementor_post_types, function ( $value ) {
			// Templates are handled in a separate process.
			return 'elementor_library' !== $value;
		} );
	}

	public static function get_builtin_wp_post_types() {
		return [ 'post', 'page', 'nav_menu_item' ];
	}

	public static function get_registered_cpt_names() {
		$post_types = get_post_types( [
			'public' => true,
			'can_export' => true,
			'_builtin' => false,
		] );

		unset(
			$post_types[ Landing_Pages_Module::CPT ],
			$post_types[ Source_Local::CPT ],
			$post_types[ Floating_Buttons_Module::CPT_FLOATING_BUTTONS ]
		);

		return array_keys( $post_types );
	}

	/**
	 * Transform a string name to title format.
	 *
	 * @param $name
	 *
	 * @return string
	 */
	public static function transform_name_to_title( $name ): string {
		if ( empty( $name ) ) {
			return '';
		}

		$title = str_replace( [ '-', '_' ], ' ', $name );

		return ucwords( $title );
	}

	public static function get_import_sessions( $should_run_cleanup = false ) {
		$import_sessions = get_option( Module::OPTION_KEY_ELEMENTOR_IMPORT_SESSIONS, [] );

		if ( $should_run_cleanup ) {
			foreach ( $import_sessions as $session_id => $import_session ) {
				if ( ! isset( $import_session['runners'] ) && isset( $import_session['instance_data'] ) ) {
					$import_sessions[ $session_id ]['runners'] = $import_session['instance_data']['runners_import_metadata'] ?? [];

					unset( $import_sessions[ $session_id ]['instance_data'] );
				}
			}

			update_option( Module::OPTION_KEY_ELEMENTOR_IMPORT_SESSIONS, $import_sessions );
		}

		return $import_sessions;
	}

	public static function update_space_between_widgets_values( $space_between_widgets ) {
		$setting_exist = isset( $space_between_widgets['size'] );
		$already_processed = isset( $space_between_widgets['column'] );

		if ( ! $setting_exist || $already_processed ) {
			return $space_between_widgets;
		}

		$size = strval( $space_between_widgets['size'] );
		$space_between_widgets['column'] = $size;
		$space_between_widgets['row'] = $size;
		$space_between_widgets['isLinked'] = true;

		return $space_between_widgets;
	}
}

<?php
/**
 * TablePress Base Controller with members and methods for all controllers
 *
 * @package TablePress
 * @subpackage Controllers
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Base Controller class
 * @package TablePress
 * @subpackage Controllers
 * @author Tobias Bäthge
 * @since 1.0.0
 */
abstract class TablePress_Controller {

	/**
	 * Instance of the Options Model.
	 *
	 * @since 1.0.0
	 * @var TablePress_Options_Model
	 */
	public $model_options;

	/**
	 * Instance of the Table Model.
	 *
	 * @since 1.0.0
	 * @var TablePress_Table_Model
	 */
	public $model_table;

	/**
	 * File name of the admin screens' parent page in the admin menu.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $parent_page = 'middle';

	/**
	 * Whether TablePress admin screens are a top-level menu item in the admin menu.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $is_top_level_page = false;

	/**
	 * Initialize all controllers, by loading Plugin and User Options, and by performing an update check.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		/*
		 * References to the TablePress models (only for backwards compatibility in TablePress Extensions!).
		 * Using `TablePress::$model_options` and `TablePress::$model_table` is recommended!
		 */
		$this->model_options = TablePress::$model_options;
		$this->model_table = TablePress::$model_table;

		// Update check, in all controllers (frontend and admin), to make sure we always have up-to-date options, should be done very early.
		$this->plugin_update_check();

		/**
		 * Filter the admin menu parent page, which is needed for the construction of plugin URLs.
		 *
		 * @since 1.0.0
		 *
		 * @param string $parent_page Current admin menu parent page.
		 */
		$this->parent_page = apply_filters( 'tablepress_admin_menu_parent_page', TablePress::$model_options->get( 'admin_menu_parent_page' ) );
		$this->is_top_level_page = in_array( $this->parent_page, array( 'top', 'middle', 'bottom' ), true );
	}

	/**
	 * Check if the plugin was updated and perform necessary actions, like updating the options.
	 *
	 * @since 1.0.0
	 */
	protected function plugin_update_check() {
		// First activation or plugin update.
		$current_plugin_options_db_version = TablePress::$model_options->get( 'plugin_options_db_version' );
		if ( $current_plugin_options_db_version < TablePress::db_version ) {
			// Allow more PHP execution time for update process.
			@set_time_limit( 300 );

			// Add TablePress capabilities to the WP_Roles objects, for new installations and all versions below 12.
			if ( $current_plugin_options_db_version < 12 ) {
				TablePress::$model_options->add_access_capabilities();
			}

			if ( 0 === TablePress::$model_options->get( 'first_activation' ) ) {
				// Save initial set of plugin options, and time of first activation of the plugin, on first activation.
				TablePress::$model_options->update( array(
					'first_activation'          => current_time( 'timestamp' ),
					'plugin_options_db_version' => TablePress::db_version,
				) );
			} else {
				// Update Plugin Options Options, if necessary.
				TablePress::$model_options->merge_plugin_options_defaults();
				$updated_options = array(
					'plugin_options_db_version' => TablePress::db_version,
					'prev_tablepress_version'   => TablePress::$model_options->get( 'tablepress_version' ),
					'tablepress_version'        => TablePress::version,
					'message_plugin_update'     => true,
				);

				// Only write files, if "Custom CSS" is to be used, and if there is "Custom CSS".
				if ( TablePress::$model_options->get( 'use_custom_css' ) && '' !== TablePress::$model_options->get( 'custom_css' ) ) {
					// Re-save "Custom CSS" to re-create all files (as TablePress Default CSS might have changed).
					/**
					 * Load WP file functions to provide filesystem access functions early.
					 */
					require_once ABSPATH . 'wp-admin/includes/file.php';
					/**
					 * Load WP admin template functions to provide `submit_button()` which is necessary for `request_filesystem_credentials()`.
					 */
					require_once ABSPATH . 'wp-admin/includes/template.php';
					$tablepress_css = TablePress::load_class( 'TablePress_CSS', 'class-css.php', 'classes' );
					$result = $tablepress_css->save_custom_css_to_file( TablePress::$model_options->get( 'custom_css' ), TablePress::$model_options->get( 'custom_css_minified' ) );
					// If saving was successful, use "Custom CSS" file.
					$updated_options['use_custom_css_file'] = $result;
					// Increase the "Custom CSS" version number for cache busting.
					if ( $result ) {
						$updated_options['custom_css_version'] = TablePress::$model_options->get( 'custom_css_version' ) + 1;
					}
				}

				TablePress::$model_options->update( $updated_options );

				// Clear table caches.
				if ( $current_plugin_options_db_version < 16 ) {
					// For pre-0.9-RC, where the arrays are serialized and not JSON encoded.
					TablePress::$model_table->invalidate_table_output_caches_tp09();
				} else {
					// For 0.9-RC and onwards.
					TablePress::$model_table->invalidate_table_output_caches();
				}

				// Add mime type field to existing posts with the TablePress Custom Post Type, so that other plugins know that they are not dealing with plain text.
				if ( $current_plugin_options_db_version < 25 ) {
					TablePress::$model_table->add_mime_type_to_posts();
				}

				// Convert old parameter names to new ones in DataTables "Custom Commands".
				if ( $current_plugin_options_db_version < 26 ) {
					TablePress::$model_table->convert_datatables_parameter_names_tp15();
				}
			}
		}

		// Maybe update the table scheme in each existing table, independently from updating the plugin options.
		if ( TablePress::$model_options->get( 'table_scheme_db_version' ) < TablePress::table_scheme_version ) {
			// Convert parameter "datatables_scrollX" to "datatables_scrollx", has to be done before merge_table_options_defaults() is called!
			if ( TablePress::$model_options->get( 'table_scheme_db_version' ) < 3 ) {
				TablePress::$model_table->merge_table_options_tp08();
			}

			TablePress::$model_table->merge_table_options_defaults();

			// Merge print_name/print_description changes made for 0.6-beta.
			if ( TablePress::$model_options->get( 'table_scheme_db_version' ) < 2 ) {
				TablePress::$model_table->merge_table_options_tp06();
			}

			TablePress::$model_options->update( array(
				'table_scheme_db_version' => TablePress::table_scheme_version,
			) );
		}

		/*
		 * Update User Options, if necessary.
		 * User Options are not saved in DB until first change occurs.
		 */
		if ( is_user_logged_in() && ( TablePress::$model_options->get( 'user_options_db_version' ) < TablePress::db_version ) ) {
			TablePress::$model_options->merge_user_options_defaults();
			TablePress::$model_options->update( array(
				'user_options_db_version' => TablePress::db_version,
			) );
		}
	}

} // class TablePress_Controller

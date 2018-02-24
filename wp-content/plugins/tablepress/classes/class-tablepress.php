<?php
/**
 * TablePress Class
 *
 * @package TablePress
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * TablePress class
 * @package TablePress
 * @author Tobias Bäthge
 * @since 1.0.0
 */
abstract class TablePress {

	/**
	 * TablePress version.
	 *
	 * Increases whenever a new plugin version is released.
	 *
	 * @since 1.0.0
	 * @const string
	 */
	const version = '1.9';

	/**
	 * TablePress internal plugin version ("options scheme" version).
	 *
	 * Increases whenever the scheme for the plugin options changes, or on a plugin update.
	 *
	 * @since 1.0.0
	 * @const int
	 */
	const db_version = 36;

	/**
	 * TablePress "table scheme" (data format structure) version.
	 *
	 * Increases whenever the scheme for a $table changes,
	 * used to be able to update plugin options and table scheme independently.
	 *
	 * @since 1.0.0
	 * @const int
	 */
	const table_scheme_version = 3;

	/**
	 * Instance of the Options Model.
	 *
	 * @since 1.3.0
	 * @var TablePress_Options_Model
	 */
	public static $model_options;

	/**
	 * Instance of the Table Model.
	 *
	 * @since 1.3.0
	 * @var TablePress_Table_Model
	 */
	public static $model_table;

	/**
	 * Instance of the controller.
	 *
	 * @since 1.0.0
	 * @var TablePress_*_Controller
	 */
	public static $controller;

	/**
	 * Name of the Shortcode to show a TablePress table.
	 *
	 * Should only be modified through the filter hook 'tablepress_table_shortcode'.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public static $shortcode = 'table';

	/**
	 * Name of the Shortcode to show extra information of a TablePress table.
	 *
	 * Should only be modified through the filter hook 'tablepress_table_info_shortcode'.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public static $shortcode_info = 'table-info';

	/**
	 * Start-up TablePress (run on WordPress "init") and load the controller for the current state.
	 *
	 * @since 1.0.0
	 */
	public static function run() {
		/**
		 * Fires when TablePress is loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'tablepress_run' );

		// Exit early if TablePress doesn't have to be loaded.
		if ( ( 'wp-login.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) ) // Login screen
			|| ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST )
			|| ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			return;
		}

		// Check if minimum requirements are fulfilled, currently WordPress 4.9.1.
		include( ABSPATH . WPINC . '/version.php' ); // Include an unmodified $wp_version.
		if ( version_compare( str_replace( '-src', '', $wp_version ), '4.9.1', '<' ) ) {
			// Show error notice to admins, if WP is not installed in the minimum required version, in which case TablePress will not work.
			if ( current_user_can( 'update_plugins' ) ) {
				add_action( 'admin_notices', array( 'TablePress', 'show_minimum_requirements_error_notice' ) );
			}
			// And exit TablePress.
			return;
		}

		/**
		 * Filter the string that is used as the [table] Shortcode.
		 *
		 * @since 1.0.0
		 *
		 * @param string $shortcode The [table] Shortcode string.
		 */
		self::$shortcode = apply_filters( 'tablepress_table_shortcode', self::$shortcode );
		/**
		 * Filter the string that is used as the [table-info] Shortcode.
		 *
		 * @since 1.0.0
		 *
		 * @param string $shortcode_info The [table-info] Shortcode string.
		 */
		self::$shortcode_info = apply_filters( 'tablepress_table_info_shortcode', self::$shortcode_info );

		// Load modals for table and options, to be accessible from everywhere via `TablePress::$model_options` and `TablePress::$model_table`.
		self::$model_options = self::load_model( 'options' );
		self::$model_table = self::load_model( 'table' );

		if ( is_admin() ) {
			$controller = 'admin';
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$controller .= '_ajax';
			}
		} else {
			$controller = 'frontend';
		}
		self::$controller = self::load_controller( $controller );
	}

	/**
	 * Load a file with require_once(), after running it through a filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file   Name of the PHP file with the class.
	 * @param string $folder Name of the folder with $class's $file.
	 */
	public static function load_file( $file, $folder ) {
		$full_path = TABLEPRESS_ABSPATH . $folder . '/' . $file;
		/**
		 * Filter the full path of a file that shall be loaded.
		 *
		 * @since 1.0.0
		 *
		 * @param string $full_path Full path of the file that shall be loaded.
		 * @param string $file      File name of the file that shall be loaded.
		 * @param string $folder    Folder name of the file that shall be loaded.
		 */
		$full_path = apply_filters( 'tablepress_load_file_full_path', $full_path, $file, $folder );
		if ( $full_path ) {
			require_once $full_path;
		}
	}

	/**
	 * Create a new instance of the $class, which is stored in $file in the $folder subfolder
	 * of the plugin's directory.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class  Name of the class.
	 * @param string $file   Name of the PHP file with the class.
	 * @param string $folder Name of the folder with $class's $file.
	 * @param mixed  $params Optional. Parameters that are passed to the constructor of $class.
	 * @return object Initialized instance of the class.
	 */
	public static function load_class( $class, $file, $folder, $params = null ) {
		/**
		 * Filter name of the class that shall be loaded.
		 *
		 * @since 1.0.0
		 *
		 * @param string $class Name of the class that shall be loaded.
		 */
		$class = apply_filters( 'tablepress_load_class_name', $class );
		if ( ! class_exists( $class, false ) ) {
			self::load_file( $file, $folder );
		}
		$the_class = new $class( $params );
		return $the_class;
	}

	/**
	 * Create a new instance of the $model, which is stored in the "models" subfolder.
	 *
	 * @since 1.0.0
	 *
	 * @param string $model Name of the model.
	 * @return object Instance of the initialized model.
	 */
	public static function load_model( $model ) {
		// Model Base Class.
		self::load_file( 'class-model.php', 'classes' );
		// Make first letter uppercase for a better looking naming pattern.
		$ucmodel = ucfirst( $model );
		$the_model = self::load_class( "TablePress_{$ucmodel}_Model", "model-{$model}.php", 'models' );
		return $the_model;
	}

	/**
	 * Create a new instance of the $view, which is stored in the "views" subfolder, and set it up with $data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $view Name of the view to load.
	 * @param array  $data Optional. Parameters/PHP variables that shall be available to the view.
	 * @return object Instance of the initialized view, already set up, just needs to be rendered.
	 */
	public static function load_view( $view, array $data = array() ) {
		// View Base Class.
		self::load_file( 'class-view.php', 'classes' );
		// Make first letter uppercase for a better looking naming pattern.
		$ucview = ucfirst( $view );
		$the_view = self::load_class( "TablePress_{$ucview}_View", "view-{$view}.php", 'views' );
		$the_view->setup( $view, $data );
		return $the_view;
	}

	/**
	 * Create a new instance of the $controller, which is stored in the "controllers" subfolder.
	 *
	 * @since 1.0.0
	 *
	 * @param string $controller Name of the controller.
	 * @return object Instance of the initialized controller.
	 */
	public static function load_controller( $controller ) {
		// Controller Base Class.
		self::load_file( 'class-controller.php', 'classes' );
		// Make first letter uppercase for a better looking naming pattern.
		$uccontroller = ucfirst( $controller );
		$the_controller = self::load_class( "TablePress_{$uccontroller}_Controller", "controller-{$controller}.php", 'controllers' );
		return $the_controller;
	}

	/**
	 * Generate the complete nonce string, from the nonce base, the action and an item, e.g. tablepress_delete_table_3.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $action Action for which the nonce is needed.
	 * @param string|bool $item   Optional. Item for which the action will be performed, like "table".
	 * @return string The resulting nonce string.
	 */
	public static function nonce( $action, $item = false ) {
		$nonce = "tablepress_{$action}";
		if ( $item ) {
			$nonce .= "_{$item}";
		}
		return $nonce;
	}

	/**
	 * Check whether a nonce string is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $action    Action for which the nonce should be checked.
	 * @param string|bool $item      Optional. Item for which the action should be performed, like "table".
	 * @param string      $query_arg Optional. Name of the nonce query string argument in $_POST.
	 * @param bool $ajax Whether the nonce comes from an AJAX request.
	 */
	public static function check_nonce( $action, $item = false, $query_arg = '_wpnonce', $ajax = false ) {
		$nonce_action = self::nonce( $action, $item );
		if ( $ajax ) {
			check_ajax_referer( $nonce_action, $query_arg );
		} else {
			check_admin_referer( $nonce_action, $query_arg );
		}
	}

	/**
	 * Calculate the column index (number) of a column header string (example: A is 1, AA is 27, ...).
	 *
	 * For the opposite, @see number_to_letter().
	 *
	 * @since 1.0.0
	 *
	 * @param string $column Column string.
	 * @return int $number Column number, 1-based.
	 */
	public static function letter_to_number( $column ) {
		$column = strtoupper( $column );
		$count = strlen( $column );
		$number = 0;
		for ( $i = 0; $i < $count; $i++ ) {
			$number += ( ord( $column[ $count - 1 - $i ] ) - 64 ) * pow( 26, $i );
		}
		return $number;
	}

	/**
	 * "Calculate" the column header string of a column index (example: 2 is B, AB is 28, ...).
	 *
	 * For the opposite, @see letter_to_number().
	 *
	 * @since 1.0.0
	 *
	 * @param int $number Column number, 1-based.
	 * @return string $column Column string.
	 */
	public static function number_to_letter( $number ) {
		$column = '';
		while ( $number > 0 ) {
			$column = chr( 65 + ( ( $number - 1 ) % 26 ) ) . $column;
			$number = floor( ( $number - 1 ) / 26 );
		}
		return $column;
	}

	/**
	 * Get a nice looking date and time string from the mySQL format of datetime strings for output.
	 *
	 * @param string $datetime  DateTime string in mySQL format or a Unix timestamp.
	 * @param string $type      Optional. Type of $datetime, 'mysql' or 'timestamp'.
	 * @param string $separator Optional. Separator between date and time.
	 * @return string Nice looking string with the date and time.
	 */
	public static function format_datetime( $datetime, $type = 'mysql', $separator = ' ' ) {
		// @TODO: Maybe change from using the stored WP Options to translated date/time schemes, like in https://core.trac.wordpress.org/changeset/35811.
		if ( 'mysql' === $type ) {
			return mysql2date( get_option( 'date_format' ), $datetime ) . $separator . mysql2date( get_option( 'time_format' ), $datetime );
		} else {
			return date_i18n( get_option( 'date_format' ), $datetime ) . $separator . date_i18n( get_option( 'time_format' ), $datetime );
		}
	}

	/**
	 * Get the name from a WP user ID (used to store information on last editor of a table).
	 *
	 * @param int $user_id WP user ID.
	 * @return string Nickname of the WP user with the $user_id.
	 */
	public static function get_user_display_name( $user_id ) {
		$user = get_userdata( $user_id );
		return ( $user && isset( $user->display_name ) ) ? $user->display_name : sprintf( '<em>%s</em>', __( 'unknown', 'tablepress' ) );
	}

	/**
	 * Generate the action URL, to be used as a link within the plugin (e.g. in the submenu navigation or List of Tables).
	 *
	 * @since 1.0.0
	 *
	 * @param array  $params    Optional. Parameters to form the query string of the URL.
	 * @param bool   $add_nonce Optional. Whether the URL shall be nonced by WordPress.
	 * @param string $target    Optional. Target File, e.g. "admin-post.php" for POST requests.
	 * @return string The URL for the given parameters (already run through esc_url() with $add_nonce === true!).
	 */
	public static function url( array $params = array(), $add_nonce = false, $target = '' ) {

		// Default action is "list", if no action given.
		if ( ! isset( $params['action'] ) ) {
			$params['action'] = 'list';
		}
		$nonce_action = $params['action'];

		if ( $target ) {
			$params['action'] = "tablepress_{$params['action']}";
		} else {
			$params['page'] = 'tablepress';
			// Top-level parent page needs special treatment for better action strings.
			if ( self::$controller->is_top_level_page ) {
				$target = 'admin.php';
				if ( ! in_array( $params['action'], array( 'list', 'edit' ), true ) ) {
					$params['page'] = "tablepress_{$params['action']}";
				}
				if ( ! in_array( $params['action'], array( 'edit' ), true ) ) {
					$params['action'] = false;
				}
			} else {
				$target = self::$controller->parent_page;
			}
		}

		// $default_params also determines the order of the values in the query string.
		$default_params = array(
			'page'   => false,
			'action' => false,
			'item'   => false,
		);
		$params = array_merge( $default_params, $params );

		$url = add_query_arg( $params, admin_url( $target ) );
		if ( $add_nonce ) {
			$url = wp_nonce_url( $url, self::nonce( $nonce_action, $params['item'] ) ); // wp_nonce_url() does esc_html()
		}
		return $url;
	}

	/**
	 * Create a redirect URL from the $target_parameters and redirect the user.
	 *
	 * @since 1.0.0
	 *
	 * @param array $params    Optional. Parameters from which the target URL is constructed.
	 * @param bool  $add_nonce Optional. Whether the URL shall be nonced by WordPress.
	 */
	public static function redirect( array $params = array(), $add_nonce = false ) {
		$redirect = self::url( $params );
		if ( $add_nonce ) {
			if ( ! isset( $params['item'] ) ) {
				$params['item'] = false;
			}
			// Don't use wp_nonce_url(), as that uses esc_html().
			$redirect = add_query_arg( '_wpnonce', wp_create_nonce( self::nonce( $params['action'], $params['item'] ) ), $redirect );
		}
		wp_redirect( $redirect );
		exit;
	}

	/**
	 * Show an error notice to admins, if TablePress's minimum requirements are not reached.
	 *
	 * @since 1.0.0
	 */
	public static function show_minimum_requirements_error_notice() {
		// Message is not translated as it is shown on every admin screen, for which we don't want to load translations.
		echo '<div class="notice notice-error form-invalid"><p>' .
			'<strong>Attention:</strong> ' .
			'The installed version of WordPress is too old for the TablePress plugin! TablePress requires an up-to-date version! <strong>Please <a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">update your WordPress installation</a></strong>!' .
			"</p></div>\n";
	}

} // class TablePress

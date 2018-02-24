<?php
/**
 * Options Model
 *
 * @package TablePress
 * @subpackage Models
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Options Model class
 * @package TablePress
 * @subpackage Models
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Options_Model extends TablePress_Model {

	/**
	 * Default Plugin Options.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $default_plugin_options = array(
		'plugin_options_db_version' => 0,
		'table_scheme_db_version'   => 0,
		'prev_tablepress_version'   => '0',
		'tablepress_version'        => TablePress::version,
		'first_activation'          => 0,
		'message_plugin_update'     => false,
		'message_donation_nag'      => true,
		'use_custom_css'            => true,
		'use_custom_css_file'       => true,
		'custom_css'                => '',
		'custom_css_minified'       => '',
		'custom_css_version'        => 0,
	);

	/**
	 * Default User Options.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $default_user_options = array(
		'user_options_db_version' => TablePress::db_version, // to prevent saving on first load
		'admin_menu_parent_page'  => 'middle',
		'message_first_visit'     => true,
	);

	/**
	 * Instance of WP_Option class for Plugin Options.
	 *
	 * @since 1.0.0
	 * @var TablePress_WP_Option
	 */
	protected $plugin_options;

	/**
	 * Instance of WP_User_Option class for User Options.
	 *
	 * @since 1.0.0
	 * @var TablePress_WP_User_Option
	 */
	protected $user_options;

	/**
	 * Init Options Model by creating the object instances for the Plugin and User Options.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();

		$params = array(
			'option_name'   => 'tablepress_plugin_options',
			'default_value' => $this->default_plugin_options,
		);
		$this->plugin_options = TablePress::load_class( 'TablePress_WP_Option', 'class-wp_option.php', 'classes', $params );

		$params = array(
			'option_name'   => 'tablepress_user_options',
			'default_value' => $this->default_user_options,
		);
		$this->user_options = TablePress::load_class( 'TablePress_WP_User_Option', 'class-wp_user_option.php', 'classes', $params );

		// Filter to map Meta capabilities to Primitive Capabilities.
		add_filter( 'map_meta_cap', array( $this, 'map_tablepress_meta_caps' ), 10, 4 );
	}

	/**
	 * Update a single option or an array of options with new values.
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $new_options  Array of new options (name => value) or name of a single option.
	 * @param mixed        $single_value Optional. New value for a single option (only if $new_options is not an array).
	 */
	public function update( $new_options, $single_value = null ) {
		// Allow saving of single options that are not in an array.
		if ( ! is_array( $new_options ) ) {
			$new_options = array( $new_options => $single_value );
		}

		$plugin_options = $this->plugin_options->get();
		$user_options = $this->user_options->get();
		foreach ( $new_options as $name => $value ) {
			if ( isset( $this->default_plugin_options[ $name ] ) ) {
				$plugin_options[ $name ] = $value;
			} elseif ( isset( $this->default_user_options[ $name ] ) ) {
				$user_options[ $name ] = $value;
			} else {
				// No valid Plugin or User Option -> discard the name/value pair.
			}
		}

		$this->plugin_options->update( $plugin_options );
		$this->user_options->update( $user_options );
	}

	/**
	 * Get the value of a single option, or an array with all options.
	 *
	 * @since 1.0.0
	 *
	 * @param string|bool $name          Optional. Name of a single option to get, or false for all options.
	 * @param mixed       $default_value Optional. Default value, if the option $name does not exist.
	 * @return mixed Value of the retrieved option $name, or $default_value if it does not exist, or array of all options.
	 */
	public function get( $name = false, $default_value = null ) {
		if ( false === $name ) {
			return array_merge( $this->plugin_options->get(), $this->user_options->get() );
		}

		// Single Option wanted.
		if ( $this->plugin_options->is_set( $name ) ) {
			return $this->plugin_options->get( $name );
		} elseif ( $this->user_options->is_set( $name ) ) {
			return $this->user_options->get( $name );
		} else {
			// No valid Plugin or User Option.
			return $default_value;
		}
	}

	/**
	 * Get all Plugin Options (only used in Debug).
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of all Plugin Options.
	 */
	public function _debug_get_plugin_options() {
		return $this->plugin_options->get();
	}

	/**
	 * Get all User Options (only used in Debug).
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of all User Options.
	 */
	public function _debug_get_user_options() {
		return $this->user_options->get();
	}

	/**
	 * Merge existing Plugin Options with default Plugin Options,
	 * remove (no longer) existing options, e.g. after a plugin update.
	 *
	 * @since 1.0.0
	 */
	public function merge_plugin_options_defaults() {
		$plugin_options = $this->plugin_options->get();
		// Remove old (i.e. no longer existing) Plugin Options.
		$plugin_options = array_intersect_key( $plugin_options, $this->default_plugin_options );
		// Merge current into new Plugin Options.
		$plugin_options = array_merge( $this->default_plugin_options, $plugin_options );

		$this->plugin_options->update( $plugin_options );
	}

	/**
	 * Merge existing User Options with default User Options,
	 * remove (no longer) existing options, e.g. after a plugin update.
	 *
	 * @since 1.0.0
	 */
	public function merge_user_options_defaults() {
		$user_options = $this->user_options->get();
		// Remove old (i.e. no longer existing) User Options.
		$user_options = array_intersect_key( $user_options, $this->default_user_options );
		// Merge current into new User Options.
		$user_options = array_merge( $this->default_user_options, $user_options );

		$this->user_options->update( $user_options );
	}

	/**
	 * Add default capabilities to "Administrator", "Editor", and "Author" user roles.
	 *
	 * @since 1.0.0
	 */
	public function add_access_capabilities() {
		// Capabilities for all roles.
		$roles = array( 'administrator', 'editor', 'author' );
		foreach ( $roles as $role ) {
			$role = get_role( $role );
			if ( empty( $role ) ) {
				continue;
			}

			// From get_post_type_capabilities().
			$role->add_cap( 'tablepress_edit_tables' );
			// $role->add_cap( 'tablepress_edit_others_tables' );
			$role->add_cap( 'tablepress_delete_tables' );
			// $role->add_cap( 'tablepress_delete_others_tables' );

			// Custom capabilities.
			$role->add_cap( 'tablepress_list_tables' );
			$role->add_cap( 'tablepress_add_tables' );
			$role->add_cap( 'tablepress_copy_tables' );
			$role->add_cap( 'tablepress_import_tables' );
			$role->add_cap( 'tablepress_export_tables' );
			$role->add_cap( 'tablepress_access_options_screen' );
			$role->add_cap( 'tablepress_access_about_screen' );
		}

		// Capabilities for single roles.
		$role = get_role( 'administrator' );
		if ( ! empty( $role ) ) {
			$role->add_cap( 'tablepress_import_tables_wptr' );
			$role->add_cap( 'tablepress_edit_options' );
		}

		// Refresh current set of capabilities of the user, to be able to directly use the new caps.
		$user = wp_get_current_user();
		$user->get_role_caps();
	}

	/**
	 * Remove all TablePress capabilities from all roles.
	 *
	 * @since 1.1.0
	 *
	 * @global WP_Roles $wp_roles WordPress User Roles abstraction object.
	 * @see add_access_capabilities()
	 */
	public function remove_access_capabilities() {
		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		foreach ( $wp_roles->roles as $role => $details ) {
			$role = $wp_roles->get_role( $role );
			if ( empty( $role ) ) {
				continue;
			}

			$role->remove_cap( 'tablepress_edit_tables' );
			$role->remove_cap( 'tablepress_delete_tables' );
			$role->remove_cap( 'tablepress_list_tables' );
			$role->remove_cap( 'tablepress_add_tables' );
			$role->remove_cap( 'tablepress_copy_tables' );
			$role->remove_cap( 'tablepress_import_tables' );
			$role->remove_cap( 'tablepress_export_tables' );
			$role->remove_cap( 'tablepress_access_options_screen' );
			$role->remove_cap( 'tablepress_access_about_screen' );
			$role->remove_cap( 'tablepress_import_tables_wptr' );
			$role->remove_cap( 'tablepress_edit_options' );
		}

		// Refresh current set of capabilities of the user, to be able to directly use the new caps.
		$user = wp_get_current_user();
		$user->get_role_caps();
	}

	/**
	 * Map TablePress meta capabilities to primitive capabilities.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $caps    Current set of primitive caps.
	 * @param string $cap     Meta cap that is to be checked/mapped.
	 * @param int    $user_id User ID for which meta cap is to be checked.
	 * @param array  $args    Arguments for the check, here e.g. the table ID.
	 * @return array $caps Modified set of primitive caps.
	 */
	public function map_tablepress_meta_caps( array $caps, $cap, $user_id, array $args ) {
		if ( ! in_array( $cap, array( 'tablepress_edit_table', 'tablepress_edit_table_id', 'tablepress_copy_table', 'tablepress_delete_table', 'tablepress_export_table', 'tablepress_preview_table' ), true ) ) {
			return $caps;
		}

		// $table_id = ( ! empty( $args ) ) ? $args[0] : false;

		// Reset current set of primitive caps.
		$caps = array();

		switch ( $cap ) {
			case 'tablepress_edit_table':
				$caps[] = 'tablepress_edit_tables';
				break;
			case 'tablepress_edit_table_id':
				$caps[] = 'tablepress_edit_tables';
				break;
			case 'tablepress_copy_table':
				$caps[] = 'tablepress_copy_tables';
				break;
			case 'tablepress_delete_table':
				$caps[] = 'tablepress_delete_tables';
				break;
			case 'tablepress_export_table':
				$caps[] = 'tablepress_export_tables';
				break;
			case 'tablepress_preview_table':
				$caps[] = 'tablepress_edit_tables';
				break;
			default:
				// Something went wrong, deny access to be on the safe side.
				$caps[] = 'do_not_allow';
				break;
		}

		/**
		 * Filter a user's TablePress capabilities.
		 *
		 * @since 1.0.0
		 *
		 * @see map_meta_cap()
		 *
		 * @param array  $caps    The user's current TablePress capabilities.
		 * @param string $cap     Capability name.
		 * @param int    $user_id The user ID.
		 * @param array  $args    Adds the context to the cap, typically the table ID.
		 */
		return apply_filters( 'tablepress_map_meta_caps', $caps, $cap, $user_id, $args );
	}

	/**
	 * Delete the WP_Option and the user option of the model.
	 *
	 * @since 1.0.0
	 */
	public function destroy() {
		$this->plugin_options->delete();
		$this->user_options->delete_for_all_users();
	}

} // class TablePress_Options_Model

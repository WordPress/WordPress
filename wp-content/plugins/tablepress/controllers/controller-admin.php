<?php
/**
 * Admin Controller for TablePress with the functionality for the non-AJAX backend
 *
 * @package TablePress
 * @subpackage Controllers
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Admin Controller class, extends Base Controller Class
 * @package TablePress
 * @subpackage Controllers
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Admin_Controller extends TablePress_Controller {

	/**
	 * Page hooks (i.e. names) WordPress uses for the TablePress admin screens,
	 * populated in add_admin_menu_entry().
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $page_hooks = array();

	/**
	 * Actions that have a view and admin menu or nav tab menu entry.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $view_actions = array();

	/**
	 * Whether language support has been loaded (to prevent doing it twice).
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $i18n_support_loaded = false;

	/**
	 * Instance of the TablePress Admin View that is rendered.
	 *
	 * @since 1.0.0
	 * @var TablePress_View
	 */
	protected $view;

	/**
	 * Instance of the TablePress Importer.
	 *
	 * @since 1.0.0
	 * @var TablePress_Import
	 */
	protected $importer;

	/**
	 * Initialize the Admin Controller, determine location the admin menu, set up actions.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();

		// Handler for changing the number of shown tables in the list of tables (via WP List Table class).
		add_filter( 'set-screen-option', array( $this, 'save_list_tables_screen_option' ), 10, 3 );

		add_action( 'admin_menu', array( $this, 'add_admin_menu_entry' ) );
		add_action( 'admin_init', array( $this, 'add_admin_actions' ) );
	}

	/**
	 * Handler for changing the number of shown tables in the list of tables (via WP List Table class).
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $false  Current value of the filter (probably bool false).
	 * @param string $option Option in which the setting is stored.
	 * @param int    $value  Current value of the setting.
	 * @return bool|int False to not save the changed setting, or the int value to be saved.
	 */
	public function save_list_tables_screen_option( $false, $option, $value ) {
		if ( 'tablepress_list_per_page' === $option ) {
			return $value;
		} else {
			return $false;
		}
	}

	/**
	 * Add admin screens to the correct place in the admin menu.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu_entry() {
		// Callback for all menu entries.
		$callback = array( $this, 'show_admin_page' );
		/**
		 * Filter the TablePress admin menu entry name.
		 *
		 * @since 1.0.0
		 *
		 * @param string $entry_name The admin menu entry name. Default "TablePress".
		 */
		$admin_menu_entry_name = apply_filters( 'tablepress_admin_menu_entry_name', 'TablePress' );

		if ( $this->is_top_level_page ) {
			// Init i18n support here as translated strings for admin menu are needed already.
			$this->init_i18n_support();
			$this->init_view_actions(); // after init_i18n_support(), as it requires translation
			$min_access_cap = $this->view_actions['list']['required_cap'];

			$icon_url = 'dashicons-list-view';
			switch ( $this->parent_page ) {
				case 'top':
					$position = 3; // position of Dashboard + 1
					break;
				case 'bottom':
					$position = ( ++$GLOBALS['_wp_last_utility_menu'] );
					break;
				case 'middle':
				default:
					$position = ( ++$GLOBALS['_wp_last_object_menu'] );
					break;
			}
			add_menu_page( 'TablePress', $admin_menu_entry_name, $min_access_cap, 'tablepress', $callback, $icon_url, $position );
			foreach ( $this->view_actions as $action => $entry ) {
				if ( ! $entry['show_entry'] ) {
					continue;
				}
				$slug = 'tablepress';
				if ( 'list' !== $action ) {
					$slug .= '_' . $action;
				}
				$this->page_hooks[] = add_submenu_page( 'tablepress', sprintf( __( '%1$s &lsaquo; %2$s', 'tablepress' ), $entry['page_title'], 'TablePress' ), $entry['admin_menu_title'], $entry['required_cap'], $slug, $callback );
			}
		} else {
			$this->init_view_actions(); // no translation necessary here
			$min_access_cap = $this->view_actions['list']['required_cap'];
			$this->page_hooks[] = add_submenu_page( $this->parent_page, 'TablePress', $admin_menu_entry_name, $min_access_cap, 'tablepress', $callback );
		}
	}

	/**
	 * Set up handlers for user actions in the backend that exceed plain viewing.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_actions() {
		// Register the callbacks for processing action requests.
		$post_actions = array( 'list', 'add', 'edit', 'options', 'export', 'import' );
		$get_actions = array( 'hide_message', 'delete_table', 'copy_table', 'preview_table', 'editor_button_thickbox', 'uninstall_tablepress' );
		foreach ( $post_actions as $action ) {
			add_action( "admin_post_tablepress_{$action}", array( $this, "handle_post_action_{$action}" ) );
		}
		foreach ( $get_actions as $action ) {
			add_action( "admin_post_tablepress_{$action}", array( $this, "handle_get_action_{$action}" ) );
		}

		// Register callbacks to trigger load behavior for admin pages.
		foreach ( $this->page_hooks as $page_hook ) {
			add_action( "load-{$page_hook}", array( $this, 'load_admin_page' ) );
		}

		$pages_with_editor_button = array( 'post.php', 'post-new.php' );
		foreach ( $pages_with_editor_button as $editor_page ) {
			add_action( "load-{$editor_page}", array( $this, 'add_editor_buttons' ) );
		}

		if ( ! is_network_admin() && ! is_user_admin() ) {
			add_action( 'admin_bar_menu', array( $this, 'add_wp_admin_bar_new_content_menu_entry' ), 71 );
		}

		add_action( 'load-plugins.php', array( $this, 'plugins_page' ) );
		add_action( 'admin_print_styles-media-upload-popup', array( $this, 'add_media_upload_thickbox_css' ) );

		// Add filters and actions for the integration into the WP WXR exporter and importer.
		add_action( 'wp_import_insert_post', array( TablePress::$model_table, 'add_table_id_on_wp_import' ), 10, 4 );
		add_filter( 'wp_import_post_meta', array( TablePress::$model_table, 'prevent_table_id_post_meta_import_on_wp_import' ), 10, 3 );
		add_filter( 'wxr_export_skip_postmeta', array( TablePress::$model_table, 'add_table_id_to_wp_export' ), 10, 3 );
	}

	/**
	 * Register actions to add "Table" button to "HTML editor" and "Visual editor" toolbars.
	 *
	 * @since 1.0.0
	 */
	public function add_editor_buttons() {
		if ( ! current_user_can( 'tablepress_list_tables' ) ) {
			return;
		}

		$this->init_i18n_support();
		add_thickbox(); // usually already loaded by media upload functions
		$admin_page = TablePress::load_class( 'TablePress_Admin_Page', 'class-admin-page-helper.php', 'classes' );
		$admin_page->enqueue_script( 'quicktags-button', array( 'quicktags', 'media-upload' ), array(
			'editor_button' => array(
				'caption'        => __( 'Table', 'tablepress' ),
				'title'          => __( 'Insert a Table from TablePress', 'tablepress' ),
				'thickbox_title' => __( 'Insert a Table from TablePress', 'tablepress' ),
				'thickbox_url'   => TablePress::url( array( 'action' => 'editor_button_thickbox' ), true, 'admin-post.php' ),
			),
		) );

		// TinyMCE integration.
		if ( user_can_richedit() ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'add_tinymce_button' ) );
			add_action( 'admin_print_styles', array( $this, 'add_tablepress_hidpi_css' ), 21 );
		}
	}

	/**
	 * Add "Table" button and separator to the TinyMCE toolbar.
	 *
	 * @since 1.0.0
	 *
	 * @param array $buttons Current set of buttons in the TinyMCE toolbar.
	 * @return array Current set of buttons in the TinyMCE toolbar, including "Table" button.
	 */
	public function add_tinymce_button( array $buttons ) {
		$buttons[] = 'tablepress_insert_table';
		return $buttons;
	}

	/**
	 * Register "Table" button plugin to TinyMCE.
	 *
	 * @since 1.0.0
	 *
	 * @param array $plugins Current set of registered TinyMCE plugins.
	 * @return array Current set of registered TinyMCE plugins, including "Table" button plugin.
	 */
	public function add_tinymce_plugin( array $plugins ) {
		$suffix = SCRIPT_DEBUG ? '' : '.min';
		$js_file = "admin/js/tinymce-button{$suffix}.js";
		$plugins['tablepress_tinymce'] = plugins_url( $js_file, TABLEPRESS__FILE__ );
		return $plugins;
	}

	/**
	 * Print TablePress HiDPI CSS to the <head> for TinyMCE button.
	 *
	 * @since 1.0.0
	 */
	public function add_tablepress_hidpi_css() {
		echo '<style type="text/css">@media print,(-webkit-min-device-pixel-ratio:1.25),(min-resolution:120dpi){';
		echo '#content_tablepress_insert_table span{background:url(' . plugins_url( 'admin/img/tablepress-editor-button-2x.png', TABLEPRESS__FILE__ ) . ') no-repeat 0 0;background-size:20px 20px}';
		echo '#content_tablepress_insert_table img{display:none}';
		echo '}</style>' . "\n";
	}

	/**
	 * Print some CSS in the Media Upload Thickbox to fix some positioning issues.
	 *
	 * These will most likely not be fixed in core, as the old media uploader is deprecated.
	 * They will be removed in TablePress, once the new media uploader is used.
	 *
	 * @since 1.4.0
	 */
	public function add_media_upload_thickbox_css() {
		echo '<style type="text/css">#media-items,#media-upload #filter{width:auto!important}.media-item .describe input[type="text"],.media-item .describe textarea{width:100%!important}.media-item .image-editor input[type="text"]{width:3em!important}</style>' . "\n";
	}

	/**
	 * Add "TablePress Table" entry to "New" dropdown menu in the WP Admin Bar.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The current WP Admin Bar object.
	 */
	public function add_wp_admin_bar_new_content_menu_entry( $wp_admin_bar ) {
		if ( ! current_user_can( 'tablepress_add_tables' ) ) {
			return;
		}
		// @TODO: Translation might not work, as textdomain might not yet be loaded here (for submenu entries).
		// Might need $this->init_i18n_support(); here.
		$wp_admin_bar->add_menu( array(
			'parent' => 'new-content',
			'id'     => 'new-tablepress-table',
			'title'  => __( 'TablePress Table', 'tablepress' ),
			'href'   => TablePress::url( array( 'action' => 'add' ) ),
		) );
	}

	/**
	 * Handle actions for loading of Plugins page.
	 *
	 * @since 1.0.0
	 */
	public function plugins_page() {
		$this->init_i18n_support();
		// Add additional links on Plugins page.
		add_filter( 'plugin_action_links_' . TABLEPRESS_BASENAME, array( $this, 'add_plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Add links to the TablePress entry in the "Plugin" column on the Plugins page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links List of links to print in the "Plugin" column on the Plugins page.
	 * @return array Extended list of links to print in the "Plugin" column on the Plugins page.
	 */
	public function add_plugin_action_links( array $links ) {
		if ( current_user_can( 'tablepress_list_tables' ) ) {
			$links[] = '<a href="' . TablePress::url() . '">' . __( 'Plugin page', 'tablepress' ) . '</a>';
		}
		return $links;
	}
	/**
	 * Add links to the TablePress entry in the "Description" column on the Plugins page.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $links List of links to print in the "Description" column on the Plugins page.
	 * @param string $file  Name of the plugin.
	 * @return array Extended list of links to print in the "Description" column on the Plugins page.
	 */
	public function add_plugin_row_meta( array $links, $file ) {
		if ( TABLEPRESS_BASENAME === $file ) {
			$links[] = '<a href="https://tablepress.org/faq/" title="' . esc_attr__( 'Frequently Asked Questions', 'tablepress' ) . '">' . __( 'FAQ', 'tablepress' ) . '</a>';
			$links[] = '<a href="https://tablepress.org/documentation/">' . __( 'Documentation', 'tablepress' ) . '</a>';
			$links[] = '<a href="https://tablepress.org/support/">' . __( 'Support', 'tablepress' ) . '</a>';
			$links[] = '<a href="https://tablepress.org/donate/" title="' . esc_attr__( 'Support TablePress with your donation!', 'tablepress' ) . '"><strong>' . __( 'Donate', 'tablepress' ) . '</strong></a>';
		}
		return $links;
	}

	/**
	 * Prepare the rendering of an admin screen, by determining the current action, loading necessary data and initializing the view.
	 *
	 * @since 1.0.0
	 */
	public function load_admin_page() {
		// Determine the action from either the GET parameter (for sub-menu entries, and the main admin menu entry).
		$action = ( ! empty( $_GET['action'] ) ) ? $_GET['action'] : 'list'; // default action is list
		if ( $this->is_top_level_page ) {
			// Or, for sub-menu entry of an admin menu "TablePress" entry, get it from the "page" GET parameter.
			if ( 'tablepress' !== $_GET['page'] ) {
				// Actions that are top-level entries, but don't have an action GET parameter (action is after last _ in string).
				$action = substr( $_GET['page'], 11 ); // $_GET['page'] has the format 'tablepress_{$action}'
			}
		} else {
			// Do this here in the else-part, instead of adding another if ( ! $this->is_top_level_page ) check.
			$this->init_i18n_support(); // done here, as for sub menu admin pages this is the first time translated strings are needed.
			$this->init_view_actions(); // for top-level menu entries, this has been done above, just like init_i18n_support().
		}

		// Check if action is a supported action, and whether the user is allowed to access this screen.
		if ( ! isset( $this->view_actions[ $action ] ) || ! current_user_can( $this->view_actions[ $action ]['required_cap'] ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
		}

		// Changes current screen ID and pagenow variable in JS, to enable automatic meta box JS handling.
		set_current_screen( "tablepress_{$action}" );
		// Set the $typenow global to the current CPT ourselves, as WP_Screen::get() does not determine the CPT correctly.
		// This is necessary as the WP Admin Menu can otherwise highlight wrong entries, see https://github.com/TobiasBg/TablePress/issues/24.
		if ( isset( $_GET['post_type'] ) && post_type_exists( $_GET['post_type'] ) ) {
			$GLOBALS['typenow'] = $_GET['post_type'];
		}

		// Pre-define some view data.
		$data = array(
			'view_actions' => $this->view_actions,
			'message'      => ( ! empty( $_GET['message'] ) ) ? $_GET['message'] : false,
		);

		// Depending on the action, load more necessary data for the corresponding view.
		switch ( $action ) {
			case 'list':
				$data['table_id'] = ( ! empty( $_GET['table_id'] ) ) ? $_GET['table_id'] : false;
				// Prime the post meta cache for cached loading of last_editor.
				$data['table_ids'] = TablePress::$model_table->load_all( true );
				$data['messages']['first_visit'] = TablePress::$model_options->get( 'message_first_visit' );
				if ( current_user_can( 'tablepress_import_tables_wptr' ) ) {
					// Check if WP-Table Reloaded is activated.
					$data['messages']['wp_table_reloaded_warning'] = is_plugin_active( 'wp-table-reloaded/wp-table-reloaded.php' );
				} else {
					$data['messages']['wp_table_reloaded_warning'] = false;
				}
				$data['messages']['plugin_update_message'] = TablePress::$model_options->get( 'message_plugin_update' );
				$data['messages']['donation_message'] = $this->maybe_show_donation_message();
				$data['table_count'] = count( $data['table_ids'] );
				break;
			case 'about':
				$data['first_activation'] = TablePress::$model_options->get( 'first_activation' );
				$exporter = TablePress::load_class( 'TablePress_Export', 'class-export.php', 'classes' );
				$data['zip_support_available'] = $exporter->zip_support_available;
				break;
			case 'options':
				// Maybe try saving "Custom CSS" to a file:
				// (called here, as the credentials form posts to this handler again, due to how request_filesystem_credentials() works)
				if ( isset( $_GET['item'] ) && 'save_custom_css' === $_GET['item'] ) {
					TablePress::check_nonce( 'options', $_GET['item'] ); // Nonce check here, as we don't have an explicit handler, and even viewing the screen needs to be checked.
					$action = 'options_custom_css'; // to load a different view
					// Try saving "Custom CSS" to a file, otherwise this gets the HTML for the credentials form.
					$tablepress_css = TablePress::load_class( 'TablePress_CSS', 'class-css.php', 'classes' );
					$result = $tablepress_css->save_custom_css_to_file_plugin_options( TablePress::$model_options->get( 'custom_css' ), TablePress::$model_options->get( 'custom_css_minified' ) );
					if ( is_string( $result ) ) {
						$data['credentials_form'] = $result; // This will only be called if the save function doesn't do a redirect.
					} elseif ( true === $result ) {
						/*
						 * At this point, saving was successful, so enable usage of CSS in files again,
						 * and also increase the "Custom CSS" version number (for cache busting).
						 */
						TablePress::$model_options->update( array(
							'use_custom_css_file' => true,
							'custom_css_version'  => TablePress::$model_options->get( 'custom_css_version' ) + 1,
						) );
						TablePress::redirect( array( 'action' => 'options', 'message' => 'success_save' ) );
					} else { // leaves only $result = false
						TablePress::redirect( array( 'action' => 'options', 'message' => 'success_save_error_custom_css' ) );
					}
					break;
				}
				$data['frontend_options']['use_custom_css'] = TablePress::$model_options->get( 'use_custom_css' );
				$data['frontend_options']['custom_css'] = TablePress::$model_options->get( 'custom_css' );
				$data['user_options']['parent_page'] = $this->parent_page;
				break;
			case 'edit':
				if ( ! empty( $_GET['table_id'] ) ) {
					// Load table, with table data, options, and visibility settings.
					$data['table'] = TablePress::$model_table->load( $_GET['table_id'], true, true );
					if ( is_wp_error( $data['table'] ) ) {
						TablePress::redirect( array( 'action' => 'list', 'message' => 'error_load_table' ) );
					}
					if ( ! current_user_can( 'tablepress_edit_table', $_GET['table_id'] ) ) {
						wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
					}
				} else {
					TablePress::redirect( array( 'action' => 'list', 'message' => 'error_no_table' ) );
				}
				break;
			case 'export':
				// Load all table IDs without priming the post meta cache, as table options/visibility are not needed.
				$data['table_ids'] = TablePress::$model_table->load_all( false );
				$data['tables_count'] = TablePress::$model_table->count_tables();
				if ( ! empty( $_GET['table_id'] ) ) {
					$data['export_ids'] = explode( ',', $_GET['table_id'] );
				} else {
					// Just show empty export form.
					$data['export_ids'] = array();
				}
				$exporter = TablePress::load_class( 'TablePress_Export', 'class-export.php', 'classes' );
				$data['zip_support_available'] = $exporter->zip_support_available;
				$data['export_formats'] = $exporter->export_formats;
				$data['csv_delimiters'] = $exporter->csv_delimiters;
				$data['export_format'] = ( ! empty( $_GET['export_format'] ) ) ? $_GET['export_format'] : false;
				$data['csv_delimiter'] = ( ! empty( $_GET['csv_delimiter'] ) ) ? $_GET['csv_delimiter'] : _x( ',', 'Default CSV delimiter in the translated language (";", ",", or "tab")', 'tablepress' );
				break;
			case 'import':
				// Load all table IDs without priming the post meta cache, as table options/visibility are not needed.
				$data['table_ids'] = TablePress::$model_table->load_all( false );
				$data['tables_count'] = TablePress::$model_table->count_tables();
				$importer = TablePress::load_class( 'TablePress_Import', 'class-import.php', 'classes' );
				$data['zip_support_available'] = $importer->zip_support_available;
				$data['html_import_support_available'] = $importer->html_import_support_available;
				$data['import_formats'] = $importer->import_formats;
				$data['import_format'] = ( ! empty( $_GET['import_format'] ) ) ? $_GET['import_format'] : false;
				$data['import_type'] = ( ! empty( $_GET['import_type'] ) ) ? $_GET['import_type'] : 'add';
				$data['import_existing_table'] = ( ! empty( $_GET['import_existing_table'] ) ) ? $_GET['import_existing_table'] : false;
				$data['import_source'] = ( ! empty( $_GET['import_source'] ) ) ? $_GET['import_source'] : 'file-upload';
				$data['import_url'] = ( ! empty( $_GET['import_url'] ) ) ? wp_unslash( $_GET['import_url'] ) : 'http://';
				$data['import_server'] = ( ! empty( $_GET['import_server'] ) ) ? wp_unslash( $_GET['import_server'] ) : ABSPATH;
				$data['import_form_field'] = ( ! empty( $_GET['import_form_field'] ) ) ? wp_unslash( $_GET['import_form_field'] ) : '';
				$data['wp_table_reloaded_installed'] = ( false !== get_option( 'wp_table_reloaded_options', false ) && false !== get_option( 'wp_table_reloaded_tables', false ) );
				$data['import_wp_table_reloaded_source'] = ( ! empty( $_GET['import_wp_table_reloaded_source'] ) ) ? $_GET['import_wp_table_reloaded_source'] : ( $data['wp_table_reloaded_installed'] ? 'db' : 'dump-file' );
				break;
		}

		/**
		 * Filter the data that is passed to the current TablePress View.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $data   Data for the view.
		 * @param string $action The current action for the view.
		 */
		$data = apply_filters( 'tablepress_view_data', $data, $action );

		// Prepare and initialize the view.
		$this->view = TablePress::load_view( $action, $data );
	}

	/**
	 * Render the view that has been initialized in load_admin_page() (called by WordPress when the actual page content is needed).
	 *
	 * @since 1.0.0
	 */
	public function show_admin_page() {
		$this->view->render();
	}

	/**
	 * Initialize i18n support, load plugin's textdomain, to retrieve correct translations.
	 *
	 * @since 1.0.0
	 */
	protected function init_i18n_support() {
		if ( $this->i18n_support_loaded ) {
			return;
		}
		load_plugin_textdomain( 'tablepress', false, dirname( TABLEPRESS_BASENAME ) . '/i18n' );
		$this->i18n_support_loaded = true;
	}

	/**
	 * Decide whether a donate message shall be shown on the "All Tables" screen, depending on passed days since installation and whether it was shown before.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether the donate message shall be shown on the "All Tables" screen.
	 */
	protected function maybe_show_donation_message() {
		// Only show the message to plugin admins.
		if ( ! current_user_can( 'tablepress_edit_options' ) ) {
			return false;
		}

		if ( ! TablePress::$model_options->get( 'message_donation_nag' ) ) {
			return false;
		}

		// Determine, how long has the plugin been installed.
		$seconds_installed = time() - TablePress::$model_options->get( 'first_activation' );
		return ( $seconds_installed > 30 * DAY_IN_SECONDS );
	}

	/**
	 * Init list of actions that have a view with their titles/names/caps.
	 *
	 * @since 1.0.0
	 */
	protected function init_view_actions() {
		$this->view_actions = array(
			'list'    => array(
				'show_entry'       => true,
				'page_title'       => __( 'All Tables', 'tablepress' ),
				'admin_menu_title' => __( 'All Tables', 'tablepress' ),
				'nav_tab_title'    => __( 'All Tables', 'tablepress' ),
				'required_cap'     => 'tablepress_list_tables',
			),
			'add'     => array(
				'show_entry'       => true,
				'page_title'       => __( 'Add New Table', 'tablepress' ),
				'admin_menu_title' => __( 'Add New Table', 'tablepress' ),
				'nav_tab_title'    => __( 'Add New', 'tablepress' ),
				'required_cap'     => 'tablepress_add_tables',
			),
			'edit'    => array(
				'show_entry'       => false,
				'page_title'       => __( 'Edit Table', 'tablepress' ),
				'admin_menu_title' => '',
				'nav_tab_title'    => '',
				'required_cap'     => 'tablepress_edit_tables',
			),
			'import'  => array(
				'show_entry'       => true,
				'page_title'       => __( 'Import a Table', 'tablepress' ),
				'admin_menu_title' => __( 'Import a Table', 'tablepress' ),
				'nav_tab_title'    => _x( 'Import', 'navigation bar', 'tablepress' ),
				'required_cap'     => 'tablepress_import_tables',
			),
			'export'  => array(
				'show_entry'       => true,
				'page_title'       => __( 'Export a Table', 'tablepress' ),
				'admin_menu_title' => __( 'Export a Table', 'tablepress' ),
				'nav_tab_title'    => _x( 'Export', 'navigation bar', 'tablepress' ),
				'required_cap'     => 'tablepress_export_tables',
			),
			'options' => array(
				'show_entry'       => true,
				'page_title'       => __( 'Plugin Options', 'tablepress' ),
				'admin_menu_title' => __( 'Plugin Options', 'tablepress' ),
				'nav_tab_title'    => __( 'Plugin Options', 'tablepress' ),
				'required_cap'     => 'tablepress_access_options_screen',
			),
			'about'   => array(
				'show_entry'       => true,
				'page_title'       => __( 'About', 'tablepress' ),
				'admin_menu_title' => __( 'About TablePress', 'tablepress' ),
				'nav_tab_title'    => __( 'About', 'tablepress' ),
				'required_cap'     => 'tablepress_access_about_screen',
			),
		);

		/**
		 * Filter the available TablePres Views/Actions and their parameters.
		 *
		 * @since 1.0.0
		 *
		 * @param array $view_actions The available Views/Actions and their parameters.
		 */
		$this->view_actions = apply_filters( 'tablepress_admin_view_actions', $this->view_actions );
	}

	/*
	 * HTTP POST actions.
	 */

	/**
	 * Handle Bulk Actions (Copy, Export, Delete) on "All Tables" list screen.
	 *
	 * @since 1.0.0
	 */
	public function handle_post_action_list() {
		TablePress::check_nonce( 'list' );

		if ( isset( $_POST['bulk-action-top'] ) && '-1' !== $_POST['bulk-action-top'] ) {
			$bulk_action = $_POST['bulk-action-top'];
		} elseif ( isset( $_POST['bulk-action-bottom'] ) && '-1' !== $_POST['bulk-action-bottom'] ) {
			$bulk_action = $_POST['bulk-action-bottom'];
		} else {
			$bulk_action = false;
		}

		if ( ! in_array( $bulk_action, array( 'copy', 'export', 'delete' ), true ) ) {
			TablePress::redirect( array( 'action' => 'list', 'message' => 'error_bulk_action_invalid' ) );
		}

		if ( empty( $_POST['table'] ) || ! is_array( $_POST['table'] ) ) {
			TablePress::redirect( array( 'action' => 'list', 'message' => 'error_no_selection' ) );
		} else {
			$tables = wp_unslash( $_POST['table'] );
		}

		$no_success = array(); // to store table IDs that failed

		switch ( $bulk_action ) {
			case 'copy':
				$this->init_i18n_support(); // for the translation of "Copy of"
				foreach ( $tables as $table_id ) {
					if ( current_user_can( 'tablepress_copy_table', $table_id ) ) {
						$copy_table_id = TablePress::$model_table->copy( $table_id );
						if ( is_wp_error( $copy_table_id ) ) {
							$no_success[] = $table_id;
						}
					} else {
						$no_success[] = $table_id;
					}
				}
				break;
			case 'export':
				/*
				 * Cap check is done on redirect target page.
				 * To export, redirect to "Export" screen, with selected table IDs.
				 */
				$table_ids = implode( ',', $tables );
				TablePress::redirect( array( 'action' => 'export', 'table_id' => $table_ids ) );
				break;
			case 'delete':
				foreach ( $tables as $table_id ) {
					if ( current_user_can( 'tablepress_delete_table', $table_id ) ) {
						$deleted = TablePress::$model_table->delete( $table_id );
						if ( is_wp_error( $deleted ) ) {
							$no_success[] = $table_id;
						}
					} else {
						$no_success[] = $table_id;
					}
				}
				break;
		}

		if ( 0 !== count( $no_success ) ) { // @TODO: maybe pass this information to the view?
			$message = "error_{$bulk_action}_not_all_tables";
		} else {
			$plural = ( count( $tables ) > 1 ) ? '_plural' : '';
			$message = "success_{$bulk_action}{$plural}";
		}

		/*
		 * Slightly more complex redirect method, to account for sort, search, and pagination in the WP_List_Table on the List View,
		 * but only if this action succeeds, to have everything fresh in the event of an error.
		 */
		$sendback = wp_get_referer();
		if ( ! $sendback ) {
			$sendback = TablePress::url( array( 'action' => 'list', 'message' => $message ) );
		} else {
			$sendback = remove_query_arg( array( 'action', 'message', 'table_id' ), $sendback );
			$sendback = add_query_arg( array( 'action' => 'list', 'message' => $message ), $sendback );
		}
		wp_redirect( $sendback );
		exit;
	}

	/**
	 * Save a table after the "Edit" screen was submitted.
	 *
	 * @since 1.0.0
	 */
	public function handle_post_action_edit() {
		if ( empty( $_POST['table'] ) || empty( $_POST['table']['id'] ) ) {
			TablePress::redirect( array( 'action' => 'list', 'message' => 'error_save' ) );
		} else {
			$edit_table = wp_unslash( $_POST['table'] );
		}

		TablePress::check_nonce( 'edit', $edit_table['id'], 'nonce-edit-table' );

		if ( ! current_user_can( 'tablepress_edit_table', $edit_table['id'] ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
		}

		// Options array must exist, so that checkboxes can be evaluated.
		if ( empty( $edit_table['options'] ) ) {
			TablePress::redirect( array( 'action' => 'edit', 'table_id' => $edit_table['id'], 'message' => 'error_save' ) );
		}

		// Evaluate options that have a checkbox (only necessary in Admin Controller, where they might not be set (if unchecked)).
		$checkbox_options = array(
			// Table Options.
			'table_head',
			'table_foot',
			'alternating_row_colors',
			'row_hover',
			'print_name',
			'print_description',
			// DataTables JS Features.
			'use_datatables',
			'datatables_sort',
			'datatables_filter',
			'datatables_paginate',
			'datatables_lengthchange',
			'datatables_info',
			'datatables_scrollx',
		);
		foreach ( $checkbox_options as $option ) {
			$edit_table['options'][ $option ] = ( isset( $edit_table['options'][ $option ] ) && 'true' === $edit_table['options'][ $option ] );
		}

		// Load table, without table data, but with options and visibility settings.
		$existing_table = TablePress::$model_table->load( $edit_table['id'], false, true );
		if ( is_wp_error( $existing_table ) ) { // @TODO: Maybe somehow load a new table here? (TablePress::$model_table->get_table_template())?
			TablePress::redirect( array( 'action' => 'edit', 'table_id' => $edit_table['id'], 'message' => 'error_save' ) );
		}

		// Check consistency of new table, and then merge with existing table.
		$table = TablePress::$model_table->prepare_table( $existing_table, $edit_table );
		if ( is_wp_error( $table ) ) {
			TablePress::redirect( array( 'action' => 'edit', 'table_id' => $edit_table['id'], 'message' => 'error_save' ) );
		}

		// DataTables Custom Commands can only be edit by trusted users.
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$table['options']['datatables_custom_commands'] = $existing_table['options']['datatables_custom_commands'];
		}

		// Save updated table.
		$saved = TablePress::$model_table->save( $table );
		if ( is_wp_error( $saved ) ) {
			TablePress::redirect( array( 'action' => 'edit', 'table_id' => $table['id'], 'message' => 'error_save' ) );
		}

		// Check if ID change is desired.
		if ( $table['id'] === $table['new_id'] ) { // if not, we are done
			TablePress::redirect( array( 'action' => 'edit', 'table_id' => $table['id'], 'message' => 'success_save' ) );
		}

		// Change table ID.
		if ( current_user_can( 'tablepress_edit_table_id', $table['id'] ) ) {
			$id_changed = TablePress::$model_table->change_table_id( $table['id'], $table['new_id'] );
			if ( ! is_wp_error( $id_changed ) ) {
				TablePress::redirect( array( 'action' => 'edit', 'table_id' => $table['new_id'], 'message' => 'success_save_success_id_change' ) );
			} else {
				TablePress::redirect( array( 'action' => 'edit', 'table_id' => $table['id'], 'message' => 'success_save_error_id_change' ) );
			}
		} else {
			TablePress::redirect( array( 'action' => 'edit', 'table_id' => $table['id'], 'message' => 'success_save_error_id_change' ) );
		}
	}

	/**
	 * Add a table, according to the parameters on the "Add new Table" screen.
	 *
	 * @since 1.0.0
	 */
	public function handle_post_action_add() {
		TablePress::check_nonce( 'add' );

		if ( ! current_user_can( 'tablepress_add_tables' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
		}

		if ( empty( $_POST['table'] ) || ! is_array( $_POST['table'] ) ) {
			TablePress::redirect( array( 'action' => 'add', 'message' => 'error_add' ) );
		} else {
			$add_table = wp_unslash( $_POST['table'] );
		}

		// Perform sanity checks of posted data.
		$name = ( isset( $add_table['name'] ) ) ? $add_table['name'] : '';
		$description = ( isset( $add_table['description'] ) ) ? $add_table['description'] : '';
		if ( ! isset( $add_table['rows'] ) || ! isset( $add_table['columns'] ) ) {
			TablePress::redirect( array( 'action' => 'add', 'message' => 'error_add' ) );
		}

		$num_rows = absint( $add_table['rows'] );
		$num_columns = absint( $add_table['columns'] );
		if ( 0 === $num_rows || 0 === $num_columns ) {
			TablePress::redirect( array( 'action' => 'add', 'message' => 'error_add' ) );
		}

		// Create a new table array with information from the posted data.
		$new_table = array(
			'name'        => $name,
			'description' => $description,
			'data'        => array_fill( 0, $num_rows, array_fill( 0, $num_columns, '' ) ),
			'visibility'  => array(
				'rows'    => array_fill( 0, $num_rows, 1 ),
				'columns' => array_fill( 0, $num_columns, 1 ),
			),
		);
		// Merge this data into an empty table template.
		$table = TablePress::$model_table->prepare_table( TablePress::$model_table->get_table_template(), $new_table, false );
		if ( is_wp_error( $table ) ) {
			TablePress::redirect( array( 'action' => 'add', 'message' => 'error_add' ) );
		}

		// Add the new table (and get its first ID).
		$table_id = TablePress::$model_table->add( $table );
		if ( is_wp_error( $table_id ) ) {
			TablePress::redirect( array( 'action' => 'add', 'message' => 'error_add' ) );
		}

		TablePress::redirect( array( 'action' => 'edit', 'table_id' => $table_id, 'message' => 'success_add' ) );
	}

	/**
	 * Save changed "Plugin Options".
	 *
	 * @since 1.0.0
	 */
	public function handle_post_action_options() {
		TablePress::check_nonce( 'options' );

		if ( ! current_user_can( 'tablepress_access_options_screen' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
		}

		if ( empty( $_POST['options'] ) || ! is_array( $_POST['options'] ) ) {
			TablePress::redirect( array( 'action' => 'options', 'message' => 'error_save' ) );
		} else {
			$posted_options = wp_unslash( $_POST['options'] );
		}

		// Valid new options that will be merged into existing ones.
		$new_options = array();

		// Check each posted option value, and (maybe) add it to the new options.
		if ( ! empty( $posted_options['admin_menu_parent_page'] ) && '-' !== $posted_options['admin_menu_parent_page'] ) {
			$new_options['admin_menu_parent_page'] = $posted_options['admin_menu_parent_page'];
			// Re-init parent information, as TablePress::redirect() URL might be wrong otherwise.
			/** This filter is documented in classes/class-controller.php */
			$this->parent_page = apply_filters( 'tablepress_admin_menu_parent_page', $posted_options['admin_menu_parent_page'] );
			$this->is_top_level_page = in_array( $this->parent_page, array( 'top', 'middle', 'bottom' ), true );
		}

		// Custom CSS can only be saved if the user is allowed to do so.
		$update_custom_css_files = false;
		if ( current_user_can( 'tablepress_edit_options' ) ) {
			// Checkbox
			$new_options['use_custom_css'] = ( isset( $posted_options['use_custom_css'] ) && 'true' === $posted_options['use_custom_css'] );

			if ( isset( $posted_options['custom_css'] ) ) {
				$new_options['custom_css'] = $posted_options['custom_css'];

				$tablepress_css = TablePress::load_class( 'TablePress_CSS', 'class-css.php', 'classes' );
				// Sanitize and tidy up Custom CSS.
				$new_options['custom_css'] = $tablepress_css->sanitize_css( $new_options['custom_css'] );
				// Minify Custom CSS
				$new_options['custom_css_minified'] = $tablepress_css->minify_css( $new_options['custom_css'] );

				// Maybe update CSS files as well.
				$custom_css_file_contents = $tablepress_css->load_custom_css_from_file( 'normal' );
				if ( false === $custom_css_file_contents ) {
					$custom_css_file_contents = '';
				}
				// Don't write to file if it already has the desired content.
				if ( $new_options['custom_css'] !== $custom_css_file_contents ) {
					$update_custom_css_files = true;
					// Set to false again. As it was set here, it will be set true again, if file saving succeeds.
					$new_options['use_custom_css_file'] = false;
				}
			}
		}

		// Save gathered new options (will be merged into existing ones), and flush caches of caching plugins, to make sure that the new Custom CSS is used.
		if ( ! empty( $new_options ) ) {
			TablePress::$model_options->update( $new_options );
			TablePress::$model_table->_flush_caching_plugins_caches();
		}

		if ( $update_custom_css_files ) { // Capability check is performed above.
			TablePress::redirect( array( 'action' => 'options', 'item' => 'save_custom_css' ), true );
		}

		TablePress::redirect( array( 'action' => 'options', 'message' => 'success_save' ) );
	}

	/**
	 * Export selected tables.
	 *
	 * @since 1.0.0
	 */
	public function handle_post_action_export() {
		TablePress::check_nonce( 'export' );

		if ( ! current_user_can( 'tablepress_export_tables' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
		}

		if ( empty( $_POST['export'] ) || ! is_array( $_POST['export'] ) ) {
			TablePress::redirect( array( 'action' => 'export', 'message' => 'error_export' ) );
		} else {
			$export = wp_unslash( $_POST['export'] );
		}

		$exporter = TablePress::load_class( 'TablePress_Export', 'class-export.php', 'classes' );

		if ( empty( $export['tables'] ) ) {
			TablePress::redirect( array( 'action' => 'export', 'message' => 'error_export' ) );
		}
		if ( empty( $export['format'] ) || ! isset( $exporter->export_formats[ $export['format'] ] ) ) {
			TablePress::redirect( array( 'action' => 'export', 'message' => 'error_export' ) );
		}
		if ( empty( $export['csv_delimiter'] ) ) {
			// Set a value, so that the variable exists.
			$export['csv_delimiter'] = '';
		}
		if ( 'csv' === $export['format'] && ! isset( $exporter->csv_delimiters[ $export['csv_delimiter'] ] ) ) {
			TablePress::redirect( array( 'action' => 'export', 'message' => 'error_export' ) );
		}

		// Use list of tables from concatenated field if available (as that's hopefully not truncated by Suhosin, which is possible for $export['tables']).
		$tables = ( ! empty( $export['tables_list'] ) ) ? explode( ',', $export['tables_list'] ) : $export['tables'];

		// Determine if ZIP file support is available.
		if ( $exporter->zip_support_available
		&& ( ( isset( $export['zip_file'] ) && 'true' === $export['zip_file'] ) || count( $tables ) > 1 ) ) {
			// Export to ZIP only if ZIP is desired or if more than one table were selected (mandatory then).
			$export_to_zip = true;
		} else {
			$export_to_zip = false;
		}

		if ( ! $export_to_zip ) {
			// This is only possible for one table, so take the first one.
			if ( ! current_user_can( 'tablepress_export_table', $tables[0] ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
			}
			// Load table, with table data, options, and visibility settings.
			$table = TablePress::$model_table->load( $tables[0], true, true );
			if ( is_wp_error( $table ) ) {
				TablePress::redirect( array( 'action' => 'export', 'message' => 'error_load_table', 'export_format' => $export['format'], 'csv_delimiter' => $export['csv_delimiter'] ) );
			}
			if ( isset( $table['is_corrupted'] ) && $table['is_corrupted'] ) {
				TablePress::redirect( array( 'action' => 'export', 'message' => 'error_table_corrupted', 'export_format' => $export['format'], 'csv_delimiter' => $export['csv_delimiter'] ) );
			}
			$download_filename = sprintf( '%1$s-%2$s-%3$s.%4$s', $table['id'], $table['name'], date( 'Y-m-d' ), $export['format'] );
			$download_filename = sanitize_file_name( $download_filename );
			// Export the table.
			$export_data = $exporter->export_table( $table, $export['format'], $export['csv_delimiter'] );
			/**
			 * Filter the exported table data.
			 *
			 * @since 1.6.0
			 *
			 * @param string $export_data   The exported table data.
			 * @param array  $table         Table to be exported.
			 * @param string $export_format Format for the export ('csv', 'html', 'json').
			 * @param string $csv_delimiter Delimiter for CSV export.
			 */
			$export_data = apply_filters( 'tablepress_export_data', $export_data, $table, $export['format'], $export['csv_delimiter'] );
			$download_data = $export_data;
		} else {
			// Zipping can use a lot of memory and execution time, but not this much hopefully.
			/** This filter is documented in the WordPress file wp-admin/admin.php */
			@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', WP_MAX_MEMORY_LIMIT ) );
			@set_time_limit( 300 );

			$zip_file = new ZipArchive();
			$download_filename = sprintf( 'tablepress-export-%1$s-%2$s.zip', date_i18n( 'Y-m-d-H-i-s' ), $export['format'] );
			$download_filename = sanitize_file_name( $download_filename );
			$full_filename = wp_tempnam( $download_filename );
			if ( true !== $zip_file->open( $full_filename, ZIPARCHIVE::OVERWRITE ) ) {
				@unlink( $full_filename );
				TablePress::redirect( array( 'action' => 'export', 'message' => 'error_create_zip_file', 'export_format' => $export['format'], 'csv_delimiter' => $export['csv_delimiter'] ) );
			}

			foreach ( $tables as $table_id ) {
				// Don't export tables for which the user doesn't have the necessary export rights.
				if ( ! current_user_can( 'tablepress_export_table', $table_id ) ) {
					continue;
				}
				// Load table, with table data, options, and visibility settings.
				$table = TablePress::$model_table->load( $table_id, true, true );
				// Don't export if the table could not be loaded.
				if ( is_wp_error( $table ) ) {
					continue;
				}
				// Don't export if the table is corrupted.
				if ( isset( $table['is_corrupted'] ) && $table['is_corrupted'] ) {
					continue;
				}
				$export_data = $exporter->export_table( $table, $export['format'], $export['csv_delimiter'] );
				/** This filter is documented in controllers/controller-admin.php */
				$export_data = apply_filters( 'tablepress_export_data', $export_data, $table, $export['format'], $export['csv_delimiter'] );
				$export_filename = sprintf( '%1$s-%2$s-%3$s.%4$s', $table['id'], $table['name'], date( 'Y-m-d' ), $export['format'] );
				$export_filename = sanitize_file_name( $export_filename );
				$zip_file->addFromString( $export_filename, $export_data );
			}

			// If something went wrong, or no files were added to the ZIP file, bail out.
			if ( ! ZIPARCHIVE::ER_OK === $zip_file->status || 0 === $zip_file->numFiles ) {
				$zip_file->close();
				@unlink( $full_filename );
				TablePress::redirect( array( 'action' => 'export', 'message' => 'error_create_zip_file', 'export_format' => $export['format'], 'csv_delimiter' => $export['csv_delimiter'] ) );
			}
			$zip_file->close();

			// Load contents of the ZIP file, to send it as a download.
			$download_data = file_get_contents( $full_filename );
			@unlink( $full_filename );
		}

		// Send download headers for export file.
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( "Content-Disposition: attachment; filename=\"{$download_filename}\"" );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . strlen( $download_data ) );
		// $filetype = text/csv, text/html, application/json
		// header( 'Content-Type: ' . $filetype. '; charset=' . get_option( 'blog_charset' ) );
		@ob_end_clean();
		flush();
		echo $download_data;
		exit;
	}

	/**
	 * Import data from either an existing source or WP-Table Reloaded.
	 *
	 * @since 1.0.0
	 */
	public function handle_post_action_import() {
		TablePress::check_nonce( 'import' );

		if ( ! current_user_can( 'tablepress_import_tables' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
		}

		if ( empty( $_POST['import'] ) || ! is_array( $_POST['import'] ) ) {
			TablePress::redirect( array( 'action' => 'import', 'message' => 'error_import' ) );
		} else {
			$import = wp_unslash( $_POST['import'] );
		}

		// Determine if this is a regular import or an import from WP-Table Reloaded.
		if ( isset( $_POST['submit_wp_table_reloaded_import'] ) && isset( $import['wp_table_reloaded'] ) && isset( $import['wp_table_reloaded']['source'] ) ) {
			if ( ! current_user_can( 'tablepress_import_tables_wptr' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
			}

			// Handle checkbox selections.
			$import_tables = ( isset( $import['wp_table_reloaded']['tables'] ) && 'true' === $import['wp_table_reloaded']['tables'] );
			$import_css = ( isset( $import['wp_table_reloaded']['css'] ) && 'true' === $import['wp_table_reloaded']['css'] );
			if ( ! $import_tables && ! $import_css ) {
				TablePress::redirect( array( 'action' => 'import', 'message' => 'error_wp_table_reloaded_nothing_selected' ) );
			}

			if ( 'db' === $import['wp_table_reloaded']['source'] ) {
				$this->_import_from_wp_table_reloaded_db( $import_tables, $import_css );
			} else {
				$this->_import_from_wp_table_reloaded_dump_file( $import_tables, $import_css );
			}
		} else {
			$this->_import_tablepress_regular( $import );
		}
	}

	/**
	 * Import data from existing source (Upload, URL, Server, Direct input).
	 *
	 * @since 1.0.0
	 *
	 * @param array $import Submitted form data.
	 */
	protected function _import_tablepress_regular( array $import ) {
		if ( ! isset( $import['type'] ) ) {
			$import['type'] = 'add';
		}
		if ( ! isset( $import['existing_table'] ) ) {
			$import['existing_table'] = '';
		}
		if ( ! isset( $import['source'] ) ) {
			$import['source'] = '';
		}

		// Check if a table to replace or append to was selected.
		if ( in_array( $import['type'], array( 'replace', 'append' ), true ) && empty( $import['existing_table'] ) ) {
			TablePress::redirect( array( 'action' => 'import', 'message' => 'error_import_no_existing_id', 'import_format' => $import['format'], 'import_type' => $import['type'], 'import_source' => $import['source'] ) );
		}

		$import_error = true;
		$unlink_file = false;
		$import_data = array();
		switch ( $import['source'] ) {
			case 'file-upload':
				if ( ! empty( $_FILES['import_file_upload'] ) && UPLOAD_ERR_OK === $_FILES['import_file_upload']['error'] ) {
					$import_data['file_location'] = $_FILES['import_file_upload']['tmp_name'];
					$import_data['file_name'] = $_FILES['import_file_upload']['name'];
					// $_FILES['import_file_upload']['type'];
					// $_FILES['import_file_upload']['size']
					$import_error = false;
					$unlink_file = true;
				}
				break;
			case 'url':
				if ( ! empty( $import['url'] ) && 'http://' !== $import['url'] ) {
					// download URL to local file.
					$import_data['file_location'] = download_url( $import['url'] );
					$import_data['file_name'] = $import['url'];
					if ( ! is_wp_error( $import_data['file_location'] ) ) {
						$import_error = false;
					}
					$unlink_file = true;
				}
				break;
			case 'server':
				if ( ! empty( $import['server'] ) && ABSPATH !== $import['server']
					&& ( ( ! is_multisite() && current_user_can( 'manage_options' ) ) || is_super_admin() )
				) {
					// For security reasons, the `server` source is only available for administrators.
					$import_data['file_location'] = $import['server'];
					$import_data['file_name'] = pathinfo( $import['server'], PATHINFO_BASENAME );
					if ( is_readable( $import['server'] ) ) {
						$import_error = false;
					}
				}
				break;
			case 'form-field':
				if ( ! empty( $import['form_field'] ) ) {
					$import_data['file_location'] = '';
					$import_data['file_name'] = __( 'Imported from Manual Input', 'tablepress' ); // Description of the table.
					$import_data['data'] = $import['form_field'];
					$import_error = false;
				}
				break;
		}

		if ( $import_error ) {
			if ( $unlink_file ) {
				@unlink( $import_data['file_location'] );
			}
			TablePress::redirect( array( 'action' => 'import', 'message' => 'error_import_source_invalid', 'import_format' => $import['format'], 'import_type' => $import['type'], 'import_existing_table' => $import['existing_table'], 'import_source' => $import['source'] ) );
		}

		$this->importer = TablePress::load_class( 'TablePress_Import', 'class-import.php', 'classes' );

		if ( 'zip' === pathinfo( $import_data['file_name'], PATHINFO_EXTENSION ) ) {
			// Determine if ZIP file support is available.
			if ( ! $this->importer->zip_support_available ) {
				if ( $unlink_file ) {
					@unlink( $import_data['file_location'] );
				}
				TablePress::redirect( array( 'action' => 'import', 'message' => 'error_no_zip_import', 'import_format' => $import['format'], 'import_type' => $import['type'], 'import_existing_table' => $import['existing_table'], 'import_source' => $import['source'] ) );
			}
			$import_zip = true;
		} else {
			$import_zip = false;
		}

		if ( ! $import_zip ) {
			if ( ! isset( $import_data['data'] ) ) {
				$import_data['data'] = file_get_contents( $import_data['file_location'] );
			}
			if ( false === $import_data['data'] ) {
				TablePress::redirect( array( 'action' => 'import', 'message' => 'error_import' ) );
			}

			$name = $import_data['file_name'];
			$description = $import_data['file_name'];
			$existing_table_id = ( in_array( $import['type'], array( 'replace', 'append' ), true ) && ! empty( $import['existing_table'] ) ) ? $import['existing_table'] : false;
			$table_id = $this->_import_tablepress_table( $import['format'], $import_data['data'], $name, $description, $existing_table_id, $import['type'] );

			if ( $unlink_file ) {
				@unlink( $import_data['file_location'] );
			}

			if ( is_wp_error( $table_id ) ) {
				TablePress::redirect( array( 'action' => 'import', 'message' => 'error_import_data' ) );
			} else {
				TablePress::redirect( array( 'action' => 'edit', 'table_id' => $table_id, 'message' => 'success_import' ) );
			}
		} else {
			// Zipping can use a lot of memory and execution time, but not this much hopefully.
			/** This filter is documented in the WordPress file wp-admin/admin.php */
			@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', WP_MAX_MEMORY_LIMIT ) );
			@set_time_limit( 300 );

			$zip = new ZipArchive();
			if ( true !== $zip->open( $import_data['file_location'], ZIPARCHIVE::CHECKCONS ) ) {
				if ( $unlink_file ) {
					@unlink( $import_data['file_location'] );
				}
				TablePress::redirect( array( 'action' => 'import', 'message' => 'error_import_zip_open' ) );
			}

			$imported_files = array();
			for ( $file_idx = 0; $file_idx < $zip->numFiles; $file_idx++ ) {
				$file_name = $zip->getNameIndex( $file_idx );
				// Skip directories.
				if ( '/' === substr( $file_name, -1 ) ) {
					continue;
				}
				// Skip the __MACOSX directory that Mac OSX adds to archives.
				if ( '__MACOSX/' === substr( $file_name, 0, 9 ) ) {
					continue;
				}
				$data = $zip->getFromIndex( $file_idx );
				if ( false === $data ) {
					continue;
				}

				$name = $file_name;
				$description = $file_name;
				$existing_table_id = ( in_array( $import['type'], array( 'replace', 'append' ), true ) ) ? false : false; // @TODO: Find a way to extract the replace/append ID from the filename, maybe?
				$table_id = $this->_import_tablepress_table( $import['format'], $data, $name, $description, $existing_table_id, 'add' );
				if ( is_wp_error( $table_id ) ) {
					continue;
				} else {
					$imported_files[] = $table_id;
				}
			};
			$zip->close();

			if ( $unlink_file ) {
				@unlink( $import_data['file_location'] );
			}

			if ( count( $imported_files ) > 1 ) {
				TablePress::redirect( array( 'action' => 'list', 'message' => 'success_import' ) );
			} elseif ( 1 === count( $imported_files ) ) {
				TablePress::redirect( array( 'action' => 'edit', 'table_id' => $imported_files[0], 'message' => 'success_import' ) );
			} else {
				TablePress::redirect( array( 'action' => 'import', 'message' => 'error_import_zip_content' ) );
			}
		}

	}

	/**
	 * Import a table by either replacing an existing table or adding it as a new table.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $format            Import format.
	 * @param string      $data              Data to import.
	 * @param string      $name              Name of the table.
	 * @param string      $description       Description of the table.
	 * @param bool|string $existing_table_id False if table shall be added new, ID of the table to be replaced or appended to otherwise.
	 * @param string      $import_type       What to do with the imported data: "add", "replace", "append".
	 * @return string|WP_Error WP_Error on error, table ID on success.
	 */
	protected function _import_tablepress_table( $format, $data, $name, $description, $existing_table_id, $import_type ) {
		$imported_table = $this->importer->import_table( $format, $data );
		if ( false === $imported_table ) {
			return new WP_Error( 'table_import_import_failed' );
		}

		if ( false === $existing_table_id ) {
			$import_type = 'add';
		}

		// To be able to replace or append to a table, editing that table must be allowed.
		if ( in_array( $import_type, array( 'replace', 'append' ), true ) && ! current_user_can( 'tablepress_edit_table', $existing_table_id ) ) {
			return new WP_Error( 'table_import_replace_append_capability_check_failed' );
		}

		// Full JSON format table can contain a table ID, try to keep that.
		$table_id_in_import = false;

		switch ( $import_type ) {
			case 'add':
				$existing_table = TablePress::$model_table->get_table_template();
				// If name and description are imported from a new table, use those.
				if ( isset( $imported_table['id'] ) ) {
					$table_id_in_import = $imported_table['id'];
				}
				if ( ! isset( $imported_table['name'] ) ) {
					$imported_table['name'] = $name;
				}
				if ( ! isset( $imported_table['description'] ) ) {
					$imported_table['description'] = $description;
				}
				if ( isset( $imported_table['visibility'] ) && isset( $imported_table['visibility']['rows'] ) && isset( $imported_table['visibility']['columns'] ) ) {
					$existing_table['visibility']['rows'] = $imported_table['visibility']['rows'];
					$existing_table['visibility']['columns'] = $imported_table['visibility']['columns'];
				}
				break;
			case 'replace':
				// Load table, without table data, but with options and visibility settings.
				$existing_table = TablePress::$model_table->load( $existing_table_id, false, true );
				if ( is_wp_error( $existing_table ) ) {
					// Add an error code to the existing WP_Error.
					$existing_table->add( 'table_import_replace_table_load', '', $existing_table_id );
					return $existing_table;
				}
				// Don't change name and description when a table is replaced.
				$imported_table['name'] = $existing_table['name'];
				$imported_table['description'] = $existing_table['description'];
				if ( isset( $imported_table['visibility'] ) && isset( $imported_table['visibility']['rows'] ) && isset( $imported_table['visibility']['columns'] ) ) {
					$existing_table['visibility']['rows'] = $imported_table['visibility']['rows'];
					$existing_table['visibility']['columns'] = $imported_table['visibility']['columns'];
				}
				break;
			case 'append':
				// Load table, with table data, options, and visibility settings.
				$existing_table = TablePress::$model_table->load( $existing_table_id, true, true );
				if ( is_wp_error( $existing_table ) ) {
					// Add an error code to the existing WP_Error.
					$existing_table->add( 'table_import_append_table_load', '', $existing_table_id );
					return $existing_table;
				}
				if ( isset( $existing_table['is_corrupted'] ) && $existing_table['is_corrupted'] ) {
					return new WP_Error( 'table_import_append_table_load_corrupted', '', $existing_table_id );
				}
				// Don't change name and description when a table is appended to.
				$imported_table['name'] = $existing_table['name'];
				$imported_table['description'] = $existing_table['description'];
				// Actual appending:
				$imported_table['data'] = array_merge( $existing_table['data'], $imported_table['data'] );
				$imported_table['data'] = $this->importer->pad_array_to_max_cols( $imported_table['data'] );
				// Append visibility information for rows.
				if ( isset( $imported_table['visibility'] ) && isset( $imported_table['visibility']['rows'] ) ) {
					$existing_table['visibility']['rows'] = array_merge( $existing_table['visibility']['rows'], $imported_table['visibility']['rows'] );
				}
				// When appending, do not overwrite options.
				if ( isset( $imported_table['options'] ) ) {
					unset( $imported_table['options'] );
				}
				break;
			default:
				return new WP_Error( 'table_import_import_type_invalid', '', $import_type );
		}

		// Merge new or existing table with information from the imported table.
		$imported_table['id'] = $existing_table['id']; // will be false for new table or the existing table ID
		// Cut visibility array (if the imported table is smaller), and pad correctly if imported table is bigger than existing table (or new template).
		$num_rows = count( $imported_table['data'] );
		$num_columns = count( $imported_table['data'][0] );
		$imported_table['visibility'] = array(
			'rows'    => array_pad( array_slice( $existing_table['visibility']['rows'], 0, $num_rows ), $num_rows, 1 ),
			'columns' => array_pad( array_slice( $existing_table['visibility']['columns'], 0, $num_columns ), $num_columns, 1 ),
		);

		// Check if new data is ok.
		$table = TablePress::$model_table->prepare_table( $existing_table, $imported_table, false );
		if ( is_wp_error( $table ) ) {
			// Add an error code to the existing WP_Error.
			$table->add( 'table_import_table_prepare', '' );
			return $table;
		}

		// DataTables Custom Commands can only be edit by trusted users.
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$table['options']['datatables_custom_commands'] = $existing_table['options']['datatables_custom_commands'];
		}

		// Replace existing table or add new table.
		if ( in_array( $import_type, array( 'replace', 'append' ), true ) ) {
			// Replace existing table with imported/appended table.
			$table_id = TablePress::$model_table->save( $table );
		} else {
			// Add the imported table (and get its first ID).
			$table_id = TablePress::$model_table->add( $table );
		}

		if ( is_wp_error( $table_id ) ) {
			// Add an error code to the existing WP_Error.
			$table_id->add( 'table_import_table_save_or_add', '' );
			return $table_id;
		}

		// Try to use ID from imported file (e.g. in full JSON format table).
		if ( false !== $table_id_in_import && $table_id !== $table_id_in_import && current_user_can( 'tablepress_edit_table_id', $table_id ) ) {
			$id_changed = TablePress::$model_table->change_table_id( $table_id, $table_id_in_import );
			if ( ! is_wp_error( $id_changed ) ) {
				$table_id = $table_id_in_import;
			}
		}

		return $table_id;
	}

	/**
	 * Import data from WP-Table Reloaded from the WordPress database.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $import_tables Whether tables shall be imported.
	 * @param bool $import_css    Whether Plugin Options (only CSS related right now) shall be imported.
	 */
	protected function _import_from_wp_table_reloaded_db( $import_tables, $import_css ) {
		if ( false === get_option( 'wp_table_reloaded_options', false ) || false === get_option( 'wp_table_reloaded_tables', false ) ) {
			TablePress::redirect( array( 'action' => 'import', 'message' => 'error_wp_table_reloaded_not_installed' ) );
		}

		$wp_table_reloaded_options = get_option( 'wp_table_reloaded_options', false );
		if ( empty( $wp_table_reloaded_options ) ) {
			$wp_table_reloaded_options = array();
		}

		// Import WP-Table Reloaded tables.
		$not_imported_tables = $imported_tables = $imported_other_id_tables = array();
		if ( $import_tables ) {
			$wp_table_reloaded_tables_list = get_option( 'wp_table_reloaded_tables', array() );
			foreach ( $wp_table_reloaded_tables_list as $wptr_table_id => $table_option_name ) {
				$wptr_table = get_option( $table_option_name, array() );
				$import_status = $this->_import_wp_table_reloaded_table( $wptr_table, $wp_table_reloaded_options );
				switch ( $import_status ) {
					case 0:
						$not_imported_tables[] = $wptr_table_id;
						break;
					case 1:
						$imported_tables[] = $wptr_table_id;
						break;
					case 2:
						$imported_other_id_tables[] = $wptr_table_id;
						break;
				}
			}
		}

		// Import WP-Table Reloaded Plugin Options (currently only CSS related options).
		$imported_css = false;
		if ( $import_css ) {
			$imported_css = $this->_import_wp_table_reloaded_plugin_options( $wp_table_reloaded_options );
		}

		// @TODO: Better handling of the different cases of imported/imported-without-ID-change/not-imported tables.
		if ( count( $imported_tables ) > 1 ) {
			TablePress::redirect( array( 'action' => 'list', 'message' => 'success_import_wp_table_reloaded' ) );
		} elseif ( 1 === count( $imported_tables ) ) {
			TablePress::redirect( array( 'action' => 'edit', 'table_id' => $imported_tables[0], 'message' => 'success_import_wp_table_reloaded' ) );
		}
		if ( count( $imported_other_id_tables ) > 0 ) {
			TablePress::redirect( array( 'action' => 'list', 'message' => 'success_import_wp_table_reloaded' ) );
		} elseif ( $imported_css ) {
			TablePress::redirect( array( 'action' => 'options', 'message' => 'success_import_wp_table_reloaded' ) );
		} else {
			TablePress::redirect( array( 'action' => 'import', 'message' => 'error_import_wp_table_reloaded' ) );
		}
	}

	/**
	 * Import data from WP-Table Reloaded from a WP-Table Reloaded Dump File.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $import_tables Whether tables shall be imported.
	 * @param bool $import_css    Whether Plugin Options (only CSS related right now) shall be imported.
	 */
	protected function _import_from_wp_table_reloaded_dump_file( $import_tables, $import_css ) {
		if ( empty( $_FILES['import_wp_table_reloaded_file_upload'] ) || empty( $_FILES['import_wp_table_reloaded_file_upload']['tmp_name'] ) || UPLOAD_ERR_OK !== $_FILES['import_wp_table_reloaded_file_upload']['error'] ) {
			TablePress::redirect( array( 'action' => 'import', 'message' => 'error_wp_table_reloaded_dump_file' ) );
		}

		$dump_file = file_get_contents( $_FILES['import_wp_table_reloaded_file_upload']['tmp_name'] );
		$dump_file = unserialize( $dump_file );
		if ( empty( $dump_file ) ) {
			@unlink( $_FILES['import_wp_table_reloaded_file_upload']['tmp_name'] );
			TablePress::redirect( array( 'action' => 'import', 'message' => 'error_wp_table_reloaded_dump_file' ) );
		}
		if ( empty( $dump_file['options'] ) || ! is_array( $dump_file['options'] ) ) {
			$dump_file['options'] = array();
		}

		// Import WP-Table Reloaded tables.
		$not_imported_tables = $imported_tables = $imported_other_id_tables = array();
		if ( $import_tables && ! empty( $dump_file['tables'] ) ) {
			foreach ( $dump_file['tables'] as $wptr_table_id => $wptr_table ) {
				$import_status = $this->_import_wp_table_reloaded_table( $wptr_table, $dump_file['options'] );
				switch ( $import_status ) {
					case 0:
						$not_imported_tables[] = $wptr_table_id;
						break;
					case 1:
						$imported_tables[] = $wptr_table_id;
						break;
					case 2:
						$imported_other_id_tables[] = $wptr_table_id;
						break;
				}
			}
		}

		// Import WP-Table Reloaded Plugin Options (currently only CSS related options).
		$imported_css = false;
		if ( $import_css ) {
			$imported_css = $this->_import_wp_table_reloaded_plugin_options( $dump_file['options'] );
		}

		@unlink( $_FILES['import_wp_table_reloaded_file_upload']['tmp_name'] );
		// @TODO: Better handling of the different cases of imported/imported-without-ID-change/not-imported tables.
		if ( count( $imported_tables ) > 1 ) {
			TablePress::redirect( array( 'action' => 'list', 'message' => 'success_import_wp_table_reloaded' ) );
		} elseif ( 1 === count( $imported_tables ) ) {
			TablePress::redirect( array( 'action' => 'edit', 'table_id' => $imported_tables[0], 'message' => 'success_import_wp_table_reloaded' ) );
		}
		if ( count( $imported_other_id_tables ) > 0 ) {
			TablePress::redirect( array( 'action' => 'list', 'message' => 'success_import_wp_table_reloaded' ) );
		} elseif ( $imported_css ) {
			TablePress::redirect( array( 'action' => 'options', 'message' => 'success_import_wp_table_reloaded' ) );
		} else {
			TablePress::redirect( array( 'action' => 'import', 'message' => 'error_import_wp_table_reloaded' ) );
		}
	}

	/**
	 * Import a WP-Table Reloaded table.
	 *
	 * @since 1.0.0
	 *
	 * @param array $wptr_table                WP-Table Reloaded table.
	 * @param array $wp_table_reloaded_options WP-Table Reloaded Plugin Options.
	 * @return int Import status: 0=Import failed; 1=Imported with ID change; 2=Imported without ID change.
	 */
	protected function _import_wp_table_reloaded_table( array $wptr_table, array $wp_table_reloaded_options ) {
		if ( empty( $wptr_table ) ) {
			return 0; // 0 means Import failed.
		}

		// Perform sanity checks of imported table.
		if ( ! isset( $wptr_table['name'] )
			|| ! isset( $wptr_table['description'] )
			|| empty( $wptr_table['data'] )
			|| empty( $wptr_table['options'] ) ) {
			return 0; // 0 means Import failed.
		}

		// Data is slashed in WP-Table Reloaded.
		$wptr_table = wp_unslash( $wptr_table );

		// Table was loaded, import the data, table options, and visibility.
		//
		// Create a new table array with information from the imported table.
		$new_table = array(
			'name'        => $wptr_table['name'],
			'description' => $wptr_table['description'],
			'data'        => $wptr_table['data'],
			'options'     => array(),
			'visibility'  => array(
				'rows'    => array_fill( 0, count( $wptr_table['data'] ), 1 ),
				'columns' => array_fill( 0, count( $wptr_table['data'][0] ), 1 ),
			),
		);
		if ( isset( $wptr_table['last_modified'] ) ) {
			$new_table['last_modified'] = $wptr_table['last_modified'];
		}
		if ( isset( $wptr_table['last_editor_id'] ) ) {
			$new_table['author'] = $wptr_table['last_editor_id'];
		}
		if ( isset( $wptr_table['options']['last_editor_id'] ) ) {
			$new_table['options']['last_editor'] = $wptr_table['last_editor_id'];
		}
		if ( isset( $wptr_table['options']['first_row_th'] ) ) {
			$new_table['options']['table_head'] = $wptr_table['options']['first_row_th'];
		}
		if ( isset( $wptr_table['options']['table_footer'] ) ) {
			$new_table['options']['table_foot'] = $wptr_table['options']['table_footer'];
		}
		if ( isset( $wptr_table['options']['custom_css_class'] ) ) {
			$new_table['options']['extra_css_classes'] = $wptr_table['options']['custom_css_class'];
		}
		if ( isset( $wptr_table['options']['use_tablesorter'] ) && isset( $wp_table_reloaded_options['enable_tablesorter'] ) ) {
			if ( $wp_table_reloaded_options['enable_tablesorter'] ) {
				$new_table['options']['use_datatables'] = $wptr_table['options']['use_tablesorter'];
			} else {
				$new_table['options']['use_datatables'] = false;
			}
		}
		// Array key is the same in both plugins for the following options.
		foreach ( array(
			'alternating_row_colors',
			'row_hover',
			'print_name',
			'print_name_position',
			'print_description',
			'print_description_position',
			'datatables_sort',
			'datatables_filter',
			'datatables_paginate',
			'datatables_lengthchange',
			'datatables_paginate_entries',
			'datatables_info',
		) as $_option ) {
			if ( isset( $wptr_table['options'][ $_option ] ) ) {
				$new_table['options'][ $_option ] = $wptr_table['options'][ $_option ];
			}
		}
		if ( isset( $wptr_table['options']['datatables_customcommands'] ) && current_user_can( 'unfiltered_html' ) ) {
			$new_table['options']['datatables_custom_commands'] = $wptr_table['options']['datatables_customcommands'];
		}
		// Not imported: $wptr_table['options']['cache_table_output'].
		// Not imported: $wptr_table['custom_fields'].

		// Fix visibility: WP-Table Reloaded uses 0 and 1 the other way around.
		foreach ( array_keys( $wptr_table['visibility']['rows'], true ) as $row_idx ) {
			$new_table['visibility']['rows'][ $row_idx ] = 0;
		}
		foreach ( array_keys( $wptr_table['visibility']['columns'], true ) as $column_idx ) {
			$new_table['visibility']['columns'][ $column_idx ] = 0;
		}

		// Merge this data into an empty table template.
		$table = TablePress::$model_table->prepare_table( TablePress::$model_table->get_table_template(), $new_table, false );
		if ( is_wp_error( $table ) ) {
			return 0; // 0 means Import failed.
		}

		// Add the new table (and get its first ID).
		$tp_table_id = TablePress::$model_table->add( $table );
		if ( is_wp_error( $tp_table_id ) ) {
			return 0; // 0 means Import failed.
		}

		// Change table ID to the ID the table had in WP-Table Reloaded (except if that ID is already taken).
		$id_changed = TablePress::$model_table->change_table_id( $tp_table_id, $wptr_table['id'] );
		if ( is_wp_error( $id_changed ) ) {
			return 2; // 2 means Imported without ID change.
		}

		return 1; // 1 means Imported with ID change.
	}

	/**
	 * Import WP-Table Reloaded Plugin Options (currently just CSS related options).
	 *
	 * @since 1.0.0
	 *
	 * @param array $wp_table_reloaded_options Plugin Options of WP-Table Reloaded that shall be imported.
	 * @return bool Whether the import was successful or not (on at least on option).
	 */
	protected function _import_wp_table_reloaded_plugin_options( array $wp_table_reloaded_options ) {
		if ( ! current_user_can( 'tablepress_edit_options' ) ) {
			return false;
		}

		$imported_options = array();
		$imported_options['use_custom_css'] = ( isset( $wp_table_reloaded_options['use_custom_css'] ) ) ? (bool) $wp_table_reloaded_options['use_custom_css'] : false;
		if ( isset( $wp_table_reloaded_options['custom_css'] ) ) {
			// Automatically convert WP-Table Reloaded Custom CSS to TablePress Custom CSS by search/replacing classes and IDs.
			$imported_options['custom_css'] = wp_unslash( $wp_table_reloaded_options['custom_css'] ); // Be careful when removing this, as it might break CSS comments from old Dump Files.
			$imported_options['custom_css'] = str_replace( '#wp-table-reloaded-id-', '#tablepress-', $imported_options['custom_css'] );
			$imported_options['custom_css'] = str_replace( '-no-1', '', $imported_options['custom_css'] );
			$imported_options['custom_css'] = str_replace( '.wp-table-reloaded-id-', '.tablepress-id-', $imported_options['custom_css'] );
			$imported_options['custom_css'] = str_replace( '.wp-table-reloaded', '.tablepress', $imported_options['custom_css'] );

			$tablepress_css = TablePress::load_class( 'TablePress_CSS', 'class-css.php', 'classes' );
			// Sanitize and tidy up Custom CSS.
			$imported_options['custom_css'] = $tablepress_css->sanitize_css( $imported_options['custom_css'] );
			// Minify Custom CSS.
			$imported_options['custom_css_minified'] = $tablepress_css->minify_css( $imported_options['custom_css'] );

			// Maybe update CSS files as well.
			//
			// Only write files, if "Custom CSS" is to be used, and if there is "Custom CSS"
			if ( $imported_options['use_custom_css'] && '' !== $imported_options['custom_css'] ) {
				$result = $tablepress_css->save_custom_css_to_file( $imported_options['custom_css'], $imported_options['custom_css_minified'] );
				// If saving was successful, use "Custom CSS" file.
				$imported_options['use_custom_css_file'] = $result;
				// If saving was successful, increase the "Custom CSS" version number for cache busting.
				if ( $result ) {
					$imported_options['custom_css_version'] = TablePress::$model_options->get( 'custom_css_version' ) + 1;
				}
			}
		}

		// Save gathered imported options.
		if ( empty( $imported_options ) ) {
			return false;
		}

		TablePress::$model_options->update( $imported_options );

		return true;
	}

	/*
	 * Save GET actions.
	 */

	/**
	 * Hide a header message on an admin screen.
	 *
	 * @since 1.0.0
	 */
	public function handle_get_action_hide_message() {
		$message_item = ! empty( $_GET['item'] ) ? $_GET['item'] : '';
		TablePress::check_nonce( 'hide_message', $message_item );

		if ( ! current_user_can( 'tablepress_list_tables' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
		}

		TablePress::$model_options->update( "message_{$message_item}", false );

		$return = ! empty( $_GET['return'] ) ? $_GET['return'] : 'list';
		TablePress::redirect( array( 'action' => $return ) );
	}

	/**
	 * Delete a table.
	 *
	 * @since 1.0.0
	 */
	public function handle_get_action_delete_table() {
		$table_id = ( ! empty( $_GET['item'] ) ) ? $_GET['item'] : false;
		TablePress::check_nonce( 'delete_table', $table_id );

		$return = ! empty( $_GET['return'] ) ? $_GET['return'] : 'list';
		$return_item = ! empty( $_GET['return_item'] ) ? $_GET['return_item'] : false;

		// Nonce check should actually catch this already.
		if ( false === $table_id ) {
			TablePress::redirect( array( 'action' => $return, 'message' => 'error_delete', 'table_id' => $return_item ) );
		}

		if ( ! current_user_can( 'tablepress_delete_table', $table_id ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
		}

		$deleted = TablePress::$model_table->delete( $table_id );
		if ( is_wp_error( $deleted ) ) {
			TablePress::redirect( array( 'action' => $return, 'message' => 'error_delete', 'table_id' => $return_item ) );
		}

		/*
		 * Slightly more complex redirect method, to account for sort, search, and pagination in the WP_List_Table on the List View,
		 * but only if this action succeeds, to have everything fresh in the event of an error.
		 */
		$sendback = wp_get_referer();
		if ( ! $sendback ) {
			$sendback = TablePress::url( array( 'action' => 'list', 'message' => 'success_delete', 'table_id' => $return_item ) );
		} else {
			$sendback = remove_query_arg( array( 'action', 'message', 'table_id' ), $sendback );
			$sendback = add_query_arg( array( 'action' => 'list', 'message' => 'success_delete', 'table_id' => $return_item ), $sendback );
		}
		wp_redirect( $sendback );
		exit;
	}

	/**
	 * Copy a table.
	 *
	 * @since 1.0.0
	 */
	public function handle_get_action_copy_table() {
		$table_id = ( ! empty( $_GET['item'] ) ) ? $_GET['item'] : false;
		TablePress::check_nonce( 'copy_table', $table_id );

		$return = ! empty( $_GET['return'] ) ? $_GET['return'] : 'list';
		$return_item = ! empty( $_GET['return_item'] ) ? $_GET['return_item'] : false;

		// Nonce check should actually catch this already.
		if ( false === $table_id ) {
			TablePress::redirect( array( 'action' => $return, 'message' => 'error_copy', 'table_id' => $return_item ) );
		}

		if ( ! current_user_can( 'tablepress_copy_table', $table_id ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
		}

		$this->init_i18n_support(); // for the translation of "Copy of".

		$copy_table_id = TablePress::$model_table->copy( $table_id );
		if ( is_wp_error( $copy_table_id ) ) {
			TablePress::redirect( array( 'action' => $return, 'message' => 'error_copy', 'table_id' => $return_item ) );
		} else {
			$return_item = $copy_table_id;
		}

		/*
		 * Slightly more complex redirect method, to account for sort, search, and pagination in the WP_List_Table on the List View,
		 * but only if this action succeeds, to have everything fresh in the event of an error.
		 */
		$sendback = wp_get_referer();
		if ( ! $sendback ) {
			$sendback = TablePress::url( array( 'action' => $return, 'message' => 'success_copy', 'table_id' => $return_item ) );
		} else {
			$sendback = remove_query_arg( array( 'action', 'message', 'table_id' ), $sendback );
			$sendback = add_query_arg( array( 'action' => $return, 'message' => 'success_copy', 'table_id' => $return_item ), $sendback );
		}
		wp_redirect( $sendback );
		exit;
	}

	/**
	 * Preview a table.
	 *
	 * @since 1.0.0
	 */
	public function handle_get_action_preview_table() {
		$table_id = ( ! empty( $_GET['item'] ) ) ? $_GET['item'] : false;
		TablePress::check_nonce( 'preview_table', $table_id );

		$this->init_i18n_support();

		// Nonce check should actually catch this already.
		if ( false === $table_id ) {
			wp_die( __( 'The preview could not be loaded.', 'tablepress' ), __( 'Preview', 'tablepress' ) );
		}

		if ( ! current_user_can( 'tablepress_preview_table', $table_id ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
		}

		// Load table, with table data, options, and visibility settings.
		$table = TablePress::$model_table->load( $table_id, true, true );
		if ( is_wp_error( $table ) ) {
			wp_die( __( 'The table could not be loaded.', 'tablepress' ), __( 'Preview', 'tablepress' ) );
		}

		// Sanitize all table data to remove unsafe HTML from the preview output.
		$table = TablePress::$model_table->sanitize( $table );

		// Create a render class instance.
		$_render = TablePress::load_class( 'TablePress_Render', 'class-render.php', 'classes' );
		// Merge desired options with default render options (see TablePress_Controller_Frontend::shortcode_table()).
		$default_render_options = $_render->get_default_render_options();
		/** This filter is documented in controllers/controller-frontend.php */
		$default_render_options = apply_filters( 'tablepress_shortcode_table_default_shortcode_atts', $default_render_options );
		$render_options = shortcode_atts( $default_render_options, $table['options'] );
		/** This filter is documented in controllers/controller-frontend.php */
		$render_options = apply_filters( 'tablepress_shortcode_table_shortcode_atts', $render_options );
		$_render->set_input( $table, $render_options );
		$view_data = array(
			'table_id'  => $table_id,
			'head_html' => $_render->get_preview_css(),
			'body_html' => $_render->get_output(),
		);

		$custom_css = TablePress::$model_options->get( 'custom_css' );
		if ( ! empty( $custom_css ) ) {
			$view_data['head_html'] .= "<style type=\"text/css\">\n{$custom_css}\n</style>\n";
		}

		// Prepare, initialize, and render the view.
		$this->view = TablePress::load_view( 'preview_table', $view_data );
		$this->view->render();
	}

	/**
	 * Show a list of tables in the Editor toolbar Thickbox (opened by TinyMCE or Quicktags button).
	 *
	 * @since 1.0.0
	 */
	public function handle_get_action_editor_button_thickbox() {
		TablePress::check_nonce( 'editor_button_thickbox' );

		if ( ! current_user_can( 'tablepress_list_tables' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
		}

		$this->init_i18n_support();

		$view_data = array(
			// Load all table IDs without priming the post meta cache, as table options/visibility are not needed.
			'table_ids' => TablePress::$model_table->load_all( false ),
		);

		set_current_screen( 'tablepress_editor_button_thickbox' );

		// Prepare, initialize, and render the view.
		$this->view = TablePress::load_view( 'editor_button_thickbox', $view_data );
		$this->view->render();
	}

	/**
	 * Uninstall TablePress, and delete all tables and options.
	 *
	 * @since 1.0.0
	 */
	public function handle_get_action_uninstall_tablepress() {
		TablePress::check_nonce( 'uninstall_tablepress' );

		$plugin = TABLEPRESS_BASENAME;

		if ( ! current_user_can( 'deactivate_plugin', $plugin ) || ! current_user_can( 'tablepress_edit_options' ) || ! current_user_can( 'tablepress_delete_tables' ) || is_plugin_active_for_network( $plugin ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'default' ), 403 );
		}

		// Deactivate TablePress for the site (but not for the network).
		deactivate_plugins( $plugin, false, false );
		update_option( 'recently_activated', array( $plugin => time() ) + (array) get_option( 'recently_activated', array() ) );

		// Delete all tables, "Custom CSS" files, and options.
		TablePress::$model_table->delete_all();
		$tablepress_css = TablePress::load_class( 'TablePress_CSS', 'class-css.php', 'classes' );
		$css_files_deleted = $tablepress_css->delete_custom_css_files();
		TablePress::$model_options->remove_access_capabilities();

		TablePress::$model_table->destroy();
		TablePress::$model_options->destroy();

		$this->init_i18n_support();

		$output = '<strong>' . __( 'TablePress was uninstalled successfully.', 'tablepress' ) . '</strong><br /><br />';
		$output .= __( 'All tables, data, and options were deleted.', 'tablepress' );
		if ( is_multisite() ) {
			$output .= ' ' . __( 'You may now ask the network admin to delete the plugin&#8217;s folder <code>tablepress</code> from the server, if no other site in the network uses it.', 'tablepress' );
		} else {
			$output .= ' ' . __( 'You may now manually delete the plugin&#8217;s folder <code>tablepress</code> from the <code>plugins</code> directory on your server or use the &#8220;Delete&#8221; link for TablePress on the WordPress &#8220;Plugins&#8221; page.', 'tablepress' );
		}
		if ( $css_files_deleted ) {
			$output .= ' ' . __( 'Your TablePress &#8220;Custom CSS&#8221; files have been deleted automatically.', 'tablepress' );
		} else {
			if ( is_multisite() ) {
				$output .= ' ' . __( 'Please also ask him to delete your TablePress &#8220;Custom CSS&#8221; files from the server.', 'tablepress' );
			} else {
				$output .= ' ' . __( 'You may now also delete your TablePress &#8220;Custom CSS&#8221; files in the <code>wp-content</code> folder.', 'tablepress' );
			}
		}
		$output .= "</p>\n<p>";
		if ( ! is_multisite() || is_super_admin() ) {
			$output .= '<a class="button" href="' . esc_url( admin_url( 'plugins.php' ) ) . '">' . __( 'Go to &#8220;Plugins&#8221; page', 'tablepress' ) . '</a> ';
		}
		$output .= '<a class="button" href="' . esc_url( admin_url( 'index.php' ) ) . '">' . __( 'Go to Dashboard', 'tablepress' ) . '</a>';

		wp_die( $output, __( 'Uninstall TablePress', 'tablepress' ), array( 'response' => 200, 'back_link' => false ) );
	}

} // class TablePress_Admin_Controller

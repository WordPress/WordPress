<?php
/**
 * List Table API: WP_Plugins_List_Table class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 3.1.0
 */

/**
 * Core class used to implement displaying installed plugins in a list table.
 *
 * @since 3.1.0
 *
 * @see WP_List_Table
 */
class WP_Plugins_List_Table extends WP_List_Table {
	/**
	 * Whether to show the auto-updates UI.
	 *
	 * @since 5.5.0
	 *
	 * @var bool True if auto-updates UI is to be shown, false otherwise.
	 */
	protected $show_autoupdates = true;

	/**
	 * Constructor.
	 *
	 * @since 3.1.0
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @global string $status
	 * @global int    $page
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		global $status, $page;

		parent::__construct(
			array(
				'plural' => 'plugins',
				'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			)
		);

		$allowed_statuses = array( 'active', 'inactive', 'recently_activated', 'upgrade', 'mustuse', 'dropins', 'search', 'paused', 'auto-update-enabled', 'auto-update-disabled' );

		$status = 'all';
		if ( isset( $_REQUEST['plugin_status'] ) && in_array( $_REQUEST['plugin_status'], $allowed_statuses, true ) ) {
			$status = $_REQUEST['plugin_status'];
		}

		if ( isset( $_REQUEST['s'] ) ) {
			$_SERVER['REQUEST_URI'] = add_query_arg( 's', wp_unslash( $_REQUEST['s'] ) );
		}

		$page = $this->get_pagenum();

		$this->show_autoupdates = wp_is_auto_update_enabled_for_type( 'plugin' )
			&& current_user_can( 'update_plugins' )
			&& ( ! is_multisite() || $this->screen->in_admin( 'network' ) );
	}

	/**
	 * @return array
	 */
	protected function get_table_classes() {
		return array( 'widefat', $this->_args['plural'] );
	}

	/**
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can( 'activate_plugins' );
	}

	/**
	 * @global string $status
	 * @global array  $plugins
	 * @global array  $totals
	 * @global int    $page
	 * @global string $orderby
	 * @global string $order
	 * @global string $s
	 */
	public function prepare_items() {
		global $status, $plugins, $totals, $page, $orderby, $order, $s;

		$orderby = ! empty( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : '';
		$order   = ! empty( $_REQUEST['order'] ) ? sanitize_text_field( $_REQUEST['order'] ) : '';

		/**
		 * Filters the full array of plugins to list in the Plugins list table.
		 *
		 * @since 3.0.0
		 *
		 * @see get_plugins()
		 *
		 * @param array $all_plugins An array of plugins to display in the list table.
		 */
		$all_plugins = apply_filters( 'all_plugins', get_plugins() );

		$plugins = array(
			'all'                => $all_plugins,
			'search'             => array(),
			'active'             => array(),
			'inactive'           => array(),
			'recently_activated' => array(),
			'upgrade'            => array(),
			'mustuse'            => array(),
			'dropins'            => array(),
			'paused'             => array(),
		);
		if ( $this->show_autoupdates ) {
			$auto_updates = (array) get_site_option( 'auto_update_plugins', array() );

			$plugins['auto-update-enabled']  = array();
			$plugins['auto-update-disabled'] = array();
		}

		$screen = $this->screen;

		if ( ! is_multisite() || ( $screen->in_admin( 'network' ) && current_user_can( 'manage_network_plugins' ) ) ) {

			/**
			 * Filters whether to display the advanced plugins list table.
			 *
			 * There are two types of advanced plugins - must-use and drop-ins -
			 * which can be used in a single site or Multisite network.
			 *
			 * The $type parameter allows you to differentiate between the type of advanced
			 * plugins to filter the display of. Contexts include 'mustuse' and 'dropins'.
			 *
			 * @since 3.0.0
			 *
			 * @param bool   $show Whether to show the advanced plugins for the specified
			 *                     plugin type. Default true.
			 * @param string $type The plugin type. Accepts 'mustuse', 'dropins'.
			 */
			if ( apply_filters( 'show_advanced_plugins', true, 'mustuse' ) ) {
				$plugins['mustuse'] = get_mu_plugins();
			}

			/** This action is documented in wp-admin/includes/class-wp-plugins-list-table.php */
			if ( apply_filters( 'show_advanced_plugins', true, 'dropins' ) ) {
				$plugins['dropins'] = get_dropins();
			}

			if ( current_user_can( 'update_plugins' ) ) {
				$current = get_site_transient( 'update_plugins' );
				foreach ( (array) $plugins['all'] as $plugin_file => $plugin_data ) {
					if ( isset( $current->response[ $plugin_file ] ) ) {
						$plugins['all'][ $plugin_file ]['update'] = true;
						$plugins['upgrade'][ $plugin_file ]       = $plugins['all'][ $plugin_file ];
					}
				}
			}
		}

		if ( ! $screen->in_admin( 'network' ) ) {
			$show = current_user_can( 'manage_network_plugins' );
			/**
			 * Filters whether to display network-active plugins alongside plugins active for the current site.
			 *
			 * This also controls the display of inactive network-only plugins (plugins with
			 * "Network: true" in the plugin header).
			 *
			 * Plugins cannot be network-activated or network-deactivated from this screen.
			 *
			 * @since 4.4.0
			 *
			 * @param bool $show Whether to show network-active plugins. Default is whether the current
			 *                   user can manage network plugins (ie. a Super Admin).
			 */
			$show_network_active = apply_filters( 'show_network_active_plugins', $show );
		}

		if ( $screen->in_admin( 'network' ) ) {
			$recently_activated = get_site_option( 'recently_activated', array() );
		} else {
			$recently_activated = get_option( 'recently_activated', array() );
		}

		foreach ( $recently_activated as $key => $time ) {
			if ( $time + WEEK_IN_SECONDS < time() ) {
				unset( $recently_activated[ $key ] );
			}
		}

		if ( $screen->in_admin( 'network' ) ) {
			update_site_option( 'recently_activated', $recently_activated );
		} else {
			update_option( 'recently_activated', $recently_activated );
		}

		$plugin_info = get_site_transient( 'update_plugins' );

		foreach ( (array) $plugins['all'] as $plugin_file => $plugin_data ) {
			// Extra info if known. array_merge() ensures $plugin_data has precedence if keys collide.
			if ( isset( $plugin_info->response[ $plugin_file ] ) ) {
				$plugin_data = array_merge( (array) $plugin_info->response[ $plugin_file ], array( 'update-supported' => true ), $plugin_data );
			} elseif ( isset( $plugin_info->no_update[ $plugin_file ] ) ) {
				$plugin_data = array_merge( (array) $plugin_info->no_update[ $plugin_file ], array( 'update-supported' => true ), $plugin_data );
			} elseif ( empty( $plugin_data['update-supported'] ) ) {
				$plugin_data['update-supported'] = false;
			}

			/*
			 * Create the payload that's used for the auto_update_plugin filter.
			 * This is the same data contained within $plugin_info->(response|no_update) however
			 * not all plugins will be contained in those keys, this avoids unexpected warnings.
			 */
			$filter_payload = array(
				'id'            => $plugin_file,
				'slug'          => '',
				'plugin'        => $plugin_file,
				'new_version'   => '',
				'url'           => '',
				'package'       => '',
				'icons'         => array(),
				'banners'       => array(),
				'banners_rtl'   => array(),
				'tested'        => '',
				'requires_php'  => '',
				'compatibility' => new stdClass(),
			);

			$filter_payload = (object) wp_parse_args( $plugin_data, $filter_payload );

			$auto_update_forced = wp_is_auto_update_forced_for_item( 'plugin', null, $filter_payload );

			if ( ! is_null( $auto_update_forced ) ) {
				$plugin_data['auto-update-forced'] = $auto_update_forced;
			}

			$plugins['all'][ $plugin_file ] = $plugin_data;
			// Make sure that $plugins['upgrade'] also receives the extra info since it is used on ?plugin_status=upgrade.
			if ( isset( $plugins['upgrade'][ $plugin_file ] ) ) {
				$plugins['upgrade'][ $plugin_file ] = $plugin_data;
			}

			// Filter into individual sections.
			if ( is_multisite() && ! $screen->in_admin( 'network' ) && is_network_only_plugin( $plugin_file ) && ! is_plugin_active( $plugin_file ) ) {
				if ( $show_network_active ) {
					// On the non-network screen, show inactive network-only plugins if allowed.
					$plugins['inactive'][ $plugin_file ] = $plugin_data;
				} else {
					// On the non-network screen, filter out network-only plugins as long as they're not individually active.
					unset( $plugins['all'][ $plugin_file ] );
				}
			} elseif ( ! $screen->in_admin( 'network' ) && is_plugin_active_for_network( $plugin_file ) ) {
				if ( $show_network_active ) {
					// On the non-network screen, show network-active plugins if allowed.
					$plugins['active'][ $plugin_file ] = $plugin_data;
				} else {
					// On the non-network screen, filter out network-active plugins.
					unset( $plugins['all'][ $plugin_file ] );
				}
			} elseif ( ( ! $screen->in_admin( 'network' ) && is_plugin_active( $plugin_file ) )
				|| ( $screen->in_admin( 'network' ) && is_plugin_active_for_network( $plugin_file ) ) ) {
				/*
				 * On the non-network screen, populate the active list with plugins that are individually activated.
				 * On the network admin screen, populate the active list with plugins that are network-activated.
				 */
				$plugins['active'][ $plugin_file ] = $plugin_data;

				if ( ! $screen->in_admin( 'network' ) && is_plugin_paused( $plugin_file ) ) {
					$plugins['paused'][ $plugin_file ] = $plugin_data;
				}
			} else {
				if ( isset( $recently_activated[ $plugin_file ] ) ) {
					// Populate the recently activated list with plugins that have been recently activated.
					$plugins['recently_activated'][ $plugin_file ] = $plugin_data;
				}
				// Populate the inactive list with plugins that aren't activated.
				$plugins['inactive'][ $plugin_file ] = $plugin_data;
			}

			if ( $this->show_autoupdates ) {
				$enabled = in_array( $plugin_file, $auto_updates, true ) && $plugin_data['update-supported'];
				if ( isset( $plugin_data['auto-update-forced'] ) ) {
					$enabled = (bool) $plugin_data['auto-update-forced'];
				}

				if ( $enabled ) {
					$plugins['auto-update-enabled'][ $plugin_file ] = $plugin_data;
				} else {
					$plugins['auto-update-disabled'][ $plugin_file ] = $plugin_data;
				}
			}
		}

		if ( strlen( $s ) ) {
			$status            = 'search';
			$plugins['search'] = array_filter( $plugins['all'], array( $this, '_search_callback' ) );
		}

		/**
		 * Filters the array of plugins for the list table.
		 *
		 * @since 6.3.0
		 *
		 * @param array[] $plugins An array of arrays of plugin data, keyed by context.
		 */
		$plugins = apply_filters( 'plugins_list', $plugins );

		$totals = array();
		foreach ( $plugins as $type => $list ) {
			$totals[ $type ] = count( $list );
		}

		if ( empty( $plugins[ $status ] ) && ! in_array( $status, array( 'all', 'search' ), true ) ) {
			$status = 'all';
		}

		$this->items = array();
		foreach ( $plugins[ $status ] as $plugin_file => $plugin_data ) {
			// Translate, don't apply markup, sanitize HTML.
			$this->items[ $plugin_file ] = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
		}

		$total_this_page = $totals[ $status ];

		$js_plugins = array();
		foreach ( $plugins as $key => $list ) {
			$js_plugins[ $key ] = array_keys( $list );
		}

		wp_localize_script(
			'updates',
			'_wpUpdatesItemCounts',
			array(
				'plugins' => $js_plugins,
				'totals'  => wp_get_update_data(),
			)
		);

		if ( ! $orderby ) {
			$orderby = 'Name';
		} else {
			$orderby = ucfirst( $orderby );
		}

		$order = strtoupper( $order );

		uasort( $this->items, array( $this, '_order_callback' ) );

		$plugins_per_page = $this->get_items_per_page( str_replace( '-', '_', $screen->id . '_per_page' ), 999 );

		$start = ( $page - 1 ) * $plugins_per_page;

		if ( $total_this_page > $plugins_per_page ) {
			$this->items = array_slice( $this->items, $start, $plugins_per_page );
		}

		$this->set_pagination_args(
			array(
				'total_items' => $total_this_page,
				'per_page'    => $plugins_per_page,
			)
		);
	}

	/**
	 * @global string $s URL encoded search term.
	 *
	 * @param array $plugin
	 * @return bool
	 */
	public function _search_callback( $plugin ) {
		global $s;

		foreach ( $plugin as $value ) {
			if ( is_string( $value ) && false !== stripos( strip_tags( $value ), urldecode( $s ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @global string $orderby
	 * @global string $order
	 * @param array $plugin_a
	 * @param array $plugin_b
	 * @return int
	 */
	public function _order_callback( $plugin_a, $plugin_b ) {
		global $orderby, $order;

		$a = $plugin_a[ $orderby ];
		$b = $plugin_b[ $orderby ];

		if ( $a === $b ) {
			return 0;
		}

		if ( 'DESC' === $order ) {
			return strcasecmp( $b, $a );
		} else {
			return strcasecmp( $a, $b );
		}
	}

	/**
	 * @global array $plugins
	 */
	public function no_items() {
		global $plugins;

		if ( ! empty( $_REQUEST['s'] ) ) {
			$s = esc_html( urldecode( wp_unslash( $_REQUEST['s'] ) ) );

			/* translators: %s: Plugin search term. */
			printf( __( 'No plugins found for: %s.' ), '<strong>' . $s . '</strong>' );

			// We assume that somebody who can install plugins in multisite is experienced enough to not need this helper link.
			if ( ! is_multisite() && current_user_can( 'install_plugins' ) ) {
				echo ' <a href="' . esc_url( admin_url( 'plugin-install.php?tab=search&s=' . urlencode( $s ) ) ) . '">' . __( 'Search for plugins in the WordPress Plugin Directory.' ) . '</a>';
			}
		} elseif ( ! empty( $plugins['all'] ) ) {
			_e( 'No plugins found.' );
		} else {
			_e( 'No plugins are currently available.' );
		}
	}

	/**
	 * Displays the search box.
	 *
	 * @since 4.6.0
	 *
	 * @param string $text     The 'submit' button label.
	 * @param string $input_id ID attribute value for the search input field.
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
			return;
		}

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}
		?>
		<p class="search-box">
			<label for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?></label>
			<input type="search" id="<?php echo esc_attr( $input_id ); ?>" class="wp-filter-search" name="s" value="<?php _admin_search_query(); ?>" />
			<?php submit_button( $text, 'hide-if-js', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
		<?php
	}

	/**
	 * @global string $status
	 *
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns() {
		global $status;

		$columns = array(
			'cb'          => ! in_array( $status, array( 'mustuse', 'dropins' ), true ) ? '<input type="checkbox" />' : '',
			'name'        => __( 'Plugin' ),
			'description' => __( 'Description' ),
		);

		if ( $this->show_autoupdates && ! in_array( $status, array( 'mustuse', 'dropins' ), true ) ) {
			$columns['auto-updates'] = __( 'Automatic Updates' );
		}

		return $columns;
	}

	/**
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array();
	}

	/**
	 * @global array $totals
	 * @global string $status
	 * @return array
	 */
	protected function get_views() {
		global $totals, $status;

		$status_links = array();
		foreach ( $totals as $type => $count ) {
			if ( ! $count ) {
				continue;
			}

			switch ( $type ) {
				case 'all':
					/* translators: %s: Number of plugins. */
					$text = _nx(
						'All <span class="count">(%s)</span>',
						'All <span class="count">(%s)</span>',
						$count,
						'plugins'
					);
					break;
				case 'active':
					/* translators: %s: Number of plugins. */
					$text = _n(
						'Active <span class="count">(%s)</span>',
						'Active <span class="count">(%s)</span>',
						$count
					);
					break;
				case 'recently_activated':
					/* translators: %s: Number of plugins. */
					$text = _n(
						'Recently Active <span class="count">(%s)</span>',
						'Recently Active <span class="count">(%s)</span>',
						$count
					);
					break;
				case 'inactive':
					/* translators: %s: Number of plugins. */
					$text = _n(
						'Inactive <span class="count">(%s)</span>',
						'Inactive <span class="count">(%s)</span>',
						$count
					);
					break;
				case 'mustuse':
					/* translators: %s: Number of plugins. */
					$text = _n(
						'Must-Use <span class="count">(%s)</span>',
						'Must-Use <span class="count">(%s)</span>',
						$count
					);
					break;
				case 'dropins':
					/* translators: %s: Number of plugins. */
					$text = _n(
						'Drop-in <span class="count">(%s)</span>',
						'Drop-ins <span class="count">(%s)</span>',
						$count
					);
					break;
				case 'paused':
					/* translators: %s: Number of plugins. */
					$text = _n(
						'Paused <span class="count">(%s)</span>',
						'Paused <span class="count">(%s)</span>',
						$count
					);
					break;
				case 'upgrade':
					/* translators: %s: Number of plugins. */
					$text = _n(
						'Update Available <span class="count">(%s)</span>',
						'Update Available <span class="count">(%s)</span>',
						$count
					);
					break;
				case 'auto-update-enabled':
					/* translators: %s: Number of plugins. */
					$text = _n(
						'Auto-updates Enabled <span class="count">(%s)</span>',
						'Auto-updates Enabled <span class="count">(%s)</span>',
						$count
					);
					break;
				case 'auto-update-disabled':
					/* translators: %s: Number of plugins. */
					$text = _n(
						'Auto-updates Disabled <span class="count">(%s)</span>',
						'Auto-updates Disabled <span class="count">(%s)</span>',
						$count
					);
					break;
			}

			if ( 'search' !== $type ) {
				$status_links[ $type ] = array(
					'url'     => add_query_arg( 'plugin_status', $type, 'plugins.php' ),
					'label'   => sprintf( $text, number_format_i18n( $count ) ),
					'current' => $type === $status,
				);
			}
		}

		return $this->get_views_links( $status_links );
	}

	/**
	 * @global string $status
	 * @return array
	 */
	protected function get_bulk_actions() {
		global $status;

		$actions = array();

		if ( 'active' !== $status ) {
			$actions['activate-selected'] = $this->screen->in_admin( 'network' ) ? _x( 'Network Activate', 'plugin' ) : _x( 'Activate', 'plugin' );
		}

		if ( 'inactive' !== $status && 'recent' !== $status ) {
			$actions['deactivate-selected'] = $this->screen->in_admin( 'network' ) ? _x( 'Network Deactivate', 'plugin' ) : _x( 'Deactivate', 'plugin' );
		}

		if ( ! is_multisite() || $this->screen->in_admin( 'network' ) ) {
			if ( current_user_can( 'update_plugins' ) ) {
				$actions['update-selected'] = __( 'Update' );
			}

			if ( current_user_can( 'delete_plugins' ) && ( 'active' !== $status ) ) {
				$actions['delete-selected'] = __( 'Delete' );
			}

			if ( $this->show_autoupdates ) {
				if ( 'auto-update-enabled' !== $status ) {
					$actions['enable-auto-update-selected'] = __( 'Enable Auto-updates' );
				}
				if ( 'auto-update-disabled' !== $status ) {
					$actions['disable-auto-update-selected'] = __( 'Disable Auto-updates' );
				}
			}
		}

		return $actions;
	}

	/**
	 * @global string $status
	 * @param string $which
	 */
	public function bulk_actions( $which = '' ) {
		global $status;

		if ( in_array( $status, array( 'mustuse', 'dropins' ), true ) ) {
			return;
		}

		parent::bulk_actions( $which );
	}

	/**
	 * @global string $status
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		global $status;

		if ( ! in_array( $status, array( 'recently_activated', 'mustuse', 'dropins' ), true ) ) {
			return;
		}

		echo '<div class="alignleft actions">';

		if ( 'recently_activated' === $status ) {
			submit_button( __( 'Clear List' ), '', 'clear-recent-list', false );
		} elseif ( 'top' === $which && 'mustuse' === $status ) {
			echo '<p>' . sprintf(
				/* translators: %s: mu-plugins directory name. */
				__( 'Files in the %s directory are executed automatically.' ),
				'<code>' . str_replace( ABSPATH, '/', WPMU_PLUGIN_DIR ) . '</code>'
			) . '</p>';
		} elseif ( 'top' === $which && 'dropins' === $status ) {
			echo '<p>' . sprintf(
				/* translators: %s: wp-content directory name. */
				__( 'Drop-ins are single files, found in the %s directory, that replace or enhance WordPress features in ways that are not possible for traditional plugins.' ),
				'<code>' . str_replace( ABSPATH, '', WP_CONTENT_DIR ) . '</code>'
			) . '</p>';
		}
		echo '</div>';
	}

	/**
	 * @return string
	 */
	public function current_action() {
		if ( isset( $_POST['clear-recent-list'] ) ) {
			return 'clear-recent-list';
		}

		return parent::current_action();
	}

	/**
	 * @global string $status
	 */
	public function display_rows() {
		global $status;

		if ( is_multisite() && ! $this->screen->in_admin( 'network' ) && in_array( $status, array( 'mustuse', 'dropins' ), true ) ) {
			return;
		}

		foreach ( $this->items as $plugin_file => $plugin_data ) {
			$this->single_row( array( $plugin_file, $plugin_data ) );
		}
	}

	/**
	 * @global string $status
	 * @global int $page
	 * @global string $s
	 * @global array $totals
	 *
	 * @param array $item
	 */
	public function single_row( $item ) {
		global $status, $page, $s, $totals;
		static $plugin_id_attrs = array();

		list( $plugin_file, $plugin_data ) = $item;

		$plugin_slug    = isset( $plugin_data['slug'] ) ? $plugin_data['slug'] : sanitize_title( $plugin_data['Name'] );
		$plugin_id_attr = $plugin_slug;

		// Ensure the ID attribute is unique.
		$suffix = 2;
		while ( in_array( $plugin_id_attr, $plugin_id_attrs, true ) ) {
			$plugin_id_attr = "$plugin_slug-$suffix";
			++$suffix;
		}

		$plugin_id_attrs[] = $plugin_id_attr;

		$context = $status;
		$screen  = $this->screen;

		// Pre-order.
		$actions = array(
			'deactivate' => '',
			'activate'   => '',
			'details'    => '',
			'delete'     => '',
		);

		// Do not restrict by default.
		$restrict_network_active = false;
		$restrict_network_only   = false;

		$requires_php = isset( $plugin_data['RequiresPHP'] ) ? $plugin_data['RequiresPHP'] : null;
		$requires_wp  = isset( $plugin_data['RequiresWP'] ) ? $plugin_data['RequiresWP'] : null;

		$compatible_php = is_php_version_compatible( $requires_php );
		$compatible_wp  = is_wp_version_compatible( $requires_wp );

		$has_dependents          = WP_Plugin_Dependencies::has_dependents( $plugin_file );
		$has_active_dependents   = WP_Plugin_Dependencies::has_active_dependents( $plugin_file );
		$has_unmet_dependencies  = WP_Plugin_Dependencies::has_unmet_dependencies( $plugin_file );
		$has_circular_dependency = WP_Plugin_Dependencies::has_circular_dependency( $plugin_file );

		if ( 'mustuse' === $context ) {
			$is_active = true;
		} elseif ( 'dropins' === $context ) {
			$dropins     = _get_dropins();
			$plugin_name = $plugin_file;

			if ( $plugin_file !== $plugin_data['Name'] ) {
				$plugin_name .= '<br />' . $plugin_data['Name'];
			}

			if ( true === ( $dropins[ $plugin_file ][1] ) ) { // Doesn't require a constant.
				$is_active   = true;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . '</strong></p>';
			} elseif ( defined( $dropins[ $plugin_file ][1] ) && constant( $dropins[ $plugin_file ][1] ) ) { // Constant is true.
				$is_active   = true;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . '</strong></p>';
			} else {
				$is_active   = false;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . ' <span class="error-message">' . __( 'Inactive:' ) . '</span></strong> ' .
					sprintf(
						/* translators: 1: Drop-in constant name, 2: wp-config.php */
						__( 'Requires %1$s in %2$s file.' ),
						"<code>define('" . $dropins[ $plugin_file ][1] . "', true);</code>",
						'<code>wp-config.php</code>'
					) . '</p>';
			}

			if ( $plugin_data['Description'] ) {
				$description .= '<p>' . $plugin_data['Description'] . '</p>';
			}
		} else {
			if ( $screen->in_admin( 'network' ) ) {
				$is_active = is_plugin_active_for_network( $plugin_file );
			} else {
				$is_active               = is_plugin_active( $plugin_file );
				$restrict_network_active = ( is_multisite() && is_plugin_active_for_network( $plugin_file ) );
				$restrict_network_only   = ( is_multisite() && is_network_only_plugin( $plugin_file ) && ! $is_active );
			}

			if ( $screen->in_admin( 'network' ) ) {
				if ( $is_active ) {
					if ( current_user_can( 'manage_network_plugins' ) ) {
						if ( $has_active_dependents ) {
							$actions['deactivate'] = __( 'Deactivate' ) .
								'<span class="screen-reader-text">' .
								__( 'You cannot deactivate this plugin as other plugins require it.' ) .
								'</span>';

						} else {
							$deactivate_url = 'plugins.php?action=deactivate' .
								'&amp;plugin=' . urlencode( $plugin_file ) .
								'&amp;plugin_status=' . $context .
								'&amp;paged=' . $page .
								'&amp;s=' . $s;

							$actions['deactivate'] = sprintf(
								'<a href="%s" id="deactivate-%s" aria-label="%s">%s</a>',
								wp_nonce_url( $deactivate_url, 'deactivate-plugin_' . $plugin_file ),
								esc_attr( $plugin_id_attr ),
								/* translators: %s: Plugin name. */
								esc_attr( sprintf( _x( 'Network Deactivate %s', 'plugin' ), $plugin_data['Name'] ) ),
								_x( 'Network Deactivate', 'plugin' )
							);
						}
					}
				} else {
					if ( current_user_can( 'manage_network_plugins' ) ) {
						if ( $compatible_php && $compatible_wp ) {
							if ( $has_unmet_dependencies ) {
								$actions['activate'] = _x( 'Network Activate', 'plugin' ) .
									'<span class="screen-reader-text">' .
									__( 'You cannot activate this plugin as it has unmet requirements.' ) .
									'</span>';
							} else {
								$activate_url = 'plugins.php?action=activate' .
									'&amp;plugin=' . urlencode( $plugin_file ) .
									'&amp;plugin_status=' . $context .
									'&amp;paged=' . $page .
									'&amp;s=' . $s;

								$actions['activate'] = sprintf(
									'<a href="%s" id="activate-%s" class="edit" aria-label="%s">%s</a>',
									wp_nonce_url( $activate_url, 'activate-plugin_' . $plugin_file ),
									esc_attr( $plugin_id_attr ),
									/* translators: %s: Plugin name. */
									esc_attr( sprintf( _x( 'Network Activate %s', 'plugin' ), $plugin_data['Name'] ) ),
									_x( 'Network Activate', 'plugin' )
								);
							}
						} else {
							$actions['activate'] = sprintf(
								'<span>%s</span>',
								_x( 'Cannot Activate', 'plugin' )
							);
						}
					}

					if ( current_user_can( 'delete_plugins' ) && ! is_plugin_active( $plugin_file ) ) {
						if ( $has_dependents && ! $has_circular_dependency ) {
							$actions['delete'] = __( 'Delete' ) .
								'<span class="screen-reader-text">' .
								__( 'You cannot delete this plugin as other plugins require it.' ) .
								'</span>';
						} else {
							$delete_url = 'plugins.php?action=delete-selected' .
								'&amp;checked[]=' . urlencode( $plugin_file ) .
								'&amp;plugin_status=' . $context .
								'&amp;paged=' . $page .
								'&amp;s=' . $s;

							$actions['delete'] = sprintf(
								'<a href="%s" id="delete-%s" class="delete" aria-label="%s">%s</a>',
								wp_nonce_url( $delete_url, 'bulk-plugins' ),
								esc_attr( $plugin_id_attr ),
								/* translators: %s: Plugin name. */
								esc_attr( sprintf( _x( 'Delete %s', 'plugin' ), $plugin_data['Name'] ) ),
								__( 'Delete' )
							);
						}
					}
				}
			} else {
				if ( $restrict_network_active ) {
					$actions = array(
						'network_active' => __( 'Network Active' ),
					);
				} elseif ( $restrict_network_only ) {
					$actions = array(
						'network_only' => __( 'Network Only' ),
					);
				} elseif ( $is_active ) {
					if ( current_user_can( 'deactivate_plugin', $plugin_file ) ) {
						if ( $has_active_dependents ) {
							$actions['deactivate'] = __( 'Deactivate' ) .
								'<span class="screen-reader-text">' .
								__( 'You cannot deactivate this plugin as other plugins depend on it.' ) .
								'</span>';
						} else {
							$deactivate_url = 'plugins.php?action=deactivate' .
								'&amp;plugin=' . urlencode( $plugin_file ) .
								'&amp;plugin_status=' . $context .
								'&amp;paged=' . $page .
								'&amp;s=' . $s;

							$actions['deactivate'] = sprintf(
								'<a href="%s" id="deactivate-%s" aria-label="%s">%s</a>',
								wp_nonce_url( $deactivate_url, 'deactivate-plugin_' . $plugin_file ),
								esc_attr( $plugin_id_attr ),
								/* translators: %s: Plugin name. */
								esc_attr( sprintf( _x( 'Deactivate %s', 'plugin' ), $plugin_data['Name'] ) ),
								__( 'Deactivate' )
							);
						}
					}

					if ( current_user_can( 'resume_plugin', $plugin_file ) && is_plugin_paused( $plugin_file ) ) {
						$resume_url = 'plugins.php?action=resume' .
							'&amp;plugin=' . urlencode( $plugin_file ) .
							'&amp;plugin_status=' . $context .
							'&amp;paged=' . $page .
							'&amp;s=' . $s;

						$actions['resume'] = sprintf(
							'<a href="%s" id="resume-%s" class="resume-link" aria-label="%s">%s</a>',
							wp_nonce_url( $resume_url, 'resume-plugin_' . $plugin_file ),
							esc_attr( $plugin_id_attr ),
							/* translators: %s: Plugin name. */
							esc_attr( sprintf( _x( 'Resume %s', 'plugin' ), $plugin_data['Name'] ) ),
							__( 'Resume' )
						);
					}
				} else {
					if ( current_user_can( 'activate_plugin', $plugin_file ) ) {
						if ( $compatible_php && $compatible_wp ) {
							if ( $has_unmet_dependencies ) {
								$actions['activate'] = _x( 'Activate', 'plugin' ) .
									'<span class="screen-reader-text">' .
									__( 'You cannot activate this plugin as it has unmet requirements.' ) .
									'</span>';
							} else {
								$activate_url = 'plugins.php?action=activate' .
									'&amp;plugin=' . urlencode( $plugin_file ) .
									'&amp;plugin_status=' . $context .
									'&amp;paged=' . $page .
									'&amp;s=' . $s;

								$actions['activate'] = sprintf(
									'<a href="%s" id="activate-%s" class="edit" aria-label="%s">%s</a>',
									wp_nonce_url( $activate_url, 'activate-plugin_' . $plugin_file ),
									esc_attr( $plugin_id_attr ),
									/* translators: %s: Plugin name. */
									esc_attr( sprintf( _x( 'Activate %s', 'plugin' ), $plugin_data['Name'] ) ),
									_x( 'Activate', 'plugin' )
								);
							}
						} else {
							$actions['activate'] = sprintf(
								'<span>%s</span>',
								_x( 'Cannot Activate', 'plugin' )
							);
						}
					}

					if ( ! is_multisite() && current_user_can( 'delete_plugins' ) ) {
						if ( $has_dependents && ! $has_circular_dependency ) {
							$actions['delete'] = __( 'Delete' ) .
								'<span class="screen-reader-text">' .
								__( 'You cannot delete this plugin as other plugins require it.' ) .
								'</span>';
						} else {
							$delete_url = 'plugins.php?action=delete-selected' .
								'&amp;checked[]=' . urlencode( $plugin_file ) .
								'&amp;plugin_status=' . $context .
								'&amp;paged=' . $page .
								'&amp;s=' . $s;

							$actions['delete'] = sprintf(
								'<a href="%s" id="delete-%s" class="delete" aria-label="%s">%s</a>',
								wp_nonce_url( $delete_url, 'bulk-plugins' ),
								esc_attr( $plugin_id_attr ),
								/* translators: %s: Plugin name. */
								esc_attr( sprintf( _x( 'Delete %s', 'plugin' ), $plugin_data['Name'] ) ),
								__( 'Delete' )
							);
						}
					}
				} // End if $is_active.
			} // End if $screen->in_admin( 'network' ).
		} // End if $context.

		$actions = array_filter( $actions );

		if ( $screen->in_admin( 'network' ) ) {

			/**
			 * Filters the action links displayed for each plugin in the Network Admin Plugins list table.
			 *
			 * @since 3.1.0
			 *
			 * @param string[] $actions     An array of plugin action links. By default this can include
			 *                              'activate', 'deactivate', and 'delete'.
			 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
			 * @param array    $plugin_data An array of plugin data. See get_plugin_data()
			 *                              and the {@see 'plugin_row_meta'} filter for the list
			 *                              of possible values.
			 * @param string   $context     The plugin context. By default this can include 'all',
			 *                              'active', 'inactive', 'recently_activated', 'upgrade',
			 *                              'mustuse', 'dropins', and 'search'.
			 */
			$actions = apply_filters( 'network_admin_plugin_action_links', $actions, $plugin_file, $plugin_data, $context );

			/**
			 * Filters the list of action links displayed for a specific plugin in the Network Admin Plugins list table.
			 *
			 * The dynamic portion of the hook name, `$plugin_file`, refers to the path
			 * to the plugin file, relative to the plugins directory.
			 *
			 * @since 3.1.0
			 *
			 * @param string[] $actions     An array of plugin action links. By default this can include
			 *                              'activate', 'deactivate', and 'delete'.
			 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
			 * @param array    $plugin_data An array of plugin data. See get_plugin_data()
			 *                              and the {@see 'plugin_row_meta'} filter for the list
			 *                              of possible values.
			 * @param string   $context     The plugin context. By default this can include 'all',
			 *                              'active', 'inactive', 'recently_activated', 'upgrade',
			 *                              'mustuse', 'dropins', and 'search'.
			 */
			$actions = apply_filters( "network_admin_plugin_action_links_{$plugin_file}", $actions, $plugin_file, $plugin_data, $context );

		} else {

			/**
			 * Filters the action links displayed for each plugin in the Plugins list table.
			 *
			 * @since 2.5.0
			 * @since 2.6.0 The `$context` parameter was added.
			 * @since 4.9.0 The 'Edit' link was removed from the list of action links.
			 *
			 * @param string[] $actions     An array of plugin action links. By default this can include
			 *                              'activate', 'deactivate', and 'delete'. With Multisite active
			 *                              this can also include 'network_active' and 'network_only' items.
			 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
			 * @param array    $plugin_data An array of plugin data. See get_plugin_data()
			 *                              and the {@see 'plugin_row_meta'} filter for the list
			 *                              of possible values.
			 * @param string   $context     The plugin context. By default this can include 'all',
			 *                              'active', 'inactive', 'recently_activated', 'upgrade',
			 *                              'mustuse', 'dropins', and 'search'.
			 */
			$actions = apply_filters( 'plugin_action_links', $actions, $plugin_file, $plugin_data, $context );

			/**
			 * Filters the list of action links displayed for a specific plugin in the Plugins list table.
			 *
			 * The dynamic portion of the hook name, `$plugin_file`, refers to the path
			 * to the plugin file, relative to the plugins directory.
			 *
			 * @since 2.7.0
			 * @since 4.9.0 The 'Edit' link was removed from the list of action links.
			 *
			 * @param string[] $actions     An array of plugin action links. By default this can include
			 *                              'activate', 'deactivate', and 'delete'. With Multisite active
			 *                              this can also include 'network_active' and 'network_only' items.
			 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
			 * @param array    $plugin_data An array of plugin data. See get_plugin_data()
			 *                              and the {@see 'plugin_row_meta'} filter for the list
			 *                              of possible values.
			 * @param string   $context     The plugin context. By default this can include 'all',
			 *                              'active', 'inactive', 'recently_activated', 'upgrade',
			 *                              'mustuse', 'dropins', and 'search'.
			 */
			$actions = apply_filters( "plugin_action_links_{$plugin_file}", $actions, $plugin_file, $plugin_data, $context );

		}

		$class       = $is_active ? 'active' : 'inactive';
		$checkbox_id = 'checkbox_' . md5( $plugin_file );
		$disabled    = '';

		if ( $has_dependents || $has_unmet_dependencies ) {
			$disabled = 'disabled';
		}

		if (
			$restrict_network_active ||
			$restrict_network_only ||
			in_array( $status, array( 'mustuse', 'dropins' ), true ) ||
			! $compatible_php
		) {
			$checkbox = '';
		} else {
			$checkbox = sprintf(
				'<label class="label-covers-full-cell" for="%1$s">' .
				'<span class="screen-reader-text">%2$s</span></label>' .
				'<input type="checkbox" name="checked[]" value="%3$s" id="%1$s" ' . $disabled . '/>',
				$checkbox_id,
				/* translators: Hidden accessibility text. %s: Plugin name. */
				sprintf( __( 'Select %s' ), $plugin_data['Name'] ),
				esc_attr( $plugin_file )
			);
		}

		if ( 'dropins' !== $context ) {
			$description = '<p>' . ( $plugin_data['Description'] ? $plugin_data['Description'] : '&nbsp;' ) . '</p>';
			$plugin_name = $plugin_data['Name'];
		}

		if (
			! empty( $totals['upgrade'] ) &&
			! empty( $plugin_data['update'] ) ||
			! $compatible_php ||
			! $compatible_wp
		) {
			$class .= ' update';
		}

		$paused = ! $screen->in_admin( 'network' ) && is_plugin_paused( $plugin_file );

		if ( $paused ) {
			$class .= ' paused';
		}

		if ( is_uninstallable_plugin( $plugin_file ) ) {
			$class .= ' is-uninstallable';
		}

		printf(
			'<tr class="%s" data-slug="%s" data-plugin="%s">',
			esc_attr( $class ),
			esc_attr( $plugin_slug ),
			esc_attr( $plugin_file )
		);

		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		$auto_updates = (array) get_site_option( 'auto_update_plugins', array() );

		foreach ( $columns as $column_name => $column_display_name ) {
			$extra_classes = '';
			if ( in_array( $column_name, $hidden, true ) ) {
				$extra_classes = ' hidden';
			}

			switch ( $column_name ) {
				case 'cb':
					echo "<th scope='row' class='check-column'>$checkbox</th>";
					break;
				case 'name':
					echo "<td class='plugin-title column-primary'><strong>$plugin_name</strong>";
					echo $this->row_actions( $actions, true );
					echo '</td>';
					break;
				case 'description':
					$classes = 'column-description desc';

					echo "<td class='$classes{$extra_classes}'>
						<div class='plugin-description'>$description</div>
						<div class='$class second plugin-version-author-uri'>";

					$plugin_meta = array();

					if ( ! empty( $plugin_data['Version'] ) ) {
						/* translators: %s: Plugin version number. */
						$plugin_meta[] = sprintf( __( 'Version %s' ), $plugin_data['Version'] );
					}

					if ( ! empty( $plugin_data['Author'] ) ) {
						$author = $plugin_data['Author'];

						if ( ! empty( $plugin_data['AuthorURI'] ) ) {
							$author = '<a href="' . $plugin_data['AuthorURI'] . '">' . $plugin_data['Author'] . '</a>';
						}

						/* translators: %s: Plugin author name. */
						$plugin_meta[] = sprintf( __( 'By %s' ), $author );
					}

					// Details link using API info, if available.
					if ( isset( $plugin_data['slug'] ) && current_user_can( 'install_plugins' ) ) {
						$plugin_meta[] = sprintf(
							'<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
							esc_url(
								network_admin_url(
									'plugin-install.php?tab=plugin-information&plugin=' . $plugin_data['slug'] .
									'&TB_iframe=true&width=600&height=550'
								)
							),
							/* translators: %s: Plugin name. */
							esc_attr( sprintf( __( 'More information about %s' ), $plugin_name ) ),
							esc_attr( $plugin_name ),
							__( 'View details' )
						);
					} elseif ( ! empty( $plugin_data['PluginURI'] ) ) {
						/* translators: %s: Plugin name. */
						$aria_label = sprintf( __( 'Visit plugin site for %s' ), $plugin_name );

						$plugin_meta[] = sprintf(
							'<a href="%s" aria-label="%s">%s</a>',
							esc_url( $plugin_data['PluginURI'] ),
							esc_attr( $aria_label ),
							__( 'Visit plugin site' )
						);
					}

					/**
					 * Filters the array of row meta for each plugin in the Plugins list table.
					 *
					 * @since 2.8.0
					 *
					 * @param string[] $plugin_meta An array of the plugin's metadata, including
					 *                              the version, author, author URI, and plugin URI.
					 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
					 * @param array    $plugin_data {
					 *     An array of plugin data.
					 *
					 *     @type string   $id               Plugin ID, e.g. `w.org/plugins/[plugin-name]`.
					 *     @type string   $slug             Plugin slug.
					 *     @type string   $plugin           Plugin basename.
					 *     @type string   $new_version      New plugin version.
					 *     @type string   $url              Plugin URL.
					 *     @type string   $package          Plugin update package URL.
					 *     @type string[] $icons            An array of plugin icon URLs.
					 *     @type string[] $banners          An array of plugin banner URLs.
					 *     @type string[] $banners_rtl      An array of plugin RTL banner URLs.
					 *     @type string   $requires         The version of WordPress which the plugin requires.
					 *     @type string   $tested           The version of WordPress the plugin is tested against.
					 *     @type string   $requires_php     The version of PHP which the plugin requires.
					 *     @type string   $upgrade_notice   The upgrade notice for the new plugin version.
					 *     @type bool     $update-supported Whether the plugin supports updates.
					 *     @type string   $Name             The human-readable name of the plugin.
					 *     @type string   $PluginURI        Plugin URI.
					 *     @type string   $Version          Plugin version.
					 *     @type string   $Description      Plugin description.
					 *     @type string   $Author           Plugin author.
					 *     @type string   $AuthorURI        Plugin author URI.
					 *     @type string   $TextDomain       Plugin textdomain.
					 *     @type string   $DomainPath       Relative path to the plugin's .mo file(s).
					 *     @type bool     $Network          Whether the plugin can only be activated network-wide.
					 *     @type string   $RequiresWP       The version of WordPress which the plugin requires.
					 *     @type string   $RequiresPHP      The version of PHP which the plugin requires.
					 *     @type string   $UpdateURI        ID of the plugin for update purposes, should be a URI.
					 *     @type string   $Title            The human-readable title of the plugin.
					 *     @type string   $AuthorName       Plugin author's name.
					 *     @type bool     $update           Whether there's an available update. Default null.
					 * }
					 * @param string   $status      Status filter currently applied to the plugin list. Possible
					 *                              values are: 'all', 'active', 'inactive', 'recently_activated',
					 *                              'upgrade', 'mustuse', 'dropins', 'search', 'paused',
					 *                              'auto-update-enabled', 'auto-update-disabled'.
					 */
					$plugin_meta = apply_filters( 'plugin_row_meta', $plugin_meta, $plugin_file, $plugin_data, $status );

					echo implode( ' | ', $plugin_meta );

					echo '</div>';

					if ( $has_dependents ) {
						$this->add_dependents_to_dependency_plugin_row( $plugin_file );
					}

					if ( WP_Plugin_Dependencies::has_dependencies( $plugin_file ) ) {
						$this->add_dependencies_to_dependent_plugin_row( $plugin_file );
					}

					/**
					 * Fires after plugin row meta.
					 *
					 * @since 6.5.0
					 *
					 * @param string $plugin_file Refer to {@see 'plugin_row_meta'} filter.
					 * @param array  $plugin_data Refer to {@see 'plugin_row_meta'} filter.
					 */
					do_action( 'after_plugin_row_meta', $plugin_file, $plugin_data );

					if ( $paused ) {
						$notice_text = __( 'This plugin failed to load properly and is paused during recovery mode.' );

						printf( '<p><span class="dashicons dashicons-warning"></span> <strong>%s</strong></p>', $notice_text );

						$error = wp_get_plugin_error( $plugin_file );

						if ( false !== $error ) {
							printf( '<div class="error-display"><p>%s</p></div>', wp_get_extension_error_description( $error ) );
						}
					}

					echo '</td>';
					break;
				case 'auto-updates':
					if ( ! $this->show_autoupdates || in_array( $status, array( 'mustuse', 'dropins' ), true ) ) {
						break;
					}

					echo "<td class='column-auto-updates{$extra_classes}'>";

					$html = array();

					if ( isset( $plugin_data['auto-update-forced'] ) ) {
						if ( $plugin_data['auto-update-forced'] ) {
							// Forced on.
							$text = __( 'Auto-updates enabled' );
						} else {
							$text = __( 'Auto-updates disabled' );
						}
						$action     = 'unavailable';
						$time_class = ' hidden';
					} elseif ( empty( $plugin_data['update-supported'] ) ) {
						$text       = '';
						$action     = 'unavailable';
						$time_class = ' hidden';
					} elseif ( in_array( $plugin_file, $auto_updates, true ) ) {
						$text       = __( 'Disable auto-updates' );
						$action     = 'disable';
						$time_class = '';
					} else {
						$text       = __( 'Enable auto-updates' );
						$action     = 'enable';
						$time_class = ' hidden';
					}

					$query_args = array(
						'action'        => "{$action}-auto-update",
						'plugin'        => $plugin_file,
						'paged'         => $page,
						'plugin_status' => $status,
					);

					$url = add_query_arg( $query_args, 'plugins.php' );

					if ( 'unavailable' === $action ) {
						$html[] = '<span class="label">' . $text . '</span>';
					} else {
						$html[] = sprintf(
							'<a href="%s" class="toggle-auto-update aria-button-if-js" data-wp-action="%s">',
							wp_nonce_url( $url, 'updates' ),
							$action
						);

						$html[] = '<span class="dashicons dashicons-update spin hidden" aria-hidden="true"></span>';
						$html[] = '<span class="label">' . $text . '</span>';
						$html[] = '</a>';
					}

					if ( ! empty( $plugin_data['update'] ) ) {
						$html[] = sprintf(
							'<div class="auto-update-time%s">%s</div>',
							$time_class,
							wp_get_auto_update_message()
						);
					}

					$html = implode( '', $html );

					/**
					 * Filters the HTML of the auto-updates setting for each plugin in the Plugins list table.
					 *
					 * @since 5.5.0
					 *
					 * @param string $html        The HTML of the plugin's auto-update column content,
					 *                            including toggle auto-update action links and
					 *                            time to next update.
					 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
					 * @param array  $plugin_data An array of plugin data. See get_plugin_data()
					 *                            and the {@see 'plugin_row_meta'} filter for the list
					 *                            of possible values.
					 */
					echo apply_filters( 'plugin_auto_update_setting_html', $html, $plugin_file, $plugin_data );

					wp_admin_notice(
						'',
						array(
							'type'               => 'error',
							'additional_classes' => array( 'notice-alt', 'inline', 'hidden' ),
						)
					);

					echo '</td>';

					break;
				default:
					$classes = "$column_name column-$column_name $class";

					echo "<td class='$classes{$extra_classes}'>";

					/**
					 * Fires inside each custom column of the Plugins list table.
					 *
					 * @since 3.1.0
					 *
					 * @param string $column_name Name of the column.
					 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
					 * @param array  $plugin_data An array of plugin data. See get_plugin_data()
					 *                            and the {@see 'plugin_row_meta'} filter for the list
					 *                            of possible values.
					 */
					do_action( 'manage_plugins_custom_column', $column_name, $plugin_file, $plugin_data );

					echo '</td>';
			}
		}

		echo '</tr>';

		if ( ! $compatible_php || ! $compatible_wp ) {
			printf(
				'<tr class="plugin-update-tr"><td colspan="%s" class="plugin-update colspanchange">',
				esc_attr( $this->get_column_count() )
			);

			$incompatible_message = '';
			if ( ! $compatible_php && ! $compatible_wp ) {
				$incompatible_message .= __( 'This plugin does not work with your versions of WordPress and PHP.' );
				if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
					$incompatible_message .= sprintf(
						/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
						' ' . __( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.' ),
						self_admin_url( 'update-core.php' ),
						esc_url( wp_get_update_php_url() )
					);
					$incompatible_message .= wp_update_php_annotation( '</p><p><em>', '</em>', false );
				} elseif ( current_user_can( 'update_core' ) ) {
					$incompatible_message .= sprintf(
						/* translators: %s: URL to WordPress Updates screen. */
						' ' . __( '<a href="%s">Please update WordPress</a>.' ),
						self_admin_url( 'update-core.php' )
					);
				} elseif ( current_user_can( 'update_php' ) ) {
					$incompatible_message .= sprintf(
						/* translators: %s: URL to Update PHP page. */
						' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
						esc_url( wp_get_update_php_url() )
					);
					$incompatible_message .= wp_update_php_annotation( '</p><p><em>', '</em>', false );
				}
			} elseif ( ! $compatible_wp ) {
				$incompatible_message .= __( 'This plugin does not work with your version of WordPress.' );
				if ( current_user_can( 'update_core' ) ) {
					$incompatible_message .= sprintf(
						/* translators: %s: URL to WordPress Updates screen. */
						' ' . __( '<a href="%s">Please update WordPress</a>.' ),
						self_admin_url( 'update-core.php' )
					);
				}
			} elseif ( ! $compatible_php ) {
				$incompatible_message .= __( 'This plugin does not work with your version of PHP.' );
				if ( current_user_can( 'update_php' ) ) {
					$incompatible_message .= sprintf(
						/* translators: %s: URL to Update PHP page. */
						' ' . __( '<a href="%s">Learn more about updating PHP</a>.' ),
						esc_url( wp_get_update_php_url() )
					);
					$incompatible_message .= wp_update_php_annotation( '</p><p><em>', '</em>', false );
				}
			}

			wp_admin_notice(
				$incompatible_message,
				array(
					'type'               => 'error',
					'additional_classes' => array( 'notice-alt', 'inline', 'update-message' ),
				)
			);

			echo '</td></tr>';
		}

		/**
		 * Fires after each row in the Plugins list table.
		 *
		 * @since 2.3.0
		 * @since 5.5.0 Added 'auto-update-enabled' and 'auto-update-disabled'
		 *              to possible values for `$status`.
		 *
		 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
		 * @param array  $plugin_data An array of plugin data. See get_plugin_data()
		 *                            and the {@see 'plugin_row_meta'} filter for the list
		 *                            of possible values.
		 * @param string $status      Status filter currently applied to the plugin list.
		 *                            Possible values are: 'all', 'active', 'inactive',
		 *                            'recently_activated', 'upgrade', 'mustuse', 'dropins',
		 *                            'search', 'paused', 'auto-update-enabled', 'auto-update-disabled'.
		 */
		do_action( 'after_plugin_row', $plugin_file, $plugin_data, $status );

		/**
		 * Fires after each specific row in the Plugins list table.
		 *
		 * The dynamic portion of the hook name, `$plugin_file`, refers to the path
		 * to the plugin file, relative to the plugins directory.
		 *
		 * @since 2.7.0
		 * @since 5.5.0 Added 'auto-update-enabled' and 'auto-update-disabled'
		 *              to possible values for `$status`.
		 *
		 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
		 * @param array  $plugin_data An array of plugin data. See get_plugin_data()
		 *                            and the {@see 'plugin_row_meta'} filter for the list
		 *                            of possible values.
		 * @param string $status      Status filter currently applied to the plugin list.
		 *                            Possible values are: 'all', 'active', 'inactive',
		 *                            'recently_activated', 'upgrade', 'mustuse', 'dropins',
		 *                            'search', 'paused', 'auto-update-enabled', 'auto-update-disabled'.
		 */
		do_action( "after_plugin_row_{$plugin_file}", $plugin_file, $plugin_data, $status );
	}

	/**
	 * Gets the name of the primary column for this specific list table.
	 *
	 * @since 4.3.0
	 *
	 * @return string Unalterable name for the primary column, in this case, 'name'.
	 */
	protected function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Prints a list of other plugins that depend on the plugin.
	 *
	 * @since 6.5.0
	 *
	 * @param string $dependency The dependency's filepath, relative to the plugins directory.
	 */
	protected function add_dependents_to_dependency_plugin_row( $dependency ) {
		$dependent_names = WP_Plugin_Dependencies::get_dependent_names( $dependency );

		if ( empty( $dependent_names ) ) {
			return;
		}

		$dependency_note = __( 'Note: This plugin cannot be deactivated or deleted until the plugins that require it are deactivated or deleted.' );

		$comma       = wp_get_list_item_separator();
		$required_by = sprintf(
			/* translators: %s: List of dependencies. */
			__( '<strong>Required by:</strong> %s' ),
			implode( $comma, $dependent_names )
		);

		printf(
			'<div class="required-by"><p>%1$s</p><p>%2$s</p></div>',
			$required_by,
			$dependency_note
		);
	}

	/**
	 * Prints a list of other plugins that the plugin depends on.
	 *
	 * @since 6.5.0
	 *
	 * @param string $dependent The dependent plugin's filepath, relative to the plugins directory.
	 */
	protected function add_dependencies_to_dependent_plugin_row( $dependent ) {
		$dependency_names = WP_Plugin_Dependencies::get_dependency_names( $dependent );

		if ( array() === $dependency_names ) {
			return;
		}

		$links = array();
		foreach ( $dependency_names as $slug => $name ) {
			$links[] = $this->get_dependency_view_details_link( $name, $slug );
		}

		$is_active = is_multisite() ? is_plugin_active_for_network( $dependent ) : is_plugin_active( $dependent );
		$comma     = wp_get_list_item_separator();
		$requires  = sprintf(
			/* translators: %s: List of dependency names. */
			__( '<strong>Requires:</strong> %s' ),
			implode( $comma, $links )
		);

		$notice        = '';
		$error_message = '';
		if ( WP_Plugin_Dependencies::has_unmet_dependencies( $dependent ) ) {
			if ( $is_active ) {
				$error_message = __( 'This plugin is active but may not function correctly because required plugins are missing or inactive.' );
			} else {
				$error_message = __( 'This plugin cannot be activated because required plugins are missing or inactive.' );
			}
			$notice = wp_get_admin_notice(
				$error_message,
				array(
					'type'               => 'error',
					'additional_classes' => array( 'inline', 'notice-alt' ),
				)
			);
		}

		printf(
			'<div class="requires"><p>%1$s</p><p>%2$s</p></div>',
			$requires,
			$notice
		);
	}

	/**
	 * Returns a 'View details' like link for a dependency.
	 *
	 * @since 6.5.0
	 *
	 * @param string $name The dependency's name.
	 * @param string $slug The dependency's slug.
	 * @return string A 'View details' link for the dependency.
	 */
	protected function get_dependency_view_details_link( $name, $slug ) {
		$dependency_data = WP_Plugin_Dependencies::get_dependency_data( $slug );

		if ( false === $dependency_data
			|| $name === $slug
			|| $name !== $dependency_data['name']
			|| empty( $dependency_data['version'] )
		) {
			return $name;
		}

		return $this->get_view_details_link( $name, $slug );
	}

	/**
	 * Returns a 'View details' link for the plugin.
	 *
	 * @since 6.5.0
	 *
	 * @param string $name The plugin's name.
	 * @param string $slug The plugin's slug.
	 * @return string A 'View details' link for the plugin.
	 */
	protected function get_view_details_link( $name, $slug ) {
		$url = add_query_arg(
			array(
				'tab'       => 'plugin-information',
				'plugin'    => $slug,
				'TB_iframe' => 'true',
				'width'     => '600',
				'height'    => '550',
			),
			network_admin_url( 'plugin-install.php' )
		);

		$name_attr = esc_attr( $name );
		return sprintf(
			"<a href='%s' class='thickbox open-plugin-details-modal' aria-label='%s' data-title='%s'>%s</a>",
			esc_url( $url ),
			/* translators: %s: Plugin name. */
			sprintf( __( 'More information about %s' ), $name_attr ),
			$name_attr,
			esc_html( $name )
		);
	}
}

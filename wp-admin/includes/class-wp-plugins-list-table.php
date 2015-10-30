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
 * @access private
 *
 * @see WP_List_Table
 */
class WP_Plugins_List_Table extends WP_List_Table {

	/**
	 * Constructor.
	 *
	 * @since 3.1.0
	 * @access public
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

		parent::__construct( array(
			'plural' => 'plugins',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );

		$status = 'all';
		if ( isset( $_REQUEST['plugin_status'] ) && in_array( $_REQUEST['plugin_status'], array( 'active', 'inactive', 'recently_activated', 'upgrade', 'mustuse', 'dropins', 'search' ) ) )
			$status = $_REQUEST['plugin_status'];

		if ( isset($_REQUEST['s']) )
			$_SERVER['REQUEST_URI'] = add_query_arg('s', wp_unslash($_REQUEST['s']) );

		$page = $this->get_pagenum();
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
		return current_user_can('activate_plugins');
	}

	/**
	 *
	 * @global string $status
	 * @global type   $plugins
	 * @global array  $totals
	 * @global int    $page
	 * @global string $orderby
	 * @global string $order
	 * @global string $s
	 */
	public function prepare_items() {
		global $status, $plugins, $totals, $page, $orderby, $order, $s;

		wp_reset_vars( array( 'orderby', 'order', 's' ) );

		/**
		 * Filter the full array of plugins to list in the Plugins list table.
		 *
		 * @since 3.0.0
		 *
		 * @see get_plugins()
		 *
		 * @param array $plugins An array of plugins to display in the list table.
		 */
		$plugins = array(
			'all' => apply_filters( 'all_plugins', get_plugins() ),
			'search' => array(),
			'active' => array(),
			'inactive' => array(),
			'recently_activated' => array(),
			'upgrade' => array(),
			'mustuse' => array(),
			'dropins' => array()
		);

		$screen = $this->screen;

		if ( ! is_multisite() || ( $screen->in_admin( 'network' ) && current_user_can( 'manage_network_plugins' ) ) ) {

			/**
			 * Filter whether to display the advanced plugins list table.
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
			if ( apply_filters( 'show_advanced_plugins', true, 'dropins' ) )
				$plugins['dropins'] = get_dropins();

			if ( current_user_can( 'update_plugins' ) ) {
				$current = get_site_transient( 'update_plugins' );
				foreach ( (array) $plugins['all'] as $plugin_file => $plugin_data ) {
					if ( isset( $current->response[ $plugin_file ] ) ) {
						$plugins['all'][ $plugin_file ]['update'] = true;
						$plugins['upgrade'][ $plugin_file ] = $plugins['all'][ $plugin_file ];
					}
				}
			}
		}

		if ( ! $screen->in_admin( 'network' ) ) {
			$show = current_user_can( 'manage_network_plugins' );
			/**
			 * Filter whether to display network-active plugins alongside plugins active for the current site.
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

		set_transient( 'plugin_slugs', array_keys( $plugins['all'] ), DAY_IN_SECONDS );

		if ( $screen->in_admin( 'network' ) ) {
			$recently_activated = get_site_option( 'recently_activated', array() );
		} else {
			$recently_activated = get_option( 'recently_activated', array() );
		}

		foreach ( $recently_activated as $key => $time ) {
			if ( $time + WEEK_IN_SECONDS < time() ) {
				unset( $recently_activated[$key] );
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
				$plugins['all'][ $plugin_file ] = $plugin_data = array_merge( (array) $plugin_info->response[ $plugin_file ], $plugin_data );
				// Make sure that $plugins['upgrade'] also receives the extra info since it is used on ?plugin_status=upgrade
				if ( isset( $plugins['upgrade'][ $plugin_file ] ) ) {
					$plugins['upgrade'][ $plugin_file ] = $plugin_data = array_merge( (array) $plugin_info->response[ $plugin_file ], $plugin_data );
				}

			} elseif ( isset( $plugin_info->no_update[ $plugin_file ] ) ) {
				$plugins['all'][ $plugin_file ] = $plugin_data = array_merge( (array) $plugin_info->no_update[ $plugin_file ], $plugin_data );
				// Make sure that $plugins['upgrade'] also receives the extra info since it is used on ?plugin_status=upgrade
				if ( isset( $plugins['upgrade'][ $plugin_file ] ) ) {
					$plugins['upgrade'][ $plugin_file ] = $plugin_data = array_merge( (array) $plugin_info->no_update[ $plugin_file ], $plugin_data );
				}
			}

			// Filter into individual sections
			if ( is_multisite() && ! $screen->in_admin( 'network' ) && is_network_only_plugin( $plugin_file ) && ! is_plugin_active( $plugin_file ) ) {
				if ( $show_network_active ) {
					// On the non-network screen, show inactive network-only plugins if allowed
					$plugins['inactive'][ $plugin_file ] = $plugin_data;
				} else {
					// On the non-network screen, filter out network-only plugins as long as they're not individually active
					unset( $plugins['all'][ $plugin_file ] );
				}
			} elseif ( ! $screen->in_admin( 'network' ) && is_plugin_active_for_network( $plugin_file ) ) {
				if ( $show_network_active ) {
					// On the non-network screen, show network-active plugins if allowed
					$plugins['active'][ $plugin_file ] = $plugin_data;
				} else {
					// On the non-network screen, filter out network-active plugins
					unset( $plugins['all'][ $plugin_file ] );
				}
			} elseif ( ( ! $screen->in_admin( 'network' ) && is_plugin_active( $plugin_file ) )
				|| ( $screen->in_admin( 'network' ) && is_plugin_active_for_network( $plugin_file ) ) ) {
				// On the non-network screen, populate the active list with plugins that are individually activated
				// On the network-admin screen, populate the active list with plugins that are network activated
				$plugins['active'][ $plugin_file ] = $plugin_data;
			} else {
				if ( isset( $recently_activated[ $plugin_file ] ) ) {
					// Populate the recently activated list with plugins that have been recently activated
					$plugins['recently_activated'][ $plugin_file ] = $plugin_data;
				}
				// Populate the inactive list with plugins that aren't activated
				$plugins['inactive'][ $plugin_file ] = $plugin_data;
			}
		}

		if ( $s ) {
			$status = 'search';
			$plugins['search'] = array_filter( $plugins['all'], array( $this, '_search_callback' ) );
		}

		$totals = array();
		foreach ( $plugins as $type => $list )
			$totals[ $type ] = count( $list );

		if ( empty( $plugins[ $status ] ) && !in_array( $status, array( 'all', 'search' ) ) )
			$status = 'all';

		$this->items = array();
		foreach ( $plugins[ $status ] as $plugin_file => $plugin_data ) {
			// Translate, Don't Apply Markup, Sanitize HTML
			$this->items[$plugin_file] = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
		}

		$total_this_page = $totals[ $status ];

		if ( ! $orderby ) {
			$orderby = 'Name';
		} else {
			$orderby = ucfirst( $orderby );
		}

		$order = strtoupper( $order );

		uasort( $this->items, array( $this, '_order_callback' ) );

		$plugins_per_page = $this->get_items_per_page( str_replace( '-', '_', $screen->id . '_per_page' ), 999 );

		$start = ( $page - 1 ) * $plugins_per_page;

		if ( $total_this_page > $plugins_per_page )
			$this->items = array_slice( $this->items, $start, $plugins_per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_this_page,
			'per_page' => $plugins_per_page,
		) );
	}

	/**
	 * @staticvar string $term
	 * @param array $plugin
	 * @return bool
	 */
	public function _search_callback( $plugin ) {
		static $term = null;
		if ( is_null( $term ) )
			$term = wp_unslash( $_REQUEST['s'] );

		foreach ( $plugin as $value ) {
			if ( false !== stripos( strip_tags( $value ), $term ) ) {
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

		$a = $plugin_a[$orderby];
		$b = $plugin_b[$orderby];

		if ( $a == $b )
			return 0;

		if ( 'DESC' === $order ) {
			return strcasecmp( $b, $a );
		} else {
			return strcasecmp( $a, $b );
		}
	}

	/**
	 *
	 * @global array $plugins
	 */
	public function no_items() {
		global $plugins;

		if ( !empty( $plugins['all'] ) )
			_e( 'No plugins found.' );
		else
			_e( 'You do not appear to have any plugins available at this time.' );
	}

	/**
	 *
	 * @global string $status
	 * @return array
	 */
	public function get_columns() {
		global $status;

		return array(
			'cb'          => !in_array( $status, array( 'mustuse', 'dropins' ) ) ? '<input type="checkbox" />' : '',
			'name'        => __( 'Plugin' ),
			'description' => __( 'Description' ),
		);
	}

	/**
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array();
	}

	/**
	 *
	 * @global array $totals
	 * @global string $status
	 * @return array
	 */
	protected function get_views() {
		global $totals, $status;

		$status_links = array();
		foreach ( $totals as $type => $count ) {
			if ( !$count )
				continue;

			switch ( $type ) {
				case 'all':
					$text = _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $count, 'plugins' );
					break;
				case 'active':
					$text = _n( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', $count );
					break;
				case 'recently_activated':
					$text = _n( 'Recently Active <span class="count">(%s)</span>', 'Recently Active <span class="count">(%s)</span>', $count );
					break;
				case 'inactive':
					$text = _n( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', $count );
					break;
				case 'mustuse':
					$text = _n( 'Must-Use <span class="count">(%s)</span>', 'Must-Use <span class="count">(%s)</span>', $count );
					break;
				case 'dropins':
					$text = _n( 'Drop-ins <span class="count">(%s)</span>', 'Drop-ins <span class="count">(%s)</span>', $count );
					break;
				case 'upgrade':
					$text = _n( 'Update Available <span class="count">(%s)</span>', 'Update Available <span class="count">(%s)</span>', $count );
					break;
			}

			if ( 'search' !== $type ) {
				$status_links[$type] = sprintf( "<a href='%s' %s>%s</a>",
					add_query_arg('plugin_status', $type, 'plugins.php'),
					( $type === $status ) ? ' class="current"' : '',
					sprintf( $text, number_format_i18n( $count ) )
					);
			}
		}

		return $status_links;
	}

	/**
	 *
	 * @global string $status
	 * @return array
	 */
	protected function get_bulk_actions() {
		global $status;

		$actions = array();

		if ( 'active' != $status )
			$actions['activate-selected'] = $this->screen->in_admin( 'network' ) ? __( 'Network Activate' ) : __( 'Activate' );

		if ( 'inactive' != $status && 'recent' != $status )
			$actions['deactivate-selected'] = $this->screen->in_admin( 'network' ) ? __( 'Network Deactivate' ) : __( 'Deactivate' );

		if ( !is_multisite() || $this->screen->in_admin( 'network' ) ) {
			if ( current_user_can( 'update_plugins' ) )
				$actions['update-selected'] = __( 'Update' );
			if ( current_user_can( 'delete_plugins' ) && ( 'active' != $status ) )
				$actions['delete-selected'] = __( 'Delete' );
		}

		return $actions;
	}

	/**
	 * @global string $status
	 * @param string $which
	 */
	public function bulk_actions( $which = '' ) {
		global $status;

		if ( in_array( $status, array( 'mustuse', 'dropins' ) ) )
			return;

		parent::bulk_actions( $which );
	}

	/**
	 * @global string $status
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		global $status;

		if ( ! in_array($status, array('recently_activated', 'mustuse', 'dropins') ) )
			return;

		echo '<div class="alignleft actions">';

		if ( 'recently_activated' == $status ) {
			submit_button( __( 'Clear List' ), 'button', 'clear-recent-list', false );
		} elseif ( 'top' === $which && 'mustuse' === $status ) {
			/* translators: %s: mu-plugins directory name */
			echo '<p>' . sprintf( __( 'Files in the %s directory are executed automatically.' ),
				'<code>' . str_replace( ABSPATH, '/', WPMU_PLUGIN_DIR ) . '</code>'
			) . '</p>';
		} elseif ( 'top' === $which && 'dropins' === $status ) {
			/* translators: %s: wp-content directory name */
			echo '<p>' . sprintf( __( 'Drop-ins are advanced plugins in the %s directory that replace WordPress functionality when present.' ),
				'<code>' . str_replace( ABSPATH, '', WP_CONTENT_DIR ) . '</code>'
			) . '</p>';
		}
		echo '</div>';
	}

	/**
	 * @return string
	 */
	public function current_action() {
		if ( isset($_POST['clear-recent-list']) )
			return 'clear-recent-list';

		return parent::current_action();
	}

	/**
	 *
	 * @global string $status
	 */
	public function display_rows() {
		global $status;

		if ( is_multisite() && ! $this->screen->in_admin( 'network' ) && in_array( $status, array( 'mustuse', 'dropins' ) ) )
			return;

		foreach ( $this->items as $plugin_file => $plugin_data )
			$this->single_row( array( $plugin_file, $plugin_data ) );
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

		list( $plugin_file, $plugin_data ) = $item;
		$context = $status;
		$screen = $this->screen;

		// Pre-order.
		$actions = array(
			'deactivate' => '',
			'activate' => '',
			'details' => '',
			'edit' => '',
			'delete' => '',
		);

		// Do not restrict by default
		$restrict_network_active = false;
		$restrict_network_only = false;

		if ( 'mustuse' === $context ) {
			$is_active = true;
		} elseif ( 'dropins' === $context ) {
			$dropins = _get_dropins();
			$plugin_name = $plugin_file;
			if ( $plugin_file != $plugin_data['Name'] )
				$plugin_name .= '<br/>' . $plugin_data['Name'];
			if ( true === ( $dropins[ $plugin_file ][1] ) ) { // Doesn't require a constant
				$is_active = true;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . '</strong></p>';
			} elseif ( defined( $dropins[ $plugin_file ][1] ) && constant( $dropins[ $plugin_file ][1] ) ) { // Constant is true
				$is_active = true;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . '</strong></p>';
			} else {
				$is_active = false;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . ' <span class="error-message">' . __( 'Inactive:' ) . '</span></strong> ' .
					/* translators: %s: drop-in constant name */
					sprintf( __( 'Requires %s in <code>wp-config.php</code>.' ),
						"define('" . $dropins[ $plugin_file ][1] . "', true);"
					) . '</p>';
			}
			if ( $plugin_data['Description'] )
				$description .= '<p>' . $plugin_data['Description'] . '</p>';
		} else {
			if ( $screen->in_admin( 'network' ) ) {
				$is_active = is_plugin_active_for_network( $plugin_file );
			} else {
				$is_active = is_plugin_active( $plugin_file );
				$restrict_network_active = ( is_multisite() && is_plugin_active_for_network( $plugin_file ) );
				$restrict_network_only = ( is_multisite() && is_network_only_plugin( $plugin_file ) && ! $is_active );
			}

			if ( $screen->in_admin( 'network' ) ) {
				if ( $is_active ) {
					if ( current_user_can( 'manage_network_plugins' ) ) {
						/* translators: %s: plugin name */
						$actions['deactivate'] = '<a href="' . wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin_file ) . '" aria-label="' . esc_attr( sprintf( __( 'Network deactivate %s' ), $plugin_data['Name'] ) ) . '">' . __( 'Network Deactivate' ) . '</a>';
						}
				} else {
					if ( current_user_can( 'manage_network_plugins' ) ) {
						/* translators: %s: plugin name */
						$actions['activate'] = '<a href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'activate-plugin_' . $plugin_file ) . '" class="edit" aria-label="' . esc_attr( sprintf( __( 'Network Activate %s' ), $plugin_data['Name'] ) ) . '">' . __( 'Network Activate' ) . '</a>';
					}
					if ( current_user_can( 'delete_plugins' ) && ! is_plugin_active( $plugin_file ) ) {
						/* translators: %s: plugin name */
						$actions['delete'] = '<a href="' . wp_nonce_url( 'plugins.php?action=delete-selected&amp;checked[]=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'bulk-plugins' ) . '" class="delete" aria-label="' . esc_attr( sprintf( __( 'Delete %s' ), $plugin_data['Name'] ) ) . '">' . __( 'Delete' ) . '</a>';
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
					/* translators: %s: plugin name */
					$actions['deactivate'] = '<a href="' . wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'deactivate-plugin_' . $plugin_file ) . '" aria-label="' . esc_attr( sprintf( __( 'Deactivate %s' ), $plugin_data['Name'] ) ) . '">' . __( 'Deactivate' ) . '</a>';
				} else {
					/* translators: %s: plugin name */
					$actions['activate'] = '<a href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'activate-plugin_' . $plugin_file ) . '" class="edit" aria-label="' . esc_attr( sprintf( __( 'Activate %s' ), $plugin_data['Name'] ) ) . '">' . __( 'Activate' ) . '</a>';

					if ( ! is_multisite() && current_user_can( 'delete_plugins' ) ) {
						/* translators: %s: plugin name */
						$actions['delete'] = '<a href="' . wp_nonce_url( 'plugins.php?action=delete-selected&amp;checked[]=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page . '&amp;s=' . $s, 'bulk-plugins' ) . '" class="delete" aria-label="' . esc_attr( sprintf( __( 'Delete %s' ), $plugin_data['Name'] ) ) . '">' . __( 'Delete' ) . '</a>';
					}
				} // end if $is_active

			 } // end if $screen->in_admin( 'network' )

			if ( ( ! is_multisite() || $screen->in_admin( 'network' ) ) && current_user_can( 'edit_plugins' ) && is_writable( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
				/* translators: %s: plugin name */
				$actions['edit'] = '<a href="plugin-editor.php?file=' . $plugin_file . '" class="edit" aria-label="' . esc_attr( sprintf( __( 'Edit %s' ), $plugin_data['Name'] ) ) . '">' . __( 'Edit' ) . '</a>';
			}
		} // end if $context

		$actions = array_filter( $actions );

		if ( $screen->in_admin( 'network' ) ) {

			/**
			 * Filter the action links displayed for each plugin in the Network Admin Plugins list table.
			 *
			 * The default action links for the Network plugins list table include
			 * 'Network Activate', 'Network Deactivate', 'Edit', and 'Delete'.
			 *
			 * @since 3.1.0 As `{$prefix}_plugin_action_links`
			 * @since 4.4.0
			 *
			 * @param array  $actions     An array of plugin action links.
			 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
			 * @param array  $plugin_data An array of plugin data.
			 * @param string $context     The plugin context. Defaults are 'All', 'Active',
			 *                            'Inactive', 'Recently Activated', 'Upgrade',
			 *                            'Must-Use', 'Drop-ins', 'Search'.
			 */
			$actions = apply_filters( 'network_admin_plugin_action_links', $actions, $plugin_file, $plugin_data, $context );

			/**
			 * Filter the list of action links displayed for a specific plugin in the Network Admin Plugins list table.
			 *
			 * The dynamic portion of the hook name, $plugin_file, refers to the path
			 * to the plugin file, relative to the plugins directory.
			 *
			 * @since 3.1.0 As `{$prefix}_plugin_action_links_{$plugin_file}`
			 * @since 4.4.0
			 *
			 * @param array  $actions     An array of plugin action links.
			 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
			 * @param array  $plugin_data An array of plugin data.
			 * @param string $context     The plugin context. Defaults are 'All', 'Active',
			 *                            'Inactive', 'Recently Activated', 'Upgrade',
			 *                            'Must-Use', 'Drop-ins', 'Search'.
			 */
			$actions = apply_filters( "network_admin_plugin_action_links_{$plugin_file}", $actions, $plugin_file, $plugin_data, $context );

		} else {

			/**
			 * Filter the action links displayed for each plugin in the Plugins list table.
			 *
			 * The default action links for the site plugins list table include
			 * 'Activate', 'Deactivate', and 'Edit', for a network site, and
			 * 'Activate', 'Deactivate', 'Edit', and 'Delete' for a single site.
			 *
			 * @since 2.5.0 As `{$prefix}_plugin_action_links`
			 * @since 4.4.0
			 *
			 * @param array  $actions     An array of plugin action links.
			 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
			 * @param array  $plugin_data An array of plugin data.
			 * @param string $context     The plugin context. Defaults are 'All', 'Active',
			 *                            'Inactive', 'Recently Activated', 'Upgrade',
			 *                            'Must-Use', 'Drop-ins', 'Search'.
			 */
			$actions = apply_filters( 'plugin_action_links', $actions, $plugin_file, $plugin_data, $context );

			/**
			 * Filter the list of action links displayed for a specific plugin in the Plugins list table.
			 *
			 * The dynamic portion of the hook name, $plugin_file, refers to the path
			 * to the plugin file, relative to the plugins directory.
			 *
			 * @since 2.7.0 As `{$prefix}_plugin_action_links_{$plugin_file}`
			 * @since 4.4.0
			 *
			 * @param array  $actions     An array of plugin action links.
			 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
			 * @param array  $plugin_data An array of plugin data.
			 * @param string $context     The plugin context. Defaults are 'All', 'Active',
			 *                            'Inactive', 'Recently Activated', 'Upgrade',
			 *                            'Must-Use', 'Drop-ins', 'Search'.
			 */
			$actions = apply_filters( "plugin_action_links_{$plugin_file}", $actions, $plugin_file, $plugin_data, $context );

		}

		$class = $is_active ? 'active' : 'inactive';
		$checkbox_id =  "checkbox_" . md5($plugin_data['Name']);
		if ( $restrict_network_active || $restrict_network_only || in_array( $status, array( 'mustuse', 'dropins' ) ) ) {
			$checkbox = '';
		} else {
			$checkbox = "<label class='screen-reader-text' for='" . $checkbox_id . "' >" . sprintf( __( 'Select %s' ), $plugin_data['Name'] ) . "</label>"
				. "<input type='checkbox' name='checked[]' value='" . esc_attr( $plugin_file ) . "' id='" . $checkbox_id . "' />";
		}
		if ( 'dropins' != $context ) {
			$description = '<p>' . ( $plugin_data['Description'] ? $plugin_data['Description'] : '&nbsp;' ) . '</p>';
			$plugin_name = $plugin_data['Name'];
		}

		$id = sanitize_title( $plugin_name );
		if ( ! empty( $totals['upgrade'] ) && ! empty( $plugin_data['update'] ) )
			$class .= ' update';

		$plugin_slug = ( isset( $plugin_data['slug'] ) ) ? $plugin_data['slug'] : '';
		printf( "<tr id='%s' class='%s' data-slug='%s'>",
			$id,
			$class,
			$plugin_slug
		);

		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$extra_classes = '';
			if ( in_array( $column_name, $hidden ) ) {
				$extra_classes = ' hidden';
			}

			switch ( $column_name ) {
				case 'cb':
					echo "<th scope='row' class='check-column'>$checkbox</th>";
					break;
				case 'name':
					echo "<td class='plugin-title column-primary'><strong>$plugin_name</strong>";
					echo $this->row_actions( $actions, true );
					echo "</td>";
					break;
				case 'description':
					$classes = 'column-description desc';

					echo "<td class='$classes{$extra_classes}'>
						<div class='plugin-description'>$description</div>
						<div class='$class second plugin-version-author-uri'>";

					$plugin_meta = array();
					if ( !empty( $plugin_data['Version'] ) )
						$plugin_meta[] = sprintf( __( 'Version %s' ), $plugin_data['Version'] );
					if ( !empty( $plugin_data['Author'] ) ) {
						$author = $plugin_data['Author'];
						if ( !empty( $plugin_data['AuthorURI'] ) )
							$author = '<a href="' . $plugin_data['AuthorURI'] . '">' . $plugin_data['Author'] . '</a>';
						$plugin_meta[] = sprintf( __( 'By %s' ), $author );
					}

					// Details link using API info, if available
					if ( isset( $plugin_data['slug'] ) && current_user_can( 'install_plugins' ) ) {
						$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
							esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_data['slug'] .
								'&TB_iframe=true&width=600&height=550' ) ),
							esc_attr( sprintf( __( 'More information about %s' ), $plugin_name ) ),
							esc_attr( $plugin_name ),
							__( 'View details' )
						);
					} elseif ( ! empty( $plugin_data['PluginURI'] ) ) {
						$plugin_meta[] = sprintf( '<a href="%s">%s</a>',
							esc_url( $plugin_data['PluginURI'] ),
							__( 'Visit plugin site' )
						);
					}

					/**
					 * Filter the array of row meta for each plugin in the Plugins list table.
					 *
					 * @since 2.8.0
					 *
					 * @param array  $plugin_meta An array of the plugin's metadata,
					 *                            including the version, author,
					 *                            author URI, and plugin URI.
					 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
					 * @param array  $plugin_data An array of plugin data.
					 * @param string $status      Status of the plugin. Defaults are 'All', 'Active',
					 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
					 *                            'Drop-ins', 'Search'.
					 */
					$plugin_meta = apply_filters( 'plugin_row_meta', $plugin_meta, $plugin_file, $plugin_data, $status );
					echo implode( ' | ', $plugin_meta );

					echo "</div></td>";
					break;
				default:
					$classes = "$column_name column-$column_name$class";

					echo "<td class='$classes{$extra_classes}'>";

					/**
					 * Fires inside each custom column of the Plugins list table.
					 *
					 * @since 3.1.0
					 *
					 * @param string $column_name Name of the column.
					 * @param string $plugin_file Path to the plugin file.
					 * @param array  $plugin_data An array of plugin data.
					 */
					do_action( 'manage_plugins_custom_column', $column_name, $plugin_file, $plugin_data );

					echo "</td>";
			}
		}

		echo "</tr>";

		/**
		 * Fires after each row in the Plugins list table.
		 *
		 * @since 2.3.0
		 *
		 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
		 * @param array  $plugin_data An array of plugin data.
		 * @param string $status      Status of the plugin. Defaults are 'All', 'Active',
		 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                            'Drop-ins', 'Search'.
		 */
		do_action( 'after_plugin_row', $plugin_file, $plugin_data, $status );

		/**
		 * Fires after each specific row in the Plugins list table.
		 *
		 * The dynamic portion of the hook name, `$plugin_file`, refers to the path
		 * to the plugin file, relative to the plugins directory.
		 *
		 * @since 2.7.0
		 *
		 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
		 * @param array  $plugin_data An array of plugin data.
		 * @param string $status      Status of the plugin. Defaults are 'All', 'Active',
		 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                            'Drop-ins', 'Search'.
		 */
		do_action( "after_plugin_row_$plugin_file", $plugin_file, $plugin_data, $status );
	}

	/**
	 * Gets the name of the primary column for this specific list table.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @return string Unalterable name for the primary column, in this case, 'name'.
	 */
	protected function get_primary_column_name() {
		return 'name';
	}
}

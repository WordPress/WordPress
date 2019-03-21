<?php
/**
 * Toolbar API: WP_Admin_Bar class
 *
 * @package WordPress
 * @subpackage Toolbar
 * @since 3.1.0
 */

/**
 * Core class used to implement the Toolbar API.
 *
 * @since 3.1.0
 */
class WP_Admin_Bar {
	private $nodes = array();
	private $bound = false;
	public $user;

	/**
	 * @param string $name
	 * @return string|array|void
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'proto':
				return is_ssl() ? 'https://' : 'http://';

			case 'menu':
				_deprecated_argument( 'WP_Admin_Bar', '3.3.0', 'Modify admin bar nodes with WP_Admin_Bar::get_node(), WP_Admin_Bar::add_node(), and WP_Admin_Bar::remove_node(), not the <code>menu</code> property.' );
				return array(); // Sorry, folks.
		}
	}

	/**
	 */
	public function initialize() {
		$this->user = new stdClass;

		if ( is_user_logged_in() ) {
			/* Populate settings we need for the menu based on the current user. */
			$this->user->blogs = get_blogs_of_user( get_current_user_id() );
			if ( is_multisite() ) {
				$this->user->active_blog    = get_active_blog_for_user( get_current_user_id() );
				$this->user->domain         = empty( $this->user->active_blog ) ? user_admin_url() : trailingslashit( get_home_url( $this->user->active_blog->blog_id ) );
				$this->user->account_domain = $this->user->domain;
			} else {
				$this->user->active_blog    = $this->user->blogs[ get_current_blog_id() ];
				$this->user->domain         = trailingslashit( home_url() );
				$this->user->account_domain = $this->user->domain;
			}
		}

		add_action( 'wp_head', 'wp_admin_bar_header' );

		add_action( 'admin_head', 'wp_admin_bar_header' );

		if ( current_theme_supports( 'admin-bar' ) ) {
			/**
			 * To remove the default padding styles from WordPress for the Toolbar, use the following code:
			 * add_theme_support( 'admin-bar', array( 'callback' => '__return_false' ) );
			 */
			$admin_bar_args  = get_theme_support( 'admin-bar' );
			$header_callback = $admin_bar_args[0]['callback'];
		}

		if ( empty( $header_callback ) ) {
			$header_callback = '_admin_bar_bump_cb';
		}

		add_action( 'wp_head', $header_callback );

		wp_enqueue_script( 'admin-bar' );
		wp_enqueue_style( 'admin-bar' );

		/**
		 * Fires after WP_Admin_Bar is initialized.
		 *
		 * @since 3.1.0
		 */
		do_action( 'admin_bar_init' );
	}

	/**
	 * @param array $node
	 */
	public function add_menu( $node ) {
		$this->add_node( $node );
	}

	/**
	 * @param string $id
	 */
	public function remove_menu( $id ) {
		$this->remove_node( $id );
	}

	/**
	 * Adds a node to the menu.
	 *
	 * @since 3.1.0
	 * @since 4.5.0 Added the ability to pass 'lang' and 'dir' meta data.
	 *
	 * @param array $args {
	 *     Arguments for adding a node.
	 *
	 *     @type string $id     ID of the item.
	 *     @type string $title  Title of the node.
	 *     @type string $parent Optional. ID of the parent node.
	 *     @type string $href   Optional. Link for the item.
	 *     @type bool   $group  Optional. Whether or not the node is a group. Default false.
	 *     @type array  $meta   Meta data including the following keys: 'html', 'class', 'rel', 'lang', 'dir',
	 *                          'onclick', 'target', 'title', 'tabindex'. Default empty.
	 * }
	 */
	public function add_node( $args ) {
		// Shim for old method signature: add_node( $parent_id, $menu_obj, $args )
		if ( func_num_args() >= 3 && is_string( func_get_arg( 0 ) ) ) {
			$args = array_merge( array( 'parent' => func_get_arg( 0 ) ), func_get_arg( 2 ) );
		}

		if ( is_object( $args ) ) {
			$args = get_object_vars( $args );
		}

		// Ensure we have a valid title.
		if ( empty( $args['id'] ) ) {
			if ( empty( $args['title'] ) ) {
				return;
			}

			_doing_it_wrong( __METHOD__, __( 'The menu ID should not be empty.' ), '3.3.0' );
			// Deprecated: Generate an ID from the title.
			$args['id'] = esc_attr( sanitize_title( trim( $args['title'] ) ) );
		}

		$defaults = array(
			'id'     => false,
			'title'  => false,
			'parent' => false,
			'href'   => false,
			'group'  => false,
			'meta'   => array(),
		);

		// If the node already exists, keep any data that isn't provided.
		if ( $maybe_defaults = $this->get_node( $args['id'] ) ) {
			$defaults = get_object_vars( $maybe_defaults );
		}

		// Do the same for 'meta' items.
		if ( ! empty( $defaults['meta'] ) && ! empty( $args['meta'] ) ) {
			$args['meta'] = wp_parse_args( $args['meta'], $defaults['meta'] );
		}

		$args = wp_parse_args( $args, $defaults );

		$back_compat_parents = array(
			'my-account-with-avatar' => array( 'my-account', '3.3' ),
			'my-blogs'               => array( 'my-sites', '3.3' ),
		);

		if ( isset( $back_compat_parents[ $args['parent'] ] ) ) {
			list( $new_parent, $version ) = $back_compat_parents[ $args['parent'] ];
			_deprecated_argument( __METHOD__, $version, sprintf( 'Use <code>%s</code> as the parent for the <code>%s</code> admin bar node instead of <code>%s</code>.', $new_parent, $args['id'], $args['parent'] ) );
			$args['parent'] = $new_parent;
		}

		$this->_set_node( $args );
	}

	/**
	 * @param array $args
	 */
	final protected function _set_node( $args ) {
		$this->nodes[ $args['id'] ] = (object) $args;
	}

	/**
	 * Gets a node.
	 *
	 * @param string $id
	 * @return object Node.
	 */
	final public function get_node( $id ) {
		if ( $node = $this->_get_node( $id ) ) {
			return clone $node;
		}
	}

	/**
	 * @param string $id
	 * @return object|void
	 */
	final protected function _get_node( $id ) {
		if ( $this->bound ) {
			return;
		}

		if ( empty( $id ) ) {
			$id = 'root';
		}

		if ( isset( $this->nodes[ $id ] ) ) {
			return $this->nodes[ $id ];
		}
	}

	/**
	 * @return array|void
	 */
	final public function get_nodes() {
		if ( ! $nodes = $this->_get_nodes() ) {
			return;
		}

		foreach ( $nodes as &$node ) {
			$node = clone $node;
		}
		return $nodes;
	}

	/**
	 * @return array|void
	 */
	final protected function _get_nodes() {
		if ( $this->bound ) {
			return;
		}

		return $this->nodes;
	}

	/**
	 * Add a group to a menu node.
	 *
	 * @since 3.3.0
	 *
	 * @param array $args {
	 *     Array of arguments for adding a group.
	 *
	 *     @type string $id     ID of the item.
	 *     @type string $parent Optional. ID of the parent node. Default 'root'.
	 *     @type array  $meta   Meta data for the group including the following keys:
	 *                         'class', 'onclick', 'target', and 'title'.
	 * }
	 */
	final public function add_group( $args ) {
		$args['group'] = true;

		$this->add_node( $args );
	}

	/**
	 * Remove a node.
	 *
	 * @param string $id The ID of the item.
	 */
	public function remove_node( $id ) {
		$this->_unset_node( $id );
	}

	/**
	 * @param string $id
	 */
	final protected function _unset_node( $id ) {
		unset( $this->nodes[ $id ] );
	}

	/**
	 */
	public function render() {
		$root = $this->_bind();
		if ( $root ) {
			$this->_render( $root );
		}
	}

	/**
	 * @return object|void
	 */
	final protected function _bind() {
		if ( $this->bound ) {
			return;
		}

		// Add the root node.
		// Clear it first, just in case. Don't mess with The Root.
		$this->remove_node( 'root' );
		$this->add_node(
			array(
				'id'    => 'root',
				'group' => false,
			)
		);

		// Normalize nodes: define internal 'children' and 'type' properties.
		foreach ( $this->_get_nodes() as $node ) {
			$node->children = array();
			$node->type     = ( $node->group ) ? 'group' : 'item';
			unset( $node->group );

			// The Root wants your orphans. No lonely items allowed.
			if ( ! $node->parent ) {
				$node->parent = 'root';
			}
		}

		foreach ( $this->_get_nodes() as $node ) {
			if ( 'root' == $node->id ) {
				continue;
			}

			// Fetch the parent node. If it isn't registered, ignore the node.
			if ( ! $parent = $this->_get_node( $node->parent ) ) {
				continue;
			}

			// Generate the group class (we distinguish between top level and other level groups).
			$group_class = ( $node->parent == 'root' ) ? 'ab-top-menu' : 'ab-submenu';

			if ( $node->type == 'group' ) {
				if ( empty( $node->meta['class'] ) ) {
					$node->meta['class'] = $group_class;
				} else {
					$node->meta['class'] .= ' ' . $group_class;
				}
			}

			// Items in items aren't allowed. Wrap nested items in 'default' groups.
			if ( $parent->type == 'item' && $node->type == 'item' ) {
				$default_id = $parent->id . '-default';
				$default    = $this->_get_node( $default_id );

				// The default group is added here to allow groups that are
				// added before standard menu items to render first.
				if ( ! $default ) {
					// Use _set_node because add_node can be overloaded.
					// Make sure to specify default settings for all properties.
					$this->_set_node(
						array(
							'id'       => $default_id,
							'parent'   => $parent->id,
							'type'     => 'group',
							'children' => array(),
							'meta'     => array(
								'class' => $group_class,
							),
							'title'    => false,
							'href'     => false,
						)
					);
					$default            = $this->_get_node( $default_id );
					$parent->children[] = $default;
				}
				$parent = $default;

				// Groups in groups aren't allowed. Add a special 'container' node.
				// The container will invisibly wrap both groups.
			} elseif ( $parent->type == 'group' && $node->type == 'group' ) {
				$container_id = $parent->id . '-container';
				$container    = $this->_get_node( $container_id );

				// We need to create a container for this group, life is sad.
				if ( ! $container ) {
					// Use _set_node because add_node can be overloaded.
					// Make sure to specify default settings for all properties.
					$this->_set_node(
						array(
							'id'       => $container_id,
							'type'     => 'container',
							'children' => array( $parent ),
							'parent'   => false,
							'title'    => false,
							'href'     => false,
							'meta'     => array(),
						)
					);

					$container = $this->_get_node( $container_id );

					// Link the container node if a grandparent node exists.
					$grandparent = $this->_get_node( $parent->parent );

					if ( $grandparent ) {
						$container->parent = $grandparent->id;

						$index = array_search( $parent, $grandparent->children, true );
						if ( $index === false ) {
							$grandparent->children[] = $container;
						} else {
							array_splice( $grandparent->children, $index, 1, array( $container ) );
						}
					}

					$parent->parent = $container->id;
				}

				$parent = $container;
			}

			// Update the parent ID (it might have changed).
			$node->parent = $parent->id;

			// Add the node to the tree.
			$parent->children[] = $node;
		}

		$root        = $this->_get_node( 'root' );
		$this->bound = true;
		return $root;
	}

	/**
	 * @global bool $is_IE
	 * @param object $root
	 */
	final protected function _render( $root ) {
		global $is_IE;

		// Add browser classes.
		// We have to do this here since admin bar shows on the front end.
		$class = 'nojq nojs';
		if ( $is_IE ) {
			if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 7' ) ) {
				$class .= ' ie7';
			} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 8' ) ) {
				$class .= ' ie8';
			} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 9' ) ) {
				$class .= ' ie9';
			}
		} elseif ( wp_is_mobile() ) {
			$class .= ' mobile';
		}

		?>
		<div id="wpadminbar" class="<?php echo $class; ?>">
			<?php if ( ! is_admin() ) { ?>
				<a class="screen-reader-shortcut" href="#wp-toolbar" tabindex="1"><?php _e( 'Skip to toolbar' ); ?></a>
			<?php } ?>
			<div class="quicklinks" id="wp-toolbar" role="navigation" aria-label="<?php esc_attr_e( 'Toolbar' ); ?>">
				<?php
				foreach ( $root->children as $group ) {
					$this->_render_group( $group );
				}
				?>
			</div>
			<?php if ( is_user_logged_in() ) : ?>
			<a class="screen-reader-shortcut" href="<?php echo esc_url( wp_logout_url() ); ?>"><?php _e( 'Log Out' ); ?></a>
			<?php endif; ?>
		</div>

		<?php
	}

	/**
	 * @param object $node
	 */
	final protected function _render_container( $node ) {
		if ( $node->type != 'container' || empty( $node->children ) ) {
			return;
		}

		echo '<div id="' . esc_attr( 'wp-admin-bar-' . $node->id ) . '" class="ab-group-container">';
		foreach ( $node->children as $group ) {
			$this->_render_group( $group );
		}
		echo '</div>';
	}

	/**
	 * @param object $node
	 */
	final protected function _render_group( $node ) {
		if ( $node->type == 'container' ) {
			$this->_render_container( $node );
			return;
		}
		if ( $node->type != 'group' || empty( $node->children ) ) {
			return;
		}

		if ( ! empty( $node->meta['class'] ) ) {
			$class = ' class="' . esc_attr( trim( $node->meta['class'] ) ) . '"';
		} else {
			$class = '';
		}

		echo "<ul id='" . esc_attr( 'wp-admin-bar-' . $node->id ) . "'$class>";
		foreach ( $node->children as $item ) {
			$this->_render_item( $item );
		}
		echo '</ul>';
	}

	/**
	 * @param object $node
	 */
	final protected function _render_item( $node ) {
		if ( $node->type != 'item' ) {
			return;
		}

		$is_parent             = ! empty( $node->children );
		$has_link              = ! empty( $node->href );
		$is_root_top_item      = 'root-default' === $node->parent;
		$is_top_secondary_item = 'top-secondary' === $node->parent;

		// Allow only numeric values, then casted to integers, and allow a tabindex value of `0` for a11y.
		$tabindex        = ( isset( $node->meta['tabindex'] ) && is_numeric( $node->meta['tabindex'] ) ) ? (int) $node->meta['tabindex'] : '';
		$aria_attributes = ( '' !== $tabindex ) ? ' tabindex="' . $tabindex . '"' : '';

		$menuclass = '';
		$arrow     = '';

		if ( $is_parent ) {
			$menuclass        = 'menupop ';
			$aria_attributes .= ' aria-haspopup="true"';
		}

		if ( ! empty( $node->meta['class'] ) ) {
			$menuclass .= $node->meta['class'];
		}

		// Print the arrow icon for the menu children with children.
		if ( ! $is_root_top_item && ! $is_top_secondary_item && $is_parent ) {
			$arrow = '<span class="wp-admin-bar-arrow" aria-hidden="true"></span>';
		}

		if ( $menuclass ) {
			$menuclass = ' class="' . esc_attr( trim( $menuclass ) ) . '"';
		}

		echo "<li id='" . esc_attr( 'wp-admin-bar-' . $node->id ) . "'$menuclass>";

		if ( $has_link ) {
			$attributes = array( 'onclick', 'target', 'title', 'rel', 'lang', 'dir' );
			echo "<a class='ab-item'$aria_attributes href='" . esc_url( $node->href ) . "'";
			if ( ! empty( $node->meta['onclick'] ) ) {
				echo ' onclick="' . esc_js( $node->meta['onclick'] ) . '"';
			}
		} else {
			$attributes = array( 'onclick', 'target', 'title', 'rel', 'lang', 'dir' );
			echo '<div class="ab-item ab-empty-item"' . $aria_attributes;
		}

		foreach ( $attributes as $attribute ) {
			if ( ! empty( $node->meta[ $attribute ] ) ) {
				echo " $attribute='" . esc_attr( $node->meta[ $attribute ] ) . "'";
			}
		}

		echo ">{$arrow}{$node->title}";

		if ( $has_link ) {
			echo '</a>';
		} else {
			echo '</div>';
		}

		if ( $is_parent ) {
			echo '<div class="ab-sub-wrapper">';
			foreach ( $node->children as $group ) {
				$this->_render_group( $group );
			}
			echo '</div>';
		}

		if ( ! empty( $node->meta['html'] ) ) {
			echo $node->meta['html'];
		}

		echo '</li>';
	}

	/**
	 * Renders toolbar items recursively.
	 *
	 * @since 3.1.0
	 * @deprecated 3.3.0 Use WP_Admin_Bar::_render_item() or WP_Admin_bar::render() instead.
	 * @see WP_Admin_Bar::_render_item()
	 * @see WP_Admin_Bar::render()
	 *
	 * @param string $id    Unused.
	 * @param object $node
	 */
	public function recursive_render( $id, $node ) {
		_deprecated_function( __METHOD__, '3.3.0', 'WP_Admin_bar::render(), WP_Admin_Bar::_render_item()' );
		$this->_render_item( $node );
	}

	/**
	 */
	public function add_menus() {
		// User related, aligned right.
		add_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 0 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 4 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_my_account_item', 7 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_recovery_mode_menu', 8 );

		// Site related.
		add_action( 'admin_bar_menu', 'wp_admin_bar_sidebar_toggle', 0 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_site_menu', 30 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_customize_menu', 40 );
		add_action( 'admin_bar_menu', 'wp_admin_bar_updates_menu', 50 );

		// Content related.
		if ( ! is_network_admin() && ! is_user_admin() ) {
			add_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
			add_action( 'admin_bar_menu', 'wp_admin_bar_new_content_menu', 70 );
		}
		add_action( 'admin_bar_menu', 'wp_admin_bar_edit_menu', 80 );

		add_action( 'admin_bar_menu', 'wp_admin_bar_add_secondary_groups', 200 );

		/**
		 * Fires after menus are added to the menu bar.
		 *
		 * @since 3.1.0
		 */
		do_action( 'add_admin_bar_menus' );
	}
}

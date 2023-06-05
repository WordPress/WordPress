<?php
/**
 * WooCommerce Navigation Menu
 *
 * @package Woocommerce Navigation
 */

namespace Automattic\WooCommerce\Admin\Features\Navigation;

use Automattic\WooCommerce\Admin\Features\Navigation\Favorites;
use Automattic\WooCommerce\Admin\Features\Navigation\Screen;
use Automattic\WooCommerce\Admin\Features\Navigation\CoreMenu;

/**
 * Contains logic for the WooCommerce Navigation menu.
 */
class Menu {
	/**
	 * Class instance.
	 *
	 * @var Menu instance
	 */
	protected static $instance = null;

	/**
	 * Array index of menu capability.
	 *
	 * @var int
	 */
	const CAPABILITY = 1;

	/**
	 * Array index of menu callback.
	 *
	 * @var int
	 */
	const CALLBACK = 2;

	/**
	 * Array index of menu callback.
	 *
	 * @var int
	 */
	const SLUG = 3;

	/**
	 * Array index of menu CSS class string.
	 *
	 * @var int
	 */
	const CSS_CLASSES = 4;

	/**
	 * Array of usable menu IDs.
	 */
	const MENU_IDS = array(
		'primary',
		'favorites',
		'plugins',
		'secondary',
	);

	/**
	 * Store menu items.
	 *
	 * @var array
	 */
	protected static $menu_items = array();

	/**
	 * Store categories with menu item IDs.
	 *
	 * @var array
	 */
	protected static $categories = array(
		'woocommerce' => array(),
	);

	/**
	 * Registered callbacks or URLs with migration boolean as key value pairs.
	 *
	 * @var array
	 */
	protected static $callbacks = array();

	/**
	 * Get class instance.
	 */
	final public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Init.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_core_items' ), 100 );
		add_filter( 'admin_enqueue_scripts', array( $this, 'enqueue_data' ), 20 );

		add_filter( 'admin_menu', array( $this, 'migrate_core_child_items' ), PHP_INT_MAX - 1 );
		add_filter( 'admin_menu', array( $this, 'migrate_menu_items' ), PHP_INT_MAX - 2 );
	}

	/**
	 * Convert a WordPress menu callback to a URL.
	 *
	 * @param string $callback Menu callback.
	 * @return string
	 */
	public static function get_callback_url( $callback ) {
		// Return the full URL.
		if ( strpos( $callback, 'http' ) === 0 ) {
			return $callback;
		}

		$pos  = strpos( $callback, '?' );
		$file = $pos > 0 ? substr( $callback, 0, $pos ) : $callback;
		if ( file_exists( ABSPATH . "/wp-admin/$file" ) ) {
			return $callback;
		}
		return 'admin.php?page=' . $callback;
	}

	/**
	 * Get the parent key if one exists.
	 *
	 * @param string $callback Callback or URL.
	 * @return string|null
	 */
	public static function get_parent_key( $callback ) {
		global $submenu;

		if ( ! $submenu ) {
			return null;
		}

		// This is already a parent item.
		if ( isset( $submenu[ $callback ] ) ) {
			return null;
		}

		foreach ( $submenu as $key => $menu ) {
			foreach ( $menu as $item ) {
				if ( $item[ self::CALLBACK ] === $callback ) {
					return $key;
				}
			}
		}

		return null;
	}

	/**
	 * Adds a top level menu item to the navigation.
	 *
	 * @param array $args Array containing the necessary arguments.
	 *    $args = array(
	 *      'id'      => (string) The unique ID of the menu item. Required.
	 *      'title'   => (string) Title of the menu item. Required.
	 *      'url'     => (string) URL or callback to be used. Required.
	 *      'order'   => (int) Menu item order.
	 *      'migrate' => (bool) Whether or not to hide the item in the wp admin menu.
	 *      'menuId'  => (string) The ID of the menu to add the category to.
	 *    ).
	 */
	private static function add_category( $args ) {
		if ( ! isset( $args['id'] ) || isset( self::$menu_items[ $args['id'] ] ) ) {
			return;
		}

		$defaults           = array(
			'id'         => '',
			'title'      => '',
			'order'      => 100,
			'migrate'    => true,
			'menuId'     => 'primary',
			'isCategory' => true,
		);
		$menu_item          = wp_parse_args( $args, $defaults );
		$menu_item['title'] = wp_strip_all_tags( wp_specialchars_decode( $menu_item['title'] ) );
		unset( $menu_item['url'] );
		unset( $menu_item['capability'] );

		if ( ! isset( $menu_item['parent'] ) ) {
			$menu_item['parent']          = 'woocommerce';
			$menu_item['backButtonLabel'] = __(
				'WooCommerce Home',
				'woocommerce'
			);
		}

		self::$menu_items[ $menu_item['id'] ]       = $menu_item;
		self::$categories[ $menu_item['id'] ]       = array();
		self::$categories[ $menu_item['parent'] ][] = $menu_item['id'];

		if ( isset( $args['url'] ) ) {
			self::$callbacks[ $args['url'] ] = $menu_item['migrate'];
		}
	}

	/**
	 * Adds a child menu item to the navigation.
	 *
	 * @param array $args Array containing the necessary arguments.
	 *    $args = array(
	 *      'id'              => (string) The unique ID of the menu item. Required.
	 *      'title'           => (string) Title of the menu item. Required.
	 *      'parent'          => (string) Parent menu item ID.
	 *      'capability'      => (string) Capability to view this menu item.
	 *      'url'             => (string) URL or callback to be used. Required.
	 *      'order'           => (int) Menu item order.
	 *      'migrate'         => (bool) Whether or not to hide the item in the wp admin menu.
	 *      'menuId'          => (string) The ID of the menu to add the item to.
	 *      'matchExpression' => (string) A regular expression used to identify if the menu item is active.
	 *    ).
	 */
	private static function add_item( $args ) {
		if ( ! isset( $args['id'] ) ) {
			return;
		}

		if ( isset( self::$menu_items[ $args['id'] ] ) ) {
			error_log(  // phpcs:ignore
				sprintf(
					/* translators: 1: Duplicate menu item path. */
					esc_html__( 'You have attempted to register a duplicate item with WooCommerce Navigation: %1$s', 'woocommerce' ),
					'`' . $args['id'] . '`'
				)
			);
			return;
		}

		$defaults           = array(
			'id'         => '',
			'title'      => '',
			'capability' => 'manage_woocommerce',
			'url'        => '',
			'order'      => 100,
			'migrate'    => true,
			'menuId'     => 'primary',
		);
		$menu_item          = wp_parse_args( $args, $defaults );
		$menu_item['title'] = wp_strip_all_tags( wp_specialchars_decode( $menu_item['title'] ) );
		$menu_item['url']   = self::get_callback_url( $menu_item['url'] );

		if ( ! isset( $menu_item['parent'] ) ) {
			$menu_item['parent'] = 'woocommerce';
		}

		$menu_item['menuId'] = self::get_item_menu_id( $menu_item );

		self::$menu_items[ $menu_item['id'] ]       = $menu_item;
		self::$categories[ $menu_item['parent'] ][] = $menu_item['id'];

		if ( isset( $args['url'] ) ) {
			self::$callbacks[ $args['url'] ] = $menu_item['migrate'];
		}
	}

	/**
	 * Get an item's menu ID from its parent.
	 *
	 * @param array $item Item args.
	 * @return string
	 */
	public static function get_item_menu_id( $item ) {
		$favorites = Favorites::get_all( get_current_user_id() );
		if ( is_array( $favorites ) && ! empty( $favorites ) && in_array( $item['id'], $favorites, true ) ) {
			return 'favorites';
		}

		if ( isset( $item['parent'] ) && isset( self::$menu_items[ $item['parent'] ] ) ) {
			$menu_id = self::$menu_items[ $item['parent'] ]['menuId'];
			return 'favorites' === $menu_id
				? 'plugins'
				: $menu_id;
		}

		return $item['menuId'];
	}

	/**
	 * Adds a plugin category.
	 *
	 * @param array $args Array containing the necessary arguments.
	 *    $args = array(
	 *      'id'      => (string) The unique ID of the menu item. Required.
	 *      'title'   => (string) Title of the menu item. Required.
	 *      'url'     => (string) URL or callback to be used. Required.
	 *      'migrate' => (bool) Whether or not to hide the item in the wp admin menu.
	 *      'order'   => (int) Menu item order.
	 *    ).
	 */
	public static function add_plugin_category( $args ) {
		$category_args = array_merge(
			$args,
			array(
				'menuId' => 'plugins',
			)
		);

		if ( ! isset( $category_args['parent'] ) ) {
			unset( $category_args['order'] );
		}

		$menu_id = self::get_item_menu_id( $category_args );
		if ( ! in_array( $menu_id, array( 'plugins', 'favorites' ), true ) ) {
			return;
		}

		$category_args['menuId'] = $menu_id;

		self::add_category( $category_args );
	}

	/**
	 * Adds a plugin item.
	 *
	 * @param array $args Array containing the necessary arguments.
	 *    $args = array(
	 *      'id'              => (string) The unique ID of the menu item. Required.
	 *      'title'           => (string) Title of the menu item. Required.
	 *      'parent'          => (string) Parent menu item ID.
	 *      'capability'      => (string) Capability to view this menu item.
	 *      'url'             => (string) URL or callback to be used. Required.
	 *      'migrate'         => (bool) Whether or not to hide the item in the wp admin menu.
	 *      'order'           => (int) Menu item order.
	 *      'matchExpression' => (string) A regular expression used to identify if the menu item is active.
	 *    ).
	 */
	public static function add_plugin_item( $args ) {
		if ( ! isset( $args['parent'] ) ) {
			unset( $args['order'] );
		}

		$item_args = array_merge(
			$args,
			array(
				'menuId' => 'plugins',
			)
		);

		$menu_id = self::get_item_menu_id( $item_args );

		if ( 'plugins' !== $menu_id ) {
			return;
		}

		self::add_item( $item_args );
	}

	/**
	 * Adds a plugin setting item.
	 *
	 * @param array $args Array containing the necessary arguments.
	 *    $args = array(
	 *      'id'         => (string) The unique ID of the menu item. Required.
	 *      'title'      => (string) Title of the menu item. Required.
	 *      'capability' => (string) Capability to view this menu item.
	 *      'url'        => (string) URL or callback to be used. Required.
	 *      'migrate'    => (bool) Whether or not to hide the item in the wp admin menu.
	 *    ).
	 */
	public static function add_setting_item( $args ) {
		unset( $args['order'] );

		if ( isset( $args['parent'] ) || isset( $args['menuId'] ) ) {
			error_log(  // phpcs:ignore
				sprintf(
					/* translators: 1: Duplicate menu item path. */
					esc_html__( 'The item ID %1$s attempted to register using an invalid option. The arguments `menuId` and `parent` are not allowed for add_setting_item()', 'woocommerce' ),
					'`' . $args['id'] . '`'
				)
			);
		}

		$item_args = array_merge(
			$args,
			array(
				'menuId' => 'secondary',
				'parent' => 'woocommerce-settings',
			)
		);

		self::add_item( $item_args );
	}



	/**
	 * Get menu item templates for a given post type.
	 *
	 * @param string $post_type Post type to add.
	 * @param array  $menu_args Arguments merged with the returned menu items.
	 * @return array
	 */
	public static function get_post_type_items( $post_type, $menu_args = array() ) {
		$post_type_object = get_post_type_object( $post_type );

		if ( ! $post_type_object || ! $post_type_object->show_in_menu ) {
			return;
		}

		$parent           = isset( $menu_args['parent'] ) ? $menu_args['parent'] . '-' : '';
		$match_expression = isset( $_GET['post'] ) && get_post_type( intval( $_GET['post'] ) ) === $post_type // phpcs:ignore WordPress.Security.NonceVerification
			? '(edit.php|post.php)'
			: null;

		return array(
			'default' => array_merge(
				array(
					'title'           => esc_attr( $post_type_object->labels->menu_name ),
					'capability'      => $post_type_object->cap->edit_posts,
					'id'              => $parent . $post_type,
					'url'             => "edit.php?post_type={$post_type}",
					'matchExpression' => $match_expression,
				),
				$menu_args
			),
			'all'     => array_merge(
				array(
					'title'           => esc_attr( $post_type_object->labels->all_items ),
					'capability'      => $post_type_object->cap->edit_posts,
					'id'              => "{$parent}{$post_type}-all-items",
					'url'             => "edit.php?post_type={$post_type}",
					'order'           => 10,
					'matchExpression' => $match_expression,
				),
				$menu_args
			),
			'new'     => array_merge(
				array(
					'title'      => esc_attr( $post_type_object->labels->add_new ),
					'capability' => $post_type_object->cap->create_posts,
					'id'         => "{$parent}{$post_type}-add-new",
					'url'        => "post-new.php?post_type={$post_type}",
					'order'      => 20,
				),
				$menu_args
			),
		);
	}

	/**
	 * Get menu item templates for a given taxonomy.
	 *
	 * @param string $taxonomy Taxonomy to add.
	 * @param array  $menu_args Arguments merged with the returned menu items.
	 * @return array
	 */
	public static function get_taxonomy_items( $taxonomy, $menu_args = array() ) {
		$taxonomy_object = get_taxonomy( $taxonomy );

		if ( ! $taxonomy_object || ! $taxonomy_object->show_in_menu ) {
			return;
		}

		$parent             = isset( $menu_args['parent'] ) ? $menu_args['parent'] . '-' : '';
		$product_type_query = ! empty( $taxonomy_object->object_type )
			? "&post_type={$taxonomy_object->object_type[0]}"
			: '';
		$match_expression   = 'term.php';                               // Match term.php pages.
		$match_expression  .= "(?=.*[?|&]taxonomy={$taxonomy}(&|$|#))"; // Lookahead to match a taxonomy URL param.
		$match_expression  .= '|';                                      // Or.
		$match_expression  .= 'edit-tags.php';                          // Match edit-tags.php pages.
		$match_expression  .= "(?=.*[?|&]taxonomy={$taxonomy}(&|$|#))"; // Lookahead to match a taxonomy URL param.

		return array(
			'default' => array_merge(
				array(
					'title'           => esc_attr( $taxonomy_object->labels->menu_name ),
					'capability'      => $taxonomy_object->cap->edit_terms,
					'id'              => $parent . $taxonomy,
					'url'             => "edit-tags.php?taxonomy={$taxonomy}{$product_type_query}",
					'matchExpression' => $match_expression,
				),
				$menu_args
			),
			'all'     => array_merge(
				array(
					'title'           => esc_attr( $taxonomy_object->labels->all_items ),
					'capability'      => $taxonomy_object->cap->edit_terms,
					'id'              => "{$parent}{$taxonomy}-all-items",
					'url'             => "edit-tags.php?taxonomy={$taxonomy}{$product_type_query}",
					'matchExpression' => $match_expression,
					'order'           => 10,
				),
				$menu_args
			),

		);
	}

	/**
	 * Add core menu items.
	 */
	public function add_core_items() {
		$categories = CoreMenu::get_categories();
		foreach ( $categories as $category ) {
			self::add_category( $category );
		}

		$items = CoreMenu::get_items();
		foreach ( $items as $item ) {
			if ( isset( $item['is_category'] ) && $item['is_category'] ) {
				self::add_category( $item );
			} else {
				self::add_item( $item );
			}
		}
	}

	/**
	 * Add an item or taxonomy.
	 *
	 * @param array $menu_item Menu item.
	 */
	public function add_item_and_taxonomy( $menu_item ) {
		if ( in_array( $menu_item[2], CoreMenu::get_excluded_items(), true ) ) {
			return;
		}

		$menu_item[2] = htmlspecialchars_decode( $menu_item[2] );

		// Don't add already added items.
		$callbacks = self::get_callbacks();
		if ( array_key_exists( $menu_item[2], $callbacks ) ) {
			return;
		}

		// Don't add these Product submenus because they are added elsewhere.
		if ( in_array( $menu_item[2], array( 'product_importer', 'product_exporter', 'product_attributes' ), true ) ) {
			return;
		}

		self::add_plugin_item(
			array(
				'title'      => $menu_item[0],
				'capability' => $menu_item[1],
				'id'         => sanitize_title( $menu_item[0] ),
				'url'        => $menu_item[2],
			)
		);

		// Determine if migrated items are a taxonomy or post_type. If they are, register them.
		$parsed_url   = wp_parse_url( $menu_item[2] );
		$query_string = isset( $parsed_url['query'] ) ? $parsed_url['query'] : false;

		if ( $query_string ) {
			$query = array();
			parse_str( $query_string, $query );

			if ( isset( $query['taxonomy'] ) ) {
				Screen::register_taxonomy( $query['taxonomy'] );
			} elseif ( isset( $query['post_type'] ) ) {
				Screen::register_post_type( $query['post_type'] );
			}
		}
	}

	/**
	 * Migrate any remaining WooCommerce child items.
	 *
	 * @param array $menu Menu items.
	 * @return array
	 */
	public function migrate_core_child_items( $menu ) {
		global $submenu;

		if ( ! isset( $submenu['woocommerce'] ) && ! isset( $submenu['edit.php?post_type=product'] ) ) {
			return $menu;
		}

		$main_items    = isset( $submenu['woocommerce'] ) ? $submenu['woocommerce'] : array();
		$product_items = isset( $submenu['edit.php?post_type=product'] ) ? $submenu['edit.php?post_type=product'] : array();

		foreach ( $main_items as $key => $menu_item ) {
			self::add_item_and_taxonomy( $menu_item );
			// phpcs:disable
			if ( ! isset( $menu_item[ self::CSS_CLASSES ] ) ) {
				$submenu['woocommerce'][ $key ][] .= ' hide-if-js';
			} else if ( strpos( $submenu['woocommerce'][ $key ][ self::CSS_CLASSES ], 'hide-if-js' ) !== false ) {
				continue;
			} else {
				$submenu['woocommerce'][ $key ][ self::CSS_CLASSES ] .= ' hide-if-js';
			}
			// phpcs:enable
		}

		foreach ( $product_items as $key => $menu_item ) {
			self::add_item_and_taxonomy( $menu_item );
		}

		return $menu;
	}

	/**
	 * Check if a menu item's callback is registered in the menu.
	 *
	 * @param array $menu_item Menu item args.
	 * @return bool
	 */
	public static function has_callback( $menu_item ) {
		if ( ! $menu_item || ! isset( $menu_item[ self::CALLBACK ] ) ) {
			return false;
		}

		$callback = $menu_item[ self::CALLBACK ];

		if (
			isset( self::$callbacks[ $callback ] ) &&
			self::$callbacks[ $callback ]
		) {
			return true;
		}

		if (
			isset( self::$callbacks[ self::get_callback_url( $callback ) ] ) &&
			self::$callbacks[ self::get_callback_url( $callback ) ]
		) {
			return true;
		}

		return false;
	}

	/**
	 * Hides all WP admin menus items and adds screen IDs to check for new items.
	 */
	public static function migrate_menu_items() {
		global $menu, $submenu;

		foreach ( $menu as $key => $menu_item ) {
			if ( self::has_callback( $menu_item ) ) {
				// Disable phpcs since we need to override submenu classes.
				// Note that `phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited` does not work to disable this check.
				// phpcs:disable
				$menu[ $key ][ self::CSS_CLASSES ] .= ' hide-if-js';
				// phps:enable
				continue;
			}

			// WordPress core menus make the parent item the same URL as the first child.
			$has_children = isset( $submenu[ $menu_item[ self::CALLBACK ] ] ) && isset( $submenu[ $menu_item[ self::CALLBACK ] ][0] );
			$first_child  = $has_children ? $submenu[ $menu_item[ self::CALLBACK ] ][0] : null;
			if ( 'woocommerce' !== $menu_item[2] && self::has_callback( $first_child ) ) {
				// Disable phpcs since we need to override submenu classes.
				// Note that `phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited` does not work to disable this check.
				// phpcs:disable
				$menu[ $key ][ self::CSS_CLASSES ] .= ' hide-if-js';
				// phps:enable
			}
		}

		// Remove excluded submenu items
		if ( isset( $submenu['woocommerce'] ) ) {
			foreach ( $submenu['woocommerce'] as $key => $submenu_item ) {
				if ( in_array( $submenu_item[ self::CALLBACK ], CoreMenu::get_excluded_items(), true ) ) {
					if ( isset( $submenu['woocommerce'][ $key ][ self::CSS_CLASSES ] ) ) {
						$submenu['woocommerce'][ $key ][ self::CSS_CLASSES ] .= ' hide-if-js';
					} else {
						$submenu['woocommerce'][ $key ][] = 'hide-if-js';
					}
				}
			}
		}

		foreach ( $submenu as $parent_key => $parent ) {
			foreach ( $parent as $key => $menu_item ) {
				if ( self::has_callback( $menu_item ) ) {
					// Disable phpcs since we need to override submenu classes.
					// Note that `phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited` does not work to disable this check.
					// phpcs:disable
					if ( ! isset( $menu_item[ self::SLUG ] ) ) {
						$submenu[ $parent_key ][ $key ][] = '';
					}
					if ( ! isset( $menu_item[ self::CSS_CLASSES ] ) ) {
						$submenu[ $parent_key ][ $key ][] .= ' hide-if-js';
					} else {
						$submenu[ $parent_key ][ $key ][ self::CSS_CLASSES ] .= ' hide-if-js';
					}
					// phps:enable
				}
			}
		}

		foreach ( array_keys( self::$callbacks ) as $callback ) {
			Screen::add_screen( $callback );
		}
	}

	/**
	 * Add a callback to identify and hide pages in the WP menu.
	 */
	public static function hide_wp_menu_item( $callback ) {
		self::$callbacks[ $callback ] = true;
	}

	/**
	 * Get registered menu items.
	 *
	 * @return array
	 */
	public static function get_items() {
		return apply_filters( 'woocommerce_navigation_menu_items', self::$menu_items );
	}

	/**
	 * Get registered menu items.
	 *
	 * @return array
	 */
	public static function get_category_items( $category ) {
		if ( ! isset( self::$categories[ $category ] ) ) {
			return array();
		}

		$menu_item_ids = self::$categories[ $category ];

		$category_menu_items = array();
		foreach ( $menu_item_ids as $id ) {
			if ( isset( self::$menu_items[ $id ] ) ) {
				$category_menu_items[] = self::$menu_items[ $id ];
			}
		}

		return apply_filters( 'woocommerce_navigation_menu_category_items', $category_menu_items );
	}

	/**
	 * Get registered callbacks.
	 *
	 * @return array
	 */
	public static function get_callbacks() {
		return apply_filters( 'woocommerce_navigation_callbacks', self::$callbacks );
	}

	/**
	 * Gets the menu item data mapped by category and menu ID.
	 *
	 * @return array
	 */
	public static function get_mapped_menu_items() {
		$menu_items   = self::get_items();
		$mapped_items = array();

		// Sort the items by order and title.
		$order     = array_column( $menu_items, 'order' );
		$title     = array_column( $menu_items, 'title' );
		array_multisort( $order, SORT_ASC, $title, SORT_ASC, $menu_items );

		foreach ( $menu_items as $id => $menu_item ) {
			$category_id = $menu_item[ 'parent' ];
			$menu_id     = $menu_item[ 'menuId' ];
			if ( ! isset( $mapped_items[ $category_id ] ) ) {
				$mapped_items[ $category_id ] = array();
				foreach ( self::MENU_IDS as $available_menu_id ) {
					$mapped_items[ $category_id ][ $available_menu_id ] = array();
				}
			}

			// Incorrect menu ID.
			if ( ! isset( $mapped_items[ $category_id ][ $menu_id ] ) ) {
				continue;
			}

			// Remove the item if the user cannot access it.
			if ( isset( $menu_item[ 'capability' ] ) && ! current_user_can( $menu_item[ 'capability' ] ) ) {
				continue;
			}

			$mapped_items[ $category_id ][ $menu_id ][] = $menu_item;
		}

		return $mapped_items;
	}

	/**
	 * Add the menu to the page output.
	 *
	 * @param array $menu Menu items.
	 * @return array
	 */
	public function enqueue_data( $menu ) {
		$data = array(
			'menuItems'     => array_values( self::get_items() ),
			'rootBackUrl'   => get_dashboard_url(),
		);

		wp_add_inline_script( WC_ADMIN_APP, 'window.wcNavigation = ' . wp_json_encode( $data ), 'before' );
	}
}

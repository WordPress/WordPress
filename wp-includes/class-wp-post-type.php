<?php
/**
 * Post API: WP_Post_Type class
 *
 * @package WordPress
 * @subpackage Post
 * @since 4.6.0
 */

/**
 * Core class used for interacting with post types.
 *
 * @since 4.6.0
 */
final class WP_Post_Type {
	/**
	 * Post type key.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var string $name
	 */
	public $name;

	/**
	 * Name of the post type shown in the menu. Usually plural.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var string $label
	 */
	public $label;

	/**
	 * An array of labels for this post type.
	 *
	 * If not set, post labels are inherited for non-hierarchical types
	 * and page labels for hierarchical ones.
	 *
	 * @see get_post_type_labels()
	 *
	 * @since 4.6.0
	 * @access public
	 * @var array $labels
	 */
	public $labels;

	/**
	 * A short descriptive summary of what the post type is.
	 *
	 * Default empty.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var string $description
	 */
	public $description = '';

	/**
	 * Whether a post type is intended for use publicly either via the admin interface or by front-end users.
	 *
	 * While the default settings of $exclude_from_search, $publicly_queryable, $show_ui, and $show_in_nav_menus
	 * are inherited from public, each does not rely on this relationship and controls a very specific intention.
	 *
	 * Default false.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool $public
	 */
	public $public = false;

	/**
	 * Whether the post type is hierarchical (e.g. page).
	 *
	 * Default false.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool $hierarchical
	 */
	public $hierarchical = false;

	/**
	 * Whether to exclude posts with this post type from front end search
	 * results.
	 *
	 * Default is the opposite value of $public.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool $exclude_from_search
	 */
	public $exclude_from_search = null;

	/**
	 * Whether queries can be performed on the front end for the post type as part of `parse_request()`.
	 *
	 * Endpoints would include:
	 * - `?post_type={post_type_key}`
	 * - `?{post_type_key}={single_post_slug}`
	 * - `?{post_type_query_var}={single_post_slug}`
	 *
	 * Default is the value of $public.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool $publicly_queryable
	 */
	public $publicly_queryable = null;

	/**
	 * Whether to generate and allow a UI for managing this post type in the admin.
	 *
	 * Default is the value of $public.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool $show_ui
	 */
	public $show_ui = null;

	/**
	 * Where to show the post type in the admin menu.
	 *
	 * To work, $show_ui must be true. If true, the post type is shown in its own top level menu. If false, no menu is
	 * shown. If a string of an existing top level menu (eg. 'tools.php' or 'edit.php?post_type=page'), the post type
	 * will be placed as a sub-menu of that.
	 *
	 * Default is the value of $show_ui.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool $show_in_menu
	 */
	public $show_in_menu = null;

	/**
	 * Makes this post type available for selection in navigation menus.
	 *
	 * Default is the value $public.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool $show_in_nav_menus
	 */
	public $show_in_nav_menus = null;

	/**
	 * Makes this post type available via the admin bar.
	 *
	 * Default is the value of $show_in_menu.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool $show_in_admin_bar
	 */
	public $show_in_admin_bar = null;

	/**
	 * The position in the menu order the post type should appear.
	 *
	 * To work, $show_in_menu must be true. Default null (at the bottom).
	 *
	 * @since 4.6.0
	 * @access public
	 * @var int $menu_position
	 */
	public $menu_position = null;

	/**
	 * The URL to the icon to be used for this menu.
	 *
	 * Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme.
	 * This should begin with 'data:image/svg+xml;base64,'. Pass the name of a Dashicons helper class
	 * to use a font icon, e.g. 'dashicons-chart-pie'. Pass 'none' to leave div.wp-menu-image empty
	 * so an icon can be added via CSS.
	 *
	 * Defaults to use the posts icon.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var string $menu_icon
	 */
	public $menu_icon = null;

	/**
	 * The string to use to build the read, edit, and delete capabilities.
	 *
	 * May be passed as an array to allow for alternative plurals when using
	 * this argument as a base to construct the capabilities, e.g.
	 * array( 'story', 'stories' ). Default 'post'.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var string $capability_type
	 */
	public $capability_type = 'post';

	/**
	 * Whether to use the internal default meta capability handling.
	 *
	 * Default false.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool $map_meta_cap
	 */
	public $map_meta_cap = false;

	/**
	 * Provide a callback function that sets up the meta boxes for the edit form.
	 *
	 * Do `remove_meta_box()` and `add_meta_box()` calls in the callback. Default null.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var string $register_meta_box_cb
	 */
	public $register_meta_box_cb = null;

	/**
	 * An array of taxonomy identifiers that will be registered for the post type.
	 *
	 * Taxonomies can be registered later with `register_taxonomy()` or `register_taxonomy_for_object_type()`.
	 *
	 * Default empty array.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var array $taxonomies
	 */
	public $taxonomies = array();

	/**
	 * Whether there should be post type archives, or if a string, the archive slug to use.
	 *
	 * Will generate the proper rewrite rules if $rewrite is enabled. Default false.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool|string $has_archive
	 */
	public $has_archive = false;

	/**
	 * Sets the query_var key for this post type.
	 *
	 * Defaults to $post_type key. If false, a post type cannot be loaded at `?{query_var}={post_slug}`.
	 * If specified as a string, the query `?{query_var_string}={post_slug}` will be valid.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var string|bool $query_var
	 */
	public $query_var;

	/**
	 * Whether to allow this post type to be exported.
	 *
	 * Default true.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool $can_export
	 */
	public $can_export = true;

	/**
	 * Whether to delete posts of this type when deleting a user.
	 *
	 * If true, posts of this type belonging to the user will be moved to trash when then user is deleted.
	 * If false, posts of this type belonging to the user will *not* be trashed or deleted.
	 * If not set (the default), posts are trashed if post_type_supports( 'author' ).
	 * Otherwise posts are not trashed or deleted. Default null.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool $delete_with_user
	 */
	public $delete_with_user = null;

	/**
	 * Whether this post type is a native or "built-in" post_type.
	 *
	 * Default false.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var bool $_builtin
	 */
	public $_builtin = false;

	/**
	 * URL segment to use for edit link of this post type.
	 *
	 * Default 'post.php?post=%d'.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var string $_edit_link
	 */
	public $_edit_link = 'post.php?post=%d';

	/**
	 * Post type capabilities.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var array $cap
	 */
	public $cap;

	/**
	 * Triggers the handling of rewrites for this post type.
	 *
	 * Defaults to true, using $post_type as slug.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var array|false $rewrite
	 */
	public $rewrite;

	/**
	 * The features supported by the post type.
	 *
	 * @since 4.6.0
	 * @access public
	 * @var array|bool $supports
	 */
	public $supports;

	/**
	 * Constructor.
	 *
	 * Will populate object properties from the provided arguments and assign other
	 * default properties based on that information.
	 *
	 * @since 4.6.0
	 * @access public
	 *
	 * @see register_post_type()
	 *
	 * @param string       $post_type Post type key.
	 * @param array|string $args      Optional. Array or string of arguments for registering a post type.
	 *                                Default empty array.
	 */
	public function __construct( $post_type, $args = array() ) {
		$this->name = $post_type;

		$this->set_props( $args );
	}

	/**
	 * Sets post type properties.
	 *
	 * @since 4.6.0
	 * @access public
	 *
	 * @param array|string $args Array or string of arguments for registering a post type.
	 */
	public function set_props( $args ) {
		$args = wp_parse_args( $args );

		/**
		 * Filter the arguments for registering a post type.
		 *
		 * @since 4.4.0
		 *
		 * @param array  $args      Array of arguments for registering a post type.
		 * @param string $post_type Post type key.
		 */
		$args = apply_filters( 'register_post_type_args', $args, $this->name );

		$has_edit_link = ! empty( $args['_edit_link'] );

		// Args prefixed with an underscore are reserved for internal use.
		$defaults = array(
			'labels'               => array(),
			'description'          => '',
			'public'               => false,
			'hierarchical'         => false,
			'exclude_from_search'  => null,
			'publicly_queryable'   => null,
			'show_ui'              => null,
			'show_in_menu'         => null,
			'show_in_nav_menus'    => null,
			'show_in_admin_bar'    => null,
			'menu_position'        => null,
			'menu_icon'            => null,
			'capability_type'      => 'post',
			'capabilities'         => array(),
			'map_meta_cap'         => null,
			'supports'             => array(),
			'register_meta_box_cb' => null,
			'taxonomies'           => array(),
			'has_archive'          => false,
			'rewrite'              => true,
			'query_var'            => true,
			'can_export'           => true,
			'delete_with_user'     => null,
			'_builtin'             => false,
			'_edit_link'           => 'post.php?post=%d',
		);

		$args = array_merge( $defaults, $args );

		$args['name'] = $this->name;

		// If not set, default to the setting for public.
		if ( null === $args['publicly_queryable'] ) {
			$args['publicly_queryable'] = $args['public'];
		}

		// If not set, default to the setting for public.
		if ( null === $args['show_ui'] ) {
			$args['show_ui'] = $args['public'];
		}

		// If not set, default to the setting for show_ui.
		if ( null === $args['show_in_menu'] || ! $args['show_ui'] ) {
			$args['show_in_menu'] = $args['show_ui'];
		}

		// If not set, default to the whether the full UI is shown.
		if ( null === $args['show_in_admin_bar'] ) {
			$args['show_in_admin_bar'] = (bool) $args['show_in_menu'];
		}

		// If not set, default to the setting for public.
		if ( null === $args['show_in_nav_menus'] ) {
			$args['show_in_nav_menus'] = $args['public'];
		}

		// If not set, default to true if not public, false if public.
		if ( null === $args['exclude_from_search'] ) {
			$args['exclude_from_search'] = ! $args['public'];
		}

		// Back compat with quirky handling in version 3.0. #14122.
		if ( empty( $args['capabilities'] ) && null === $args['map_meta_cap'] && in_array( $args['capability_type'], array( 'post', 'page' ) ) ) {
			$args['map_meta_cap'] = true;
		}

		// If not set, default to false.
		if ( null === $args['map_meta_cap'] ) {
			$args['map_meta_cap'] = false;
		}

		// If there's no specified edit link and no UI, remove the edit link.
		if ( ! $args['show_ui'] && ! $has_edit_link ) {
			$args['_edit_link'] = '';
		}

		$this->cap = get_post_type_capabilities( (object) $args );
		unset( $args['capabilities'] );

		if ( is_array( $args['capability_type'] ) ) {
			$args['capability_type'] = $args['capability_type'][0];
		}

		if ( false !== $args['query_var'] ) {
			if ( true === $args['query_var'] ) {
				$args['query_var'] = $this->name;
			} else {
				$args['query_var'] = sanitize_title_with_dashes( $args['query_var'] );
			}
		}

		if ( false !== $args['rewrite'] && ( is_admin() || '' != get_option( 'permalink_structure' ) ) ) {
			if ( ! is_array( $args['rewrite'] ) ) {
				$args['rewrite'] = array();
			}
			if ( empty( $args['rewrite']['slug'] ) ) {
				$args['rewrite']['slug'] = $this->name;
			}
			if ( ! isset( $args['rewrite']['with_front'] ) ) {
				$args['rewrite']['with_front'] = true;
			}
			if ( ! isset( $args['rewrite']['pages'] ) ) {
				$args['rewrite']['pages'] = true;
			}
			if ( ! isset( $args['rewrite']['feeds'] ) || ! $args['has_archive'] ) {
				$args['rewrite']['feeds'] = (bool) $args['has_archive'];
			}
			if ( ! isset( $args['rewrite']['ep_mask'] ) ) {
				if ( isset( $args['permalink_epmask'] ) ) {
					$args['rewrite']['ep_mask'] = $args['permalink_epmask'];
				} else {
					$args['rewrite']['ep_mask'] = EP_PERMALINK;
				}
			}
		}

		foreach ( $args as $property_name => $property_value ) {
			$this->$property_name = $property_value;
		}

		$this->labels = get_post_type_labels( $this );
		$this->label  = $this->labels->name;
	}

	/**
	 * Sets the features support for the post type.
	 *
	 * @since 4.6.0
	 * @access public
	 */
	public function add_supports() {
		if ( ! empty( $this->supports ) ) {
			add_post_type_support( $this->name, $this->supports );
			unset( $this->supports );
		} elseif ( false !== $this->supports ) {
			// Add default features.
			add_post_type_support( $this->name, array( 'title', 'editor' ) );
		}
	}

	/**
	 * Adds the necessary rewrite rules for the post type.
	 *
	 * @since 4.6.0
	 * @access public
	 *
	 * @global WP_Rewrite $wp_rewrite WordPress Rewrite Component.
	 * @global WP         $wp         Current WordPress environment instance.
	 */
	public function add_rewrite_rules() {
		global $wp_rewrite, $wp;

		if ( false !== $this->query_var && $wp && is_post_type_viewable( $this ) ) {
			$wp->add_query_var( $this->query_var );
		}

		if ( false !== $this->rewrite && ( is_admin() || '' != get_option( 'permalink_structure' ) ) ) {
			if ( $this->hierarchical ) {
				add_rewrite_tag( "%$this->name%", '(.+?)', $this->query_var ? "{$this->query_var}=" : "post_type=$this->name&pagename=" );
			} else {
				add_rewrite_tag( "%$this->name%", '([^/]+)', $this->query_var ? "{$this->query_var}=" : "post_type=$this->name&name=" );
			}

			if ( $this->has_archive ) {
				$archive_slug = true === $this->has_archive ? $this->rewrite['slug'] : $this->has_archive;
				if ( $this->rewrite['with_front'] ) {
					$archive_slug = substr( $wp_rewrite->front, 1 ) . $archive_slug;
				} else {
					$archive_slug = $wp_rewrite->root . $archive_slug;
				}

				add_rewrite_rule( "{$archive_slug}/?$", "index.php?post_type=$this->name", 'top' );
				if ( $this->rewrite['feeds'] && $wp_rewrite->feeds ) {
					$feeds = '(' . trim( implode( '|', $wp_rewrite->feeds ) ) . ')';
					add_rewrite_rule( "{$archive_slug}/feed/$feeds/?$", "index.php?post_type=$this->name" . '&feed=$matches[1]', 'top' );
					add_rewrite_rule( "{$archive_slug}/$feeds/?$", "index.php?post_type=$this->name" . '&feed=$matches[1]', 'top' );
				}
				if ( $this->rewrite['pages'] ) {
					add_rewrite_rule( "{$archive_slug}/{$wp_rewrite->pagination_base}/([0-9]{1,})/?$", "index.php?post_type=$this->name" . '&paged=$matches[1]', 'top' );
				}
			}

			$permastruct_args         = $this->rewrite;
			$permastruct_args['feed'] = $permastruct_args['feeds'];
			add_permastruct( $this->name, "{$this->rewrite['slug']}/%$this->name%", $permastruct_args );
		}
	}

	/**
	 * Registers the post type meta box if a custom callback was specified.
	 *
	 * @since 4.6.0
	 * @access public
	 */
	public function register_meta_boxes() {
		if ( $this->register_meta_box_cb ) {
			add_action( 'add_meta_boxes_' . $this->name, $this->register_meta_box_cb, 10, 1 );
		}
	}

	/**
	 * Adds the future post hook action for the post type.
	 *
	 * @since 4.6.0
	 * @access public
	 */
	public function add_hooks() {
		add_action( 'future_' . $this->name, '_future_post_hook', 5, 2 );
	}

	/**
	 * Registers the taxonomies for the post type.
	 *
	 * @since 4.6.0
	 * @access public
	 */
	public function register_taxonomies() {
		foreach ( $this->taxonomies as $taxonomy ) {
			register_taxonomy_for_object_type( $taxonomy, $this->name );
		}
	}

	/**
	 * Removes the features support for the post type.
	 *
	 * @since 4.6.0
	 * @access public
	 *
	 * @global array $_wp_post_type_features Post type features.
	 */
	public function remove_supports() {
		global $_wp_post_type_features;

		unset( $_wp_post_type_features[ $this->name ] );
	}

	/**
	 * Removes any rewrite rules, permastructs, and rules for the post type.
	 *
	 * @since 4.6.0
	 * @access public
	 *
	 * @global WP_Rewrite $wp_rewrite          WordPress rewrite component.
	 * @global WP         $wp                  Current WordPress environment instance.
	 * @global array      $post_type_meta_caps Used to remove meta capabilities.
	 */
	public function remove_rewrite_rules() {
		global $wp, $wp_rewrite, $post_type_meta_caps;

		// Remove query var.
		if ( false !== $this->query_var ) {
			$wp->remove_query_var( $this->query_var );
		}

		// Remove any rewrite rules, permastructs, and rules.
		if ( false !== $this->rewrite ) {
			remove_rewrite_tag( "%$this->name%" );
			remove_permastruct( $this->name );
			foreach ( $wp_rewrite->extra_rules_top as $regex => $query ) {
				if ( false !== strpos( $query, "index.php?post_type=$this->name" ) ) {
					unset( $wp_rewrite->extra_rules_top[ $regex ] );
				}
			}
		}

		// Remove registered custom meta capabilities.
		foreach ( $this->cap as $cap ) {
			unset( $post_type_meta_caps[ $cap ] );
		}
	}

	/**
	 * Unregisters the post type meta box if a custom callback was specified.
	 *
	 * @since 4.6.0
	 * @access public
	 */
	public function unregister_meta_boxes() {
		if ( $this->register_meta_box_cb ) {
			remove_action( 'add_meta_boxes_' . $this->name, $this->register_meta_box_cb, 10 );
		}
	}

	/**
	 * Removes the post type from all taxonomies.
	 *
	 * @since 4.6.0
	 * @access public
	 */
	public function unregister_taxonomies() {
		foreach ( get_object_taxonomies( $this->name ) as $taxonomy ) {
			unregister_taxonomy_for_object_type( $taxonomy, $this->name );
		}
	}

	/**
	 * Removes the future post hook action for the post type.
	 *
	 * @since 4.6.0
	 * @access public
	 */
	public function remove_hooks() {
		remove_action( 'future_' . $this->name, '_future_post_hook', 5 );
	}
}

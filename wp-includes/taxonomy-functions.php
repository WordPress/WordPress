<?php
/**
 * Taxonomy API: Top-level taxonomy functionality
 *
 * @package WordPress
 * @subpackage Taxonomy
 * @since 4.4.0
 */

//
// Taxonomy Registration
//

/**
 * Creates the initial taxonomies.
 *
 * This function fires twice: in wp-settings.php before plugins are loaded (for
 * backwards compatibility reasons), and again on the {@see 'init'} action. We must
 * avoid registering rewrite rules before the {@see 'init'} action.
 *
 * @since 2.8.0
 *
 * @global WP_Rewrite $wp_rewrite The WordPress rewrite class.
 */
function create_initial_taxonomies() {
	global $wp_rewrite;

	if ( ! did_action( 'init' ) ) {
		$rewrite = array( 'category' => false, 'post_tag' => false, 'post_format' => false );
	} else {

		/**
		 * Filter the post formats rewrite base.
		 *
		 * @since 3.1.0
		 *
		 * @param string $context Context of the rewrite base. Default 'type'.
		 */
		$post_format_base = apply_filters( 'post_format_rewrite_base', 'type' );
		$rewrite = array(
			'category' => array(
				'hierarchical' => true,
				'slug' => get_option('category_base') ? get_option('category_base') : 'category',
				'with_front' => ! get_option('category_base') || $wp_rewrite->using_index_permalinks(),
				'ep_mask' => EP_CATEGORIES,
			),
			'post_tag' => array(
				'hierarchical' => false,
				'slug' => get_option('tag_base') ? get_option('tag_base') : 'tag',
				'with_front' => ! get_option('tag_base') || $wp_rewrite->using_index_permalinks(),
				'ep_mask' => EP_TAGS,
			),
			'post_format' => $post_format_base ? array( 'slug' => $post_format_base ) : false,
		);
	}

	register_taxonomy( 'category', 'post', array(
		'hierarchical' => true,
		'query_var' => 'category_name',
		'rewrite' => $rewrite['category'],
		'public' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'_builtin' => true,
	) );

	register_taxonomy( 'post_tag', 'post', array(
	 	'hierarchical' => false,
		'query_var' => 'tag',
		'rewrite' => $rewrite['post_tag'],
		'public' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'_builtin' => true,
	) );

	register_taxonomy( 'nav_menu', 'nav_menu_item', array(
		'public' => false,
		'hierarchical' => false,
		'labels' => array(
			'name' => __( 'Navigation Menus' ),
			'singular_name' => __( 'Navigation Menu' ),
		),
		'query_var' => false,
		'rewrite' => false,
		'show_ui' => false,
		'_builtin' => true,
		'show_in_nav_menus' => false,
	) );

	register_taxonomy( 'link_category', 'link', array(
		'hierarchical' => false,
		'labels' => array(
			'name' => __( 'Link Categories' ),
			'singular_name' => __( 'Link Category' ),
			'search_items' => __( 'Search Link Categories' ),
			'popular_items' => null,
			'all_items' => __( 'All Link Categories' ),
			'edit_item' => __( 'Edit Link Category' ),
			'update_item' => __( 'Update Link Category' ),
			'add_new_item' => __( 'Add New Link Category' ),
			'new_item_name' => __( 'New Link Category Name' ),
			'separate_items_with_commas' => null,
			'add_or_remove_items' => null,
			'choose_from_most_used' => null,
		),
		'capabilities' => array(
			'manage_terms' => 'manage_links',
			'edit_terms'   => 'manage_links',
			'delete_terms' => 'manage_links',
			'assign_terms' => 'manage_links',
		),
		'query_var' => false,
		'rewrite' => false,
		'public' => false,
		'show_ui' => false,
		'_builtin' => true,
	) );

	register_taxonomy( 'post_format', 'post', array(
		'public' => true,
		'hierarchical' => false,
		'labels' => array(
			'name' => _x( 'Format', 'post format' ),
			'singular_name' => _x( 'Format', 'post format' ),
		),
		'query_var' => true,
		'rewrite' => $rewrite['post_format'],
		'show_ui' => false,
		'_builtin' => true,
		'show_in_nav_menus' => current_theme_supports( 'post-formats' ),
	) );
}

/**
 * Retrieves a list of registered taxonomy names or objects.
 *
 * @since 3.0.0
 *
 * @global array $wp_taxonomies The registered taxonomies.
 *
 * @param array  $args     Optional. An array of `key => value` arguments to match against the taxonomy objects.
 *                         Default empty array.
 * @param string $output   Optional. The type of output to return in the array. Accepts either taxonomy 'names'
 *                         or 'objects'. Default 'names'.
 * @param string $operator Optional. The logical operation to perform. Accepts 'and' or 'or'. 'or' means only
 *                         one element from the array needs to match; 'and' means all elements must match.
 *                         Default 'and'.
 * @return array A list of taxonomy names or objects.
 */
function get_taxonomies( $args = array(), $output = 'names', $operator = 'and' ) {
	global $wp_taxonomies;

	$field = ('names' == $output) ? 'name' : false;

	return wp_filter_object_list($wp_taxonomies, $args, $operator, $field);
}

/**
 * Return all of the taxonomy names that are of $object_type.
 *
 * It appears that this function can be used to find all of the names inside of
 * $wp_taxonomies global variable.
 *
 * `<?php $taxonomies = get_object_taxonomies('post'); ?>` Should
 * result in `Array( 'category', 'post_tag' )`
 *
 * @since 2.3.0
 *
 * @global array $wp_taxonomies The registered taxonomies.
 *
 * @param array|string|WP_Post $object Name of the type of taxonomy object, or an object (row from posts)
 * @param string               $output Optional. The type of output to return in the array. Accepts either
 *                                     taxonomy 'names' or 'objects'. Default 'names'.
 * @return array The names of all taxonomy of $object_type.
 */
function get_object_taxonomies( $object, $output = 'names' ) {
	global $wp_taxonomies;

	if ( is_object($object) ) {
		if ( $object->post_type == 'attachment' )
			return get_attachment_taxonomies($object);
		$object = $object->post_type;
	}

	$object = (array) $object;

	$taxonomies = array();
	foreach ( (array) $wp_taxonomies as $tax_name => $tax_obj ) {
		if ( array_intersect($object, (array) $tax_obj->object_type) ) {
			if ( 'names' == $output )
				$taxonomies[] = $tax_name;
			else
				$taxonomies[ $tax_name ] = $tax_obj;
		}
	}

	return $taxonomies;
}

/**
 * Retrieves the taxonomy object of $taxonomy.
 *
 * The get_taxonomy function will first check that the parameter string given
 * is a taxonomy object and if it is, it will return it.
 *
 * @since 2.3.0
 *
 * @global array $wp_taxonomies The registered taxonomies.
 *
 * @param string $taxonomy Name of taxonomy object to return.
 * @return object|false The Taxonomy Object or false if $taxonomy doesn't exist.
 */
function get_taxonomy( $taxonomy ) {
	global $wp_taxonomies;

	if ( ! taxonomy_exists( $taxonomy ) )
		return false;

	return $wp_taxonomies[$taxonomy];
}

/**
 * Checks that the taxonomy name exists.
 *
 * Formerly is_taxonomy(), introduced in 2.3.0.
 *
 * @since 3.0.0
 *
 * @global array $wp_taxonomies The registered taxonomies.
 *
 * @param string $taxonomy Name of taxonomy object.
 * @return bool Whether the taxonomy exists.
 */
function taxonomy_exists( $taxonomy ) {
	global $wp_taxonomies;

	return isset( $wp_taxonomies[$taxonomy] );
}

/**
 * Whether the taxonomy object is hierarchical.
 *
 * Checks to make sure that the taxonomy is an object first. Then Gets the
 * object, and finally returns the hierarchical value in the object.
 *
 * A false return value might also mean that the taxonomy does not exist.
 *
 * @since 2.3.0
 *
 * @param string $taxonomy Name of taxonomy object.
 * @return bool Whether the taxonomy is hierarchical.
 */
function is_taxonomy_hierarchical($taxonomy) {
	if ( ! taxonomy_exists($taxonomy) )
		return false;

	$taxonomy = get_taxonomy($taxonomy);
	return $taxonomy->hierarchical;
}

/**
 * Creates or modifies a taxonomy object.
 *
 * Note: Do not use before the {@see 'init'} hook.
 *
 * A simple function for creating or modifying a taxonomy object based on the
 * parameters given. The function will accept an array (third optional
 * parameter), along with strings for the taxonomy name and another string for
 * the object type.
 *
 * @since 2.3.0
 * @since 4.2.0 Introduced `show_in_quick_edit` argument.
 * @since 4.4.0 The `show_ui` argument is now enforced on the term editing screen.
 *
 * @global array $wp_taxonomies Registered taxonomies.
 * @global WP    $wp            WP instance.
 *
 * @param string       $taxonomy    Taxonomy key, must not exceed 32 characters.
 * @param array|string $object_type Name of the object type for the taxonomy object.
 * @param array|string $args        {
 *     Optional. Array or query string of arguments for registering a taxonomy.
 *
 *     @type string        $label                 Name of the taxonomy shown in the menu. Usually plural. If not set,
 *                                                `$labels['name']` will be used.
 *     @type array         $labels                An array of labels for this taxonomy. By default, Tag labels are used for
 *                                                non-hierarchical taxonmies, and Category labels are used for hierarchical
 *                                                taxonomies. See accepted values in get_taxonomy_labels().
 *                                                Default empty array.
 *     @type string        $description           A short descriptive summary of what the taxonomy is for. Default empty.
 *     @type bool          $public                Whether the taxonomy is publicly queryable. Default true.
 *     @type bool          $hierarchical          Whether the taxonomy is hierarchical. Default false.
 *     @type bool          $show_ui               Whether to generate and allow a UI for managing terms in this taxonomy in
 *                                                the admin. If not set, the default is inherited from `$public`
 *                                                (default true).
 *     @type bool          $show_in_menu          Whether to show the taxonomy in the admin menu. If true, the taxonomy is
 *                                                shown as a submenu of the object type menu. If false, no menu is shown.
 *                                                `$show_ui` must be true. If not set, default is inherited from `$show_ui`
 *                                                (default true).
 *     @type bool          $show_in_nav_menus     Makes this taxonomy available for selection in navigation menus. If not
 *                                                set, the default is inherited from `$public` (default true).
 *     @type bool          $show_tagcloud         Whether to list the taxonomy in the Tag Cloud Widget controls. If not set,
 *                                                the default is inherited from `$show_ui` (default true).
 *     @type bool          $show_in_quick_edit    Whether to show the taxonomy in the quick/bulk edit panel. It not set,
 *                                                the default is inherited from `$show_ui` (default true).
 *     @type bool          $show_admin_column     Whether to display a column for the taxonomy on its post type listing
 *                                                screens. Default false.
 *     @type bool|callable $meta_box_cb           Provide a callback function for the meta box display. If not set,
 *                                                post_categories_meta_box() is used for hierarchical taxonomies, and
 *                                                post_tags_meta_box() is used for non-hierarchical. If false, no meta
 *                                                box is shown.
 *     @type array         $capabilities {
 *         Array of capabilities for this taxonomy.
 *
 *         @type string $manage_terms Default 'manage_categories'.
 *         @type string $edit_terms   Default 'manage_categories'.
 *         @type string $delete_terms Default 'manage_categories'.
 *         @type string $assign_terms Default 'edit_posts'.
 *     }
 *     @type bool|array    $rewrite {
 *         Triggers the handling of rewrites for this taxonomy. Default true, using $taxonomy as slug. To prevent
 *         rewrite, set to false. To specify rewrite rules, an array can be passed with any of these keys:
 *    
 *         @type string $slug         Customize the permastruct slug. Default `$taxonomy` key.
 *         @type bool   $with_front   Should the permastruct be prepended with WP_Rewrite::$front. Default true.
 *         @type bool   $hierarchical Either hierarchical rewrite tag or not. Default false.
 *         @type int    $ep_mask      Assign an endpoint mask. Default `EP_NONE`.
 *     }
 *     @type string        $query_var             Sets the query var key for this taxonomy. Default `$taxonomy` key. If
 *                                                false, a taxonomy cannot be loaded at `?{query_var}={term_slug}`. If a
 *                                                string, the query `?{query_var}={term_slug}` will be valid.
 *     @type callable      $update_count_callback Works much like a hook, in that it will be called when the count is
 *                                                updated. Default _update_post_term_count() for taxonomies attached
 *                                                to post types, which confirms that the objects are published before
 *                                                counting them. Default _update_generic_term_count() for taxonomies
 *                                                attached to other object types, such as users.
 *     @type bool          $_builtin              This taxonomy is a "built-in" taxonomy. INTERNAL USE ONLY!
 *                                                Default false.
 * }
 * @return WP_Error|void WP_Error, if errors.
 */
function register_taxonomy( $taxonomy, $object_type, $args = array() ) {
	global $wp_taxonomies, $wp;

	if ( ! is_array( $wp_taxonomies ) )
		$wp_taxonomies = array();

	$defaults = array(
		'labels'                => array(),
		'description'           => '',
		'public'                => true,
		'hierarchical'          => false,
		'show_ui'               => null,
		'show_in_menu'          => null,
		'show_in_nav_menus'     => null,
		'show_tagcloud'         => null,
		'show_in_quick_edit'	=> null,
		'show_admin_column'     => false,
		'meta_box_cb'           => null,
		'capabilities'          => array(),
		'rewrite'               => true,
		'query_var'             => $taxonomy,
		'update_count_callback' => '',
		'_builtin'              => false,
	);
	$args = wp_parse_args( $args, $defaults );

	if ( empty( $taxonomy ) || strlen( $taxonomy ) > 32 ) {
		_doing_it_wrong( __FUNCTION__, __( 'Taxonomy names must be between 1 and 32 characters in length.' ), '4.2' );
		return new WP_Error( 'taxonomy_length_invalid', __( 'Taxonomy names must be between 1 and 32 characters in length.' ) );
	}

	if ( false !== $args['query_var'] && ! empty( $wp ) ) {
		if ( true === $args['query_var'] )
			$args['query_var'] = $taxonomy;
		else
			$args['query_var'] = sanitize_title_with_dashes( $args['query_var'] );
		$wp->add_query_var( $args['query_var'] );
	}

	if ( false !== $args['rewrite'] && ( is_admin() || '' != get_option( 'permalink_structure' ) ) ) {
		$args['rewrite'] = wp_parse_args( $args['rewrite'], array(
			'with_front' => true,
			'hierarchical' => false,
			'ep_mask' => EP_NONE,
		) );

		if ( empty( $args['rewrite']['slug'] ) )
			$args['rewrite']['slug'] = sanitize_title_with_dashes( $taxonomy );

		if ( $args['hierarchical'] && $args['rewrite']['hierarchical'] )
			$tag = '(.+?)';
		else
			$tag = '([^/]+)';

		add_rewrite_tag( "%$taxonomy%", $tag, $args['query_var'] ? "{$args['query_var']}=" : "taxonomy=$taxonomy&term=" );
		add_permastruct( $taxonomy, "{$args['rewrite']['slug']}/%$taxonomy%", $args['rewrite'] );
	}

	// If not set, default to the setting for public.
	if ( null === $args['show_ui'] )
		$args['show_ui'] = $args['public'];

	// If not set, default to the setting for show_ui.
	if ( null === $args['show_in_menu' ] || ! $args['show_ui'] )
		$args['show_in_menu' ] = $args['show_ui'];

	// If not set, default to the setting for public.
	if ( null === $args['show_in_nav_menus'] )
		$args['show_in_nav_menus'] = $args['public'];

	// If not set, default to the setting for show_ui.
	if ( null === $args['show_tagcloud'] )
		$args['show_tagcloud'] = $args['show_ui'];

	// If not set, default to the setting for show_ui.
	if ( null === $args['show_in_quick_edit'] ) {
		$args['show_in_quick_edit'] = $args['show_ui'];
	}

	$default_caps = array(
		'manage_terms' => 'manage_categories',
		'edit_terms'   => 'manage_categories',
		'delete_terms' => 'manage_categories',
		'assign_terms' => 'edit_posts',
	);
	$args['cap'] = (object) array_merge( $default_caps, $args['capabilities'] );
	unset( $args['capabilities'] );

	$args['name'] = $taxonomy;
	$args['object_type'] = array_unique( (array) $object_type );

	$args['labels'] = get_taxonomy_labels( (object) $args );
	$args['label'] = $args['labels']->name;

	// If not set, use the default meta box
	if ( null === $args['meta_box_cb'] ) {
		if ( $args['hierarchical'] )
			$args['meta_box_cb'] = 'post_categories_meta_box';
		else
			$args['meta_box_cb'] = 'post_tags_meta_box';
	}

	$wp_taxonomies[ $taxonomy ] = (object) $args;

	// register callback handling for metabox
 	add_filter( 'wp_ajax_add-' . $taxonomy, '_wp_ajax_add_hierarchical_term' );

	/**
	 * Fires after a taxonomy is registered.
	 *
	 * @since 3.3.0
	 *
	 * @param string       $taxonomy    Taxonomy slug.
	 * @param array|string $object_type Object type or array of object types.
	 * @param array        $args        Array of taxonomy registration arguments.
	 */
	do_action( 'registered_taxonomy', $taxonomy, $object_type, $args );
}

/**
 * Builds an object with all taxonomy labels out of a taxonomy object
 *
 * Accepted keys of the label array in the taxonomy object:
 *
 * - name - general name for the taxonomy, usually plural. The same as and overridden by $tax->label. Default is Tags/Categories
 * - singular_name - name for one object of this taxonomy. Default is Tag/Category
 * - select_name - prompt to select a taxonomy when using a dropdown list in the Categories widget. Default is 'Select Category'.
 * - search_items - Default is Search Tags/Search Categories
 * - popular_items - This string isn't used on hierarchical taxonomies. Default is Popular Tags
 * - all_items - Default is All Tags/All Categories
 * - parent_item - This string isn't used on non-hierarchical taxonomies. In hierarchical ones the default is Parent Category
 * - parent_item_colon - The same as `parent_item`, but with colon `:` in the end
 * - edit_item - Default is Edit Tag/Edit Category
 * - view_item - Default is View Tag/View Category
 * - update_item - Default is Update Tag/Update Category
 * - add_new_item - Default is Add New Tag/Add New Category
 * - new_item_name - Default is New Tag Name/New Category Name
 * - separate_items_with_commas - This string isn't used on hierarchical taxonomies. Default is "Separate tags with commas", used in the meta box.
 * - add_or_remove_items - This string isn't used on hierarchical taxonomies. Default is "Add or remove tags", used in the meta box when JavaScript is disabled.
 * - choose_from_most_used - This string isn't used on hierarchical taxonomies. Default is "Choose from the most used tags", used in the meta box.
 * - not_found - Default is "No tags found"/"No categories found", used in the meta box and taxonomy list table.
 * - no_terms - Default is "No tags"/"No categories", used in the posts and media list tables.
 *
 * Above, the first default value is for non-hierarchical taxonomies (like tags) and the second one is for hierarchical taxonomies (like categories).
 *
 * @todo Better documentation for the labels array.
 *
 * @since 3.0.0
 * @since 4.3.0 Added the `no_terms` label.
 *
 * @param object $tax Taxonomy object.
 * @return object object with all the labels as member variables.
 */
function get_taxonomy_labels( $tax ) {
	$tax->labels = (array) $tax->labels;

	if ( isset( $tax->helps ) && empty( $tax->labels['separate_items_with_commas'] ) )
		$tax->labels['separate_items_with_commas'] = $tax->helps;

	if ( isset( $tax->no_tagcloud ) && empty( $tax->labels['not_found'] ) )
		$tax->labels['not_found'] = $tax->no_tagcloud;

	$nohier_vs_hier_defaults = array(
		'name' => array( _x( 'Tags', 'taxonomy general name' ), _x( 'Categories', 'taxonomy general name' ) ),
		'singular_name' => array( _x( 'Tag', 'taxonomy singular name' ), _x( 'Category', 'taxonomy singular name' ) ),
		'select_name' => array( null, __( 'Select Category' ) ),
		'search_items' => array( __( 'Search Tags' ), __( 'Search Categories' ) ),
		'popular_items' => array( __( 'Popular Tags' ), null ),
		'all_items' => array( __( 'All Tags' ), __( 'All Categories' ) ),
		'parent_item' => array( null, __( 'Parent Category' ) ),
		'parent_item_colon' => array( null, __( 'Parent Category:' ) ),
		'edit_item' => array( __( 'Edit Tag' ), __( 'Edit Category' ) ),
		'view_item' => array( __( 'View Tag' ), __( 'View Category' ) ),
		'update_item' => array( __( 'Update Tag' ), __( 'Update Category' ) ),
		'add_new_item' => array( __( 'Add New Tag' ), __( 'Add New Category' ) ),
		'new_item_name' => array( __( 'New Tag Name' ), __( 'New Category Name' ) ),
		'separate_items_with_commas' => array( __( 'Separate tags with commas' ), null ),
		'add_or_remove_items' => array( __( 'Add or remove tags' ), null ),
		'choose_from_most_used' => array( __( 'Choose from the most used tags' ), null ),
		'not_found' => array( __( 'No tags found.' ), __( 'No categories found.' ) ),
		'no_terms' => array( __( 'No tags' ), __( 'No categories' ) ),
	);
	$nohier_vs_hier_defaults['menu_name'] = $nohier_vs_hier_defaults['name'];

	return _get_custom_object_labels( $tax, $nohier_vs_hier_defaults );
}

/**
 * Add an already registered taxonomy to an object type.
 *
 * @since 3.0.0
 *
 * @global array $wp_taxonomies The registered taxonomies.
 *
 * @param string $taxonomy    Name of taxonomy object.
 * @param string $object_type Name of the object type.
 * @return bool True if successful, false if not.
 */
function register_taxonomy_for_object_type( $taxonomy, $object_type) {
	global $wp_taxonomies;

	if ( !isset($wp_taxonomies[$taxonomy]) )
		return false;

	if ( ! get_post_type_object($object_type) )
		return false;

	if ( ! in_array( $object_type, $wp_taxonomies[$taxonomy]->object_type ) )
		$wp_taxonomies[$taxonomy]->object_type[] = $object_type;

	// Filter out empties.
	$wp_taxonomies[ $taxonomy ]->object_type = array_filter( $wp_taxonomies[ $taxonomy ]->object_type );

	return true;
}

/**
 * Remove an already registered taxonomy from an object type.
 *
 * @since 3.7.0
 *
 * @global array $wp_taxonomies The registered taxonomies.
 *
 * @param string $taxonomy    Name of taxonomy object.
 * @param string $object_type Name of the object type.
 * @return bool True if successful, false if not.
 */
function unregister_taxonomy_for_object_type( $taxonomy, $object_type ) {
	global $wp_taxonomies;

	if ( ! isset( $wp_taxonomies[ $taxonomy ] ) )
		return false;

	if ( ! get_post_type_object( $object_type ) )
		return false;

	$key = array_search( $object_type, $wp_taxonomies[ $taxonomy ]->object_type, true );
	if ( false === $key )
		return false;

	unset( $wp_taxonomies[ $taxonomy ]->object_type[ $key ] );
	return true;
}

//
// Term API
//

/**
 * Retrieve object_ids of valid taxonomy and term.
 *
 * The strings of $taxonomies must exist before this function will continue. On
 * failure of finding a valid taxonomy, it will return an WP_Error class, kind
 * of like Exceptions in PHP 5, except you can't catch them. Even so, you can
 * still test for the WP_Error class and get the error message.
 *
 * The $terms aren't checked the same as $taxonomies, but still need to exist
 * for $object_ids to be returned.
 *
 * It is possible to change the order that object_ids is returned by either
 * using PHP sort family functions or using the database by using $args with
 * either ASC or DESC array. The value should be in the key named 'order'.
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|array    $term_ids   Term id or array of term ids of terms that will be used.
 * @param string|array $taxonomies String of taxonomy name or Array of string values of taxonomy names.
 * @param array|string $args       Change the order of the object_ids, either ASC or DESC.
 * @return WP_Error|array If the taxonomy does not exist, then WP_Error will be returned. On success.
 *	the array can be empty meaning that there are no $object_ids found or it will return the $object_ids found.
 */
function get_objects_in_term( $term_ids, $taxonomies, $args = array() ) {
	global $wpdb;

	if ( ! is_array( $term_ids ) ) {
		$term_ids = array( $term_ids );
	}
	if ( ! is_array( $taxonomies ) ) {
		$taxonomies = array( $taxonomies );
	}
	foreach ( (array) $taxonomies as $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy' ) );
		}
	}

	$defaults = array( 'order' => 'ASC' );
	$args = wp_parse_args( $args, $defaults );

	$order = ( 'desc' == strtolower( $args['order'] ) ) ? 'DESC' : 'ASC';

	$term_ids = array_map('intval', $term_ids );

	$taxonomies = "'" . implode( "', '", $taxonomies ) . "'";
	$term_ids = "'" . implode( "', '", $term_ids ) . "'";

	$object_ids = $wpdb->get_col("SELECT tr.object_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ($taxonomies) AND tt.term_id IN ($term_ids) ORDER BY tr.object_id $order");

	if ( ! $object_ids ){
		return array();
	}
	return $object_ids;
}

/**
 * Given a taxonomy query, generates SQL to be appended to a main query.
 *
 * @since 3.1.0
 *
 * @see WP_Tax_Query
 *
 * @param array  $tax_query         A compact tax query
 * @param string $primary_table
 * @param string $primary_id_column
 * @return array
 */
function get_tax_sql( $tax_query, $primary_table, $primary_id_column ) {
	$tax_query_obj = new WP_Tax_Query( $tax_query );
	return $tax_query_obj->get_sql( $primary_table, $primary_id_column );
}

/**
 * Get all Term data from database by Term ID.
 *
 * The usage of the get_term function is to apply filters to a term object. It
 * is possible to get a term object from the database before applying the
 * filters.
 *
 * $term ID must be part of $taxonomy, to get from the database. Failure, might
 * be able to be captured by the hooks. Failure would be the same value as $wpdb
 * returns for the get_row method.
 *
 * There are two hooks, one is specifically for each term, named 'get_term', and
 * the second is for the taxonomy name, 'term_$taxonomy'. Both hooks gets the
 * term object, and the taxonomy name as parameters. Both hooks are expected to
 * return a Term object.
 *
 * {@see 'get_term'} hook - Takes two parameters the term Object and the taxonomy name.
 * Must return term object. Used in get_term() as a catch-all filter for every
 * $term.
 *
 * {@see 'get_$taxonomy'} hook - Takes two parameters the term Object and the taxonomy
 * name. Must return term object. $taxonomy will be the taxonomy name, so for
 * example, if 'category', it would be 'get_category' as the filter name. Useful
 * for custom taxonomies or plugging into default taxonomies.
 *
 * @todo Better formatting for DocBlock
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @see sanitize_term_field() The $context param lists the available values for get_term_by() $filter param.
 *
 * @param int|object $term     If integer, will get from database. If object will apply filters and return $term.
 * @param string     $taxonomy Taxonomy name that $term is part of.
 * @param string     $output   Constant OBJECT, ARRAY_A, or ARRAY_N
 * @param string     $filter   Optional, default is raw or no WordPress defined filter will applied.
 * @return object|array|null|WP_Error Term Row from database. Will return null if $term is empty. If taxonomy does not
 * exist then WP_Error will be returned.
 */
function get_term($term, $taxonomy, $output = OBJECT, $filter = 'raw') {
	global $wpdb;

	if ( empty( $term ) ) {
		return new WP_Error( 'invalid_term', __( 'Empty Term' ) );
	}

	if ( ! taxonomy_exists( $taxonomy ) ) {
		return new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy' ) );
	}

	if ( is_object($term) && empty($term->filter) ) {
		wp_cache_add( $term->term_id, $term, $taxonomy );
		$_term = $term;
	} else {
		if ( is_object($term) )
			$term = $term->term_id;
		if ( !$term = (int) $term )
			return null;
		if ( ! $_term = wp_cache_get( $term, $taxonomy ) ) {
			$_term = $wpdb->get_row( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = %s AND t.term_id = %d LIMIT 1", $taxonomy, $term) );
			if ( ! $_term )
				return null;
			wp_cache_add( $term, $_term, $taxonomy );
		}
	}

	/**
	 * Filter a term.
	 *
	 * @since 2.3.0
	 *
	 * @param int|object $_term    Term object or ID.
	 * @param string     $taxonomy The taxonomy slug.
	 */
	$_term = apply_filters( 'get_term', $_term, $taxonomy );

	/**
	 * Filter a taxonomy.
	 *
	 * The dynamic portion of the filter name, `$taxonomy`, refers
	 * to the taxonomy slug.
	 *
	 * @since 2.3.0
	 *
	 * @param int|object $_term    Term object or ID.
	 * @param string     $taxonomy The taxonomy slug.
	 */
	$_term = apply_filters( "get_$taxonomy", $_term, $taxonomy );
	$_term = sanitize_term($_term, $taxonomy, $filter);

	if ( $output == OBJECT ) {
		return $_term;
	} elseif ( $output == ARRAY_A ) {
		$__term = get_object_vars($_term);
		return $__term;
	} elseif ( $output == ARRAY_N ) {
		$__term = array_values(get_object_vars($_term));
		return $__term;
	} else {
		return $_term;
	}
}

/**
 * Get all Term data from database by Term field and data.
 *
 * Warning: $value is not escaped for 'name' $field. You must do it yourself, if
 * required.
 *
 * The default $field is 'id', therefore it is possible to also use null for
 * field, but not recommended that you do so.
 *
 * If $value does not exist, the return value will be false. If $taxonomy exists
 * and $field and $value combinations exist, the Term will be returned.
 *
 * @todo Better formatting for DocBlock.
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @see sanitize_term_field() The $context param lists the available values for get_term_by() $filter param.
 *
 * @param string     $field    Either 'slug', 'name', 'id' (term_id), or 'term_taxonomy_id'
 * @param string|int $value    Search for this term value
 * @param string     $taxonomy Taxonomy Name
 * @param string     $output   Constant OBJECT, ARRAY_A, or ARRAY_N
 * @param string     $filter   Optional, default is raw or no WordPress defined filter will applied.
 * @return object|array|null|WP_Error|false Term Row from database.
 *                                          Will return false if $taxonomy does not exist or $term was not found.
 */
function get_term_by($field, $value, $taxonomy, $output = OBJECT, $filter = 'raw') {
	global $wpdb;

	if ( ! taxonomy_exists($taxonomy) )
		return false;

	if ( 'slug' == $field ) {
		$field = 't.slug';
		$value = sanitize_title($value);
		if ( empty($value) )
			return false;
	} elseif ( 'name' == $field ) {
		// Assume already escaped
		$value = wp_unslash($value);
		$field = 't.name';
	} elseif ( 'term_taxonomy_id' == $field ) {
		$value = (int) $value;
		$field = 'tt.term_taxonomy_id';
	} else {
		$term = get_term( (int) $value, $taxonomy, $output, $filter );
		if ( is_wp_error( $term ) || is_null( $term ) ) {
			$term = false;
		}
		return $term;
	}

	$term = $wpdb->get_row( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = %s AND $field = %s LIMIT 1", $taxonomy, $value ) );
	if ( ! $term )
		return false;

	wp_cache_add( $term->term_id, $term, $taxonomy );

	/** This filter is documented in wp-includes/taxonomy-functions.php */
	$term = apply_filters( 'get_term', $term, $taxonomy );

	/** This filter is documented in wp-includes/taxonomy-functions.php */
	$term = apply_filters( "get_$taxonomy", $term, $taxonomy );

	$term = sanitize_term($term, $taxonomy, $filter);

	if ( $output == OBJECT ) {
		return $term;
	} elseif ( $output == ARRAY_A ) {
		return get_object_vars($term);
	} elseif ( $output == ARRAY_N ) {
		return array_values(get_object_vars($term));
	} else {
		return $term;
	}
}

/**
 * Merge all term children into a single array of their IDs.
 *
 * This recursive function will merge all of the children of $term into the same
 * array of term IDs. Only useful for taxonomies which are hierarchical.
 *
 * Will return an empty array if $term does not exist in $taxonomy.
 *
 * @since 2.3.0
 *
 * @param string $term_id  ID of Term to get children.
 * @param string $taxonomy Taxonomy Name.
 * @return array|WP_Error List of Term IDs. WP_Error returned if `$taxonomy` does not exist.
 */
function get_term_children( $term_id, $taxonomy ) {
	if ( ! taxonomy_exists($taxonomy) )
		return new WP_Error('invalid_taxonomy', __('Invalid taxonomy'));

	$term_id = intval( $term_id );

	$terms = _get_term_hierarchy($taxonomy);

	if ( ! isset($terms[$term_id]) )
		return array();

	$children = $terms[$term_id];

	foreach ( (array) $terms[$term_id] as $child ) {
		if ( $term_id == $child ) {
			continue;
		}

		if ( isset($terms[$child]) )
			$children = array_merge($children, get_term_children($child, $taxonomy));
	}

	return $children;
}

/**
 * Get sanitized Term field.
 *
 * Does checks for $term, based on the $taxonomy. The function is for contextual
 * reasons and for simplicity of usage. See sanitize_term_field() for more
 * information.
 *
 * @since 2.3.0
 *
 * @param string $field    Term field to fetch.
 * @param int    $term     Term ID.
 * @param string $taxonomy Taxonomy Name.
 * @param string $context  Optional, default is display. Look at sanitize_term_field() for available options.
 * @return string|int|null|WP_Error Will return an empty string if $term is not an object or if $field is not set in $term.
 */
function get_term_field( $field, $term, $taxonomy, $context = 'display' ) {
	$term = (int) $term;
	$term = get_term( $term, $taxonomy );
	if ( is_wp_error($term) )
		return $term;

	if ( !is_object($term) )
		return '';

	if ( !isset($term->$field) )
		return '';

	return sanitize_term_field($field, $term->$field, $term->term_id, $taxonomy, $context);
}

/**
 * Sanitizes Term for editing.
 *
 * Return value is sanitize_term() and usage is for sanitizing the term for
 * editing. Function is for contextual and simplicity.
 *
 * @since 2.3.0
 *
 * @param int|object $id       Term ID or object.
 * @param string     $taxonomy Taxonomy name.
 * @return string|int|null|WP_Error Will return empty string if $term is not an object.
 */
function get_term_to_edit( $id, $taxonomy ) {
	$term = get_term( $id, $taxonomy );

	if ( is_wp_error($term) )
		return $term;

	if ( !is_object($term) )
		return '';

	return sanitize_term($term, $taxonomy, 'edit');
}

/**
 * Retrieve the terms in a given taxonomy or list of taxonomies.
 *
 * You can fully inject any customizations to the query before it is sent, as
 * well as control the output with a filter.
 *
 * The {@see 'get_terms'} filter will be called when the cache has the term and will
 * pass the found term along with the array of $taxonomies and array of $args.
 * This filter is also called before the array of terms is passed and will pass
 * the array of terms, along with the $taxonomies and $args.
 *
 * The {@see 'list_terms_exclusions'} filter passes the compiled exclusions along with
 * the $args.
 *
 * The {@see 'get_terms_orderby'} filter passes the `ORDER BY` clause for the query
 * along with the $args array.
 *
 * @since 2.3.0
 * @since 4.2.0 Introduced 'name' and 'childless' parameters.
 * @since 4.4.0 Introduced the ability to pass 'term_id' as an alias of 'id' for the `orderby` parameter.
 *
 * @global wpdb  $wpdb WordPress database abstraction object.
 * @global array $wp_filter
 *
 * @param string|array $taxonomies Taxonomy name or list of Taxonomy names.
 * @param array|string $args {
 *     Optional. Array or string of arguments to get terms.
 *
 *     @type string       $orderby           Field(s) to order terms by. Accepts term fields ('name', 'slug',
 *                                           'term_group', 'term_id', 'id', 'description'), 'count' for term
 *                                           taxonomy count, 'include' to match the 'order' of the $include param,
 *                                           or 'none' to skip ORDER BY. Defaults to 'name'.
 *     @type string       $order             Whether to order terms in ascending or descending order.
 *                                           Accepts 'ASC' (ascending) or 'DESC' (descending).
 *                                           Default 'ASC'.
 *     @type bool|int     $hide_empty        Whether to hide terms not assigned to any posts. Accepts
 *                                           1|true or 0|false. Default 1|true.
 *     @type array|string $include           Array or comma/space-separated string of term ids to include.
 *                                           Default empty array.
 *     @type array|string $exclude           Array or comma/space-separated string of term ids to exclude.
 *                                           If $include is non-empty, $exclude is ignored.
 *                                           Default empty array.
 *     @type array|string $exclude_tree      Array or comma/space-separated string of term ids to exclude
 *                                           along with all of their descendant terms. If $include is
 *                                           non-empty, $exclude_tree is ignored. Default empty array.
 *     @type int|string   $number            Maximum number of terms to return. Accepts ''|0 (all) or any
 *                                           positive number. Default ''|0 (all).
 *     @type int          $offset            The number by which to offset the terms query. Default empty.
 *     @type string       $fields            Term fields to query for. Accepts 'all' (returns an array of complete
 *                                           term objects), 'ids' (returns an array of ids), 'id=>parent' (returns
 *                                           an associative array with ids as keys, parent term IDs as values),
 *                                           'names' (returns an array of term names), 'count' (returns the number
 *                                           of matching terms), 'id=>name' (returns an associative array with ids
 *                                           as keys, term names as values), or 'id=>slug' (returns an associative
 *                                           array with ids as keys, term slugs as values). Default 'all'.
 *     @type string|array $name              Optional. Name or array of names to return term(s) for. Default empty.
 *     @type string|array $slug              Optional. Slug or array of slugs to return term(s) for. Default empty.
 *     @type bool         $hierarchical      Whether to include terms that have non-empty descendants (even
 *                                           if $hide_empty is set to true). Default true.
 *     @type string       $search            Search criteria to match terms. Will be SQL-formatted with
 *                                           wildcards before and after. Default empty.
 *     @type string       $name__like        Retrieve terms with criteria by which a term is LIKE $name__like.
 *                                           Default empty.
 *     @type string       $description__like Retrieve terms where the description is LIKE $description__like.
 *                                           Default empty.
 *     @type bool         $pad_counts        Whether to pad the quantity of a term's children in the quantity
 *                                           of each term's "count" object variable. Default false.
 *     @type string       $get               Whether to return terms regardless of ancestry or whether the terms
 *                                           are empty. Accepts 'all' or empty (disabled). Default empty.
 *     @type int          $child_of          Term ID to retrieve child terms of. If multiple taxonomies
 *                                           are passed, $child_of is ignored. Default 0.
 *     @type int|string   $parent            Parent term ID to retrieve direct-child terms of. Default empty.
 *     @type bool         $childless         True to limit results to terms that have no children. This parameter has
 *                                           no effect on non-hierarchical taxonomies. Default false.
 *     @type string       $cache_domain      Unique cache key to be produced when this query is stored in an
 *                                           object cache. Default is 'core'.
 * }
 * @return array|int|WP_Error List of Term Objects and their children. Will return WP_Error, if any of $taxonomies
 *                        do not exist.
 */
function get_terms( $taxonomies, $args = '' ) {
	global $wpdb;
	$empty_array = array();

	$single_taxonomy = ! is_array( $taxonomies ) || 1 === count( $taxonomies );
	if ( ! is_array( $taxonomies ) ) {
		$taxonomies = array( $taxonomies );
	}

	foreach ( $taxonomies as $taxonomy ) {
		if ( ! taxonomy_exists($taxonomy) ) {
			return new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy' ) );
		}
	}

	$defaults = array('orderby' => 'name', 'order' => 'ASC',
		'hide_empty' => true, 'exclude' => array(), 'exclude_tree' => array(), 'include' => array(),
		'number' => '', 'fields' => 'all', 'name' => '', 'slug' => '', 'parent' => '', 'childless' => false,
		'hierarchical' => true, 'child_of' => 0, 'get' => '', 'name__like' => '', 'description__like' => '',
		'pad_counts' => false, 'offset' => '', 'search' => '', 'cache_domain' => 'core' );
	$args = wp_parse_args( $args, $defaults );
	$args['number'] = absint( $args['number'] );
	$args['offset'] = absint( $args['offset'] );

	// Save queries by not crawling the tree in the case of multiple taxes or a flat tax.
	$has_hierarchical_tax = false;
	foreach ( $taxonomies as $_tax ) {
		if ( is_taxonomy_hierarchical( $_tax ) ) {
			$has_hierarchical_tax = true;
		}
	}

	if ( ! $has_hierarchical_tax ) {
		$args['hierarchical'] = false;
		$args['pad_counts'] = false;
	}

	// 'parent' overrides 'child_of'.
	if ( 0 < intval( $args['parent'] ) ) {
		$args['child_of'] = false;
	}

	if ( 'all' == $args['get'] ) {
		$args['childless'] = false;
		$args['child_of'] = 0;
		$args['hide_empty'] = 0;
		$args['hierarchical'] = false;
		$args['pad_counts'] = false;
	}

	/**
	 * Filter the terms query arguments.
	 *
	 * @since 3.1.0
	 *
	 * @param array $args       An array of get_term() arguments.
	 * @param array $taxonomies An array of taxonomies.
	 */
	$args = apply_filters( 'get_terms_args', $args, $taxonomies );

	// Avoid the query if the queried parent/child_of term has no descendants.
	$child_of = $args['child_of'];
	$parent   = $args['parent'];

	if ( $child_of ) {
		$_parent = $child_of;
	} elseif ( $parent ) {
		$_parent = $parent;
	} else {
		$_parent = false;
	}

	if ( $_parent ) {
		$in_hierarchy = false;
		foreach ( $taxonomies as $_tax ) {
			$hierarchy = _get_term_hierarchy( $_tax );

			if ( isset( $hierarchy[ $_parent ] ) ) {
				$in_hierarchy = true;
			}
		}

		if ( ! $in_hierarchy ) {
			return $empty_array;
		}
	}

	// $args can be whatever, only use the args defined in defaults to compute the key.
	$filter_key = ( has_filter('list_terms_exclusions') ) ? serialize($GLOBALS['wp_filter']['list_terms_exclusions']) : '';
	$key = md5( serialize( wp_array_slice_assoc( $args, array_keys( $defaults ) ) ) . serialize( $taxonomies ) . $filter_key );
	$last_changed = wp_cache_get( 'last_changed', 'terms' );
	if ( ! $last_changed ) {
		$last_changed = microtime();
		wp_cache_set( 'last_changed', $last_changed, 'terms' );
	}
	$cache_key = "get_terms:$key:$last_changed";
	$cache = wp_cache_get( $cache_key, 'terms' );
	if ( false !== $cache ) {

		/**
		 * Filter the given taxonomy's terms cache.
		 *
		 * @since 2.3.0
		 *
		 * @param array $cache      Cached array of terms for the given taxonomy.
		 * @param array $taxonomies An array of taxonomies.
		 * @param array $args       An array of get_terms() arguments.
		 */
		return apply_filters( 'get_terms', $cache, $taxonomies, $args );
	}

	$_orderby = strtolower( $args['orderby'] );
	if ( 'count' == $_orderby ) {
		$orderby = 'tt.count';
	} elseif ( 'name' == $_orderby ) {
		$orderby = 't.name';
	} elseif ( 'slug' == $_orderby ) {
		$orderby = 't.slug';
	} elseif ( 'include' == $_orderby && ! empty( $args['include'] ) ) {
		$include = implode( ',', array_map( 'absint', $args['include'] ) );
		$orderby = "FIELD( t.term_id, $include )";
	} elseif ( 'term_group' == $_orderby ) {
		$orderby = 't.term_group';
	} elseif ( 'description' == $_orderby ) {
		$orderby = 'tt.description';
	} elseif ( 'none' == $_orderby ) {
		$orderby = '';
	} elseif ( empty( $_orderby ) || 'id' == $_orderby || 'term_id' === $_orderby ) {
		$orderby = 't.term_id';
	} else {
		$orderby = 't.name';
	}

	/**
	 * Filter the ORDERBY clause of the terms query.
	 *
	 * @since 2.8.0
	 *
	 * @param string $orderby    `ORDERBY` clause of the terms query.
	 * @param array  $args       An array of terms query arguments.
	 * @param array  $taxonomies An array of taxonomies.
	 */
	$orderby = apply_filters( 'get_terms_orderby', $orderby, $args, $taxonomies );

	$order = strtoupper( $args['order'] );
	if ( ! empty( $orderby ) ) {
		$orderby = "ORDER BY $orderby";
	} else {
		$order = '';
	}

	if ( '' !== $order && ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
		$order = 'ASC';
	}

	$where = "tt.taxonomy IN ('" . implode("', '", $taxonomies) . "')";

	$exclude = $args['exclude'];
	$exclude_tree = $args['exclude_tree'];
	$include = $args['include'];

	$inclusions = '';
	if ( ! empty( $include ) ) {
		$exclude = '';
		$exclude_tree = '';
		$inclusions = implode( ',', wp_parse_id_list( $include ) );
	}

	if ( ! empty( $inclusions ) ) {
		$inclusions = ' AND t.term_id IN ( ' . $inclusions . ' )';
		$where .= $inclusions;
	}

	$exclusions = array();
	if ( ! empty( $exclude_tree ) ) {
		$exclude_tree = wp_parse_id_list( $exclude_tree );
		$excluded_children = $exclude_tree;
		foreach ( $exclude_tree as $extrunk ) {
			$excluded_children = array_merge(
				$excluded_children,
				(array) get_terms( $taxonomies[0], array( 'child_of' => intval( $extrunk ), 'fields' => 'ids', 'hide_empty' => 0 ) )
			);
		}
		$exclusions = array_merge( $excluded_children, $exclusions );
	}

	if ( ! empty( $exclude ) ) {
		$exclusions = array_merge( wp_parse_id_list( $exclude ), $exclusions );
	}

	// 'childless' terms are those without an entry in the flattened term hierarchy.
	$childless = (bool) $args['childless'];
	if ( $childless ) {
		foreach ( $taxonomies as $_tax ) {
			$term_hierarchy = _get_term_hierarchy( $_tax );
			$exclusions = array_merge( array_keys( $term_hierarchy ), $exclusions );
		}
	}

	if ( ! empty( $exclusions ) ) {
		$exclusions = ' AND t.term_id NOT IN (' . implode( ',', array_map( 'intval', $exclusions ) ) . ')';
	} else {
		$exclusions = '';
	}

	/**
	 * Filter the terms to exclude from the terms query.
	 *
	 * @since 2.3.0
	 *
	 * @param string $exclusions `NOT IN` clause of the terms query.
	 * @param array  $args       An array of terms query arguments.
	 * @param array  $taxonomies An array of taxonomies.
	 */
	$exclusions = apply_filters( 'list_terms_exclusions', $exclusions, $args, $taxonomies );

	if ( ! empty( $exclusions ) ) {
		$where .= $exclusions;
	}

	if ( ! empty( $args['name'] ) ) {
		$names = (array) $args['name'];
		foreach ( $names as &$_name ) {
			$_name = sanitize_term_field( 'name', $_name, 0, reset( $taxonomies ), 'db' );
		}

		$where .= " AND t.name IN ('" . implode( "', '", array_map( 'esc_sql', $names ) ) . "')";
	}

	if ( ! empty( $args['slug'] ) ) {
		if ( is_array( $args['slug'] ) ) {
			$slug = array_map( 'sanitize_title', $args['slug'] );
			$where .= " AND t.slug IN ('" . implode( "', '", $slug ) . "')";
		} else {
			$slug = sanitize_title( $args['slug'] );
			$where .= " AND t.slug = '$slug'";
		}
	}

	if ( ! empty( $args['name__like'] ) ) {
		$where .= $wpdb->prepare( " AND t.name LIKE %s", '%' . $wpdb->esc_like( $args['name__like'] ) . '%' );
	}

	if ( ! empty( $args['description__like'] ) ) {
		$where .= $wpdb->prepare( " AND tt.description LIKE %s", '%' . $wpdb->esc_like( $args['description__like'] ) . '%' );
	}

	if ( '' !== $parent ) {
		$parent = (int) $parent;
		$where .= " AND tt.parent = '$parent'";
	}

	$hierarchical = $args['hierarchical'];
	if ( 'count' == $args['fields'] ) {
		$hierarchical = false;
	}
	if ( $args['hide_empty'] && !$hierarchical ) {
		$where .= ' AND tt.count > 0';
	}

	$number = $args['number'];
	$offset = $args['offset'];

	// Don't limit the query results when we have to descend the family tree.
	if ( $number && ! $hierarchical && ! $child_of && '' === $parent ) {
		if ( $offset ) {
			$limits = 'LIMIT ' . $offset . ',' . $number;
		} else {
			$limits = 'LIMIT ' . $number;
		}
	} else {
		$limits = '';
	}

	if ( ! empty( $args['search'] ) ) {
		$like = '%' . $wpdb->esc_like( $args['search'] ) . '%';
		$where .= $wpdb->prepare( ' AND ((t.name LIKE %s) OR (t.slug LIKE %s))', $like, $like );
	}

	$selects = array();
	switch ( $args['fields'] ) {
		case 'all':
			$selects = array( 't.*', 'tt.*' );
			break;
		case 'ids':
		case 'id=>parent':
			$selects = array( 't.term_id', 'tt.parent', 'tt.count', 'tt.taxonomy' );
			break;
		case 'names':
			$selects = array( 't.term_id', 'tt.parent', 'tt.count', 't.name', 'tt.taxonomy' );
			break;
		case 'count':
			$orderby = '';
			$order = '';
			$selects = array( 'COUNT(*)' );
			break;
		case 'id=>name':
			$selects = array( 't.term_id', 't.name', 'tt.count', 'tt.taxonomy' );
			break;
		case 'id=>slug':
			$selects = array( 't.term_id', 't.slug', 'tt.count', 'tt.taxonomy' );
			break;
	}

	$_fields = $args['fields'];

	/**
	 * Filter the fields to select in the terms query.
	 *
	 * Field lists modified using this filter will only modify the term fields returned
	 * by the function when the `$fields` parameter set to 'count' or 'all'. In all other
	 * cases, the term fields in the results array will be determined by the `$fields`
	 * parameter alone.
	 *
	 * Use of this filter can result in unpredictable behavior, and is not recommended.
	 *
	 * @since 2.8.0
	 *
	 * @param array $selects    An array of fields to select for the terms query.
	 * @param array $args       An array of term query arguments.
	 * @param array $taxonomies An array of taxonomies.
	 */
	$fields = implode( ', ', apply_filters( 'get_terms_fields', $selects, $args, $taxonomies ) );

	$join = "INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id";

	$pieces = array( 'fields', 'join', 'where', 'orderby', 'order', 'limits' );

	/**
	 * Filter the terms query SQL clauses.
	 *
	 * @since 3.1.0
	 *
	 * @param array $pieces     Terms query SQL clauses.
	 * @param array $taxonomies An array of taxonomies.
	 * @param array $args       An array of terms query arguments.
	 */
	$clauses = apply_filters( 'terms_clauses', compact( $pieces ), $taxonomies, $args );

	$fields = isset( $clauses[ 'fields' ] ) ? $clauses[ 'fields' ] : '';
	$join = isset( $clauses[ 'join' ] ) ? $clauses[ 'join' ] : '';
	$where = isset( $clauses[ 'where' ] ) ? $clauses[ 'where' ] : '';
	$orderby = isset( $clauses[ 'orderby' ] ) ? $clauses[ 'orderby' ] : '';
	$order = isset( $clauses[ 'order' ] ) ? $clauses[ 'order' ] : '';
	$limits = isset( $clauses[ 'limits' ] ) ? $clauses[ 'limits' ] : '';

	$query = "SELECT $fields FROM $wpdb->terms AS t $join WHERE $where $orderby $order $limits";

	if ( 'count' == $_fields ) {
		return $wpdb->get_var( $query );
	}

	$terms = $wpdb->get_results($query);
	if ( 'all' == $_fields ) {
		update_term_cache( $terms );
	}

	if ( empty($terms) ) {
		wp_cache_add( $cache_key, array(), 'terms', DAY_IN_SECONDS );

		/** This filter is documented in wp-includes/taxonomy-functions.php */
		return apply_filters( 'get_terms', array(), $taxonomies, $args );
	}

	if ( $child_of ) {
		foreach ( $taxonomies as $_tax ) {
			$children = _get_term_hierarchy( $_tax );
			if ( ! empty( $children ) ) {
				$terms = _get_term_children( $child_of, $terms, $_tax );
			}
		}
	}

	// Update term counts to include children.
	if ( $args['pad_counts'] && 'all' == $_fields ) {
		foreach ( $taxonomies as $_tax ) {
			_pad_term_counts( $terms, $_tax );
		}
	}

	// Make sure we show empty categories that have children.
	if ( $hierarchical && $args['hide_empty'] && is_array( $terms ) ) {
		foreach ( $terms as $k => $term ) {
			if ( ! $term->count ) {
				$children = get_term_children( $term->term_id, $term->taxonomy );
				if ( is_array( $children ) ) {
					foreach ( $children as $child_id ) {
						$child = get_term( $child_id, $term->taxonomy );
						if ( $child->count ) {
							continue 2;
						}
					}
				}

				// It really is empty.
				unset($terms[$k]);
			}
		}
	}

	$_terms = array();
	if ( 'id=>parent' == $_fields ) {
		foreach ( $terms as $term ) {
			$_terms[ $term->term_id ] = $term->parent;
		}
	} elseif ( 'ids' == $_fields ) {
		foreach ( $terms as $term ) {
			$_terms[] = $term->term_id;
		}
	} elseif ( 'names' == $_fields ) {
		foreach ( $terms as $term ) {
			$_terms[] = $term->name;
		}
	} elseif ( 'id=>name' == $_fields ) {
		foreach ( $terms as $term ) {
			$_terms[ $term->term_id ] = $term->name;
		}
	} elseif ( 'id=>slug' == $_fields ) {
		foreach ( $terms as $term ) {
			$_terms[ $term->term_id ] = $term->slug;
		}
	}

	if ( ! empty( $_terms ) ) {
		$terms = $_terms;
	}

	if ( $number && is_array( $terms ) && count( $terms ) > $number ) {
		$terms = array_slice( $terms, $offset, $number );
	}

	wp_cache_add( $cache_key, $terms, 'terms', DAY_IN_SECONDS );

	/** This filter is documented in wp-includes/taxonomy */
	return apply_filters( 'get_terms', $terms, $taxonomies, $args );
}

/**
 * Check if Term exists.
 *
 * Formerly is_term(), introduced in 2.3.0.
 *
 * @since 3.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|string $term     The term to check
 * @param string     $taxonomy The taxonomy name to use
 * @param int        $parent   Optional. ID of parent term under which to confine the exists search.
 * @return mixed Returns null if the term does not exist. Returns the term ID
 *               if no taxonomy is specified and the term ID exists. Returns
 *               an array of the term ID and the term taxonomy ID the taxonomy
 *               is specified and the pairing exists.
 */
function term_exists( $term, $taxonomy = '', $parent = null ) {
	global $wpdb;

	$select = "SELECT term_id FROM $wpdb->terms as t WHERE ";
	$tax_select = "SELECT tt.term_id, tt.term_taxonomy_id FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy as tt ON tt.term_id = t.term_id WHERE ";

	if ( is_int($term) ) {
		if ( 0 == $term )
			return 0;
		$where = 't.term_id = %d';
		if ( !empty($taxonomy) )
			return $wpdb->get_row( $wpdb->prepare( $tax_select . $where . " AND tt.taxonomy = %s", $term, $taxonomy ), ARRAY_A );
		else
			return $wpdb->get_var( $wpdb->prepare( $select . $where, $term ) );
	}

	$term = trim( wp_unslash( $term ) );
	$slug = sanitize_title( $term );

	$where = 't.slug = %s';
	$else_where = 't.name = %s';
	$where_fields = array($slug);
	$else_where_fields = array($term);
	$orderby = 'ORDER BY t.term_id ASC';
	$limit = 'LIMIT 1';
	if ( !empty($taxonomy) ) {
		if ( is_numeric( $parent ) ) {
			$parent = (int) $parent;
			$where_fields[] = $parent;
			$else_where_fields[] = $parent;
			$where .= ' AND tt.parent = %d';
			$else_where .= ' AND tt.parent = %d';
		}

		$where_fields[] = $taxonomy;
		$else_where_fields[] = $taxonomy;

		if ( $result = $wpdb->get_row( $wpdb->prepare("SELECT tt.term_id, tt.term_taxonomy_id FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy as tt ON tt.term_id = t.term_id WHERE $where AND tt.taxonomy = %s $orderby $limit", $where_fields), ARRAY_A) )
			return $result;

		return $wpdb->get_row( $wpdb->prepare("SELECT tt.term_id, tt.term_taxonomy_id FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy as tt ON tt.term_id = t.term_id WHERE $else_where AND tt.taxonomy = %s $orderby $limit", $else_where_fields), ARRAY_A);
	}

	if ( $result = $wpdb->get_var( $wpdb->prepare("SELECT term_id FROM $wpdb->terms as t WHERE $where $orderby $limit", $where_fields) ) )
		return $result;

	return $wpdb->get_var( $wpdb->prepare("SELECT term_id FROM $wpdb->terms as t WHERE $else_where $orderby $limit", $else_where_fields) );
}

/**
 * Check if a term is an ancestor of another term.
 *
 * You can use either an id or the term object for both parameters.
 *
 * @since 3.4.0
 *
 * @param int|object $term1    ID or object to check if this is the parent term.
 * @param int|object $term2    The child term.
 * @param string     $taxonomy Taxonomy name that $term1 and `$term2` belong to.
 * @return bool Whether `$term2` is a child of `$term1`.
 */
function term_is_ancestor_of( $term1, $term2, $taxonomy ) {
	if ( ! isset( $term1->term_id ) )
		$term1 = get_term( $term1, $taxonomy );
	if ( ! isset( $term2->parent ) )
		$term2 = get_term( $term2, $taxonomy );

	if ( empty( $term1->term_id ) || empty( $term2->parent ) )
		return false;
	if ( $term2->parent == $term1->term_id )
		return true;

	return term_is_ancestor_of( $term1, get_term( $term2->parent, $taxonomy ), $taxonomy );
}

/**
 * Sanitize Term all fields.
 *
 * Relies on sanitize_term_field() to sanitize the term. The difference is that
 * this function will sanitize <strong>all</strong> fields. The context is based
 * on sanitize_term_field().
 *
 * The $term is expected to be either an array or an object.
 *
 * @since 2.3.0
 *
 * @param array|object $term     The term to check.
 * @param string       $taxonomy The taxonomy name to use.
 * @param string       $context  Optional. Context in which to sanitize the term. Accepts 'edit', 'db',
 *                               'display', 'attribute', or 'js'. Default 'display'.
 * @return array|object Term with all fields sanitized.
 */
function sanitize_term($term, $taxonomy, $context = 'display') {
	$fields = array( 'term_id', 'name', 'description', 'slug', 'count', 'parent', 'term_group', 'term_taxonomy_id', 'object_id' );

	$do_object = is_object( $term );

	$term_id = $do_object ? $term->term_id : (isset($term['term_id']) ? $term['term_id'] : 0);

	foreach ( (array) $fields as $field ) {
		if ( $do_object ) {
			if ( isset($term->$field) )
				$term->$field = sanitize_term_field($field, $term->$field, $term_id, $taxonomy, $context);
		} else {
			if ( isset($term[$field]) )
				$term[$field] = sanitize_term_field($field, $term[$field], $term_id, $taxonomy, $context);
		}
	}

	if ( $do_object )
		$term->filter = $context;
	else
		$term['filter'] = $context;

	return $term;
}

/**
 * Cleanse the field value in the term based on the context.
 *
 * Passing a term field value through the function should be assumed to have
 * cleansed the value for whatever context the term field is going to be used.
 *
 * If no context or an unsupported context is given, then default filters will
 * be applied.
 *
 * There are enough filters for each context to support a custom filtering
 * without creating your own filter function. Simply create a function that
 * hooks into the filter you need.
 *
 * @since 2.3.0
 *
 * @param string $field    Term field to sanitize.
 * @param string $value    Search for this term value.
 * @param int    $term_id  Term ID.
 * @param string $taxonomy Taxonomy Name.
 * @param string $context  Context in which to sanitize the term field. Accepts 'edit', 'db', 'display',
 *                         'attribute', or 'js'.
 * @return mixed Sanitized field.
 */
function sanitize_term_field($field, $value, $term_id, $taxonomy, $context) {
	$int_fields = array( 'parent', 'term_id', 'count', 'term_group', 'term_taxonomy_id', 'object_id' );
	if ( in_array( $field, $int_fields ) ) {
		$value = (int) $value;
		if ( $value < 0 )
			$value = 0;
	}

	if ( 'raw' == $context )
		return $value;

	if ( 'edit' == $context ) {

		/**
		 * Filter a term field to edit before it is sanitized.
		 *
		 * The dynamic portion of the filter name, `$field`, refers to the term field.
		 *
		 * @since 2.3.0
		 *
		 * @param mixed $value     Value of the term field.
		 * @param int   $term_id   Term ID.
		 * @param string $taxonomy Taxonomy slug.
		 */
		$value = apply_filters( "edit_term_{$field}", $value, $term_id, $taxonomy );

		/**
		 * Filter the taxonomy field to edit before it is sanitized.
		 *
		 * The dynamic portions of the filter name, `$taxonomy` and `$field`, refer
		 * to the taxonomy slug and taxonomy field, respectively.
		 *
		 * @since 2.3.0
		 *
		 * @param mixed $value   Value of the taxonomy field to edit.
		 * @param int   $term_id Term ID.
		 */
		$value = apply_filters( "edit_{$taxonomy}_{$field}", $value, $term_id );

		if ( 'description' == $field )
			$value = esc_html($value); // textarea_escaped
		else
			$value = esc_attr($value);
	} elseif ( 'db' == $context ) {

		/**
		 * Filter a term field value before it is sanitized.
		 *
		 * The dynamic portion of the filter name, `$field`, refers to the term field.
		 *
		 * @since 2.3.0
		 *
		 * @param mixed  $value    Value of the term field.
		 * @param string $taxonomy Taxonomy slug.
		 */
		$value = apply_filters( "pre_term_{$field}", $value, $taxonomy );

		/**
		 * Filter a taxonomy field before it is sanitized.
		 *
		 * The dynamic portions of the filter name, `$taxonomy` and `$field`, refer
		 * to the taxonomy slug and field name, respectively.
		 *
		 * @since 2.3.0
		 *
		 * @param mixed $value Value of the taxonomy field.
		 */
		$value = apply_filters( "pre_{$taxonomy}_{$field}", $value );

		// Back compat filters
		if ( 'slug' == $field ) {
			/**
			 * Filter the category nicename before it is sanitized.
			 *
			 * Use the pre_{$taxonomy}_{$field} hook instead.
			 *
			 * @since 2.0.3
			 *
			 * @param string $value The category nicename.
			 */
			$value = apply_filters( 'pre_category_nicename', $value );
		}

	} elseif ( 'rss' == $context ) {

		/**
		 * Filter the term field for use in RSS.
		 *
		 * The dynamic portion of the filter name, `$field`, refers to the term field.
		 *
		 * @since 2.3.0
		 *
		 * @param mixed  $value    Value of the term field.
		 * @param string $taxonomy Taxonomy slug.
		 */
		$value = apply_filters( "term_{$field}_rss", $value, $taxonomy );

		/**
		 * Filter the taxonomy field for use in RSS.
		 *
		 * The dynamic portions of the hook name, `$taxonomy`, and `$field`, refer
		 * to the taxonomy slug and field name, respectively.
		 *
		 * @since 2.3.0
		 *
		 * @param mixed $value Value of the taxonomy field.
		 */
		$value = apply_filters( "{$taxonomy}_{$field}_rss", $value );
	} else {
		// Use display filters by default.

		/**
		 * Filter the term field sanitized for display.
		 *
		 * The dynamic portion of the filter name, `$field`, refers to the term field name.
		 *
		 * @since 2.3.0
		 *
		 * @param mixed  $value    Value of the term field.
		 * @param int    $term_id  Term ID.
		 * @param string $taxonomy Taxonomy slug.
		 * @param string $context  Context to retrieve the term field value.
		 */
		$value = apply_filters( "term_{$field}", $value, $term_id, $taxonomy, $context );

		/**
		 * Filter the taxonomy field sanitized for display.
		 *
		 * The dynamic portions of the filter name, `$taxonomy`, and `$field`, refer
		 * to the taxonomy slug and taxonomy field, respectively.
		 *
		 * @since 2.3.0
		 *
		 * @param mixed  $value   Value of the taxonomy field.
		 * @param int    $term_id Term ID.
		 * @param string $context Context to retrieve the taxonomy field value.
		 */
		$value = apply_filters( "{$taxonomy}_{$field}", $value, $term_id, $context );
	}

	if ( 'attribute' == $context ) {
		$value = esc_attr($value);
	} elseif ( 'js' == $context ) {
		$value = esc_js($value);
	}
	return $value;
}

/**
 * Count how many terms are in Taxonomy.
 *
 * Default $args is 'hide_empty' which can be 'hide_empty=true' or array('hide_empty' => true).
 *
 * @todo Document $args as a hash notation.
 *
 * @since 2.3.0
 *
 * @param string       $taxonomy Taxonomy name
 * @param array|string $args     Overwrite defaults. See get_terms()
 * @return array|int|WP_Error How many terms are in $taxonomy. WP_Error if $taxonomy does not exist.
 */
function wp_count_terms( $taxonomy, $args = array() ) {
	$defaults = array('hide_empty' => false);
	$args = wp_parse_args($args, $defaults);

	// backwards compatibility
	if ( isset($args['ignore_empty']) ) {
		$args['hide_empty'] = $args['ignore_empty'];
		unset($args['ignore_empty']);
	}

	$args['fields'] = 'count';

	return get_terms($taxonomy, $args);
}

/**
 * Will unlink the object from the taxonomy or taxonomies.
 *
 * Will remove all relationships between the object and any terms in
 * a particular taxonomy or taxonomies. Does not remove the term or
 * taxonomy itself.
 *
 * @since 2.3.0
 *
 * @param int          $object_id  The term Object Id that refers to the term.
 * @param string|array $taxonomies List of Taxonomy Names or single Taxonomy name.
 */
function wp_delete_object_term_relationships( $object_id, $taxonomies ) {
	$object_id = (int) $object_id;

	if ( !is_array($taxonomies) )
		$taxonomies = array($taxonomies);

	foreach ( (array) $taxonomies as $taxonomy ) {
		$term_ids = wp_get_object_terms( $object_id, $taxonomy, array( 'fields' => 'ids' ) );
		$term_ids = array_map( 'intval', $term_ids );
		wp_remove_object_terms( $object_id, $term_ids, $taxonomy );
	}
}

/**
 * Removes a term from the database.
 *
 * If the term is a parent of other terms, then the children will be updated to
 * that term's parent.
 *
 * The `$args` 'default' will only override the terms found, if there is only one
 * term found. Any other and the found terms are used.
 *
 * The $args 'force_default' will force the term supplied as default to be
 * assigned even if the object was not going to be termless
 *
 * @todo Document $args as a hash notation.
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int          $term     Term ID.
 * @param string       $taxonomy Taxonomy Name.
 * @param array|string $args     Optional. Change 'default' term id and override found term ids.
 * @return bool|int|WP_Error Returns false if not term; true if completes delete action.
 */
function wp_delete_term( $term, $taxonomy, $args = array() ) {
	global $wpdb;

	$term = (int) $term;

	if ( ! $ids = term_exists($term, $taxonomy) )
		return false;
	if ( is_wp_error( $ids ) )
		return $ids;

	$tt_id = $ids['term_taxonomy_id'];

	$defaults = array();

	if ( 'category' == $taxonomy ) {
		$defaults['default'] = get_option( 'default_category' );
		if ( $defaults['default'] == $term )
			return 0; // Don't delete the default category
	}

	$args = wp_parse_args($args, $defaults);

	if ( isset( $args['default'] ) ) {
		$default = (int) $args['default'];
		if ( ! term_exists( $default, $taxonomy ) ) {
			unset( $default );
		}
	}

	if ( isset( $args['force_default'] ) ) {
		$force_default = $args['force_default'];
	}

	/**
	 * Fires when deleting a term, before any modifications are made to posts or terms.
	 *
	 * @since 4.1.0
	 *
	 * @param int    $term     Term ID.
	 * @param string $taxonomy Taxonomy Name.
	 */
	do_action( 'pre_delete_term', $term, $taxonomy );

	// Update children to point to new parent
	if ( is_taxonomy_hierarchical($taxonomy) ) {
		$term_obj = get_term($term, $taxonomy);
		if ( is_wp_error( $term_obj ) )
			return $term_obj;
		$parent = $term_obj->parent;

		$edit_ids = $wpdb->get_results( "SELECT term_id, term_taxonomy_id FROM $wpdb->term_taxonomy WHERE `parent` = " . (int)$term_obj->term_id );
		$edit_tt_ids = wp_list_pluck( $edit_ids, 'term_taxonomy_id' );

		/**
		 * Fires immediately before a term to delete's children are reassigned a parent.
		 *
		 * @since 2.9.0
		 *
		 * @param array $edit_tt_ids An array of term taxonomy IDs for the given term.
		 */
		do_action( 'edit_term_taxonomies', $edit_tt_ids );

		$wpdb->update( $wpdb->term_taxonomy, compact( 'parent' ), array( 'parent' => $term_obj->term_id) + compact( 'taxonomy' ) );

		// Clean the cache for all child terms.
		$edit_term_ids = wp_list_pluck( $edit_ids, 'term_id' );
		clean_term_cache( $edit_term_ids, $taxonomy );

		/**
		 * Fires immediately after a term to delete's children are reassigned a parent.
		 *
		 * @since 2.9.0
		 *
		 * @param array $edit_tt_ids An array of term taxonomy IDs for the given term.
		 */
		do_action( 'edited_term_taxonomies', $edit_tt_ids );
	}

	// Get the term before deleting it or its term relationships so we can pass to actions below.
	$deleted_term = get_term( $term, $taxonomy );

	$objects = $wpdb->get_col( $wpdb->prepare( "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $tt_id ) );

	foreach ( (array) $objects as $object ) {
		$terms = wp_get_object_terms($object, $taxonomy, array('fields' => 'ids', 'orderby' => 'none'));
		if ( 1 == count($terms) && isset($default) ) {
			$terms = array($default);
		} else {
			$terms = array_diff($terms, array($term));
			if (isset($default) && isset($force_default) && $force_default)
				$terms = array_merge($terms, array($default));
		}
		$terms = array_map('intval', $terms);
		wp_set_object_terms($object, $terms, $taxonomy);
	}

	// Clean the relationship caches for all object types using this term.
	$tax_object = get_taxonomy( $taxonomy );
	foreach ( $tax_object->object_type as $object_type )
		clean_object_term_cache( $objects, $object_type );

	/**
	 * Fires immediately before a term taxonomy ID is deleted.
	 *
	 * @since 2.9.0
	 *
	 * @param int $tt_id Term taxonomy ID.
	 */
	do_action( 'delete_term_taxonomy', $tt_id );
	$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $tt_id ) );

	/**
	 * Fires immediately after a term taxonomy ID is deleted.
	 *
	 * @since 2.9.0
	 *
	 * @param int $tt_id Term taxonomy ID.
	 */
	do_action( 'deleted_term_taxonomy', $tt_id );

	// Delete the term if no taxonomies use it.
	if ( !$wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE term_id = %d", $term) ) )
		$wpdb->delete( $wpdb->terms, array( 'term_id' => $term ) );

	clean_term_cache($term, $taxonomy);

	/**
	 * Fires after a term is deleted from the database and the cache is cleaned.
	 *
	 * @since 2.5.0
	 *
	 * @param int     $term         Term ID.
	 * @param int     $tt_id        Term taxonomy ID.
	 * @param string  $taxonomy     Taxonomy slug.
	 * @param mixed   $deleted_term Copy of the already-deleted term, in the form specified
	 *                              by the parent function. WP_Error otherwise.
	 */
	do_action( 'delete_term', $term, $tt_id, $taxonomy, $deleted_term );

	/**
	 * Fires after a term in a specific taxonomy is deleted.
	 *
	 * The dynamic portion of the hook name, `$taxonomy`, refers to the specific
	 * taxonomy the term belonged to.
	 *
	 * @since 2.3.0
	 *
	 * @param int     $term         Term ID.
	 * @param int     $tt_id        Term taxonomy ID.
	 * @param mixed   $deleted_term Copy of the already-deleted term, in the form specified
	 *                              by the parent function. WP_Error otherwise.
	 */
	do_action( "delete_$taxonomy", $term, $tt_id, $deleted_term );

	return true;
}

/**
 * Deletes one existing category.
 *
 * @since 2.0.0
 *
 * @param int $cat_ID
 * @return bool|int|WP_Error Returns true if completes delete action; false if term doesn't exist;
 * 	Zero on attempted deletion of default Category; WP_Error object is also a possibility.
 */
function wp_delete_category( $cat_ID ) {
	return wp_delete_term( $cat_ID, 'category' );
}

/**
 * Retrieves the terms associated with the given object(s), in the supplied taxonomies.
 *
 * @since 2.3.0
 * @since 4.2.0 Added support for 'taxonomy', 'parent', and 'term_taxonomy_id' values of `$orderby`.
 *              Introduced `$parent` argument.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int|array    $object_ids The ID(s) of the object(s) to retrieve.
 * @param string|array $taxonomies The taxonomies to retrieve terms from.
 * @param array|string $args {
 *     Array of arguments.
 *     @type string $orderby Field by which results should be sorted. Accepts 'name', 'count', 'slug', 'term_group',
 *                           'term_order', 'taxonomy', 'parent', or 'term_taxonomy_id'. Default 'name'.
 *     @type string $order   Sort order. Accepts 'ASC' or 'DESC'. Default 'ASC'.
 *     @type string $fields  Fields to return for matched terms. Accepts 'all', 'ids', 'names', and
 *                           'all_with_object_id'. Note that 'all' or 'all_with_object_id' will result in an array of
 *                           term objects being returned, 'ids' will return an array of integers, and 'names' an array
 *                           of strings.
 *     @type int    $parent  Optional. Limit results to the direct children of a given term ID.
 * }
 * @return array|WP_Error The requested term data or empty array if no terms found.
 *                        WP_Error if any of the $taxonomies don't exist.
 */
function wp_get_object_terms($object_ids, $taxonomies, $args = array()) {
	global $wpdb;

	if ( empty( $object_ids ) || empty( $taxonomies ) )
		return array();

	if ( !is_array($taxonomies) )
		$taxonomies = array($taxonomies);

	foreach ( $taxonomies as $taxonomy ) {
		if ( ! taxonomy_exists($taxonomy) )
			return new WP_Error('invalid_taxonomy', __('Invalid taxonomy'));
	}

	if ( !is_array($object_ids) )
		$object_ids = array($object_ids);
	$object_ids = array_map('intval', $object_ids);

	$defaults = array(
		'orderby' => 'name',
		'order'   => 'ASC',
		'fields'  => 'all',
		'parent'  => '',
	);
	$args = wp_parse_args( $args, $defaults );

	$terms = array();
	if ( count($taxonomies) > 1 ) {
		foreach ( $taxonomies as $index => $taxonomy ) {
			$t = get_taxonomy($taxonomy);
			if ( isset($t->args) && is_array($t->args) && $args != array_merge($args, $t->args) ) {
				unset($taxonomies[$index]);
				$terms = array_merge($terms, wp_get_object_terms($object_ids, $taxonomy, array_merge($args, $t->args)));
			}
		}
	} else {
		$t = get_taxonomy($taxonomies[0]);
		if ( isset($t->args) && is_array($t->args) )
			$args = array_merge($args, $t->args);
	}

	$orderby = $args['orderby'];
	$order = $args['order'];
	$fields = $args['fields'];

	if ( in_array( $orderby, array( 'term_id', 'name', 'slug', 'term_group' ) ) ) {
		$orderby = "t.$orderby";
	} elseif ( in_array( $orderby, array( 'count', 'parent', 'taxonomy', 'term_taxonomy_id' ) ) ) {
		$orderby = "tt.$orderby";
	} elseif ( 'term_order' === $orderby ) {
		$orderby = 'tr.term_order';
	} elseif ( 'none' === $orderby ) {
		$orderby = '';
		$order = '';
	} else {
		$orderby = 't.term_id';
	}

	// tt_ids queries can only be none or tr.term_taxonomy_id
	if ( ('tt_ids' == $fields) && !empty($orderby) )
		$orderby = 'tr.term_taxonomy_id';

	if ( !empty($orderby) )
		$orderby = "ORDER BY $orderby";

	$order = strtoupper( $order );
	if ( '' !== $order && ! in_array( $order, array( 'ASC', 'DESC' ) ) )
		$order = 'ASC';

	$taxonomy_array = $taxonomies;
	$object_id_array = $object_ids;
	$taxonomies = "'" . implode("', '", $taxonomies) . "'";
	$object_ids = implode(', ', $object_ids);

	$select_this = '';
	if ( 'all' == $fields ) {
		$select_this = 't.*, tt.*';
	} elseif ( 'ids' == $fields ) {
		$select_this = 't.term_id';
	} elseif ( 'names' == $fields ) {
		$select_this = 't.name';
	} elseif ( 'slugs' == $fields ) {
		$select_this = 't.slug';
	} elseif ( 'all_with_object_id' == $fields ) {
		$select_this = 't.*, tt.*, tr.object_id';
	}

	$where = array(
		"tt.taxonomy IN ($taxonomies)",
		"tr.object_id IN ($object_ids)",
	);

	if ( '' !== $args['parent'] ) {
		$where[] = $wpdb->prepare( 'tt.parent = %d', $args['parent'] );
	}

	$where = implode( ' AND ', $where );

	$query = "SELECT $select_this FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE $where $orderby $order";

	$objects = false;
	if ( 'all' == $fields || 'all_with_object_id' == $fields ) {
		$_terms = $wpdb->get_results( $query );
		foreach ( $_terms as $key => $term ) {
			$_terms[$key] = sanitize_term( $term, $taxonomy, 'raw' );
		}
		$terms = array_merge( $terms, $_terms );
		update_term_cache( $terms );
		$objects = true;
	} elseif ( 'ids' == $fields || 'names' == $fields || 'slugs' == $fields ) {
		$_terms = $wpdb->get_col( $query );
		$_field = ( 'ids' == $fields ) ? 'term_id' : 'name';
		foreach ( $_terms as $key => $term ) {
			$_terms[$key] = sanitize_term_field( $_field, $term, $term, $taxonomy, 'raw' );
		}
		$terms = array_merge( $terms, $_terms );
	} elseif ( 'tt_ids' == $fields ) {
		$terms = $wpdb->get_col("SELECT tr.term_taxonomy_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tr.object_id IN ($object_ids) AND tt.taxonomy IN ($taxonomies) $orderby $order");
		foreach ( $terms as $key => $tt_id ) {
			$terms[$key] = sanitize_term_field( 'term_taxonomy_id', $tt_id, 0, $taxonomy, 'raw' ); // 0 should be the term id, however is not needed when using raw context.
		}
	}

	if ( ! $terms ) {
		$terms = array();
	} elseif ( $objects && 'all_with_object_id' !== $fields ) {
		$_tt_ids = array();
		$_terms = array();
		foreach ( $terms as $term ) {
			if ( in_array( $term->term_taxonomy_id, $_tt_ids ) ) {
				continue;
			}

			$_tt_ids[] = $term->term_taxonomy_id;
			$_terms[] = $term;
		}
		$terms = $_terms;
	} elseif ( ! $objects ) {
		$terms = array_values( array_unique( $terms ) );
	}

	/**
	 * Filter the terms for a given object or objects.
	 *
	 * @since 4.2.0
	 *
	 * @param array $terms           An array of terms for the given object or objects.
	 * @param array $object_id_array Array of object IDs for which `$terms` were retrieved.
	 * @param array $taxonomy_array  Array of taxonomies from which `$terms` were retrieved.
	 * @param array $args            An array of arguments for retrieving terms for the given
	 *                               object(s). See wp_get_object_terms() for details.
	 */
	$terms = apply_filters( 'get_object_terms', $terms, $object_id_array, $taxonomy_array, $args );

	/**
	 * Filter the terms for a given object or objects.
	 *
	 * The `$taxonomies` parameter passed to this filter is formatted as a SQL fragment. The
	 * {@see 'get_object_terms'} filter is recommended as an alternative.
	 *
	 * @since 2.8.0
	 *
	 * @param array     $terms      An array of terms for the given object or objects.
	 * @param int|array $object_ids Object ID or array of IDs.
	 * @param string    $taxonomies SQL-formatted (comma-separated and quoted) list of taxonomy names.
	 * @param array     $args       An array of arguments for retrieving terms for the given object(s).
	 *                              See {@see wp_get_object_terms()} for details.
	 */
	return apply_filters( 'wp_get_object_terms', $terms, $object_ids, $taxonomies, $args );
}

/**
 * Add a new term to the database.
 *
 * A non-existent term is inserted in the following sequence:
 * 1. The term is added to the term table, then related to the taxonomy.
 * 2. If everything is correct, several actions are fired.
 * 3. The 'term_id_filter' is evaluated.
 * 4. The term cache is cleaned.
 * 5. Several more actions are fired.
 * 6. An array is returned containing the term_id and term_taxonomy_id.
 *
 * If the 'slug' argument is not empty, then it is checked to see if the term
 * is invalid. If it is not a valid, existing term, it is added and the term_id
 * is given.
 *
 * If the taxonomy is hierarchical, and the 'parent' argument is not empty,
 * the term is inserted and the term_id will be given.
 *
 * Error handling:
 * If $taxonomy does not exist or $term is empty,
 * a WP_Error object will be returned.
 *
 * If the term already exists on the same hierarchical level,
 * or the term slug and name are not unique, a WP_Error object will be returned.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @since 2.3.0
 *
 * @param string       $term     The term to add or update.
 * @param string       $taxonomy The taxonomy to which to add the term.
 * @param array|string $args {
 *     Optional. Array or string of arguments for inserting a term.
 *
 *     @type string $alias_of    Slug of the term to make this term an alias of.
 *                               Default empty string. Accepts a term slug.
 *     @type string $description The term description. Default empty string.
 *     @type int    $parent      The id of the parent term. Default 0.
 *     @type string $slug        The term slug to use. Default empty string.
 * }
 * @return array|WP_Error An array containing the `term_id` and `term_taxonomy_id`,
 *                        {@see WP_Error} otherwise.
 */
function wp_insert_term( $term, $taxonomy, $args = array() ) {
	global $wpdb;

	if ( ! taxonomy_exists($taxonomy) ) {
		return new WP_Error('invalid_taxonomy', __('Invalid taxonomy'));
	}
	/**
	 * Filter a term before it is sanitized and inserted into the database.
	 *
	 * @since 3.0.0
	 *
	 * @param string $term     The term to add or update.
	 * @param string $taxonomy Taxonomy slug.
	 */
	$term = apply_filters( 'pre_insert_term', $term, $taxonomy );
	if ( is_wp_error( $term ) ) {
		return $term;
	}
	if ( is_int($term) && 0 == $term ) {
		return new WP_Error('invalid_term_id', __('Invalid term ID'));
	}
	if ( '' == trim($term) ) {
		return new WP_Error('empty_term_name', __('A name is required for this term'));
	}
	$defaults = array( 'alias_of' => '', 'description' => '', 'parent' => 0, 'slug' => '');
	$args = wp_parse_args( $args, $defaults );

	if ( $args['parent'] > 0 && ! term_exists( (int) $args['parent'] ) ) {
		return new WP_Error( 'missing_parent', __( 'Parent term does not exist.' ) );
	}
	$args['name'] = $term;
	$args['taxonomy'] = $taxonomy;
	$args = sanitize_term($args, $taxonomy, 'db');

	// expected_slashed ($name)
	$name = wp_unslash( $args['name'] );
	$description = wp_unslash( $args['description'] );
	$parent = (int) $args['parent'];

	$slug_provided = ! empty( $args['slug'] );
	if ( ! $slug_provided ) {
		$slug = sanitize_title( $name );
	} else {
		$slug = $args['slug'];
	}

	$term_group = 0;
	if ( $args['alias_of'] ) {
		$alias = get_term_by( 'slug', $args['alias_of'], $taxonomy );
		if ( ! empty( $alias->term_group ) ) {
			// The alias we want is already in a group, so let's use that one.
			$term_group = $alias->term_group;
		} elseif ( ! empty( $alias->term_id ) ) {
			/*
			 * The alias is not in a group, so we create a new one
			 * and add the alias to it.
			 */
			$term_group = $wpdb->get_var("SELECT MAX(term_group) FROM $wpdb->terms") + 1;

			wp_update_term( $alias->term_id, $taxonomy, array(
				'term_group' => $term_group,
			) );
		}
	}

	/*
	 * Prevent the creation of terms with duplicate names at the same level of a taxonomy hierarchy,
	 * unless a unique slug has been explicitly provided.
	 */
	if ( $name_match = get_term_by( 'name', $name, $taxonomy ) ) {
		$slug_match = get_term_by( 'slug', $slug, $taxonomy );
		if ( ! $slug_provided || $name_match->slug === $slug || $slug_match ) {
			if ( is_taxonomy_hierarchical( $taxonomy ) ) {
				$siblings = get_terms( $taxonomy, array( 'get' => 'all', 'parent' => $parent ) );

				$existing_term = null;
				if ( $name_match->slug === $slug && in_array( $name, wp_list_pluck( $siblings, 'name' ) ) ) {
					$existing_term = $name_match;
				} elseif ( $slug_match && in_array( $slug, wp_list_pluck( $siblings, 'slug' ) ) ) {
					$existing_term = $slug_match;
				}

				if ( $existing_term ) {
					return new WP_Error( 'term_exists', __( 'A term with the name provided already exists with this parent.' ), $existing_term->term_id );
				}
			} else {
				return new WP_Error( 'term_exists', __( 'A term with the name provided already exists in this taxonomy.' ), $name_match->term_id );
			}
		}
	}

	$slug = wp_unique_term_slug( $slug, (object) $args );

	if ( false === $wpdb->insert( $wpdb->terms, compact( 'name', 'slug', 'term_group' ) ) ) {
		return new WP_Error( 'db_insert_error', __( 'Could not insert term into the database' ), $wpdb->last_error );
	}

	$term_id = (int) $wpdb->insert_id;

	// Seems unreachable, However, Is used in the case that a term name is provided, which sanitizes to an empty string.
	if ( empty($slug) ) {
		$slug = sanitize_title($slug, $term_id);

		/** This action is documented in wp-includes/taxonomy-functions.php */
		do_action( 'edit_terms', $term_id, $taxonomy );
		$wpdb->update( $wpdb->terms, compact( 'slug' ), compact( 'term_id' ) );

		/** This action is documented in wp-includes/taxonomy-functions.php */
		do_action( 'edited_terms', $term_id, $taxonomy );
	}

	$tt_id = $wpdb->get_var( $wpdb->prepare( "SELECT tt.term_taxonomy_id FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = %s AND t.term_id = %d", $taxonomy, $term_id ) );

	if ( !empty($tt_id) ) {
		return array('term_id' => $term_id, 'term_taxonomy_id' => $tt_id);
	}
	$wpdb->insert( $wpdb->term_taxonomy, compact( 'term_id', 'taxonomy', 'description', 'parent') + array( 'count' => 0 ) );
	$tt_id = (int) $wpdb->insert_id;

	/*
	 * Sanity check: if we just created a term with the same parent + taxonomy + slug but a higher term_id than
	 * an existing term, then we have unwittingly created a duplicate term. Delete the dupe, and use the term_id
	 * and term_taxonomy_id of the older term instead. Then return out of the function so that the "create" hooks
	 * are not fired.
	 */
	$duplicate_term = $wpdb->get_row( $wpdb->prepare( "SELECT t.term_id, tt.term_taxonomy_id FROM $wpdb->terms t INNER JOIN $wpdb->term_taxonomy tt ON ( tt.term_id = t.term_id ) WHERE t.slug = %s AND tt.parent = %d AND tt.taxonomy = %s AND t.term_id < %d AND tt.term_taxonomy_id != %d", $slug, $parent, $taxonomy, $term_id, $tt_id ) );
	if ( $duplicate_term ) {
		$wpdb->delete( $wpdb->terms, array( 'term_id' => $term_id ) );
		$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $tt_id ) );

		$term_id = (int) $duplicate_term->term_id;
		$tt_id   = (int) $duplicate_term->term_taxonomy_id;

		clean_term_cache( $term_id, $taxonomy );
		return array( 'term_id' => $term_id, 'term_taxonomy_id' => $tt_id );
	}

	/**
	 * Fires immediately after a new term is created, before the term cache is cleaned.
	 *
	 * @since 2.3.0
	 *
	 * @param int    $term_id  Term ID.
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	do_action( "create_term", $term_id, $tt_id, $taxonomy );

	/**
	 * Fires after a new term is created for a specific taxonomy.
	 *
	 * The dynamic portion of the hook name, `$taxonomy`, refers
	 * to the slug of the taxonomy the term was created for.
	 *
	 * @since 2.3.0
	 *
	 * @param int $term_id Term ID.
	 * @param int $tt_id   Term taxonomy ID.
	 */
	do_action( "create_$taxonomy", $term_id, $tt_id );

	/**
	 * Filter the term ID after a new term is created.
	 *
	 * @since 2.3.0
	 *
	 * @param int $term_id Term ID.
	 * @param int $tt_id   Taxonomy term ID.
	 */
	$term_id = apply_filters( 'term_id_filter', $term_id, $tt_id );

	clean_term_cache($term_id, $taxonomy);

	/**
	 * Fires after a new term is created, and after the term cache has been cleaned.
	 *
	 * @since 2.3.0
	 *
	 * @param int    $term_id  Term ID.
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	do_action( 'created_term', $term_id, $tt_id, $taxonomy );

	/**
	 * Fires after a new term in a specific taxonomy is created, and after the term
	 * cache has been cleaned.
	 *
	 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
	 *
	 * @since 2.3.0
	 *
	 * @param int $term_id Term ID.
	 * @param int $tt_id   Term taxonomy ID.
	 */
	do_action( "created_$taxonomy", $term_id, $tt_id );

	return array('term_id' => $term_id, 'term_taxonomy_id' => $tt_id);
}

/**
 * Create Term and Taxonomy Relationships.
 *
 * Relates an object (post, link etc) to a term and taxonomy type. Creates the
 * term and taxonomy relationship if it doesn't already exist. Creates a term if
 * it doesn't exist (using the slug).
 *
 * A relationship means that the term is grouped in or belongs to the taxonomy.
 * A term has no meaning until it is given context by defining which taxonomy it
 * exists under.
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb The WordPress database abstraction object.
 *
 * @param int              $object_id The object to relate to.
 * @param array|int|string $terms     A single term slug, single term id, or array of either term slugs or ids.
 *                                    Will replace all existing related terms in this taxonomy.
 * @param string           $taxonomy  The context in which to relate the term to the object.
 * @param bool             $append    Optional. If false will delete difference of terms. Default false.
 * @return array|WP_Error Affected Term IDs.
 */
function wp_set_object_terms( $object_id, $terms, $taxonomy, $append = false ) {
	global $wpdb;

	$object_id = (int) $object_id;

	if ( ! taxonomy_exists($taxonomy) )
		return new WP_Error('invalid_taxonomy', __('Invalid taxonomy'));

	if ( !is_array($terms) )
		$terms = array($terms);

	if ( ! $append )
		$old_tt_ids =  wp_get_object_terms($object_id, $taxonomy, array('fields' => 'tt_ids', 'orderby' => 'none'));
	else
		$old_tt_ids = array();

	$tt_ids = array();
	$term_ids = array();
	$new_tt_ids = array();

	foreach ( (array) $terms as $term) {
		if ( !strlen(trim($term)) )
			continue;

		if ( !$term_info = term_exists($term, $taxonomy) ) {
			// Skip if a non-existent term ID is passed.
			if ( is_int($term) )
				continue;
			$term_info = wp_insert_term($term, $taxonomy);
		}
		if ( is_wp_error($term_info) )
			return $term_info;
		$term_ids[] = $term_info['term_id'];
		$tt_id = $term_info['term_taxonomy_id'];
		$tt_ids[] = $tt_id;

		if ( $wpdb->get_var( $wpdb->prepare( "SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = %d AND term_taxonomy_id = %d", $object_id, $tt_id ) ) )
			continue;

		/**
		 * Fires immediately before an object-term relationship is added.
		 *
		 * @since 2.9.0
		 *
		 * @param int $object_id Object ID.
		 * @param int $tt_id     Term taxonomy ID.
		 */
		do_action( 'add_term_relationship', $object_id, $tt_id );
		$wpdb->insert( $wpdb->term_relationships, array( 'object_id' => $object_id, 'term_taxonomy_id' => $tt_id ) );

		/**
		 * Fires immediately after an object-term relationship is added.
		 *
		 * @since 2.9.0
		 *
		 * @param int $object_id Object ID.
		 * @param int $tt_id     Term taxonomy ID.
		 */
		do_action( 'added_term_relationship', $object_id, $tt_id );
		$new_tt_ids[] = $tt_id;
	}

	if ( $new_tt_ids )
		wp_update_term_count( $new_tt_ids, $taxonomy );

	if ( ! $append ) {
		$delete_tt_ids = array_diff( $old_tt_ids, $tt_ids );

		if ( $delete_tt_ids ) {
			$in_delete_tt_ids = "'" . implode( "', '", $delete_tt_ids ) . "'";
			$delete_term_ids = $wpdb->get_col( $wpdb->prepare( "SELECT tt.term_id FROM $wpdb->term_taxonomy AS tt WHERE tt.taxonomy = %s AND tt.term_taxonomy_id IN ($in_delete_tt_ids)", $taxonomy ) );
			$delete_term_ids = array_map( 'intval', $delete_term_ids );

			$remove = wp_remove_object_terms( $object_id, $delete_term_ids, $taxonomy );
			if ( is_wp_error( $remove ) ) {
				return $remove;
			}
		}
	}

	$t = get_taxonomy($taxonomy);
	if ( ! $append && isset($t->sort) && $t->sort ) {
		$values = array();
		$term_order = 0;
		$final_tt_ids = wp_get_object_terms($object_id, $taxonomy, array('fields' => 'tt_ids'));
		foreach ( $tt_ids as $tt_id )
			if ( in_array($tt_id, $final_tt_ids) )
				$values[] = $wpdb->prepare( "(%d, %d, %d)", $object_id, $tt_id, ++$term_order);
		if ( $values )
			if ( false === $wpdb->query( "INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id, term_order) VALUES " . join( ',', $values ) . " ON DUPLICATE KEY UPDATE term_order = VALUES(term_order)" ) )
				return new WP_Error( 'db_insert_error', __( 'Could not insert term relationship into the database' ), $wpdb->last_error );
	}

	wp_cache_delete( $object_id, $taxonomy . '_relationships' );

	/**
	 * Fires after an object's terms have been set.
	 *
	 * @since 2.8.0
	 *
	 * @param int    $object_id  Object ID.
	 * @param array  $terms      An array of object terms.
	 * @param array  $tt_ids     An array of term taxonomy IDs.
	 * @param string $taxonomy   Taxonomy slug.
	 * @param bool   $append     Whether to append new terms to the old terms.
	 * @param array  $old_tt_ids Old array of term taxonomy IDs.
	 */
	do_action( 'set_object_terms', $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids );
	return $tt_ids;
}

/**
 * Add term(s) associated with a given object.
 *
 * @since 3.6.0
 *
 * @param int              $object_id The ID of the object to which the terms will be added.
 * @param array|int|string $terms     The slug(s) or ID(s) of the term(s) to add.
 * @param array|string     $taxonomy  Taxonomy name.
 * @return array|WP_Error Affected Term IDs
 */
function wp_add_object_terms( $object_id, $terms, $taxonomy ) {
	return wp_set_object_terms( $object_id, $terms, $taxonomy, true );
}

/**
 * Remove term(s) associated with a given object.
 *
 * @since 3.6.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int              $object_id The ID of the object from which the terms will be removed.
 * @param array|int|string $terms     The slug(s) or ID(s) of the term(s) to remove.
 * @param array|string     $taxonomy  Taxonomy name.
 * @return bool|WP_Error True on success, false or WP_Error on failure.
 */
function wp_remove_object_terms( $object_id, $terms, $taxonomy ) {
	global $wpdb;

	$object_id = (int) $object_id;

	if ( ! taxonomy_exists( $taxonomy ) ) {
		return new WP_Error( 'invalid_taxonomy', __( 'Invalid Taxonomy' ) );
	}

	if ( ! is_array( $terms ) ) {
		$terms = array( $terms );
	}

	$tt_ids = array();

	foreach ( (array) $terms as $term ) {
		if ( ! strlen( trim( $term ) ) ) {
			continue;
		}

		if ( ! $term_info = term_exists( $term, $taxonomy ) ) {
			// Skip if a non-existent term ID is passed.
			if ( is_int( $term ) ) {
				continue;
			}
		}

		if ( is_wp_error( $term_info ) ) {
			return $term_info;
		}

		$tt_ids[] = $term_info['term_taxonomy_id'];
	}

	if ( $tt_ids ) {
		$in_tt_ids = "'" . implode( "', '", $tt_ids ) . "'";

		/**
		 * Fires immediately before an object-term relationship is deleted.
		 *
		 * @since 2.9.0
		 *
		 * @param int   $object_id Object ID.
		 * @param array $tt_ids    An array of term taxonomy IDs.
		 */
		do_action( 'delete_term_relationships', $object_id, $tt_ids );
		$deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->term_relationships WHERE object_id = %d AND term_taxonomy_id IN ($in_tt_ids)", $object_id ) );

		/**
		 * Fires immediately after an object-term relationship is deleted.
		 *
		 * @since 2.9.0
		 *
		 * @param int   $object_id Object ID.
		 * @param array $tt_ids    An array of term taxonomy IDs.
		 */
		do_action( 'deleted_term_relationships', $object_id, $tt_ids );

		wp_update_term_count( $tt_ids, $taxonomy );

		return (bool) $deleted;
	}

	return false;
}

/**
 * Will make slug unique, if it isn't already.
 *
 * The `$slug` has to be unique global to every taxonomy, meaning that one
 * taxonomy term can't have a matching slug with another taxonomy term. Each
 * slug has to be globally unique for every taxonomy.
 *
 * The way this works is that if the taxonomy that the term belongs to is
 * hierarchical and has a parent, it will append that parent to the $slug.
 *
 * If that still doesn't return an unique slug, then it try to append a number
 * until it finds a number that is truly unique.
 *
 * The only purpose for `$term` is for appending a parent, if one exists.
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $slug The string that will be tried for a unique slug.
 * @param object $term The term object that the `$slug` will belong to.
 * @return string Will return a true unique slug.
 */
function wp_unique_term_slug( $slug, $term ) {
	global $wpdb;

	$needs_suffix = true;
	$original_slug = $slug;

	// As of 4.1, duplicate slugs are allowed as long as they're in different taxonomies.
	if ( ! term_exists( $slug ) || get_option( 'db_version' ) >= 30133 && ! get_term_by( 'slug', $slug, $term->taxonomy ) ) {
		$needs_suffix = false;
	}

	/*
	 * If the taxonomy supports hierarchy and the term has a parent, make the slug unique
	 * by incorporating parent slugs.
	 */
	$parent_suffix = '';
	if ( $needs_suffix && is_taxonomy_hierarchical( $term->taxonomy ) && ! empty( $term->parent ) ) {
		$the_parent = $term->parent;
		while ( ! empty($the_parent) ) {
			$parent_term = get_term($the_parent, $term->taxonomy);
			if ( is_wp_error($parent_term) || empty($parent_term) )
				break;
			$parent_suffix .= '-' . $parent_term->slug;
			if ( ! term_exists( $slug . $parent_suffix ) ) {
				break;
			}

			if ( empty($parent_term->parent) )
				break;
			$the_parent = $parent_term->parent;
		}
	}

	// If we didn't get a unique slug, try appending a number to make it unique.

	/**
	 * Filter whether the proposed unique term slug is bad.
	 *
	 * @since 4.3.0
	 *
	 * @param bool   $needs_suffix Whether the slug needs to be made unique with a suffix.
	 * @param string $slug         The slug.
	 * @param object $term         Term object.
	 */
	if ( apply_filters( 'wp_unique_term_slug_is_bad_slug', $needs_suffix, $slug, $term ) ) {
		if ( $parent_suffix ) {
			$slug .= $parent_suffix;
		} else {
			if ( ! empty( $term->term_id ) )
				$query = $wpdb->prepare( "SELECT slug FROM $wpdb->terms WHERE slug = %s AND term_id != %d", $slug, $term->term_id );
			else
				$query = $wpdb->prepare( "SELECT slug FROM $wpdb->terms WHERE slug = %s", $slug );

			if ( $wpdb->get_var( $query ) ) {
				$num = 2;
				do {
					$alt_slug = $slug . "-$num";
					$num++;
					$slug_check = $wpdb->get_var( $wpdb->prepare( "SELECT slug FROM $wpdb->terms WHERE slug = %s", $alt_slug ) );
				} while ( $slug_check );
				$slug = $alt_slug;
			}
		}
	}

	/**
	 * Filter the unique term slug.
	 *
	 * @since 4.3.0
	 *
	 * @param string $slug          Unique term slug.
	 * @param object $term          Term object.
	 * @param string $original_slug Slug originally passed to the function for testing.
	 */
	return apply_filters( 'wp_unique_term_slug', $slug, $term, $original_slug );
}

/**
 * Update term based on arguments provided.
 *
 * The $args will indiscriminately override all values with the same field name.
 * Care must be taken to not override important information need to update or
 * update will fail (or perhaps create a new term, neither would be acceptable).
 *
 * Defaults will set 'alias_of', 'description', 'parent', and 'slug' if not
 * defined in $args already.
 *
 * 'alias_of' will create a term group, if it doesn't already exist, and update
 * it for the $term.
 *
 * If the 'slug' argument in $args is missing, then the 'name' in $args will be
 * used. It should also be noted that if you set 'slug' and it isn't unique then
 * a WP_Error will be passed back. If you don't pass any slug, then a unique one
 * will be created for you.
 *
 * For what can be overrode in `$args`, check the term scheme can contain and stay
 * away from the term keys.
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int          $term_id  The ID of the term
 * @param string       $taxonomy The context in which to relate the term to the object.
 * @param array|string $args     Optional. Array of get_terms() arguments. Default empty array.
 * @return array|WP_Error Returns Term ID and Taxonomy Term ID
 */
function wp_update_term( $term_id, $taxonomy, $args = array() ) {
	global $wpdb;

	if ( ! taxonomy_exists( $taxonomy ) ) {
		return new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy' ) );
	}

	$term_id = (int) $term_id;

	// First, get all of the original args
	$term = get_term( $term_id, $taxonomy, ARRAY_A );

	if ( is_wp_error( $term ) ) {
		return $term;
	}

	if ( ! $term ) {
		return new WP_Error( 'invalid_term', __( 'Empty Term' ) );
	}

	// Escape data pulled from DB.
	$term = wp_slash($term);

	// Merge old and new args with new args overwriting old ones.
	$args = array_merge($term, $args);

	$defaults = array( 'alias_of' => '', 'description' => '', 'parent' => 0, 'slug' => '');
	$args = wp_parse_args($args, $defaults);
	$args = sanitize_term($args, $taxonomy, 'db');
	$parsed_args = $args;

	// expected_slashed ($name)
	$name = wp_unslash( $args['name'] );
	$description = wp_unslash( $args['description'] );

	$parsed_args['name'] = $name;
	$parsed_args['description'] = $description;

	if ( '' == trim($name) )
		return new WP_Error('empty_term_name', __('A name is required for this term'));

	if ( $parsed_args['parent'] > 0 && ! term_exists( (int) $parsed_args['parent'] ) ) {
		return new WP_Error( 'missing_parent', __( 'Parent term does not exist.' ) );
	}

	$empty_slug = false;
	if ( empty( $args['slug'] ) ) {
		$empty_slug = true;
		$slug = sanitize_title($name);
	} else {
		$slug = $args['slug'];
	}

	$parsed_args['slug'] = $slug;

	$term_group = isset( $parsed_args['term_group'] ) ? $parsed_args['term_group'] : 0;
	if ( $args['alias_of'] ) {
		$alias = get_term_by( 'slug', $args['alias_of'], $taxonomy );
		if ( ! empty( $alias->term_group ) ) {
			// The alias we want is already in a group, so let's use that one.
			$term_group = $alias->term_group;
		} elseif ( ! empty( $alias->term_id ) ) {
			/*
			 * The alias is not in a group, so we create a new one
			 * and add the alias to it.
			 */
			$term_group = $wpdb->get_var("SELECT MAX(term_group) FROM $wpdb->terms") + 1;

			wp_update_term( $alias->term_id, $taxonomy, array(
				'term_group' => $term_group,
			) );
		}

		$parsed_args['term_group'] = $term_group;
	}

	/**
	 * Filter the term parent.
	 *
	 * Hook to this filter to see if it will cause a hierarchy loop.
	 *
	 * @since 3.1.0
	 *
	 * @param int    $parent      ID of the parent term.
	 * @param int    $term_id     Term ID.
	 * @param string $taxonomy    Taxonomy slug.
	 * @param array  $parsed_args An array of potentially altered update arguments for the given term.
	 * @param array  $args        An array of update arguments for the given term.
	 */
	$parent = apply_filters( 'wp_update_term_parent', $args['parent'], $term_id, $taxonomy, $parsed_args, $args );

	// Check for duplicate slug
	$duplicate = get_term_by( 'slug', $slug, $taxonomy );
	if ( $duplicate && $duplicate->term_id != $term_id ) {
		// If an empty slug was passed or the parent changed, reset the slug to something unique.
		// Otherwise, bail.
		if ( $empty_slug || ( $parent != $term['parent']) )
			$slug = wp_unique_term_slug($slug, (object) $args);
		else
			return new WP_Error('duplicate_term_slug', sprintf(__('The slug &#8220;%s&#8221; is already in use by another term'), $slug));
	}

	$tt_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT tt.term_taxonomy_id FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = %s AND t.term_id = %d", $taxonomy, $term_id) );

	// Check whether this is a shared term that needs splitting.
	$_term_id = _split_shared_term( $term_id, $tt_id );
	if ( ! is_wp_error( $_term_id ) ) {
		$term_id = $_term_id;
	}

	/**
	 * Fires immediately before the given terms are edited.
	 *
	 * @since 2.9.0
	 *
	 * @param int    $term_id  Term ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	do_action( 'edit_terms', $term_id, $taxonomy );
	$wpdb->update($wpdb->terms, compact( 'name', 'slug', 'term_group' ), compact( 'term_id' ) );
	if ( empty($slug) ) {
		$slug = sanitize_title($name, $term_id);
		$wpdb->update( $wpdb->terms, compact( 'slug' ), compact( 'term_id' ) );
	}

	/**
	 * Fires immediately after the given terms are edited.
	 *
	 * @since 2.9.0
	 *
	 * @param int    $term_id  Term ID
	 * @param string $taxonomy Taxonomy slug.
	 */
	do_action( 'edited_terms', $term_id, $taxonomy );

	/**
	 * Fires immediate before a term-taxonomy relationship is updated.
	 *
	 * @since 2.9.0
	 *
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	do_action( 'edit_term_taxonomy', $tt_id, $taxonomy );

	$wpdb->update( $wpdb->term_taxonomy, compact( 'term_id', 'taxonomy', 'description', 'parent' ), array( 'term_taxonomy_id' => $tt_id ) );

	/**
	 * Fires immediately after a term-taxonomy relationship is updated.
	 *
	 * @since 2.9.0
	 *
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	do_action( 'edited_term_taxonomy', $tt_id, $taxonomy );

	// Clean the relationship caches for all object types using this term.
	$objects = $wpdb->get_col( $wpdb->prepare( "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $tt_id ) );
	$tax_object = get_taxonomy( $taxonomy );
	foreach ( $tax_object->object_type as $object_type ) {
		clean_object_term_cache( $objects, $object_type );
	}

	/**
	 * Fires after a term has been updated, but before the term cache has been cleaned.
	 *
	 * @since 2.3.0
	 *
	 * @param int    $term_id  Term ID.
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	do_action( "edit_term", $term_id, $tt_id, $taxonomy );

	/**
	 * Fires after a term in a specific taxonomy has been updated, but before the term
	 * cache has been cleaned.
	 *
	 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
	 *
	 * @since 2.3.0
	 *
	 * @param int $term_id Term ID.
	 * @param int $tt_id   Term taxonomy ID.
	 */
	do_action( "edit_$taxonomy", $term_id, $tt_id );

	/** This filter is documented in wp-includes/taxonomy-functions.php */
	$term_id = apply_filters( 'term_id_filter', $term_id, $tt_id );

	clean_term_cache($term_id, $taxonomy);

	/**
	 * Fires after a term has been updated, and the term cache has been cleaned.
	 *
	 * @since 2.3.0
	 *
	 * @param int    $term_id  Term ID.
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	do_action( "edited_term", $term_id, $tt_id, $taxonomy );

	/**
	 * Fires after a term for a specific taxonomy has been updated, and the term
	 * cache has been cleaned.
	 *
	 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
	 *
	 * @since 2.3.0
	 *
	 * @param int $term_id Term ID.
	 * @param int $tt_id   Term taxonomy ID.
	 */
	do_action( "edited_$taxonomy", $term_id, $tt_id );

	return array('term_id' => $term_id, 'term_taxonomy_id' => $tt_id);
}

/**
 * Enable or disable term counting.
 *
 * @since 2.5.0
 *
 * @staticvar bool $_defer
 *
 * @param bool $defer Optional. Enable if true, disable if false.
 * @return bool Whether term counting is enabled or disabled.
 */
function wp_defer_term_counting($defer=null) {
	static $_defer = false;

	if ( is_bool($defer) ) {
		$_defer = $defer;
		// flush any deferred counts
		if ( !$defer )
			wp_update_term_count( null, null, true );
	}

	return $_defer;
}

/**
 * Updates the amount of terms in taxonomy.
 *
 * If there is a taxonomy callback applied, then it will be called for updating
 * the count.
 *
 * The default action is to count what the amount of terms have the relationship
 * of term ID. Once that is done, then update the database.
 *
 * @since 2.3.0
 *
 * @staticvar array $_deferred
 *
 * @param int|array $terms    The term_taxonomy_id of the terms.
 * @param string    $taxonomy The context of the term.
 * @return bool If no terms will return false, and if successful will return true.
 */
function wp_update_term_count( $terms, $taxonomy, $do_deferred=false ) {
	static $_deferred = array();

	if ( $do_deferred ) {
		foreach ( (array) array_keys($_deferred) as $tax ) {
			wp_update_term_count_now( $_deferred[$tax], $tax );
			unset( $_deferred[$tax] );
		}
	}

	if ( empty($terms) )
		return false;

	if ( !is_array($terms) )
		$terms = array($terms);

	if ( wp_defer_term_counting() ) {
		if ( !isset($_deferred[$taxonomy]) )
			$_deferred[$taxonomy] = array();
		$_deferred[$taxonomy] = array_unique( array_merge($_deferred[$taxonomy], $terms) );
		return true;
	}

	return wp_update_term_count_now( $terms, $taxonomy );
}

/**
 * Perform term count update immediately.
 *
 * @since 2.5.0
 *
 * @param array  $terms    The term_taxonomy_id of terms to update.
 * @param string $taxonomy The context of the term.
 * @return true Always true when complete.
 */
function wp_update_term_count_now( $terms, $taxonomy ) {
	$terms = array_map('intval', $terms);

	$taxonomy = get_taxonomy($taxonomy);
	if ( !empty($taxonomy->update_count_callback) ) {
		call_user_func($taxonomy->update_count_callback, $terms, $taxonomy);
	} else {
		$object_types = (array) $taxonomy->object_type;
		foreach ( $object_types as &$object_type ) {
			if ( 0 === strpos( $object_type, 'attachment:' ) )
				list( $object_type ) = explode( ':', $object_type );
		}

		if ( $object_types == array_filter( $object_types, 'post_type_exists' ) ) {
			// Only post types are attached to this taxonomy
			_update_post_term_count( $terms, $taxonomy );
		} else {
			// Default count updater
			_update_generic_term_count( $terms, $taxonomy );
		}
	}

	clean_term_cache($terms, '', false);

	return true;
}

//
// Cache
//

/**
 * Removes the taxonomy relationship to terms from the cache.
 *
 * Will remove the entire taxonomy relationship containing term `$object_id`. The
 * term IDs have to exist within the taxonomy `$object_type` for the deletion to
 * take place.
 *
 * @since 2.3.0
 *
 * @see get_object_taxonomies() for more on $object_type.
 *
 * @param int|array    $object_ids  Single or list of term object ID(s).
 * @param array|string $object_type The taxonomy object type.
 */
function clean_object_term_cache($object_ids, $object_type) {
	if ( !is_array($object_ids) )
		$object_ids = array($object_ids);

	$taxonomies = get_object_taxonomies( $object_type );

	foreach ( $object_ids as $id ) {
		foreach ( $taxonomies as $taxonomy ) {
			wp_cache_delete($id, "{$taxonomy}_relationships");
		}
	}

	/**
	 * Fires after the object term cache has been cleaned.
	 *
	 * @since 2.5.0
	 *
	 * @param array  $object_ids An array of object IDs.
	 * @param string $objet_type Object type.
	 */
	do_action( 'clean_object_term_cache', $object_ids, $object_type );
}

/**
 * Will remove all of the term ids from the cache.
 *
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @global bool $_wp_suspend_cache_invalidation
 *
 * @param int|array $ids            Single or list of Term IDs.
 * @param string    $taxonomy       Optional. Can be empty and will assume `tt_ids`, else will use for context.
 *                                  Default empty.
 * @param bool      $clean_taxonomy Optional. Whether to clean taxonomy wide caches (true), or just individual
 *                                  term object caches (false). Default true.
 */
function clean_term_cache($ids, $taxonomy = '', $clean_taxonomy = true) {
	global $wpdb, $_wp_suspend_cache_invalidation;

	if ( ! empty( $_wp_suspend_cache_invalidation ) ) {
		return;
	}

	if ( !is_array($ids) )
		$ids = array($ids);

	$taxonomies = array();
	// If no taxonomy, assume tt_ids.
	if ( empty($taxonomy) ) {
		$tt_ids = array_map('intval', $ids);
		$tt_ids = implode(', ', $tt_ids);
		$terms = $wpdb->get_results("SELECT term_id, taxonomy FROM $wpdb->term_taxonomy WHERE term_taxonomy_id IN ($tt_ids)");
		$ids = array();
		foreach ( (array) $terms as $term ) {
			$taxonomies[] = $term->taxonomy;
			$ids[] = $term->term_id;
			wp_cache_delete($term->term_id, $term->taxonomy);
		}
		$taxonomies = array_unique($taxonomies);
	} else {
		$taxonomies = array($taxonomy);
		foreach ( $taxonomies as $taxonomy ) {
			foreach ( $ids as $id ) {
				wp_cache_delete($id, $taxonomy);
			}
		}
	}

	foreach ( $taxonomies as $taxonomy ) {
		if ( $clean_taxonomy ) {
			wp_cache_delete('all_ids', $taxonomy);
			wp_cache_delete('get', $taxonomy);
			delete_option("{$taxonomy}_children");
			// Regenerate {$taxonomy}_children
			_get_term_hierarchy($taxonomy);
		}

		/**
		 * Fires once after each taxonomy's term cache has been cleaned.
		 *
		 * @since 2.5.0
		 *
		 * @param array  $ids      An array of term IDs.
		 * @param string $taxonomy Taxonomy slug.
		 */
		do_action( 'clean_term_cache', $ids, $taxonomy );
	}

	wp_cache_set( 'last_changed', microtime(), 'terms' );
}

/**
 * Retrieves the taxonomy relationship to the term object id.
 *
 * @since 2.3.0
 *
 * @param int    $id       Term object ID.
 * @param string $taxonomy Taxonomy name.
 * @return bool|mixed Empty array if $terms found, but not `$taxonomy`. False if nothing is in cache
 *                    for `$taxonomy` and `$id`.
 */
function get_object_term_cache( $id, $taxonomy ) {
	return wp_cache_get( $id, "{$taxonomy}_relationships" );
}

/**
 * Updates the cache for the given term object ID(s).
 *
 * Note: Due to performance concerns, great care should be taken to only update
 * term caches when necessary. Processing time can increase exponentially depending
 * on both the number of passed term IDs and the number of taxonomies those terms
 * belong to.
 *
 * Caches will only be updated for terms not already cached.
 *
 * @since 2.3.0
 *
 * @param string|array $object_ids  Comma-separated list or array of term object IDs.
 * @param array|string $object_type The taxonomy object type.
 * @return void|false False if all of the terms in `$object_ids` are already cached.
 */
function update_object_term_cache($object_ids, $object_type) {
	if ( empty($object_ids) )
		return;

	if ( !is_array($object_ids) )
		$object_ids = explode(',', $object_ids);

	$object_ids = array_map('intval', $object_ids);

	$taxonomies = get_object_taxonomies($object_type);

	$ids = array();
	foreach ( (array) $object_ids as $id ) {
		foreach ( $taxonomies as $taxonomy ) {
			if ( false === wp_cache_get($id, "{$taxonomy}_relationships") ) {
				$ids[] = $id;
				break;
			}
		}
	}

	if ( empty( $ids ) )
		return false;

	$terms = wp_get_object_terms( $ids, $taxonomies, array(
		'fields' => 'all_with_object_id',
		'orderby' => 'none',
	) );

	$object_terms = array();
	foreach ( (array) $terms as $term )
		$object_terms[$term->object_id][$term->taxonomy][] = $term;

	foreach ( $ids as $id ) {
		foreach ( $taxonomies as $taxonomy ) {
			if ( ! isset($object_terms[$id][$taxonomy]) ) {
				if ( !isset($object_terms[$id]) )
					$object_terms[$id] = array();
				$object_terms[$id][$taxonomy] = array();
			}
		}
	}

	foreach ( $object_terms as $id => $value ) {
		foreach ( $value as $taxonomy => $terms ) {
			wp_cache_add( $id, $terms, "{$taxonomy}_relationships" );
		}
	}
}

/**
 * Updates Terms to Taxonomy in cache.
 *
 * @since 2.3.0
 *
 * @param array  $terms    List of term objects to change.
 * @param string $taxonomy Optional. Update Term to this taxonomy in cache. Default empty.
 */
function update_term_cache( $terms, $taxonomy = '' ) {
	foreach ( (array) $terms as $term ) {
		$term_taxonomy = $taxonomy;
		if ( empty($term_taxonomy) )
			$term_taxonomy = $term->taxonomy;

		wp_cache_add( $term->term_id, $term, $term_taxonomy );
	}
}

//
// Private
//

/**
 * Retrieves children of taxonomy as Term IDs.
 *
 * @ignore
 * @since 2.3.0
 *
 * @param string $taxonomy Taxonomy name.
 * @return array Empty if $taxonomy isn't hierarchical or returns children as Term IDs.
 */
function _get_term_hierarchy( $taxonomy ) {
	if ( !is_taxonomy_hierarchical($taxonomy) )
		return array();
	$children = get_option("{$taxonomy}_children");

	if ( is_array($children) )
		return $children;
	$children = array();
	$terms = get_terms($taxonomy, array('get' => 'all', 'orderby' => 'id', 'fields' => 'id=>parent'));
	foreach ( $terms as $term_id => $parent ) {
		if ( $parent > 0 )
			$children[$parent][] = $term_id;
	}
	update_option("{$taxonomy}_children", $children);

	return $children;
}

/**
 * Get the subset of $terms that are descendants of $term_id.
 *
 * If `$terms` is an array of objects, then _get_term_children() returns an array of objects.
 * If `$terms` is an array of IDs, then _get_term_children() returns an array of IDs.
 *
 * @access private
 * @since 2.3.0
 *
 * @param int    $term_id   The ancestor term: all returned terms should be descendants of `$term_id`.
 * @param array  $terms     The set of terms - either an array of term objects or term IDs - from which those that
 *                          are descendants of $term_id will be chosen.
 * @param string $taxonomy  The taxonomy which determines the hierarchy of the terms.
 * @param array  $ancestors Optional. Term ancestors that have already been identified. Passed by reference, to keep
 *                          track of found terms when recursing the hierarchy. The array of located ancestors is used
 *                          to prevent infinite recursion loops. For performance, `term_ids` are used as array keys,
 *                          with 1 as value. Default empty array.
 * @return array|WP_Error The subset of $terms that are descendants of $term_id.
 */
function _get_term_children( $term_id, $terms, $taxonomy, &$ancestors = array() ) {
	$empty_array = array();
	if ( empty($terms) )
		return $empty_array;

	$term_list = array();
	$has_children = _get_term_hierarchy($taxonomy);

	if  ( ( 0 != $term_id ) && ! isset($has_children[$term_id]) )
		return $empty_array;

	// Include the term itself in the ancestors array, so we can properly detect when a loop has occurred.
	if ( empty( $ancestors ) ) {
		$ancestors[ $term_id ] = 1;
	}

	foreach ( (array) $terms as $term ) {
		$use_id = false;
		if ( !is_object($term) ) {
			$term = get_term($term, $taxonomy);
			if ( is_wp_error( $term ) )
				return $term;
			$use_id = true;
		}

		// Don't recurse if we've already identified the term as a child - this indicates a loop.
		if ( isset( $ancestors[ $term->term_id ] ) ) {
			continue;
		}

		if ( $term->parent == $term_id ) {
			if ( $use_id )
				$term_list[] = $term->term_id;
			else
				$term_list[] = $term;

			if ( !isset($has_children[$term->term_id]) )
				continue;

			$ancestors[ $term->term_id ] = 1;

			if ( $children = _get_term_children( $term->term_id, $terms, $taxonomy, $ancestors) )
				$term_list = array_merge($term_list, $children);
		}
	}

	return $term_list;
}

/**
 * Add count of children to parent count.
 *
 * Recalculates term counts by including items from child terms. Assumes all
 * relevant children are already in the $terms argument.
 *
 * @access private
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array  $terms    List of term IDs, passed by reference.
 * @param string $taxonomy Term context.
 */
function _pad_term_counts( &$terms, $taxonomy ) {
	global $wpdb;

	// This function only works for hierarchical taxonomies like post categories.
	if ( !is_taxonomy_hierarchical( $taxonomy ) )
		return;

	$term_hier = _get_term_hierarchy($taxonomy);

	if ( empty($term_hier) )
		return;

	$term_items = array();
	$terms_by_id = array();
	$term_ids = array();

	foreach ( (array) $terms as $key => $term ) {
		$terms_by_id[$term->term_id] = & $terms[$key];
		$term_ids[$term->term_taxonomy_id] = $term->term_id;
	}

	// Get the object and term ids and stick them in a lookup table.
	$tax_obj = get_taxonomy($taxonomy);
	$object_types = esc_sql($tax_obj->object_type);
	$results = $wpdb->get_results("SELECT object_id, term_taxonomy_id FROM $wpdb->term_relationships INNER JOIN $wpdb->posts ON object_id = ID WHERE term_taxonomy_id IN (" . implode(',', array_keys($term_ids)) . ") AND post_type IN ('" . implode("', '", $object_types) . "') AND post_status = 'publish'");
	foreach ( $results as $row ) {
		$id = $term_ids[$row->term_taxonomy_id];
		$term_items[$id][$row->object_id] = isset($term_items[$id][$row->object_id]) ? ++$term_items[$id][$row->object_id] : 1;
	}

	// Touch every ancestor's lookup row for each post in each term.
	foreach ( $term_ids as $term_id ) {
		$child = $term_id;
		$ancestors = array();
		while ( !empty( $terms_by_id[$child] ) && $parent = $terms_by_id[$child]->parent ) {
			$ancestors[] = $child;
			if ( !empty( $term_items[$term_id] ) )
				foreach ( $term_items[$term_id] as $item_id => $touches ) {
					$term_items[$parent][$item_id] = isset($term_items[$parent][$item_id]) ? ++$term_items[$parent][$item_id]: 1;
				}
			$child = $parent;

			if ( in_array( $parent, $ancestors ) ) {
				break;
			}
		}
	}

	// Transfer the touched cells.
	foreach ( (array) $term_items as $id => $items )
		if ( isset($terms_by_id[$id]) )
			$terms_by_id[$id]->count = count($items);
}

//
// Default callbacks
//

/**
 * Will update term count based on object types of the current taxonomy.
 *
 * Private function for the default callback for post_tag and category
 * taxonomies.
 *
 * @access private
 * @since 2.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array  $terms    List of Term taxonomy IDs.
 * @param object $taxonomy Current taxonomy object of terms.
 */
function _update_post_term_count( $terms, $taxonomy ) {
	global $wpdb;

	$object_types = (array) $taxonomy->object_type;

	foreach ( $object_types as &$object_type )
		list( $object_type ) = explode( ':', $object_type );

	$object_types = array_unique( $object_types );

	if ( false !== ( $check_attachments = array_search( 'attachment', $object_types ) ) ) {
		unset( $object_types[ $check_attachments ] );
		$check_attachments = true;
	}

	if ( $object_types )
		$object_types = esc_sql( array_filter( $object_types, 'post_type_exists' ) );

	foreach ( (array) $terms as $term ) {
		$count = 0;

		// Attachments can be 'inherit' status, we need to base count off the parent's status if so.
		if ( $check_attachments )
			$count += (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts p1 WHERE p1.ID = $wpdb->term_relationships.object_id AND ( post_status = 'publish' OR ( post_status = 'inherit' AND post_parent > 0 AND ( SELECT post_status FROM $wpdb->posts WHERE ID = p1.post_parent ) = 'publish' ) ) AND post_type = 'attachment' AND term_taxonomy_id = %d", $term ) );

		if ( $object_types )
			$count += (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts WHERE $wpdb->posts.ID = $wpdb->term_relationships.object_id AND post_status = 'publish' AND post_type IN ('" . implode("', '", $object_types ) . "') AND term_taxonomy_id = %d", $term ) );

		/** This action is documented in wp-includes/taxonomy-functions.php */
		do_action( 'edit_term_taxonomy', $term, $taxonomy->name );
		$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );

		/** This action is documented in wp-includes/taxonomy-functions.php */
		do_action( 'edited_term_taxonomy', $term, $taxonomy->name );
	}
}

/**
 * Will update term count based on number of objects.
 *
 * Default callback for the 'link_category' taxonomy.
 *
 * @since 3.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array  $terms    List of term taxonomy IDs.
 * @param object $taxonomy Current taxonomy object of terms.
 */
function _update_generic_term_count( $terms, $taxonomy ) {
	global $wpdb;

	foreach ( (array) $terms as $term ) {
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $term ) );

		/** This action is documented in wp-includes/taxonomy-functions.php */
		do_action( 'edit_term_taxonomy', $term, $taxonomy->name );
		$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );

		/** This action is documented in wp-includes/taxonomy-functions.php */
		do_action( 'edited_term_taxonomy', $term, $taxonomy->name );
	}
}

/**
 * Create a new term for a term_taxonomy item that currently shares its term
 * with another term_taxonomy.
 *
 * @ignore
 * @since 4.2.0
 * @since 4.3.0 Introduced `$record` parameter. Also, `$term_id` and
 *              `$term_taxonomy_id` can now accept objects.
 *
 * @global wpdb $wpdb
 *
 * @param int|object $term_id          ID of the shared term, or the shared term object.
 * @param int|object $term_taxonomy_id ID of the term_taxonomy item to receive a new term, or the term_taxonomy object
 *                                     (corresponding to a row from the term_taxonomy table).
 * @param bool       $record           Whether to record data about the split term in the options table. The recording
 *                                     process has the potential to be resource-intensive, so during batch operations
 *                                     it can be beneficial to skip inline recording and do it just once, after the
 *                                     batch is processed. Only set this to `false` if you know what you are doing.
 *                                     Default: true.
 * @return int|WP_Error When the current term does not need to be split (or cannot be split on the current
 *                      database schema), `$term_id` is returned. When the term is successfully split, the
 *                      new term_id is returned. A WP_Error is returned for miscellaneous errors.
 */
function _split_shared_term( $term_id, $term_taxonomy_id, $record = true ) {
	global $wpdb;

	if ( is_object( $term_id ) ) {
		$shared_term = $term_id;
		$term_id = intval( $shared_term->term_id );
	}

	if ( is_object( $term_taxonomy_id ) ) {
		$term_taxonomy = $term_taxonomy_id;
		$term_taxonomy_id = intval( $term_taxonomy->term_taxonomy_id );
	}

	// If there are no shared term_taxonomy rows, there's nothing to do here.
	$shared_tt_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_taxonomy tt WHERE tt.term_id = %d AND tt.term_taxonomy_id != %d", $term_id, $term_taxonomy_id ) );

	if ( ! $shared_tt_count ) {
		return $term_id;
	}

	/*
	 * Verify that the term_taxonomy_id passed to the function is actually associated with the term_id.
	 * If there's a mismatch, it may mean that the term is already split. Return the actual term_id from the db.
	 */
	$check_term_id = $wpdb->get_var( $wpdb->prepare( "SELECT term_id FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = %d", $term_taxonomy_id ) );
	if ( $check_term_id != $term_id ) {
		return $check_term_id;
	}

	// Pull up data about the currently shared slug, which we'll use to populate the new one.
	if ( empty( $shared_term ) ) {
		$shared_term = $wpdb->get_row( $wpdb->prepare( "SELECT t.* FROM $wpdb->terms t WHERE t.term_id = %d", $term_id ) );
	}

	$new_term_data = array(
		'name' => $shared_term->name,
		'slug' => $shared_term->slug,
		'term_group' => $shared_term->term_group,
	);

	if ( false === $wpdb->insert( $wpdb->terms, $new_term_data ) ) {
		return new WP_Error( 'db_insert_error', __( 'Could not split shared term.' ), $wpdb->last_error );
	}

	$new_term_id = (int) $wpdb->insert_id;

	// Update the existing term_taxonomy to point to the newly created term.
	$wpdb->update( $wpdb->term_taxonomy,
		array( 'term_id' => $new_term_id ),
		array( 'term_taxonomy_id' => $term_taxonomy_id )
	);

	// Reassign child terms to the new parent.
	if ( empty( $term_taxonomy ) ) {
		$term_taxonomy = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = %d", $term_taxonomy_id ) );
	}

	$children_tt_ids = $wpdb->get_col( $wpdb->prepare( "SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE parent = %d AND taxonomy = %s", $term_id, $term_taxonomy->taxonomy ) );
	if ( ! empty( $children_tt_ids ) ) {
		foreach ( $children_tt_ids as $child_tt_id ) {
			$wpdb->update( $wpdb->term_taxonomy,
				array( 'parent' => $new_term_id ),
				array( 'term_taxonomy_id' => $child_tt_id )
			);
			clean_term_cache( $term_id, $term_taxonomy->taxonomy );
		}
	} else {
		// If the term has no children, we must force its taxonomy cache to be rebuilt separately.
		clean_term_cache( $new_term_id, $term_taxonomy->taxonomy );
	}

	// Clean the cache for term taxonomies formerly shared with the current term.
	$shared_term_taxonomies = $wpdb->get_row( $wpdb->prepare( "SELECT taxonomy FROM $wpdb->term_taxonomy WHERE term_id = %d", $term_id ) );
	if ( $shared_term_taxonomies ) {
		foreach ( $shared_term_taxonomies as $shared_term_taxonomy ) {
			clean_term_cache( $term_id, $shared_term_taxonomy );
		}
	}

	// Keep a record of term_ids that have been split, keyed by old term_id. See {@see wp_get_split_term()}.
	if ( $record ) {
		$split_term_data = get_option( '_split_terms', array() );
		if ( ! isset( $split_term_data[ $term_id ] ) ) {
			$split_term_data[ $term_id ] = array();
		}

		$split_term_data[ $term_id ][ $term_taxonomy->taxonomy ] = $new_term_id;
		update_option( '_split_terms', $split_term_data );
	}

	/**
	 * Fires after a previously shared taxonomy term is split into two separate terms.
	 *
	 * @since 4.2.0
	 *
	 * @param int    $term_id          ID of the formerly shared term.
	 * @param int    $new_term_id      ID of the new term created for the $term_taxonomy_id.
	 * @param int    $term_taxonomy_id ID for the term_taxonomy row affected by the split.
	 * @param string $taxonomy         Taxonomy for the split term.
	 */
	do_action( 'split_shared_term', $term_id, $new_term_id, $term_taxonomy_id, $term_taxonomy->taxonomy );

	return $new_term_id;
}

/**
 * Splits a batch of shared taxonomy terms.
 *
 * @since 4.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function _wp_batch_split_terms() {
	global $wpdb;

	$lock_name = 'term_split.lock';

	// Try to lock.
	$lock_result = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO `$wpdb->options` ( `option_name`, `option_value`, `autoload` ) VALUES (%s, %s, 'no') /* LOCK */", $lock_name, time() ) );

	if ( ! $lock_result ) {
		$lock_result = get_option( $lock_name );

		// Bail if we were unable to create a lock, or if the existing lock is still valid.
		if ( ! $lock_result || ( $lock_result > ( time() - HOUR_IN_SECONDS ) ) ) {
			wp_schedule_single_event( time() + ( 5 * MINUTE_IN_SECONDS ), 'wp_split_shared_term_batch' );
			return;
		}
	}

	// Update the lock, as by this point we've definitely got a lock, just need to fire the actions.
	update_option( $lock_name, time() );

	// Get a list of shared terms (those with more than one associated row in term_taxonomy).
	$shared_terms = $wpdb->get_results(
		"SELECT tt.term_id, t.*, count(*) as term_tt_count FROM {$wpdb->term_taxonomy} tt
		 LEFT JOIN {$wpdb->terms} t ON t.term_id = tt.term_id
		 GROUP BY t.term_id
		 HAVING term_tt_count > 1
		 LIMIT 10"
	);

	// No more terms, we're done here.
	if ( ! $shared_terms ) {
		update_option( 'finished_splitting_shared_terms', true );
		delete_option( $lock_name );
		return;
	}

	// Shared terms found? We'll need to run this script again.
	wp_schedule_single_event( time() + ( 2 * MINUTE_IN_SECONDS ), 'wp_split_shared_term_batch' );

	// Rekey shared term array for faster lookups.
	$_shared_terms = array();
	foreach ( $shared_terms as $shared_term ) {
		$term_id = intval( $shared_term->term_id );
		$_shared_terms[ $term_id ] = $shared_term;
	}
	$shared_terms = $_shared_terms;

	// Get term taxonomy data for all shared terms.
	$shared_term_ids = implode( ',', array_keys( $shared_terms ) );
	$shared_tts = $wpdb->get_results( "SELECT * FROM {$wpdb->term_taxonomy} WHERE `term_id` IN ({$shared_term_ids})" );

	// Split term data recording is slow, so we do it just once, outside the loop.
	$split_term_data = get_option( '_split_terms', array() );
	$skipped_first_term = $taxonomies = array();
	foreach ( $shared_tts as $shared_tt ) {
		$term_id = intval( $shared_tt->term_id );

		// Don't split the first tt belonging to a given term_id.
		if ( ! isset( $skipped_first_term[ $term_id ] ) ) {
			$skipped_first_term[ $term_id ] = 1;
			continue;
		}

		if ( ! isset( $split_term_data[ $term_id ] ) ) {
			$split_term_data[ $term_id ] = array();
		}

		// Keep track of taxonomies whose hierarchies need flushing.
		if ( ! isset( $taxonomies[ $shared_tt->taxonomy ] ) ) {
			$taxonomies[ $shared_tt->taxonomy ] = 1;
		}

		// Split the term.
		$split_term_data[ $term_id ][ $shared_tt->taxonomy ] = _split_shared_term( $shared_terms[ $term_id ], $shared_tt, false );
	}

	// Rebuild the cached hierarchy for each affected taxonomy.
	foreach ( array_keys( $taxonomies ) as $tax ) {
		delete_option( "{$tax}_children" );
		_get_term_hierarchy( $tax );
	}

	update_option( '_split_terms', $split_term_data );

	delete_option( $lock_name );
}

/**
 * In order to avoid the _wp_batch_split_terms() job being accidentally removed,
 * check that it's still scheduled while we haven't finished splitting terms.
 *
 * @ignore
 * @since 4.3.0
 */
function _wp_check_for_scheduled_split_terms() {
	if ( ! get_option( 'finished_splitting_shared_terms' ) && ! wp_next_scheduled( 'wp_split_shared_term_batch' ) ) {
		wp_schedule_single_event( time() + MINUTE_IN_SECONDS, 'wp_split_shared_term_batch' );
	}
}

/**
 * Check default categories when a term gets split to see if any of them need to be updated.
 *
 * @ignore
 * @since 4.2.0
 *
 * @param int    $term_id          ID of the formerly shared term.
 * @param int    $new_term_id      ID of the new term created for the $term_taxonomy_id.
 * @param int    $term_taxonomy_id ID for the term_taxonomy row affected by the split.
 * @param string $taxonomy         Taxonomy for the split term.
 */
function _wp_check_split_default_terms( $term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {
	if ( 'category' != $taxonomy ) {
		return;
	}

	foreach ( array( 'default_category', 'default_link_category', 'default_email_category' ) as $option ) {
		if ( $term_id == get_option( $option, -1 ) ) {
			update_option( $option, $new_term_id );
		}
	}
}

/**
 * Check menu items when a term gets split to see if any of them need to be updated.
 *
 * @ignore
 * @since 4.2.0
 *
 * @global wpdb $wpdb
 *
 * @param int    $term_id          ID of the formerly shared term.
 * @param int    $new_term_id      ID of the new term created for the $term_taxonomy_id.
 * @param int    $term_taxonomy_id ID for the term_taxonomy row affected by the split.
 * @param string $taxonomy         Taxonomy for the split term.
 */
function _wp_check_split_terms_in_menus( $term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {
	global $wpdb;
	$post_ids = $wpdb->get_col( $wpdb->prepare(
		"SELECT m1.post_id
		FROM {$wpdb->postmeta} AS m1
			INNER JOIN {$wpdb->postmeta} AS m2 ON ( m2.post_id = m1.post_id )
			INNER JOIN {$wpdb->postmeta} AS m3 ON ( m3.post_id = m1.post_id )
		WHERE ( m1.meta_key = '_menu_item_type' AND m1.meta_value = 'taxonomy' )
			AND ( m2.meta_key = '_menu_item_object' AND m2.meta_value = '%s' )
			AND ( m3.meta_key = '_menu_item_object_id' AND m3.meta_value = %d )",
		$taxonomy,
		$term_id
	) );

	if ( $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			update_post_meta( $post_id, '_menu_item_object_id', $new_term_id, $term_id );
		}
	}
}

/**
 * If the term being split is a nav_menu, change associations.
 *
 * @ignore
 * @since 4.3.0
 *
 * @param int    $term_id          ID of the formerly shared term.
 * @param int    $new_term_id      ID of the new term created for the $term_taxonomy_id.
 * @param int    $term_taxonomy_id ID for the term_taxonomy row affected by the split.
 * @param string $taxonomy         Taxonomy for the split term.
 */
function _wp_check_split_nav_menu_terms( $term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {
	if ( 'nav_menu' !== $taxonomy ) {
		return;
	}

	// Update menu locations.
	$locations = get_nav_menu_locations();
	foreach ( $locations as $location => $menu_id ) {
		if ( $term_id == $menu_id ) {
			$locations[ $location ] = $new_term_id;
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Get data about terms that previously shared a single term_id, but have since been split.
 *
 * @since 4.2.0
 *
 * @param int $old_term_id Term ID. This is the old, pre-split term ID.
 * @return array Array of new term IDs, keyed by taxonomy.
 */
function wp_get_split_terms( $old_term_id ) {
	$split_terms = get_option( '_split_terms', array() );

	$terms = array();
	if ( isset( $split_terms[ $old_term_id ] ) ) {
		$terms = $split_terms[ $old_term_id ];
	}

	return $terms;
}

/**
 * Get the new term ID corresponding to a previously split term.
 *
 * @since 4.2.0
 *
 * @param int    $old_term_id Term ID. This is the old, pre-split term ID.
 * @param string $taxonomy    Taxonomy that the term belongs to.
 * @return int|false If a previously split term is found corresponding to the old term_id and taxonomy,
 *                   the new term_id will be returned. If no previously split term is found matching
 *                   the parameters, returns false.
 */
function wp_get_split_term( $old_term_id, $taxonomy ) {
	$split_terms = wp_get_split_terms( $old_term_id );

	$term_id = false;
	if ( isset( $split_terms[ $taxonomy ] ) ) {
		$term_id = (int) $split_terms[ $taxonomy ];
	}

	return $term_id;
}

/**
 * Generate a permalink for a taxonomy term archive.
 *
 * @since 2.5.0
 *
 * @global WP_Rewrite $wp_rewrite
 *
 * @param object|int|string $term     The term object, ID, or slug whose link will be retrieved.
 * @param string            $taxonomy Optional. Taxonomy. Default empty.
 * @return string|WP_Error HTML link to taxonomy term archive on success, WP_Error if term does not exist.
 */
function get_term_link( $term, $taxonomy = '' ) {
	global $wp_rewrite;

	if ( !is_object($term) ) {
		if ( is_int( $term ) ) {
			$term = get_term( $term, $taxonomy );
		} else {
			$term = get_term_by( 'slug', $term, $taxonomy );
		}
	}

	if ( !is_object($term) )
		$term = new WP_Error('invalid_term', __('Empty Term'));

	if ( is_wp_error( $term ) )
		return $term;

	$taxonomy = $term->taxonomy;

	$termlink = $wp_rewrite->get_extra_permastruct($taxonomy);

	$slug = $term->slug;
	$t = get_taxonomy($taxonomy);

	if ( empty($termlink) ) {
		if ( 'category' == $taxonomy )
			$termlink = '?cat=' . $term->term_id;
		elseif ( $t->query_var )
			$termlink = "?$t->query_var=$slug";
		else
			$termlink = "?taxonomy=$taxonomy&term=$slug";
		$termlink = home_url($termlink);
	} else {
		if ( $t->rewrite['hierarchical'] ) {
			$hierarchical_slugs = array();
			$ancestors = get_ancestors( $term->term_id, $taxonomy, 'taxonomy' );
			foreach ( (array)$ancestors as $ancestor ) {
				$ancestor_term = get_term($ancestor, $taxonomy);
				$hierarchical_slugs[] = $ancestor_term->slug;
			}
			$hierarchical_slugs = array_reverse($hierarchical_slugs);
			$hierarchical_slugs[] = $slug;
			$termlink = str_replace("%$taxonomy%", implode('/', $hierarchical_slugs), $termlink);
		} else {
			$termlink = str_replace("%$taxonomy%", $slug, $termlink);
		}
		$termlink = home_url( user_trailingslashit($termlink, 'category') );
	}
	// Back Compat filters.
	if ( 'post_tag' == $taxonomy ) {

		/**
		 * Filter the tag link.
		 *
		 * @since 2.3.0
		 * @deprecated 2.5.0 Use 'term_link' instead.
		 *
		 * @param string $termlink Tag link URL.
		 * @param int    $term_id  Term ID.
		 */
		$termlink = apply_filters( 'tag_link', $termlink, $term->term_id );
	} elseif ( 'category' == $taxonomy ) {

		/**
		 * Filter the category link.
		 *
		 * @since 1.5.0
		 * @deprecated 2.5.0 Use 'term_link' instead.
		 *
		 * @param string $termlink Category link URL.
		 * @param int    $term_id  Term ID.
		 */
		$termlink = apply_filters( 'category_link', $termlink, $term->term_id );
	}

	/**
	 * Filter the term link.
	 *
	 * @since 2.5.0
	 *
	 * @param string $termlink Term link URL.
	 * @param object $term     Term object.
	 * @param string $taxonomy Taxonomy slug.
	 */
	return apply_filters( 'term_link', $termlink, $term, $taxonomy );
}

/**
 * Display the taxonomies of a post with available options.
 *
 * This function can be used within the loop to display the taxonomies for a
 * post without specifying the Post ID. You can also use it outside the Loop to
 * display the taxonomies for a specific post.
 *
 * @since 2.5.0
 *
 * @param array $args {
 *     Arguments about which post to use and how to format the output. Shares all of the arguments
 *     supported by get_the_taxonomies(), in addition to the following.
 *
 *     @type  int|WP_Post $post   Post ID or object to get taxonomies of. Default current post.
 *     @type  string      $before Displays before the taxonomies. Default empty string.
 *     @type  string      $sep    Separates each taxonomy. Default is a space.
 *     @type  string      $after  Displays after the taxonomies. Default empty string.
 * }
 * @param array $args See {@link get_the_taxonomies()} for a description of arguments and their defaults.
 */
function the_taxonomies( $args = array() ) {
	$defaults = array(
		'post' => 0,
		'before' => '',
		'sep' => ' ',
		'after' => '',
	);

	$r = wp_parse_args( $args, $defaults );

	echo $r['before'] . join( $r['sep'], get_the_taxonomies( $r['post'], $r ) ) . $r['after'];
}

/**
 * Retrieve all taxonomies associated with a post.
 *
 * This function can be used within the loop. It will also return an array of
 * the taxonomies with links to the taxonomy and name.
 *
 * @since 2.5.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @param array $args {
 *     Optional. Arguments about how to format the list of taxonomies. Default empty array.
 *
 *     @type string $template      Template for displaying a taxonomy label and list of terms.
 *                                 Default is "Label: Terms."
 *     @type string $term_template Template for displaying a single term in the list. Default is the term name
 *                                 linked to its archive.
 * }
 * @return array List of taxonomies.
 */
function get_the_taxonomies( $post = 0, $args = array() ) {
	$post = get_post( $post );

	$args = wp_parse_args( $args, array(
		/* translators: %s: taxonomy label, %l: list of terms formatted as per $term_template */
		'template' => __( '%s: %l.' ),
		'term_template' => '<a href="%1$s">%2$s</a>',
	) );

	$taxonomies = array();

	if ( ! $post ) {
		return $taxonomies;
	}

	foreach ( get_object_taxonomies( $post ) as $taxonomy ) {
		$t = (array) get_taxonomy( $taxonomy );
		if ( empty( $t['label'] ) ) {
			$t['label'] = $taxonomy;
		}
		if ( empty( $t['args'] ) ) {
			$t['args'] = array();
		}
		if ( empty( $t['template'] ) ) {
			$t['template'] = $args['template'];
		}
		if ( empty( $t['term_template'] ) ) {
			$t['term_template'] = $args['term_template'];
		}

		$terms = get_object_term_cache( $post->ID, $taxonomy );
		if ( false === $terms ) {
			$terms = wp_get_object_terms( $post->ID, $taxonomy, $t['args'] );
		}
		$links = array();

		foreach ( $terms as $term ) {
			$links[] = wp_sprintf( $t['term_template'], esc_attr( get_term_link( $term ) ), $term->name );
		}
		if ( $links ) {
			$taxonomies[$taxonomy] = wp_sprintf( $t['template'], $t['label'], $links, $terms );
		}
	}
	return $taxonomies;
}

/**
 * Retrieve all taxonomies of a post with just the names.
 *
 * @since 2.5.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return array
 */
function get_post_taxonomies( $post = 0 ) {
	$post = get_post( $post );

	return get_object_taxonomies($post);
}

/**
 * Determine if the given object is associated with any of the given terms.
 *
 * The given terms are checked against the object's terms' term_ids, names and slugs.
 * Terms given as integers will only be checked against the object's terms' term_ids.
 * If no terms are given, determines if object is associated with any terms in the given taxonomy.
 *
 * @since 2.7.0
 *
 * @param int              $object_id ID of the object (post ID, link ID, ...).
 * @param string           $taxonomy  Single taxonomy name.
 * @param int|string|array $terms     Optional. Term term_id, name, slug or array of said. Default null.
 * @return bool|WP_Error WP_Error on input error.
 */
function is_object_in_term( $object_id, $taxonomy, $terms = null ) {
	if ( !$object_id = (int) $object_id )
		return new WP_Error( 'invalid_object', __( 'Invalid object ID' ) );

	$object_terms = get_object_term_cache( $object_id, $taxonomy );
	if ( false === $object_terms )
		 $object_terms = wp_get_object_terms( $object_id, $taxonomy );

	if ( is_wp_error( $object_terms ) )
		return $object_terms;
	if ( empty( $object_terms ) )
		return false;
	if ( empty( $terms ) )
		return ( !empty( $object_terms ) );

	$terms = (array) $terms;

	if ( $ints = array_filter( $terms, 'is_int' ) )
		$strs = array_diff( $terms, $ints );
	else
		$strs =& $terms;

	foreach ( $object_terms as $object_term ) {
		// If term is an int, check against term_ids only.
		if ( $ints && in_array( $object_term->term_id, $ints ) ) {
			return true;
		}

		if ( $strs ) {
			// Only check numeric strings against term_id, to avoid false matches due to type juggling.
			$numeric_strs = array_map( 'intval', array_filter( $strs, 'is_numeric' ) );
			if ( in_array( $object_term->term_id, $numeric_strs, true ) ) {
				return true;
			}

			if ( in_array( $object_term->name, $strs ) ) return true;
			if ( in_array( $object_term->slug, $strs ) ) return true;
		}
	}

	return false;
}

/**
 * Determine if the given object type is associated with the given taxonomy.
 *
 * @since 3.0.0
 *
 * @param string $object_type Object type string.
 * @param string $taxonomy    Single taxonomy name.
 * @return bool True if object is associated with the taxonomy, otherwise false.
 */
function is_object_in_taxonomy( $object_type, $taxonomy ) {
	$taxonomies = get_object_taxonomies( $object_type );
	if ( empty( $taxonomies ) ) {
		return false;
	}
	return in_array( $taxonomy, $taxonomies );
}

/**
 * Get an array of ancestor IDs for a given object.
 *
 * @since 3.1.0
 * @since 4.1.0 Introduced the `$resource_type` argument.
 *
 * @param int    $object_id     Optional. The ID of the object. Default 0.
 * @param string $object_type   Optional. The type of object for which we'll be retrieving
 *                              ancestors. Accepts a post type or a taxonomy name. Default empty.
 * @param string $resource_type Optional. Type of resource $object_type is. Accepts 'post_type'
 *                              or 'taxonomy'. Default empty.
 * @return array An array of ancestors from lowest to highest in the hierarchy.
 */
function get_ancestors( $object_id = 0, $object_type = '', $resource_type = '' ) {
	$object_id = (int) $object_id;

	$ancestors = array();

	if ( empty( $object_id ) ) {

		/** This filter is documented in wp-includes/taxonomy-functions.php */
		return apply_filters( 'get_ancestors', $ancestors, $object_id, $object_type, $resource_type );
	}

	if ( ! $resource_type ) {
		if ( is_taxonomy_hierarchical( $object_type ) ) {
			$resource_type = 'taxonomy';
		} elseif ( post_type_exists( $object_type ) ) {
			$resource_type = 'post_type';
		}
	}

	if ( 'taxonomy' === $resource_type ) {
		$term = get_term($object_id, $object_type);
		while ( ! is_wp_error($term) && ! empty( $term->parent ) && ! in_array( $term->parent, $ancestors ) ) {
			$ancestors[] = (int) $term->parent;
			$term = get_term($term->parent, $object_type);
		}
	} elseif ( 'post_type' === $resource_type ) {
		$ancestors = get_post_ancestors($object_id);
	}

	/**
	 * Filter a given object's ancestors.
	 *
	 * @since 3.1.0
	 * @since 4.1.1 Introduced the `$resource_type` parameter.
	 *
	 * @param array  $ancestors     An array of object ancestors.
	 * @param int    $object_id     Object ID.
	 * @param string $object_type   Type of object.
	 * @param string $resource_type Type of resource $object_type is.
	 */
	return apply_filters( 'get_ancestors', $ancestors, $object_id, $object_type, $resource_type );
}

/**
 * Returns the term's parent's term_ID.
 *
 * @since 3.1.0
 *
 * @param int    $term_id  Term ID.
 * @param string $taxonomy Taxonomy name.
 * @return int|false False on error.
 */
function wp_get_term_taxonomy_parent_id( $term_id, $taxonomy ) {
	$term = get_term( $term_id, $taxonomy );
	if ( ! $term || is_wp_error( $term ) ) {
		return false;
	}
	return (int) $term->parent;
}

/**
 * Checks the given subset of the term hierarchy for hierarchy loops.
 * Prevents loops from forming and breaks those that it finds.
 *
 * Attached to the {@see 'wp_update_term_parent'} filter.
 *
 * @since 3.1.0
 *
 * @param int    $parent   `term_id` of the parent for the term we're checking.
 * @param int    $term_id  The term we're checking.
 * @param string $taxonomy The taxonomy of the term we're checking.
 *
 * @return int The new parent for the term.
 */
function wp_check_term_hierarchy_for_loops( $parent, $term_id, $taxonomy ) {
	// Nothing fancy here - bail
	if ( !$parent )
		return 0;

	// Can't be its own parent.
	if ( $parent == $term_id )
		return 0;

	// Now look for larger loops.
	if ( !$loop = wp_find_hierarchy_loop( 'wp_get_term_taxonomy_parent_id', $term_id, $parent, array( $taxonomy ) ) )
		return $parent; // No loop

	// Setting $parent to the given value causes a loop.
	if ( isset( $loop[$term_id] ) )
		return 0;

	// There's a loop, but it doesn't contain $term_id. Break the loop.
	foreach ( array_keys( $loop ) as $loop_member )
		wp_update_term( $loop_member, $taxonomy, array( 'parent' => 0 ) );

	return $parent;
}

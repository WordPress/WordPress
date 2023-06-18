<?php
/**
 * Generate a snippet to register a new taxonomy.
 *
 * @package WPCode
 */

/**
 * The Taxonomy generator class.
 */
class WPCode_Generator_Taxonomy extends WPCode_Generator_Type {

	/**
	 * The generator slug.
	 *
	 * @var string
	 */
	public $name = 'taxonomy';

	/**
	 * The categories for this generator.
	 *
	 * @var string[]
	 */
	public $categories = array(
		'content',
	);

	/**
	 * Set the translatable strings.
	 *
	 * @return void
	 */
	protected function set_strings() {
		$this->title       = __( 'Taxonomy', 'insert-headers-and-footers' );
		$this->description = __( 'Create a custom taxonomy for your posts using this generator.', 'insert-headers-and-footers' );
	}

	/**
	 * Load the generator tabs.
	 *
	 * @return void
	 */
	protected function load_tabs() {
		$this->tabs = array(
			'info'         => array(
				'label'   => __( 'Info', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						// Column 1 fields.
						array(
							'type'    => 'description',
							'label'   => __( 'Overview', 'insert-headers-and-footers' ),
							'content' => __( 'Use this generator to create custom taxonomies for your WordPress site.', 'insert-headers-and-footers' ),
						),
					),
					// Column 2.
					array(
						// Column 2 fields.
						array(
							'type'    => 'list',
							'label'   => __( 'Usage', 'insert-headers-and-footers' ),
							'content' => array(
								__( 'Fill in the forms using the menu on the left.', 'insert-headers-and-footers' ),
								__( 'Click the "Update Code" button.', 'insert-headers-and-footers' ),
								__( 'Click on "Use Snippet" to create a new snippet with the generated code.', 'insert-headers-and-footers' ),
								__( 'Activate and save the snippet and you\'re ready to go', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 3.
					array(
						// Column 3 fields.
						array(
							'type'    => 'description',
							'label'   => __( 'Examples', 'insert-headers-and-footers' ),
							'content' => __( 'Use this to add more taxonomies to posts or custom post types. For example, if you used the Post Type generator to create an Artist post type you can use this one to create a Genre taxonomy.', 'insert-headers-and-footers' ),
						),
					),
				),
			),
			'general'      => array(
				'label'   => __( 'General', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Function name', 'insert-headers-and-footers' ),
							'description' => __( 'Make this unique to avoid conflicts with other snippets', 'insert-headers-and-footers' ),
							'id'          => 'function_name',
							'placeholder' => 'register_custom_taxonomy',
							'default'     => 'register_custom_taxonomy' . time(),
							// This makes it unique for people who don't want to customize.
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Text Domain', 'insert-headers-and-footers' ),
							'description' => __( 'Optional text domain for translations.', 'insert-headers-and-footers' ),
							'id'          => 'text_domain',
							'placeholder' => 'text_domain',
							'default'     => 'text_domain',
						),
					),
				),
			),
			'taxonomy'     => array(
				'label'   => __( 'Taxonomy', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Taxonomy Key', 'insert-headers-and-footers' ),
							'description' => __( 'Name of taxonomy used in the code, lowercase maximum 20 characters.', 'insert-headers-and-footers' ),
							'id'          => 'taxonomy',
							'placeholder' => 'taxonomy',
							'default'     => 'taxonomy',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Name (Singular)', 'insert-headers-and-footers' ),
							'description' => __( 'The singular taxonomy name (e.g. Genre, Year).', 'insert-headers-and-footers' ),
							'id'          => 'label',
							'placeholder' => 'Taxonomy',
							'default'     => 'Taxonomy',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Name (Plural)', 'insert-headers-and-footers' ),
							'description' => __( 'The taxonomy plural name (e.g. Genres, Years).', 'insert-headers-and-footers' ),
							'id'          => 'label_count',
							'placeholder' => 'Taxonomies',
							'default'     => 'Taxonomies',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Link To Post Type(s)', 'insert-headers-and-footers' ),
							'description' => __( 'Comma-separated list of Post Types (e.g. post, page)', 'insert-headers-and-footers' ),
							'id'          => 'post_types',
							'placeholder' => 'post',
							'default'     => 'post',
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Hierarchical', 'insert-headers-and-footers' ),
							'description' => __( 'Hierarchical taxonomies can have descendants.', 'insert-headers-and-footers' ),
							'id'          => 'hierarchical',
							'default'     => 'false',
							'options'     => array(
								'false' => __( 'No, like tags', 'insert-headers-and-footers' ),
								'true'  => __( 'Yes, like categories', 'insert-headers-and-footers' ),
							),
						),
					),
				),
			),
			'labels'       => array(
				'label'   => __( 'Labels', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Menu Name', 'insert-headers-and-footers' ),
							'id'          => 'label_menu_name',
							'placeholder' => 'Taxonomy',
							'default'     => 'Taxonomy',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'All Items', 'insert-headers-and-footers' ),
							'id'          => 'label_all_items',
							'placeholder' => 'All Items',
							'default'     => 'All Items',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Parent Item', 'insert-headers-and-footers' ),
							'id'          => 'label_parent_item',
							'placeholder' => 'Parent Item',
							'default'     => 'Item Archives',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Parent Item colon', 'insert-headers-and-footers' ),
							'id'          => 'label_parent_item_colon',
							'placeholder' => 'Parent Item:',
							'default'     => 'Parent Item:',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'New Item Name', 'insert-headers-and-footers' ),
							'id'          => 'label_new_item',
							'placeholder' => 'New Item Name',
							'default'     => 'New Item Name',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Add New Item', 'insert-headers-and-footers' ),
							'id'          => 'label_add_new_item',
							'placeholder' => 'Add New Item',
							'default'     => 'Add New Item',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Edit Item', 'insert-headers-and-footers' ),
							'id'          => 'label_edit_item',
							'placeholder' => 'Edit Item',
							'default'     => 'Edit Item',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Update Item', 'insert-headers-and-footers' ),
							'id'          => 'label_update_item',
							'placeholder' => 'Update Item',
							'default'     => 'Update Item',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'View Item', 'insert-headers-and-footers' ),
							'id'          => 'label_view_item',
							'placeholder' => 'View Item',
							'default'     => 'View Item',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Separate Items with commas', 'insert-headers-and-footers' ),
							'id'          => 'label_separate_items_with_commas',
							'placeholder' => 'Separate Items with commas',
							'default'     => 'Separate Items with commas',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Add or Remove Items', 'insert-headers-and-footers' ),
							'id'          => 'label_add_or_remove_items',
							'placeholder' => 'Add or remove items',
							'default'     => 'Add or remove items',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Choose From Most Used', 'insert-headers-and-footers' ),
							'id'          => 'label_choose_from_most_used',
							'placeholder' => 'Choose from the most used',
							'default'     => 'Choose from the most used',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Popular Items', 'insert-headers-and-footers' ),
							'id'          => 'label_popular_items',
							'placeholder' => 'Popular Items',
							'default'     => 'Popular Items',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Search Items', 'insert-headers-and-footers' ),
							'id'          => 'label_search_items',
							'placeholder' => 'Search Items',
							'default'     => 'Search Items',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Not Found', 'insert-headers-and-footers' ),
							'id'          => 'label_not_found',
							'placeholder' => 'Not Found',
							'default'     => 'Not Found',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'No items', 'insert-headers-and-footers' ),
							'id'          => 'label_no_items',
							'placeholder' => 'No items',
							'default'     => 'No items',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Items list', 'insert-headers-and-footers' ),
							'id'          => 'label_items_list',
							'placeholder' => 'Items list',
							'default'     => 'Items list',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Items list navigation', 'insert-headers-and-footers' ),
							'id'          => 'label_items_list_navigation',
							'placeholder' => 'Items list navigation',
							'default'     => 'Items list navigation',
						),
					),
				),
			),
			'visibility'   => array(
				'label'   => __( 'Visibility', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Public', 'insert-headers-and-footers' ),
							// Translators: Placeholders add a link to the wp.org documentation page.
							'description' => sprintf( __( 'Should this taxonomy be %1$svisible to authors%2$s?', 'insert-headers-and-footers' ), '<a href="https://developer.wordpress.org/reference/functions/register_taxonomy/#additional-parameter-information" target="_blank">', '</a>' ),
							'id'          => 'public',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Show UI', 'insert-headers-and-footers' ),
							'description' => __( 'Should this taxonomy have an User Interface for managing?', 'insert-headers-and-footers' ),
							'id'          => 'show_ui',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Show Admin Column', 'insert-headers-and-footers' ),
							'description' => __( 'Should this taxonomy add a column in the list of associated post types?', 'insert-headers-and-footers' ),
							'id'          => 'show_admin_column',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Show Tag Cloud', 'insert-headers-and-footers' ),
							'description' => __( 'Should this taxonomy be visible in the tag cloud widget?', 'insert-headers-and-footers' ),
							'id'          => 'show_tagcloud',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Show in Navigation Menus', 'insert-headers-and-footers' ),
							'description' => __( 'Should this taxonomy be available in menus (Appearance > Menus).', 'insert-headers-and-footers' ),
							'id'          => 'show_in_nav_menus',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
					),
				),
			),
			'permalinks'   => array(
				'label'   => __( 'Permalinks', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Permalink Rewrite', 'insert-headers-and-footers' ),
							'description' => __( 'Use Default Permalinks, disable automatic rewriting or use custom permalinks.', 'insert-headers-and-footers' ),
							'id'          => 'rewrite',
							'default'     => 'true',
							'options'     => array(
								'true'   => __( 'Default (taxonomy key)', 'insert-headers-and-footers' ),
								'false'  => __( 'Disable permalink rewrites', 'insert-headers-and-footers' ),
								'custom' => __( 'Custom permalink structure', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'URL Slug', 'insert-headers-and-footers' ),
							'description' => __( 'If you selected custom permalinks use this field for the rewrite base, e.g. taxonomy in https://yoursite.com/taxonomy', 'insert-headers-and-footers' ),
							'id'          => 'rewrite_slug',
							'default'     => '',
							'placeholder' => '',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Prepend permastruct', 'insert-headers-and-footers' ),
							'description' => __( 'Should the permastruct be prepended to the url (with_front parameter).', 'insert-headers-and-footers' ),
							'id'          => 'rewrite_with_front',
							'default'     => 'true',
							'options'     => array(
								'true'  => __( 'Yes - default', 'insert-headers-and-footers' ),
								'false' => __( 'No', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'        => 'select',
							'label'       => __( 'Hierarchical URL Slug', 'insert-headers-and-footers' ),
							'description' => __( 'For hierarchical taxonomies use the whole hierarchy in the URL?', 'insert-headers-and-footers' ),
							'id'          => 'rewrite_hierarchical',
							'default'     => 'false',
							'options'     => array(
								'false' => __( 'No - default', 'insert-headers-and-footers' ),
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
							),
						),
					),
				),
			),
			'capabilities' => array(
				'label'   => __( 'Capabilities', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Capabilities', 'insert-headers-and-footers' ),
							// Translators: Placeholders add link to wp.org docs.
							'description' => sprintf( __( 'User capabilities in relation to this taxonomy. %1$sSee Documentation.%2$s', 'insert-headers-and-footers' ), '<a href="https://developer.wordpress.org/reference/functions/register_taxonomy/#additional-parameter-information" target="_blank">', '</a>' ),
							'id'          => 'capabilities',
							'default'     => 'true',
							'options'     => array(
								'true'   => __( 'Base capabilities - default', 'insert-headers-and-footers' ),
								'custom' => __( 'Custom Capabilities', 'insert-headers-and-footers' ),
							),
						),
						array(
							'type'    => 'description',
							'label'   => __( 'Custom Capabilities', 'insert-headers-and-footers' ),
							'content' => __( 'Use the fields on the right to assign custom capabilities for this taxonomy.' ),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Edit Terms', 'insert-headers-and-footers' ),
							'id'          => 'edit_terms',
							'default'     => 'manage_categories',
							'placeholder' => 'manage_categories',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Delete Terms', 'insert-headers-and-footers' ),
							'id'          => 'delete_terms',
							'default'     => 'manage_categories',
							'placeholder' => 'manage_categories',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Manage Terms', 'insert-headers-and-footers' ),
							'id'          => 'manage_terms',
							'default'     => 'manage_categories',
							'placeholder' => 'manage_categories',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Assign Terms', 'insert-headers-and-footers' ),
							'id'          => 'assign_terms',
							'default'     => 'edit_posts',
							'placeholder' => 'edit_posts',
						),
					),
				),
			),
			'rest_api'     => array(
				'label'   => __( 'Rest API', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Show in Rest API?', 'insert-headers-and-footers' ),
							'description' => __( 'Add the taxonomy to the WordPress wp-json API.', 'insert-headers-and-footers' ),
							'id'          => 'show_in_rest',
							'default'     => 'false',
							'options'     => array(
								'true'  => __( 'Yes', 'insert-headers-and-footers' ),
								'false' => __( 'No - default', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Rest Base', 'insert-headers-and-footers' ),
							'description' => __( 'The base slug that this taxonomy will use in the REST API.', 'insert-headers-and-footers' ),
							'id'          => 'rest_base',
							'default'     => '',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Rest Controller Class', 'insert-headers-and-footers' ),
							'description' => __( 'The name of a custom Rest Controller class instead of WP_REST_Terms_Controller.', 'insert-headers-and-footers' ),
							'id'          => 'rest_controller_class',
							'default'     => '',
						),
					),
				),
			),
		);
	}

	/**
	 * Get the snippet code with dynamic values applied.
	 *
	 * @return string
	 */
	public function get_snippet_code() {

		$rewrite         = $this->get_value( 'rewrite' );
		$rewrite_options = '';
		if ( 'true' === $rewrite ) {
			$rewrite = '';
		} elseif ( 'custom' === $rewrite ) {
			$rewrite         = "\t\t'rewrite'                    => \$rewrite_options,";
			$rewrite_options = "
	\$rewrite_options = array(
		'slug'         => '{$this->get_value('rewrite_slug')}',
		'with_front'   => {$this->get_value( 'rewrite_with_front')},
		'hierarchical' => {$this->get_value( 'rewrite_hierarchical')},
 	); 
			";
		} else {
			$rewrite = "\t\t'rewrite'               => $rewrite,";
		}

		$custom_capabilities = '';
		$capabilities        = $this->get_value( 'capabilities' );

		if ( 'custom' === $capabilities ) {
			$custom_capabilities = "
	\$capabilities = array(
		'edit_terms'   => '{$this->get_value( 'edit_terms')}',
		'delete_terms' => '{$this->get_value( 'delete_terms')}',
		'manage_terms' => '{$this->get_value( 'manage_terms')}',
		'assign_terms' => '{$this->get_value( 'assign_terms')}',
	);
			";
			$capabilities        = "        'capabilities'          => \$capabilities,";
		} else {
			$capabilities = '';
		}

		return <<<EOD
// Register Custom Taxonomy
function {$this->get_value( 'function_name' )}() {

	\$labels = array(
		'name'                       => _x( '{$this->get_value( 'label_count' )}', 'Taxonomy General Name', '{$this->get_value( 'text_domain' )}' ),
		'singular_name'              => _x( '{$this->get_value( 'label' )}', 'Taxonomy Singular Name', '{$this->get_value( 'text_domain' )}' ),
		'menu_name'                  => __( '{$this->get_value( 'label_menu_name' )}', '{$this->get_value( 'text_domain' )}' ),
		'all_items'                  => __( '{$this->get_value( 'label_all_items' )}', '{$this->get_value( 'text_domain' )}' ),
		'parent_item'                => __( '{$this->get_value( 'label_parent_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'parent_item_colon'          => __( '{$this->get_value( 'label_parent_item_colon' )}', '{$this->get_value( 'text_domain' )}' ),
		'new_item_name'              => __( '{$this->get_value( 'label_new_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'add_new_item'               => __( '{$this->get_value( 'label_add_new_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'edit_item'                  => __( '{$this->get_value( 'label_edit_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'update_item'                => __( '{$this->get_value( 'label_update_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'view_item'                  => __( '{$this->get_value( 'label_view_item' )}', '{$this->get_value( 'text_domain' )}' ),
		'separate_items_with_commas' => __( '{$this->get_value( 'label_separate_items_with_commas' )}', '{$this->get_value( 'text_domain' )}' ),
		'add_or_remove_items'        => __( '{$this->get_value( 'label_add_or_remove_items' )}', '{$this->get_value( 'text_domain' )}' ),
		'choose_from_most_used'      => __( '{$this->get_value( 'label_choose_from_most_used' )}', '{$this->get_value( 'text_domain' )}' ),
		'popular_items'              => __( '{$this->get_value( 'label_popular_items' )}', '{$this->get_value( 'text_domain' )}' ),
		'search_items'               => __( '{$this->get_value( 'label_search_items' )}', '{$this->get_value( 'text_domain' )}' ),
		'not_found'                  => __( '{$this->get_value( 'label_not_found' )}', '{$this->get_value( 'text_domain' )}' ),
		'no_terms'                   => __( '{$this->get_value( 'label_no_items' )}', '{$this->get_value( 'text_domain' )}' ),
		'items_list'                 => __( '{$this->get_value( 'label_items_list' )}', '{$this->get_value( 'text_domain' )}' ),
		'items_list_navigation'      => __( '{$this->get_value( 'label_items_list_navigation' )}', '{$this->get_value( 'text_domain' )}' ),
	);
	$rewrite_options
	$custom_capabilities
	\$args = array(
		'labels'                => \$labels,
		'hierarchical'          => {$this->get_value( 'hierarchical' )},
		'public'                => {$this->get_value( 'public' )},
		'show_ui'               => {$this->get_value( 'show_ui' )},
		'show_admin_column'     => {$this->get_value( 'show_admin_column' )},
		'show_in_nav_menus'     => {$this->get_value( 'show_in_nav_menus' )},
		'show_tagcloud'         => {$this->get_value( 'show_tagcloud' )},
		'show_in_rest'          => {$this->get_value( 'show_in_rest' )},
$rewrite
$capabilities
{$this->get_optional_value( 'show_in_rest' )}{$this->get_optional_value( 'rest_base', true )}{$this->get_optional_value( 'rest_controller_class', true )}
	);
	register_taxonomy( '{$this->get_value( 'taxonomy' )}', {$this->get_value_comma_separated( 'post_types' )}, \$args );

}
add_action( 'init', '{$this->get_value( 'function_name' )}', 5 );
EOD;
	}

}
